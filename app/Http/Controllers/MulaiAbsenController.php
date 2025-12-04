<?php

namespace App\Http\Controllers;

use App\Models\Absensiguru;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- WAJIB DITAMBAHKAN
use Carbon\Carbon;

class MulaiAbsenController extends Controller
{

    public function index(Request $request, $jadwal_id)
    {
        // 1. Ambil data jadwal
        $jadwal = Jadwal::with(['kelas', 'mapel', 'user'])->findOrFail($jadwal_id);

        // 2. Security Check (Owner) - Ini sudah ada
        if (Auth::id() != $jadwal->users_id) { 
            abort(403, 'Anda tidak diizinkan mengakses jadwal ini.');
        }

        // ===============================================
        // PERUBAHAN 2: Cek "Tiket Session"
        // ===============================================
        $sessionKey = 'izin_absen_' . $jadwal_id;

        // Cek apakah data absen HARI INI sudah ada. 
        // Jika sudah ada (mode edit), kita Bolehkan masuk tanpa cek lokasi.
        $sudah_absen_hari_ini = Absensi::where('jadwal_id', $jadwal_id)
                                    ->whereDate('tanggal', Carbon::today())
                                    ->exists();

        // Jika user TIDAK punya tiket DAN ini BUKAN mode edit
        if (!$request->session()->has($sessionKey) && !$sudah_absen_hari_ini) {
            // Kembalikan ke halaman daftar jadwal dengan pesan error
            return redirect()->route('absensi') 
                ->with('error', 'Akses ditolak. Silakan klik "Mulai Absen" dan lakukan verifikasi lokasi terlebih dahulu.');
        }

        // 4. Hapus tiket setelah dipakai (PENTING!)
        // Ini agar tiketnya hanya bisa dipakai sekali.
        $request->session()->forget($sessionKey);
        // ===============================================
        // AKHIR PERUBAHAN
        // ===============================================


        // 5. Ambil data siswa (Logika lama, tetap berjalan)
        $siswa = Siswa::where('kelas_id', $jadwal->kelas_id)
            ->orderBy('name', 'asc')
            ->get();

        $tanggal_hari_ini = Carbon::today();
        $absensi_hari_ini = collect(); 

        // 6. Query data absensi hari ini
        $absensi_query = Absensi::where('jadwal_id', $jadwal_id)
            ->whereDate('tanggal', $tanggal_hari_ini);
        
        // 7. Cek apakah sudah ada data absen (gunakan variabel di atas)
        $sudah_absen = $sudah_absen_hari_ini;

        // 8. Jika sudah ada, ambil datanya
        if ($sudah_absen) {
            $absensi_hari_ini = $absensi_query->get()->keyBy('siswa_id');
        }

        // 9. Tampilkan view
        return view('absensi.mulaiabsen', [
            'jadwal' => $jadwal,
            'siswa' => $siswa,
            'sudah_absen' => $sudah_absen,
            'absensi_hari_ini' => $absensi_hari_ini, 
        ]);
    }

    public function simpanAbsen(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal' => 'required|date',
            'status' => 'required|array', // Pastikan 'status' ada
            'status.*.stat' => 'required|string|in:Hadir,Izin,Sakit,Alpha', // Validasi status
            'status.*.ket' => 'nullable|string|max:255', // Validasi keterangan (opsional)
        ]);

        $jadwal_id = $request->jadwal_id;
        $tanggal = $request->tanggal;

        $user = Auth::user();
        Absensiguru::updateOrCreate([
            'jadwal_id' => $jadwal_id,
            'users_id' => $user->id,
            'tanggal' => $tanggal,
            'status' => 'Hadir'
        ]);

        DB::beginTransaction();
        try {
            // 2. Loop data 'status' 
            // $request->status sekarang akan berisi array:
            // [ 'siswa_id' => ['status' => 'Hadir', 'ket' => '...'] ]
            foreach ($request->status as $siswa_id => $data) {

                // 3. Gunakan updateOrCreate
                Absensi::updateOrCreate(
                    [
                        // Kunci unik untuk dicari
                        'jadwal_id' => $jadwal_id,
                        'siswa_id' => $siswa_id,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status' => $data['stat'],
                        'ket' => $data['ket'] ?? null,
                    ]
                );
            }

            DB::commit(); // Simpan semua perubahan

            return redirect()->route('absensi')->with('success', 'Absensi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada error
            return back()->withErrors('Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }
}