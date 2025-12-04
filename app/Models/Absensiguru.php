<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensiguru extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     * @var string
     */
    protected $table = 'absensiguru'; // Sesuaikan jika nama tabel Anda berbeda

    /**
     * Kolom yang boleh diisi
     * @var array
     */
    protected $fillable = [
        'jadwal_id',
        'users_id',
        'tanggal',
        'status', 
        'ket',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}