<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Absensi;

class RekapMapelController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacherId = $user->id;

        // 1. Ambil Daftar Kelas & Mapel (Sama seperti sebelumnya)
        $jadwalGuru = Jadwal::where('users_id', $teacherId)
                        ->with(['kelas', 'mapel'])
                        ->get();

        $listKelas = $jadwalGuru->unique('kelas_id')->map(function($j) {
            return $j->kelas;
        });

        $listMapel = $jadwalGuru->unique('mapel_id')->map(function($j) {
            return $j->mapel;
        });

        // 2. Filter Input
        $selectedKelasId = $request->input('kelas_id');
        $selectedMapelId = $request->input('mapel_id');
        $selectedMonthYear = $request->input('bulan', Carbon::now()->format('Y-m'));

        try {
            $date = Carbon::createFromFormat('Y-m', $selectedMonthYear);
        } catch (\Exception $e) {
            $date = Carbon::now();
        }
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        
        // Buat Range Tanggal Awal (Sebulan Penuh)
        $allDates = CarbonPeriod::create($startDate, $endDate);
        
        // Variabel untuk dikirim ke View
        $dates = collect(); // Default kosong
        $siswas = collect();
        $rekapData = [];
        $summaryData = [];

        // 3. Proses Data
        if ($selectedKelasId && $selectedMapelId) {
            // A. Ambil Hari Mengajar dari Jadwal
            // Kita cari tahu hari apa saja guru ini mengajar Mapel X di Kelas Y
            // Contoh Hasil: ['Senin', 'Kamis']
            $targetJadwals = Jadwal::where('users_id', $teacherId)
                                ->where('kelas_id', $selectedKelasId)
                                ->where('mapel_id', $selectedMapelId)
                                ->get();
            
            $validDays = $targetJadwals->pluck('hari')->unique()->toArray(); 
            $targetJadwalIds = $targetJadwals->pluck('id');

            // B. Filter Tanggal Berdasarkan Hari Mengajar
            foreach ($allDates as $dt) {
                // translatedFormat('l') menghasilkan nama hari lokal (Senin, Selasa, dll)
                // Pastikan locale Laravel Anda sudah 'id'
                if (in_array($dt->translatedFormat('l'), $validDays)) {
                    $dates->push($dt->copy());
                }
            }

            // C. Ambil Siswa
            $siswas = Siswa::where('kelas_id', $selectedKelasId)
                        ->orderBy('name')
                        ->get();

            // D. Ambil Absensi
            $absensiRaw = Absensi::whereIn('jadwal_id', $targetJadwalIds)
                                ->whereBetween('tanggal', [$startDate, $endDate])
                                ->get()
                                ->groupBy(['siswa_id', 'tanggal']);

            // E. Mapping Data
            foreach ($siswas as $siswa) {
                $summaryData[$siswa->id] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];

                // Loop hanya pada tanggal yang sudah difilter ($dates)
                foreach ($dates as $dt) {
                    $dateStr = $dt->format('Y-m-d');
                    $dayNum = $dt->format('j');
                    $status = ''; 

                    if (isset($absensiRaw[$siswa->id]) && isset($absensiRaw[$siswa->id][$dateStr])) {
                        $rec = $absensiRaw[$siswa->id][$dateStr]->first();
                        $s = substr($rec->status, 0, 1);
                        $status = $s;

                        if(isset($summaryData[$siswa->id][$s])) {
                            $summaryData[$siswa->id][$s]++;
                        }
                    }

                    $rekapData[$siswa->id][$dayNum] = $status;
                }
            }
        }

        return view('app.rekap_mapel', compact(
            'listKelas', 'listMapel', 
            'selectedKelasId', 'selectedMapelId', 'selectedMonthYear', 
            'dates', // Ini sekarang berisi Collection tanggal terpilih saja
            'date', 
            'siswas', 'rekapData', 'summaryData'
        ));
    }

    public function cetak(Request $request)
    {
        $user = Auth::user();
        $teacherId = $user->id;
        
        $kelas_id = $request->input('kelas_id');
        $mapel_id = $request->input('mapel_id');
        $bulan = $request->input('bulan');

        if(!$kelas_id || !$mapel_id) return redirect()->back();

        $kelas = Kelas::find($kelas_id);
        $mapel = Mapel::find($mapel_id);
        $guru  = $user;

        // Parsing Tanggal
        $date = Carbon::createFromFormat('Y-m', $bulan);
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $allDates = CarbonPeriod::create($startDate, $endDate);

        // --- FILTER LOGIC (Sama seperti index) ---
        $targetJadwals = Jadwal::where('users_id', $teacherId)
                            ->where('kelas_id', $kelas_id)
                            ->where('mapel_id', $mapel_id)
                            ->get();
        
        $validDays = $targetJadwals->pluck('hari')->unique()->toArray();
        $targetJadwalIds = $targetJadwals->pluck('id');

        // Filter Tanggal
        $dates = collect();
        foreach ($allDates as $dt) {
            if (in_array($dt->translatedFormat('l'), $validDays)) {
                $dates->push($dt->copy());
            }
        }
        // ------------------------------------------

        $siswas = Siswa::where('kelas_id', $kelas_id)->orderBy('name')->get();
        
        $absensiRaw = Absensi::whereIn('jadwal_id', $targetJadwalIds)
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->get()
                            ->groupBy(['siswa_id', 'tanggal']);
        
        $rekapData = [];
        $summaryData = [];

        foreach ($siswas as $siswa) {
            $summaryData[$siswa->id] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
            foreach ($dates as $dt) {
                $dateStr = $dt->format('Y-m-d');
                $dayNum = $dt->format('j');
                $status = ''; 

                if (isset($absensiRaw[$siswa->id]) && isset($absensiRaw[$siswa->id][$dateStr])) {
                    $rec = $absensiRaw[$siswa->id][$dateStr]->first();
                    $s = substr($rec->status, 0, 1);
                    $status = $s;
                    if(isset($summaryData[$siswa->id][$s])) $summaryData[$siswa->id][$s]++;
                }
                $rekapData[$siswa->id][$dayNum] = $status;
            }
        }

        return view('app.cetak_rekap_mapel', compact(
            'kelas', 'mapel', 'guru',
            'date', 'dates', 
            'siswas', 'rekapData', 'summaryData'
        ));
    }
}