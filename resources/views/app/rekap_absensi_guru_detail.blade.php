<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi {{ $guru->name }} - {{ $selectedDate->translatedFormat('F Y') }}</title>
    
    {{-- Style CSS khusus untuk halaman cetak --}}
    <style>
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }
        .container {
            width: 95%;
            margin: 0 auto;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 5px;
            font-weight: bold;
        }
        h1 { font-size: 18pt; }
        h2 { font-size: 16pt; margin-bottom: 20px;}
        h3 { font-size: 12pt; text-align: left; margin-top: 25px; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px;}

        /* Tabel Info Guru */
        .header-info {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11pt;
            border: none;
        }
        .header-info td {
            border: none;
            padding: 3px 5px;
        }

        /* Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #e9e9e9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* Tabel Ringkasan */
        .summary-table td {
            width: 25%;
            border: none;
            padding: 2px 5px;
        }

        /* Pengaturan saat di-print */
        @media print {
            .no-print {
                display: none; /* Sembunyikan tombol */
            }
            body {
                margin: 0;
                padding: 0;
            }
            /* Menghindari tabel terpotong antar halaman */
            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; } /* Ulangi header tabel di tiap halaman */
        }
    </style>
</head>

{{-- 
  PERUBAHAN DI SINI:
  Hapus 'onload="window.print()"' dari tag body.
--}}
<body>

    <div class="container">
        
        {{-- Tombol untuk cetak ulang atau menutup tab --}}
        <div class="no-print" style="text-align: center; margin-bottom: 15px;">
            <button onclick="window.print()">Cetak Ulang</button>
            <button onclick="window.close()">Tutup Halaman Ini</button>
        </div>
        
        <h1>Laporan Kehadiran Guru</h1>
        <h2>Periode: {{ $selectedDate->translatedFormat('F Y') }}</h2>
        
        <table class="header-info">
            <tr>
                <td style="width: 18%;"><b>Nama Guru</b></td>
                <td style="width: 2%;">:</td>
                <td><b>{{ $guru->name }}</b></td>
            </tr>
        </table>

        <hr>
        
        <h3>Data Kehadiran Harian (Sesuai Jadwal)</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Status Kehadiran</th>
                    <th>Jam Absen</th>
                    
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanHarian as $laporan)
                    <tr style="{{ $laporan['status'] == 'Kosong' ? 'background-color: #fff5f5; color: #a50000;' : '' }}">
                        <td>{{ $laporan['tanggal']->format('d M Y') }}</td>
                        <td>{{ $laporan['tanggal']->translatedFormat('l') }}</td>
                        <td>{{ $laporan['jadwal']->jam_mulai }} - {{ $laporan['jadwal']->jam_selesai }}</td>
                        <td>{{ $laporan['jadwal']->mapel->name ?? 'N/A' }}</td>
                        <td>{{ $laporan['jadwal']->kelas->name ?? 'N/A' }}</td>
                        <td class="text-center text-bold">{{ $laporan['status'] }}</td>
                        <td>{{ $laporan['tanggal']->format('H:i') }}</td>

                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data kehadiran (sesuai jadwal) untuk bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 30px; width: 100%; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 200px;">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <br><br><br>
            <p>Ansori, M.Pd., Gr.</p>
        </div>
    </div>
    

</body>
</html>