<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';
    protected $primaryKey = 'key'; // Kunci utama adalah 'key'
    public $incrementing = false; // 'key' bukan auto-increment
    protected $keyType = 'string'; // Tipe datanya string

    protected $fillable = ['key', 'value'];
}