<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Mapel</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 2px 0; }
        .info-table { width: 100%; margin-bottom: 15px; border: none; }
        .info-table td { border: none; padding: 2px; text-align: left; font-size: 10pt; }
        
        table.data { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        table.data th, table.data td { border: 1px solid #000; padding: 3px; text-align: center; font-size: 8pt; }
        table.data th { background-color: #f0f0f0; }
        
        .text-left { text-align: left !important; padding-left: 5px; }
        .weekend { background-color: #e0e0e0; }
        
        .footer-sign { margin-top: 20px; display: flex; justify-content: flex-end; }
        .sign-box { width: 200px; text-align: center; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>REKAPITULASI KEHADIRAN SISWA PER MATA PELAJARAN</h2>
        <h3>Periode: {{ $date->translatedFormat('F Y') }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><b>Mata Pelajaran</b></td>
            <td width="2%">:</td>
            <td width="33%">{{ $mapel->name }}</td>
            <td width="15%"><b>Guru Pengampu</b></td>
            <td width="2%">:</td>
            <td>{{ $guru->name }}</td>
        </tr>
        <tr>
            <td><b>Kelas</b></td>
            <td>:</td>
            <td>{{ $kelas->name }}</td>
            <td></td><td></td><td></td>
        </tr>
    </table>

    <table class="data">
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
                <th width="25">H</th><th width="25">I</th><th width="25">S</th><th width="25">A</th>
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
                            $st = $rekapData[$siswa->id][$day] ?? '';
                            $isW = ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '';
                        @endphp
                        <td class="{{ $isW }}">{{ $st }}</td>
                    @endforeach
                    <td>{{ $summaryData[$siswa->id]['H'] }}</td>
                    <td>{{ $summaryData[$siswa->id]['I'] }}</td>
                    <td>{{ $summaryData[$siswa->id]['S'] }}</td>
                    <td>{{ $summaryData[$siswa->id]['A'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Mengetahui,<br>Guru Mata Pelajaran</p>
            <br><br><br><br>
            <p><b>{{ $guru->name }}</b></p>
        </div>
    </div>

</body>
</html>