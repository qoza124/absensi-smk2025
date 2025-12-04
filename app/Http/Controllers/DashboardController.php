<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Import Model
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Absensiguru;
use App\Models\Absensi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'Wali Kelas' || $user->role == 'Kesiswaan' || $user->role == 'Guru') {
            return redirect('absensi')->with('sukses', 'Login Sukses!');
        }
        
        return $this->dashboard();
    }

    public function dashboard()
    {
        $today = Carbon::today();

        // 1. Menghitung Data Master
        $countKelas = Kelas::count();
        $countGuru  = User::whereIn('role', ['Guru', 'Wali Kelas', 'Kesiswaan'])->count();
        $countSiswa = Siswa::count();
        $countMapel = Mapel::count();
        
        // 2. Menghitung Guru & Siswa Hadir HARI INI (Distinct / Unik)
        // Jika guru mengajar 3 kelas dan hadir semua, tetap dihitung 1 orang.
        $guruHadirToday = Absensiguru::whereDate('tanggal', $today)
                            ->where('status', 'Hadir')
                            ->distinct('users_id') // <-- Menghindari duplikasi orang
                            ->count('users_id');

        $siswaHadirToday = Absensi::whereDate('tanggal', $today)
                            ->where('status', 'Hadir')
                            ->distinct('siswa_id') // <-- Menghindari duplikasi siswa
                            ->count('siswa_id');

        // 3. Menghitung yang BELUM Absensi
        // Hitung siapa yang sudah punya record absensi hari ini (Status apapun: Hadir/Izin/Sakit/Alpha)
        $guruSudahAbsenCount = Absensiguru::whereDate('tanggal', $today)
                                ->distinct('users_id')
                                ->count('users_id');

        $siswaSudahAbsenCount = Absensi::whereDate('tanggal', $today)
                                ->distinct('siswa_id')
                                ->count('siswa_id');

        // Total Belum = Total Master - Total yang Sudah Absen
        $guruBelumAbsen = $countGuru - $guruSudahAbsenCount;
        $siswaBelumAbsen = $countSiswa - $siswaSudahAbsenCount;

        // Pastikan tidak minus (jika ada data error)
        if($guruBelumAbsen < 0) $guruBelumAbsen = 0;
        if($siswaBelumAbsen < 0) $siswaBelumAbsen = 0;

        return view('app.dashboard', compact(
            'countKelas', 
            'countGuru', 
            'countSiswa', 
            'countMapel', 
            'guruHadirToday',
            'siswaHadirToday',
            'guruBelumAbsen', // <-- Variabel Baru
            'siswaBelumAbsen' // <-- Variabel Baru
        ));
    }
}