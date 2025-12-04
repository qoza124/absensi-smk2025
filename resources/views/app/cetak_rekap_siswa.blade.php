<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Siswa</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 2px; }
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 9pt; }
        th { background-color: #f0f0f0; }
        .text-left { text-align: left; padding-left: 5px; }
        
        /* TANGGAL MERAH UNTUK PRINT */
        .weekend { 
            background-color: #ffebeb !important; 
            color: red !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEHADIRAN SISWA</h2>
        <h3>Kelas: {{ $kelas->name }} | Periode: {{ $selectedDate->translatedFormat('F Y') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="30">No</th>
                <th rowspan="2" width="200">Nama Siswa</th>
                <th colspan="{{ $dates->count() }}">Tanggal</th>
                <th colspan="4">Total</th>
            </tr>
            <tr>
                @foreach($dates as $dt)
                    <th class="{{ ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '' }}">
                        {{ $dt->format('j') }}
                    </th>
                @endforeach
                <th width="25">H</th> <th width="25">I</th> <th width="25">S</th> <th width="25">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $siswa->name }}</td>
                    @foreach($dates as $dt)
                        @php 
                            $day = $dt->format('j');
                            $st = $rekapData[$siswa->id][$day] ?? '-';
                            $isW = ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '';
                        @endphp
                        <td class="{{ $isW }}">{{ $st == '-' ? '' : $st }}</td>
                    @endforeach
                    <td>{{ $summaryData[$siswa->id]['H'] }}</td>
                    <td>{{ $summaryData[$siswa->id]['I'] }}</td>
                    <td>{{ $summaryData[$siswa->id]['S'] }}</td>
                    <td>{{ $summaryData[$siswa->id]['A'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <table style="border: none; margin-top: 20px;">
        <tr style="border: none;">
            <td style="border: none; width: 70%; text-align: left; vertical-align: top;">
                <strong>Keterangan:</strong><br>
                H = Hadir, I = Izin, S = Sakit, A = Alpha
            </td>
            <td style="border: none; width: 30%; text-align: center;">
                Mengetahui,<br>
                Wali Kelas {{ $kelas->name }}
                <br><br><br><br>
                <b>{{ $kelas->user->name ?? '.........................' }}</b>
            </td>
        </tr>
    </table>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>