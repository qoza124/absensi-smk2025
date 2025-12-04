<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
   public function user() // Nama method relasinya (standarnya singular)
    {
        // Jika foreign key Anda adalah 'users_id' (bukan 'user_id'):
        
            return $this->belongsTo(User::class, 'users_id');

        // Jika foreign key Anda standar ('user_id'), cukup ini:
        // return $this->belongsTo(User::class);
        
    }
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kelas_id');
    } 

    protected $table = 'kelas';
    protected $fillable = ['name', 'users_id'];
}
