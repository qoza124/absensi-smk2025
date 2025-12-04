<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth; 
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Exception;



class SiswaController extends Controller
{

    public function index(Request $request){ // <-- TAMBAHKAN Request $request
        
        $user = Auth::user(); // Dapatkan pengguna yang sedang login
        
        // Ambil ID kelas yang dipilih dari filter (jika ada)
        // Ini akan datang dari URL, contoh: /siswa?kelas_id=3
        $selectedKelasId = $request->query('kelas_id');
        
        // Inisialisasi variabel sebagai koleksi kosong
        $siswa = collect();
        $kelas = collect(); 

        if ($user->role == 'Admin') {
            // JIKA ADMIN:
            
            // 1. Admin SELALU dapat semua kelas untuk dropdown filter
            $kelas = Kelas::all(); 

            // 2. Siapkan query builder untuk Siswa
            $querySiswa = Siswa::query(); // Gunakan query builder

            // 3. Cek apakah admin sedang memfilter
            if ($selectedKelasId) {
                // Jika ya, tambahkan kondisi 'where'
                $querySiswa->where('kelas_id', $selectedKelasId);
            }
            
            // 4. Ambil datanya (bisa semua siswa, bisa yang terfilter)
            $siswa = $querySiswa->get();

        } elseif ($user->role == 'Wali Kelas') {
            // JIKA WALI: (Logika ini tidak berubah, sudah benar)
            
            // 1. Cari SEMUA kelas yang 'users_id'-nya adalah ID si wali
            $daftarKelasWali = Kelas::where('users_id', $user->id)->get();

            // 2. DAFTAR KELAS INI AKAN DIGUNAKAN UNTUK DROPDOWN MODAL
            $kelas = $daftarKelasWali; 

            // 3. Periksa apakah si wali ini mengampu setidaknya satu kelas
            if ($daftarKelasWali->isNotEmpty()) {
                
                // 4. Ambil HANYA ID dari koleksi kelas tersebut untuk mencari siswa
                $daftarIdKelas = $daftarKelasWali->pluck('id');
                
                // 5. Tampilkan siswa yang 'kelas_id'-nya ADA DI DALAM array $daftarIdKelas
                $siswa = Siswa::whereIn('kelas_id', $daftarIdKelas)->get();
            }
        }

        // 3. Kirim data ke view
        //    $siswa -> (daftar siswa yang difilter atau semua)
        //    $kelas -> (daftar kelas yang difilter atau semua)
        //    $selectedKelasId -> (ID kelas yang sedang aktif di filter, agar dropdown tetap 'terpilih')
        return view('app.siswa', compact('siswa', 'kelas', 'selectedKelasId')); // <-- TAMBAHKAN 'selectedKelasId'
    }

    // ===================================================================
    // TIDAK PERLU ADA PERUBAHAN PADA METODE DI BAWAH INI
    // ===================================================================

    public function tambah(Request $request){
        
        $request->validate([
            'kelas_id' => 'required',
        ]);
        Siswa::create([
            'name' => $request->name,
            'kelas_id' => $request-> kelas_id,
        ]);

        return redirect('siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }
    
    public function edit(Request $request, $id){
        $siswa = Siswa::find($id);
        $siswa->name = $request->name;
        $siswa->kelas_id = $request->kelas_id;
        $siswa->update();
        return redirect('siswa')->with('success', 'Siswa berhasil diubah!');
    }

    public function hapus(String $id){
        $siswa = Siswa::find($id);
        $siswa->delete();
        return redirect('siswa')->with('success', 'Siswa berhasil dihapus!');
    }

    public function import(Request $request) 
    {
        // 1. Validasi request (hanya validasi file)
        $request->validate([
            'file_excel' => 'required|mimes:xls,xlsx|max:2048' // Max 2MB
        ], [
            'file_excel.required' => 'File Excel wajib diisi.',
            'file_excel.mimes' => 'File harus berekstensi .xls atau .xlsx.'
        ]);

        // 2. Tangkap file
        $file = $request->file('file_excel');

        // 3. Lakukan proses import menggunakan try-catch
        try {
            // Kita tidak lagi passing $kelas_id ke constructor
            // Import class akan membaca kelas_id dari file Excel
            Excel::import(new SiswaImport, $file);
            
            return redirect('siswa')->with('success', 'Data siswa berhasil diimpor!');

        } catch (Exception $e) {
            // Jika terjadi error saat import (misal kelas_id tidak valid)
            return redirect('siswa')->with('error', 'Gagal mengimpor data. Pastikan ID Kelas di Excel valid. Pesan error: ' . $e->getMessage());
        }
    }

    // ===================================================================
    // TAMBAHAN METODE BARU UNTUK DOWNLOAD TEMPLATE
    // ===================================================================
    
    public function downloadTemplate()
    {
        // Kita panggil Maatwebsite Excel::download
        // Masukkan class Export baru kita dan nama file yang diinginkan
        return Excel::download(new SiswaTemplateExport(), 'template_import_data_siswa.xlsx');
    }
}