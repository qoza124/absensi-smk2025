<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    public function user() // Nama method relasinya (standarnya singular)
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function kelas() // Nama method relasinya (standarnya singular)
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    public function mapel() // Nama method relasinya (standarnya singular)
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }public function tahun() // Nama method relasinya (standarnya singular)
    {
        return $this->belongsTo(Tahun::class, 'tahun_id');
    }
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }

    protected $table = 'jadwal';
    protected $fillable = ['users_id', 'tahun_id', 'kelas_id', 'mapel_id' ,'hari', 'jam_mulai', 'jam_selesai'];
}
