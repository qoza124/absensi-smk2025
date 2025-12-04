<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    protected $table = 'tahun';
    protected $fillable = ['tahun', 'mulai', 'selesai', 'is_active'];

    // Helper untuk mengambil tahun aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}