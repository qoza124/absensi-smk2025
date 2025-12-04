<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    public function index()
    {
        // Ambil pengaturan saat ini, atau gunakan default jika belum ada
        $settings = Lokasi::all()->keyBy('key');

        $data = [
            'sekolah_lat' => $settings->get('sekolah_lat')?->value ?? '-7.182586', // Default Bandung
            'sekolah_long' => $settings->get('sekolah_long')?->value ?? '112.917493', // Default Bandung
            'sekolah_radius' => $settings->get('sekolah_radius')?->value ?? '200', // Default 100 meter
        ];

        return view('app.lokasi', $data);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'sekolah_lat' => 'required|numeric',
            'sekolah_long' => 'required|numeric',
            'sekolah_radius' => 'required|integer|min:10', // Minimal radius 10m
        ]);

        // Gunakan updateOrCreate untuk menyimpan data
        Lokasi::updateOrCreate(
            ['key' => 'sekolah_lat'],
            ['value' => $request->sekolah_lat]
        );

        Lokasi::updateOrCreate(
            ['key' => 'sekolah_long'],
            ['value' => $request->sekolah_long]
        );

        Lokasi::updateOrCreate(
            ['key' => 'sekolah_radius'],
            ['value' => $request->sekolah_radius]
        );

        return redirect()->route('lokasi.index')
                         ->with('success', 'Pengaturan lokasi sekolah berhasil disimpan.');
    }
}