<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MulaiAbsenController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\RekapSiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapMapelController;

use Illuminate\Support\Facades\Route;

// ==========================================================
// GRUP UNTUK TAMU (GUEST)
// Hanya bisa diakses jika BELUM login.
// Jika sudah login, akan otomatis redirect ke /dashboard.
// ==========================================================
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'postlogin']);
});


// Rute Logout
// Sebaiknya rute logout hanya bisa diakses oleh yang sudah login,
// jadi kita letakkan di dalam grup 'auth'.
// Route::get('logout', [AuthController::class, 'logout']); // <-- Pindahkan ke dalam grup auth

// ==========================================================
// GRUP UNTUK PENGGUNA YANG SUDAH LOGIN (AUTH)
// ==========================================================
Route::group(['middleware' => 'auth'], function () {

    // Rute Logout dipindahkan ke sini
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('/myprofil', [ProfileController::class, 'index'])->name('myprofil');
    Route::put('/myprofil/update', [ProfileController::class, 'updateData'])->name('myprofil.update');
    Route::put('/myprofil/password', [ProfileController::class, 'updatePassword'])->name('myprofil.password');
    // Di dalam grup middleware auth, dekat route profil lainnya
    Route::put('/myprofil/foto', [ProfileController::class, 'updateFoto'])->name('myprofil.foto');

    Route::get('/', [DashboardController::class, 'index']);
    
    //Route::get('/login', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('home');

    // ==========================================================
    // GRUP 1: KHUSUS ADMIN
    // ==========================================================
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/kelas', [KelasController::class, 'index']);
        Route::post('/kelas/tambah', [KelasController::class, 'tambah']);
        Route::put('/kelas/{id}', [KelasController::class, 'edit']);
        Route::delete('/kelas/{id}', [KelasController::class, 'hapus']);

        Route::get('/mapel', [MapelController::class, 'index']);
        Route::post('/mapel/tambah', [MapelController::class, 'tambah']);
        Route::put('/mapel/{id}', [MapelController::class, 'edit']);
        Route::delete('/mapel/{id}', [MapelController::class, 'hapus']);

        Route::get('/user', [UserController::class, 'index']);
        Route::post('/user/tambah', [UserController::class, 'tambah']);
        Route::put('/user/{id}', [UserController::class, 'edit']);
        Route::delete('/user/{id}', [UserController::class, 'hapus']);
        Route::put('/user/reset/{id}', [UserController::class, 'reset']);

        Route::get('/jadwal', [JadwalController::class, 'index']);
        Route::post('/jadwal/tambah', [JadwalController::class, 'tambah']);
        Route::put('/jadwal/{id}', [JadwalController::class, 'edit']);
        Route::delete('/jadwal/{id}', [JadwalController::class, 'hapus']);

        Route::get('/tahun', [TahunController::class, 'index']);
        Route::post('/tahun/tambah', [TahunController::class, 'tambah']);
        Route::put('/tahun/{id}', [TahunController::class, 'edit']);
        Route::delete('/tahun/{id}', [TahunController::class, 'hapus']);
        Route::put('/tahun/aktifkan/{id}', [TahunController::class, 'setAktif']);



        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
        Route::post('/lokasi', [LokasiController::class, 'simpan'])->name('lokasi.simpan');

        Route::get('/rekap-absensi-guru', [AbsensiGuruController::class, 'index'])->name('rekap.absensi_guru');
        Route::get('/rekap-absensi-guru/print/{user_id}', [AbsensiGuruController::class, 'printDetailGuru'])
            ->name('rekap.absensi_guru.print_detail');
        Route::get('/rekap-absensi-guru/cetak-semua', [App\Http\Controllers\AbsensiGuruController::class, 'cetakSemua'])->name('rekap.absensi_guru.cetak_semua');
    });

    // ==========================================================
    // GRUP 2: KHUSUS WALI KELAS
    // ==========================================================
    Route::middleware(['role:Wali Kelas,Guru,Kesiswaan'])->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
        Route::get('/absensi{jadwal_id}', [MulaiAbsenController::class, 'index'])
            ->name('absensi.ambil');
        Route::post('/absensisimpan', [MulaiAbsenController::class, 'simpanAbsen'])->name('absensi.simpan');
        Route::post('/absensi/cek-lokasi', [AbsensiController::class, 'cekLokasi'])
            ->name('absensi.cek_lokasi');
        Route::get('/rekap-mapel', [RekapMapelController::class, 'index'])->name('rekap.mapel.index');
        Route::get('/rekap-mapel/cetak', [RekapMapelController::class, 'cetak'])->name('rekap.mapel.cetak');
        Route::post('/absensi/harian', [AbsensiController::class, 'storeHarian'])->name('absensi.harian');
        Route::post('/absensi/izin', [AbsensiController::class, 'storeIzin'])->name('absensi.izin');
        Route::post('/absensi/cek-lokasi-harian', [AbsensiController::class, 'cekLokasiHarian'])->name('absensi.cek_lokasi_harian');
    });

    // ==========================================================
    // GRUP 3: RUTE BERSAMA (ADMIN & WALI KELAS)
    // ==========================================================
    Route::middleware(['role:Admin,Wali Kelas'])->group(function () {
        Route::get('/siswa', [SiswaController::class, 'index'])->name('homeguru');
        Route::post('/siswa/tambah', [SiswaController::class, 'tambah']);
        Route::put('/siswa/{id}', [SiswaController::class, 'edit']);
        Route::delete('/siswa/{id}', [SiswaController::class, 'hapus']);
        Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::get('siswa/download-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
        Route::get('/rekap-absensi-siswa', [RekapSiswaController::class, 'index'])->name('rekap.siswa.index');
        Route::get('/rekap-absensi-siswa/cetak', [RekapSiswaController::class, 'cetak'])->name('rekap.siswa.cetak');
        Route::get('/rekap-absensi-siswa/harian', [RekapSiswaController::class, 'rekapHarian'])->name('rekap.siswa.harian');
        Route::get('/rekap-absensi-siswa/mingguan', [RekapSiswaController::class, 'rekapMingguan'])->name('rekap.siswa.mingguan');
        // Route untuk Cetak (Tambahkan di group middleware auth)
        Route::get('/rekap-absensi-siswa/harian/cetak', [RekapSiswaController::class, 'cetakHarian'])->name('rekap.siswa.harian.cetak');
        Route::get('/rekap-absensi-siswa/mingguan/cetak', [RekapSiswaController::class, 'cetakMingguan'])->name('rekap.siswa.mingguan.cetak');
    });


});

// TANDA } DI BAWAH INI ADALAH ERROR SINTAKS DI FILE ASLI ANDA
// TANDA INI SUDAH SAYA HAPUS DI BLOK KODE DI ATAS
// }