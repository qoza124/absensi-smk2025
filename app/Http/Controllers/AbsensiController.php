<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Absensiguru; // <-- Pastikan Model ini di-use
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Lokasi;
use App\Traits\LokasiTrait;

class AbsensiController extends Controller
{
    use LokasiTrait;

    public function index()
    {
        $guru_id = Auth::id();
        $hari_ini = Carbon::now()->translatedFormat('l'); 
        $tanggal_hari_ini = Carbon::today();

        // 1. Data Jadwal Mengajar (Logika Lama)
        $jadwal = Jadwal::where('users_id', $guru_id)
            ->where('hari', $hari_ini)
            ->with(['mapel', 'kelas'])
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $jadwal_ids_hari_ini = $jadwal->pluck('id');
        $sudah_absen_ids = Absensi::whereIn('jadwal_id', $jadwal_ids_hari_ini)
            ->whereDate('tanggal', $tanggal_hari_ini)
            ->pluck('jadwal_id')
            ->unique();

        // ============================================================
        // 2. LOGIKA BARU: Cek Absensi Harian (Non-Mengajar)
        // ============================================================
        // Mencari record di mana jadwal_id NULL pada tanggal hari ini
        $absen_harian = Absensiguru::where('users_id', $guru_id)
            ->whereDate('tanggal', $tanggal_hari_ini)
            ->whereNull('jadwal_id') // Absen harian tidak punya jadwal_id
            ->first();

        return view('absensi.absensi', [
            'jadwal' => $jadwal,
            'sudah_absen_ids' => $sudah_absen_ids,
            'absen_harian' => $absen_harian // <-- Kirim ke view
        ]);
    }

    // --- FITUR 1: ABSEN HARIAN (DATANG) ---
    public function storeHarian(Request $request)
    {
        // Kita gunakan session 'izin_absen_harian' dari hasil cekLokasiHarian (nanti dibuat)
        // Atau jika Anda ingin simpel, validasi lokasi ulang di sini, tapi kita pakai bypass tiket saja biar aman.
        
        $user = Auth::user();
        $tanggal = Carbon::now()->format('Y-m-d');

        // Cek apakah sudah absen
        $cek = Absensiguru::where('users_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->whereNull('jadwal_id')
                ->exists();
        
        if($cek) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensiguru::create([
            'jadwal_id' => null, // Penting: NULL
            'users_id' => $user->id,
            'tanggal' => $tanggal,
            'status' => 'Hadir',
            'ket' => 'Absensi Harian/Datang'
        ]);

        return redirect()->back()->with('sukses', 'Absensi Harian Berhasil!');
    }

    // --- FITUR 2: IZIN / SAKIT ---
    public function storeIzin(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
            'tanggal' => 'required|date'
        ]);

        $user = Auth::user();

        // Cek duplikasi
        $cek = Absensiguru::where('users_id', $user->id)
                ->whereDate('tanggal', $request->tanggal)
                ->whereNull('jadwal_id')
                ->first();

        if($cek) {
            // Jika sudah ada, kita update saja (misal ralat dari Hadir ke Sakit)
            $cek->update([
                'status' => $request->status,
                'ket' => $request->keterangan
            ]);
            $msg = 'Data absensi/izin berhasil diperbarui.';
        } else {
            // Buat baru
            Absensiguru::create([
                'jadwal_id' => null,
                'users_id' => $user->id,
                'tanggal' => $request->tanggal,
                'status' => $request->status,
                'ket' => $request->keterangan
            ]);
            $msg = 'Pengajuan izin berhasil disimpan.';
        }

        return redirect()->back()->with('sukses', $msg);
    }

    // --- VALIDASI LOKASI KHUSUS HARIAN ---
    public function cekLokasiHarian(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        // Ambil setting lokasi (Logic sama dengan cekLokasi jadwal)
        $settings = Lokasi::all()->keyBy('key');
        $lat_sekolah = $settings->get('sekolah_lat')?->value;
        $long_sekolah = $settings->get('sekolah_long')?->value;
        $radius_sekolah = (float)($settings->get('sekolah_radius')?->value ?? 100);

        // Bypass jika belum setting
        if (!$lat_sekolah || !$long_sekolah) {
            return response()->json(['valid' => true, 'message' => 'Lokasi sekolah belum diatur. Absen diizinkan.']);
        }

        $jarak = $this->hitungJarak($lat_sekolah, $long_sekolah, $request->lat, $request->long);

        if ($jarak <= $radius_sekolah) {
            return response()->json(['valid' => true, 'message' => "Lokasi valid. Jarak: " . round($jarak) . "m"]);
        } else {
            return response()->json(['valid' => false, 'message' => "Anda diluar jangkauan (" . round($jarak) . "m)."], 403);
        }
    }
    public function cekLokasi(Request $request)
    {
        // ===============================================
        // PERUBAHAN 1: Validasi jadwal_id
        // ===============================================
        $request->validate([
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'jadwal_id' => 'required|exists:jadwal,id' // <-- TAMBAHKAN INI
        ]);

        // 1. Ambil data lokasi sekolah
        $settings = Lokasi::all()->keyBy('key');
        // ... (data lokasi tetap sama) ...
        $lat_sekolah = $settings->get('sekolah_lat')?->value;
        $long_sekolah = $settings->get('sekolah_long')?->value;
        $radius_sekolah = (float)($settings->get('sekolah_radius')?->value ?? 100); 

        // 2. Jika admin belum setting (Bypass)
        if (!$lat_sekolah || !$long_sekolah) {
            
            // ===============================================
            // PERUBAHAN 2: Berikan "Tiket Session" saat bypass
            // ===============================================
            $request->session()->put('izin_absen_' . $request->jadwal_id, true);

            return response()->json([
                'valid' => true,
                'message' => 'Bypass. Lokasi sekolah belum diatur oleh Admin.',
                'jarak' => 0
            ]);
        }

        // 3. Ambil lokasi guru
        $lat_guru = (float)$request->lat;
        $long_guru = (float)$request->long;

        // 4. Hitung Jarak
        $jarak = $this->hitungJarak($lat_sekolah, $long_sekolah, $lat_guru, $long_guru);
        $jarak = round($jarak, 2); 

        // 5. Bandingkan
        if ($jarak <= $radius_sekolah) {
            // Di dalam radius
            
            // ===============================================
            // PERUBAHAN 3: Berikan "Tiket Session" saat valid
            // ===============================================
            $request->session()->put('izin_absen_' . $request->jadwal_id, true);

            return response()->json([
                'valid' => true,
                'message' => "Lokasi terverifikasi. Jarak Anda: $jarak meter.",
                'jarak' => $jarak
            ]);
        } else {
            // Di luar radius (JANGAN BERIKAN TIKET)
            $debug_message = "Anda berada di luar radius sekolah! \n\n" .
                // ... (Pesan error tetap sama) ...
                "Jarak Anda: $jarak meter.";
            
            return response()->json([
                'valid' => false,
                'message' => $debug_message, 
                'jarak' => $jarak
            ], 403); 
        }
    }
}