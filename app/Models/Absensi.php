<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     * @var string
     */
    protected $table = 'absensi'; // Sesuaikan jika nama tabel Anda berbeda

    /**
     * Kolom yang boleh diisi
     * @var array
     */
    protected $fillable = [
        'jadwal_id',
        'siswa_id',
        'tanggal',
        'status', // 'H', 'I', 'S', 'A'
        'ket', // Opsional
    ];

    /**
     * Relasi ke tabel Jadwal
     * Absensi ini milik satu Jadwal
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    /**
     * Relasi ke tabel Siswa
     * Absensi ini milik satu Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}