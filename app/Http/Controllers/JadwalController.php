<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jadwal;
use App\Models\Tahun;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Data Master
        $rolesToDisplay = ['Guru', 'Wali Kelas', 'Kesiswaan'];
        $users = User::whereIn('role', $rolesToDisplay)->get();
        $tahun = Tahun::all();
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        // 2. Filter Kelas
        $selectedKelasId = $request->input('kelas_id');
        if (!$selectedKelasId && $kelas->isNotEmpty()) {
            $selectedKelasId = $kelas->first()->id;
        }

        // 3. Definisi Grid
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; 
        $timeSlots = range(1, 10); // Jam 1 s.d 10

        // 4. Susun Matrix Jadwal
        $scheduleMatrix = [];
        if ($selectedKelasId) {
            $jadwalData = Jadwal::where('kelas_id', $selectedKelasId)
                                ->with(['mapel', 'user', 'tahun']) 
                                ->get();
            
            foreach ($jadwalData as $j) {
                // Loop dari Jam Mulai sampai Jam Selesai
                // Agar jadwal tersimpan di setiap slot jamnya (1, 2, 3...)
                for ($k = $j->jam_mulai; $k <= $j->jam_selesai; $k++) {
                     $scheduleMatrix[$j->hari][$k] = $j;
                }
            }
        }

        $selectedKelas = $kelas->find($selectedKelasId);

        return view('app.jadwal', compact(
            'users', 'tahun', 'kelas', 'mapel', 
            'selectedKelasId', 'selectedKelas',
            'days', 'timeSlots', 'scheduleMatrix'
        ));
    }
    
    // ... (Fungsi tambah, edit, hapus tetap sama seperti sebelumnya) ...
    public function tambah(Request $request){
        $request->validate([
            'users_id' => 'required', 'tahun_id' => 'required', 'kelas_id' => 'required',
            'mapel_id' => 'required', 'hari' => 'required', 'jam_mulai' => 'required', 'jam_selesai' => 'required',
        ]);
        Jadwal::create($request->all()); // Shortcut jika fillable aman
        return redirect('jadwal')->with('sukses', 'Jadwal berhasil ditambahkan!');
    }

    public function edit(Request $request, $id){
        $jadwal = Jadwal::find($id);
        $jadwal->update($request->all());
        return redirect('jadwal')->with('sukses', 'Jadwal berhasil diubah!');
    }

    public function hapus(String $id){
        $jadwal = Jadwal::find($id);
        $jadwal->delete();
        return redirect('jadwal')->with('sukses', 'Jadwal berhasil dihapus!');
    }
}