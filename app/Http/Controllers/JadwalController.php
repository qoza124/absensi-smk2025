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

    public function index(Jadwal $id){
        $rolesToDisplay = ['Guru', 'Wali Kelas', 'Kesiswaan'];
        
        // 2. Gunakan 'whereIn' untuk memfilter dan 'get' untuk mengambil data
        $users = User::whereIn('role', $rolesToDisplay)->get();
        //$activeTahunId = Tahun::active()->first()?->id;
        $jadwal = Jadwal::all();
        $tahun = Tahun::all();
        $kelas = Kelas::all();
        $mapel = Mapel::all();
        //dd($kelas->all());
        return view('app.jadwal', compact('jadwal', 'tahun', 'users', 'kelas', 'mapel'));
    }

    public function tambah(Request $request){
        
        $request->validate([
            'users_id' => 'required',
            'tahun_id' => 'required',
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);
        Jadwal::create([
            'users_id' => $request-> users_id,
            'tahun_id' => $request-> tahun_id,
            'kelas_id' => $request-> kelas_id,
            'mapel_id' => $request-> mapel_id,
            'hari' => $request-> hari,
            'jam_mulai' => $request-> jam_mulai,
            'jam_selesai' => $request-> jam_selesai,
        ]);

        return redirect('jadwal')->with('sukses', 'Jadwal berhasil ditambahkan!');
    }
    public function edit(Request $request, $id){
        $jadwal = Jadwal::find($id);
        $jadwal->users_id = $request->users_id;
        $jadwal->tahun_id = $request->tahun_id;
        $jadwal->kelas_id = $request->kelas_id;
        $jadwal->mapel_id = $request->mapel_id;
        $jadwal->hari = $request-> hari;
        $jadwal->jam_mulai = $request-> jam_mulai;
        $jadwal->jam_selesai = $request-> jam_selesai;
        $jadwal->update();
        //dd($kelas->all());
        return redirect('jadwal')->with('sukses', 'Jadwal berhasil diubah!');
    }

    public function hapus(String $id){
        $jadwal = Jadwal::find($id);
        $jadwal->delete();
        return redirect('jadwal')->with('sukses', 'Jadwal berhasil dihapus!');
    }
}
