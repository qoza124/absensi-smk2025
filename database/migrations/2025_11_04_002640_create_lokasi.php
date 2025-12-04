<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lokasi', function (Blueprint $table) {
            // 'key' akan menjadi 'sekolah_lat', 'sekolah_long', 'sekolah_radius'
            $table->string('key')->primary(); 
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lokasi');
    }
};