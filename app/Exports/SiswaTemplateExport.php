<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SiswaTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        // Data contoh
        // Ini akan menjadi baris-baris di bawah header
        return [
            ['Contoh Siswa Satu', 1],
            ['Contoh Siswa Dua', 3],
        ];
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Ini akan menjadi baris header (baris pertama)
        return [
            'nama_siswa',
            'kelas_id'
        ];
    }
}