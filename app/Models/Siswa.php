<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
   public function kelas() // Nama method relasinya (standarnya singular)
    {
        // Jika foreign key Anda adalah 'users_id' (bukan 'user_id'):
        
            return $this->belongsTo(Kelas::class, 'kelas_id');

        // Jika foreign key Anda standar ('user_id'), cukup ini:
        // return $this->belongsTo(User::class);
        
    } 

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }

    protected $table = 'siswa';
    protected $fillable = ['name', 'kelas_id'];
}
