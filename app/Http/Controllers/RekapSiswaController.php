<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
// Tambahkan model Jadwal jika diperlukan untuk fitur harian/mingguan di masa depan
use App\Models\Jadwal;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class RekapSiswaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pengaturan Awal (Filter Kelas & Tanggal)
        $user = Auth::user();
        
        // Logika Akses Kelas (Admin vs Wali Kelas)
        if ($user->role == 'Wali Kelas') {
            $kelas_list = Kelas::where('users_id', $user->id)->get();
            $defaultKelas = $kelas_list->first()->id ?? null;
        } else {
            $kelas_list = Kelas::all();
            $defaultKelas = null;
        }

        $selectedKelasId = $request->input('kelas_id', $defaultKelas);
        $selectedMonthYear = $request->input('bulan', Carbon::now()->format('Y-m'));

        // Parsing Tanggal
        try {
            $date = Carbon::createFromFormat('Y-m', $selectedMonthYear);
        } catch (\Exception $e) {
            $date = Carbon::now();
        }
        
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $dates = CarbonPeriod::create($startDate, $endDate);

        // 2. Ambil Data Siswa berdasarkan Kelas
        $siswas = collect();
        $rekapData = [];
        $summaryData = [];

        if ($selectedKelasId) {
            $siswas = Siswa::where('kelas_id', $selectedKelasId)
                        ->orderBy('name', 'asc')
                        ->get();

            // 3. Ambil Data Absensi
            $absensiRaw = Absensi::whereIn('siswa_id', $siswas->pluck('id'))
                                ->whereBetween('tanggal', [$startDate, $endDate])
                                ->get()
                                ->groupBy(['siswa_id', 'tanggal']);

            // 4. Logika Pemrosesan Data (Matrix)
            foreach ($siswas as $siswa) {
                // Inisialisasi Summary
                $summaryData[$siswa->id] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];

                foreach ($dates as $dt) {
                    $dateStr = $dt->format('Y-m-d');
                    $dayNum = $dt->format('j');
                    
                    // Default Status
                    $statusHarian = '-'; 

                    // Cek apakah ada data absensi di tanggal & siswa ini
                    if (isset($absensiRaw[$siswa->id]) && isset($absensiRaw[$siswa->id][$dateStr])) {
                        
                        // Ambil semua record absensi hari itu (bisa lebih dari 1 mapel)
                        $dailyRecords = $absensiRaw[$siswa->id][$dateStr];
                        
                        $statuses = $dailyRecords->pluck('status')->toArray();

                        // ==========================================================
                        // PERUBAHAN LOGIKA PRIORITAS DI SINI
                        // Prioritas: Hadir > Izin > Sakit > Alpha
                        // ==========================================================
                        if (in_array('Hadir', $statuses)) {
                            $statusHarian = 'H';
                            $summaryData[$siswa->id]['H']++;
                        } elseif (in_array('Izin', $statuses)) {
                            $statusHarian = 'I';
                            $summaryData[$siswa->id]['I']++;
                        } elseif (in_array('Sakit', $statuses)) {
                            $statusHarian = 'S';
                            $summaryData[$siswa->id]['S']++;
                        } elseif (in_array('Alpha', $statuses)) {
                            $statusHarian = 'A';
                            $summaryData[$siswa->id]['A']++;
                        }
                    }

                    $rekapData[$siswa->id][$dayNum] = $statusHarian;
                }
            }
        }

        // 5. Return View
        return view('app.rekap_absensi_siswa', compact(
            'kelas_list', 
            'selectedKelasId', 
            'selectedMonthYear', 
            'dates', 
            'siswas', 
            'rekapData', 
            'summaryData',
            'date' 
        ));
    }

    public function cetak(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $bulan = $request->input('bulan');

        // Panggil helper function agar logika cetak juga sama
        $data = $this->getDataRekap($kelas_id, $bulan); 
        
        return view('app.cetak_rekap_siswa', $data);
    }

    // Helper function untuk mengambil data (agar tidak duplikasi kode)
    private function getDataRekap($kelasId, $monthYear) {
        try {
            $date = Carbon::createFromFormat('Y-m', $monthYear);
        } catch (\Exception $e) {
            $date = Carbon::now();
        }
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $dates = CarbonPeriod::create($startDate, $endDate);

        $kelas = Kelas::find($kelasId);
        $siswas = Siswa::where('kelas_id', $kelasId)->orderBy('name')->get();
        
        $rekapData = [];
        $summaryData = [];

        $absensiRaw = Absensi::whereIn('siswa_id', $siswas->pluck('id'))
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->get()
                            ->groupBy(['siswa_id', 'tanggal']);

        foreach ($siswas as $siswa) {
            $summaryData[$siswa->id] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
            foreach ($dates as $dt) {
                $dateStr = $dt->format('Y-m-d');
                $dayNum = $dt->format('j');
                $statusHarian = '-'; 

                if (isset($absensiRaw[$siswa->id]) && isset($absensiRaw[$siswa->id][$dateStr])) {
                    $statuses = $absensiRaw[$siswa->id][$dateStr]->pluck('status')->toArray();
                    
                    // ==========================================================
                    // PERUBAHAN LOGIKA PRIORITAS DI HELPER JUGA
                    // ==========================================================
                    if (in_array('Hadir', $statuses)) { 
                        $statusHarian = 'H'; 
                        $summaryData[$siswa->id]['H']++; 
                    } elseif (in_array('Izin', $statuses)) { 
                        $statusHarian = 'I'; 
                        $summaryData[$siswa->id]['I']++; 
                    } elseif (in_array('Sakit', $statuses)) { 
                        $statusHarian = 'S'; 
                        $summaryData[$siswa->id]['S']++; 
                    } elseif (in_array('Alpha', $statuses)) { 
                        $statusHarian = 'A'; 
                        $summaryData[$siswa->id]['A']++; 
                    }
                }
                $rekapData[$siswa->id][$dayNum] = $statusHarian;
            }
        }

        return [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'dates' => $dates,
            'rekapData' => $rekapData,
            'summaryData' => $summaryData,
            'selectedDate' => $date
        ];
    }
    
    // --- Method Tambahan untuk Rekap Harian & Mingguan ---
    // (Tambahkan method rekapHarian, rekapMingguan, cetakHarian, cetakMingguan yang sudah dibuat sebelumnya di sini)
    // ...
    // ...
    
    // --- CONTOH: REKAP HARIAN (10 JAM) ---
    public function rekapHarian(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'Wali Kelas') {
            $kelas_list = Kelas::where('users_id', $user->id)->get();
            $defaultKelas = $kelas_list->first()->id ?? null;
        } else {
            $kelas_list = Kelas::all();
            $defaultKelas = null;
        }

        $selectedKelasId = $request->input('kelas_id', $defaultKelas);
        $selectedDateStr = $request->input('tanggal', Carbon::now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($selectedDateStr);
        $hariIni = $selectedDate->translatedFormat('l'); 

        $siswas = collect();
        $matrixHarian = [];

        if ($selectedKelasId) {
            $siswas = Siswa::where('kelas_id', $selectedKelasId)->orderBy('name')->get();
            $jadwals = \App\Models\Jadwal::where('kelas_id', $selectedKelasId)
                        ->where('hari', $hariIni)
                        ->with('mapel')
                        ->get();

            $absensis = Absensi::whereIn('jadwal_id', $jadwals->pluck('id'))
                        ->whereDate('tanggal', $selectedDateStr)
                        ->get()
                        ->groupBy(['siswa_id', 'jadwal_id']);

            foreach ($siswas as $siswa) {
                for ($jam = 1; $jam <= 10; $jam++) {
                    $jadwalAktif = $jadwals->filter(function($j) use ($jam) {
                        return $jam >= $j->jam_mulai && $jam <= $j->jam_selesai;
                    })->first();

                    if ($jadwalAktif) {
                        $status = '-';
                        if (isset($absensis[$siswa->id]) && isset($absensis[$siswa->id][$jadwalAktif->id])) {
                            $rec = $absensis[$siswa->id][$jadwalAktif->id]->first();
                            if ($rec->status == 'Hadir') $status = 'H';
                            elseif ($rec->status == 'Izin') $status = 'I';
                            elseif ($rec->status == 'Sakit') $status = 'S';
                            elseif ($rec->status == 'Alpha') $status = 'A';
                        }
                        $matrixHarian[$siswa->id][$jam] = [
                            'ada_jadwal' => true,
                            'mapel' => $jadwalAktif->mapel->name,
                            'status' => $status
                        ];
                    } else {
                        $matrixHarian[$siswa->id][$jam] = [
                            'ada_jadwal' => false, 'mapel' => '', 'status' => ''
                        ];
                    }
                }
            }
        }
        return view('app.rekap_siswa_harian', compact('kelas_list', 'selectedKelasId', 'selectedDate', 'siswas', 'matrixHarian'));
    }

    public function cetakHarian(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $tanggal = $request->input('tanggal', Carbon::now()->format('Y-m-d'));
        if(!$kelas_id) return redirect()->back();

        $kelas = Kelas::find($kelas_id);
        $selectedDate = Carbon::parse($tanggal);
        $hariIni = $selectedDate->translatedFormat('l');
        $siswas = Siswa::where('kelas_id', $kelas_id)->orderBy('name')->get();
        $jadwals = \App\Models\Jadwal::where('kelas_id', $kelas_id)->where('hari', $hariIni)->with('mapel')->get();
        $absensis = Absensi::whereIn('jadwal_id', $jadwals->pluck('id'))->whereDate('tanggal', $tanggal)->get()->groupBy(['siswa_id', 'jadwal_id']);

        $matrixHarian = [];
        foreach ($siswas as $siswa) {
            for ($jam = 1; $jam <= 10; $jam++) {
                $jadwalAktif = $jadwals->filter(fn($j) => $jam >= $j->jam_mulai && $jam <= $j->jam_selesai)->first();
                $status = null;
                if ($jadwalAktif && isset($absensis[$siswa->id][$jadwalAktif->id])) {
                    $status = substr($absensis[$siswa->id][$jadwalAktif->id]->first()->status, 0, 1);
                }
                $matrixHarian[$siswa->id][$jam] = [
                    'ada_jadwal' => $jadwalAktif ? true : false,
                    'status' => $status
                ];
            }
        }
        return view('app.cetak_rekap_siswa_harian', compact('kelas', 'selectedDate', 'siswas', 'matrixHarian'));
    }

    // --- CONTOH: REKAP MINGGUAN (MAPEL) ---
    public function rekapMingguan(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'Wali Kelas') {
            $kelas_list = Kelas::where('users_id', $user->id)->get();
            $defaultKelas = $kelas_list->first()->id ?? null;
        } else {
            $kelas_list = Kelas::all();
            $defaultKelas = null;
        }
        $selectedKelasId = $request->input('kelas_id', $defaultKelas);
        $startStr = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endStr = $request->input('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startStr);
        $endDate = Carbon::parse($endStr);
        $dates = CarbonPeriod::create($startDate, $endDate);
        $siswas = collect();
        $rekapMingguan = [];

        if ($selectedKelasId) {
            $siswas = Siswa::where('kelas_id', $selectedKelasId)->orderBy('name')->get();
            $allJadwal = \App\Models\Jadwal::where('kelas_id', $selectedKelasId)->with('mapel')->orderBy('jam_mulai')->get()->groupBy('hari');
            $absensiRaw = Absensi::whereIn('siswa_id', $siswas->pluck('id'))->whereBetween('tanggal', [$startDate, $endDate])->get()->groupBy(['siswa_id', 'tanggal', 'jadwal_id']);

            foreach ($siswas as $siswa) {
                foreach ($dates as $dt) {
                    $dateStr = $dt->format('Y-m-d');
                    $namaHari = $dt->translatedFormat('l');
                    $dataHariIni = [];
                    if (isset($allJadwal[$namaHari])) {
                        foreach ($allJadwal[$namaHari] as $jadwal) {
                            $status = '-';
                            if (isset($absensiRaw[$siswa->id][$dateStr][$jadwal->id])) {
                                $status = substr($absensiRaw[$siswa->id][$dateStr][$jadwal->id]->first()->status, 0, 1);
                            }
                            $dataHariIni[] = ['mapel' => $jadwal->mapel->name, 'status' => $status];
                        }
                    }
                    $rekapMingguan[$siswa->id][$dateStr] = $dataHariIni;
                }
            }
        }
        return view('app.rekap_siswa_mingguan', compact('kelas_list', 'selectedKelasId', 'startDate', 'endDate', 'dates', 'siswas', 'rekapMingguan'));
    }

    public function cetakMingguan(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $startStr = $request->input('start_date');
        $endStr = $request->input('end_date');
        if(!$kelas_id) return redirect()->back();

        $kelas = Kelas::find($kelas_id);
        $startDate = Carbon::parse($startStr);
        $endDate = Carbon::parse($endStr);
        $dates = CarbonPeriod::create($startDate, $endDate);
        $siswas = Siswa::where('kelas_id', $kelas_id)->orderBy('name')->get();
        $allJadwal = \App\Models\Jadwal::where('kelas_id', $kelas_id)->with('mapel')->orderBy('jam_mulai')->get()->groupBy('hari');
        $absensiRaw = Absensi::whereIn('siswa_id', $siswas->pluck('id'))->whereBetween('tanggal', [$startDate, $endDate])->get()->groupBy(['siswa_id', 'tanggal', 'jadwal_id']);

        $rekapMingguan = [];
        foreach ($siswas as $siswa) {
            foreach ($dates as $dt) {
                $dateStr = $dt->format('Y-m-d');
                $namaHari = $dt->translatedFormat('l');
                $dataHariIni = [];
                if (isset($allJadwal[$namaHari])) {
                    foreach ($allJadwal[$namaHari] as $jadwal) {
                        $status = '-';
                        if (isset($absensiRaw[$siswa->id][$dateStr][$jadwal->id])) {
                            $status = substr($absensiRaw[$siswa->id][$dateStr][$jadwal->id]->first()->status, 0, 1);
                        }
                        $dataHariIni[] = ['mapel' => $jadwal->mapel->name, 'status' => $status];
                    }
                }
                $rekapMingguan[$siswa->id][$dateStr] = $dataHariIni;
            }
        }
        return view('app.cetak_rekap_siswa_mingguan', compact('kelas', 'startDate', 'endDate', 'dates', 'siswas', 'rekapMingguan'));
    }
}