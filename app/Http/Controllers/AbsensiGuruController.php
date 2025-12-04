<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensiguru; // Model absensi guru
use App\Models\User;
use App\Models\Jadwal;
// Model User (untuk filter nama guru)
use Carbon\Carbon;           // Untuk filter tanggal
use Carbon\CarbonPeriod;     // Untuk membuat range tanggal

class AbsensiGuruController extends Controller
{
    /**
     * Menampilkan halaman rekap absensi guru untuk Admin dalam format grid bulanan.
     */
    public function index(Request $request)
    {
        // 1. Tentukan Bulan dan Tahun yang dipilih
        // Input 'bulan' akan berformat 'YYYY-MM'
        $selectedMonthYear = $request->input('bulan', Carbon::now()->format('Y-m'));

        // Coba parsing tanggal, jika gagal, gunakan tanggal hari ini
        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $selectedMonthYear);
        } catch (\Exception $e) {
            // Jika format input salah, kembali ke bulan ini
            $selectedMonthYear = Carbon::now()->format('Y-m');
            $selectedDate = Carbon::now();
        }

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();

        // 2. Buat Range Tanggal (untuk header tabel)
        // CarbonPeriod akan berisi semua tanggal dari $startDate s/d $endDate
        $dates = CarbonPeriod::create($startDate, $endDate);

        // 3. Ambil semua guru (untuk baris tabel)
        $gurus = User::whereIn('role', ['Guru', 'Wali Kelas', 'Kesiswaan'])
            ->orderBy('name', 'asc')
            ->get();

        // 4. Ambil semua data absensi pada rentang tanggal yang dipilih
        $absensiBulanIni = Absensiguru::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        // 5. Susun ulang data absensi agar mudah dibaca di view
        // Format: [user_id][day_number] => 'Status'
        // (Contoh: $rekapData[15][21] = 'Hadir')
        $rekapData = [];
        foreach ($absensiBulanIni as $absensi) {
            // 'j' = format hari tanpa angka nol di depan (1, 2, ... 31)
            $day = Carbon::parse($absensi->tanggal)->format('j');
            $rekapData[$absensi->users_id][$day] = $absensi->status;
        }

        // 6. Kirim semua data ke view
        return view('app.rekap_absensi_guru', [ // Tetap pakai view yang sama
            'gurus' => $gurus,
            'dates' => $dates,                 // Objek CarbonPeriod
            'rekapData' => $rekapData,          // Data absensi yang sudah disusun
            'selectedMonthYear' => $selectedMonthYear, // 'YYYY-MM'
            'selectedDate' => $selectedDate         // Objek Carbon dari bulan terpilih
        ]);
    }

    public function printDetailGuru(Request $request, $user_id)
    {
        // 1. Validasi User (Guru)
        $guru = User::whereIn('role', ['Guru', 'Wali Kelas', 'Kesiswaan'])
            ->findOrFail($user_id);

        // 2. Tentukan Bulan dan Tahun
        $selectedMonthYear = $request->input('bulan', Carbon::now()->format('Y-m'));
        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $selectedMonthYear);
        } catch (\Exception $e) {
            $selectedMonthYear = Carbon::now()->format('Y-m');
            $selectedDate = Carbon::now();
        }

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();
        $dates = CarbonPeriod::create($startDate, $endDate); // Semua tanggal di bulan ini

        // 3. Ambil Semua Jadwal Tetap Guru
        $jadwals = Jadwal::where('users_id', $guru->id)
            ->with(['mapel', 'kelas', 'tahun'])
            ->orderBy('hari') // Urutkan berdasarkan hari
            ->orderBy('jam_mulai') // Lalu urutkan berdasarkan jam
            ->get()
            ->groupBy('hari'); // Kelompokkan berdasarkan hari (Senin, Selasa, dst.)

        // 4. Ambil Semua Absensi Guru di Bulan Ini
        // Kita buat struktur data yang mudah dicari: [tanggal][jadwal_id] => data absensi
        $absensiData = Absensiguru::where('users_id', $guru->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->mapToGroups(function ($item) {
                // 1. Grup berdasarkan tanggal (YYYY-MM-DD)
                return [$item->tanggal => $item];
            })
            ->map(function ($items) {
                // 2. Di dalam grup tanggal, buat key berdasarkan jadwal_id
                return $items->keyBy('jadwal_id');
            });

        // 5. Buat Laporan Kelengkapan Absensi (Data Masuk Lengkap)
        $laporanHarian = [];
        $summary = ['wajib' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'kosong' => 0];

        // Mapping nama hari dari Carbon (Inggris) ke DB Anda (Indonesia)
        $hariMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu', // Asumsi ada jadwal Sabtu
            'Sunday' => 'Minggu',  // Asumsi ada jadwal Minggu
        ];

        foreach ($dates as $date) {
            $namaHariIndonesia = $hariMapping[$date->format('l')] ?? null;

            // Cek apakah guru punya jadwal di hari ini (misal: "Senin")
            if (isset($jadwals[$namaHariIndonesia])) {

                // Loop untuk setiap jadwal di hari itu (misal: jam ke 1-2 dan jam ke 4-5)
                foreach ($jadwals[$namaHariIndonesia] as $jadwal) {
                    $summary['wajib']++; // Total sesi yang wajib diajar bertambah
                    $tanggalStr = $date->format('Y-m-d');

                    // Cek apakah ada data absensi untuk jadwal ini di tanggal ini
                    $status = 'Kosong'; // Default jika guru tidak melakukan absensi
                    $ket = '-';

                    if (isset($absensiData[$tanggalStr]) && isset($absensiData[$tanggalStr][$jadwal->id])) {
                        // Jika ada data absensi
                        $absensi = $absensiData[$tanggalStr][$jadwal->id];
                        $status = $absensi->status;
                        $ket = $absensi->ket ?? '-';
                    }

                    // Update ringkasan (summary)
                    if ($status == 'Hadir')
                        $summary['hadir']++;
                    elseif ($status == 'Izin')
                        $summary['izin']++;
                    elseif ($status == 'Sakit')
                        $summary['sakit']++;
                    elseif ($status == 'Alpha')
                        $summary['alpha']++;
                    elseif ($status == 'Kosong')
                        $summary['kosong']++; // 'Kosong' berarti tidak absen

                    // Tambahkan ke laporan detail
                    $laporanHarian[] = [
                        'tanggal' => $date->copy(),
                        'jadwal' => $jadwal,
                        'status' => $status,
                        'keterangan' => $ket,
                    ];
                }
            }
        }

        // 6. Kirim ke view print detail
        return view('app.rekap_absensi_guru_detail', [
            'guru' => $guru,
            'jadwals' => $jadwals, // Jadwal per hari
            'laporanHarian' => $laporanHarian, // Detail absensi harian per jadwal
            'summary' => $summary, // Ringkasan total
            'selectedDate' => $selectedDate, // Untuk judul (cth: "November 2025")
        ]);
    }
    public function cetakSemua(Request $request)
    {
        // 1. Tentukan Bulan dan Tahun
        $selectedMonthYear = $request->input('bulan', \Carbon\Carbon::now()->format('Y-m'));
        try {
            $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonthYear);
        } catch (\Exception $e) {
            $selectedMonthYear = \Carbon\Carbon::now()->format('Y-m');
            $selectedDate = \Carbon\Carbon::now();
        }

        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();
        $dates = \Carbon\CarbonPeriod::create($startDate, $endDate);

        // Ambil data Guru
        $gurus = \App\Models\User::whereIn('role', ['Guru', 'Wali Kelas', 'Kesiswaan'])
            ->orderBy('name', 'asc')
            ->get();

        // Ambil Absensi
        $absensiBulanIni = \App\Models\Absensiguru::whereBetween('tanggal', [$startDate, $endDate])->get();

        // Susun Data
        $rekapData = [];
        $summaryData = [];

        foreach ($gurus as $guru) {
            $summaryData[$guru->id] = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
        }

        foreach ($absensiBulanIni as $absensi) {
            $day = \Carbon\Carbon::parse($absensi->tanggal)->format('j');
            $rekapData[$absensi->users_id][$day] = $absensi->status;

            if (isset($summaryData[$absensi->users_id]) && array_key_exists($absensi->status, $summaryData[$absensi->users_id])) {
                $summaryData[$absensi->users_id][$absensi->status]++;
            }
        }

        return view('app.cetak_rekap_absensi_guru_all', [
            'gurus' => $gurus,
            'dates' => $dates,
            'rekapData' => $rekapData,
            'summaryData' => $summaryData,
            'selectedDate' => $selectedDate
        ]);
    }
}