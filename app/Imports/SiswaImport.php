<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas; // Import model Kelas
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Menggunakan baris pertama sebagai header
use Maatwebsite\Excel\Concerns\WithValidation; // Untuk validasi
use Maatwebsite\Excel\Validators\Failure;       // Untuk menangani kegagalan
use Maatwebsite\Excel\Concerns\SkipsOnFailure;  // Untuk skip baris jika gagal

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // $row['nama_siswa'] dan $row['kelas_id']
        // didapat dari header file Excel/CSV
        
        return new Siswa([
            'name'     => $row['nama_siswa'],
            'kelas_id' => $row['kelas_id'],
        ]);
    }

    /**
     * Validasi setiap baris
     */
    public function rules(): array
    {
        return [
            'nama_siswa' => 'required|string|max:255',
            
            // Cek apakah 'kelas_id' ada di tabel 'kelas'
            'kelas_id' => 'required|integer|exists:kelas,id',
        ];
    }

    /**
     * Pesan kustom jika validasi gagal
     */
    public function customValidationMessages()
    {
        return [
            'nama_siswa.required' => 'Kolom nama_siswa tidak boleh kosong.',
            'kelas_id.required'   => 'Kolom kelas_id tidak boleh kosong.',
            'kelas_id.exists'     => 'ID Kelas tidak ditemukan di database.',
        ];
    }

    /**
     * Tangani kegagalan. Kita biarkan kosong agar
     * controller bisa menangkap Exception jika ada error.
     * Implementasi SkipsOnFailure butuh method ini.
     */
    public function onFailure(Failure ...$failures)
    {
        // Biarkan kosong agar proses import berhenti
        // dan error ditampilkan di halaman (via try-catch di Controller)
    }
}