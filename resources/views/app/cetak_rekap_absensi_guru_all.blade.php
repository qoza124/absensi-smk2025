<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi - {{ $selectedDate->translatedFormat('F Y') }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 9pt; }
        th { background-color: #f0f0f0; }
        .text-left { text-align: left; padding-left: 5px; }
        .weekend { background-color: #e0e0e0; }
        .header-title { text-align: center; margin-bottom: 20px; }
        h2, h3 { margin: 5px 0; }
    </style>
</head>
<body>

    <div class="header-title">
        <h2>REKAPITULASI KEHADIRAN GURU & PEGAWAI</h2>
        <h3>Periode: {{ $selectedDate->translatedFormat('F Y') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="30">No</th>
                <th rowspan="2" width="300">Nama Guru</th>
                <th colspan="{{ $dates->count() }}">Tanggal</th>
                <th rowspan="2">Total Kehadiran</th>
            </tr>
            <tr>
                @foreach ($dates as $date)
                    @php $isWeekend = $date->isSaturday() || $date->isSunday(); @endphp
                    <th width="25" class="{{ $isWeekend ? 'weekend' : '' }}">
                        {{ $date->format('j') }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($gurus as $guru)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $guru->name }}</td>
                    
                    @foreach ($dates as $date)
                        @php
                            $isWeekend = $date->isSaturday() || $date->isSunday();
                            $dayNum = $date->format('j');
                            $status = $rekapData[$guru->id][$dayNum] ?? '-';
                            
                            $code = '-';
                            if($status == 'Hadir') $code = 'H';
                            elseif($status == 'Izin') $code = 'I';
                            elseif($status == 'Sakit') $code = 'S';
                            elseif($status == 'Alpha') $code = 'A';
                        @endphp
                        <td class="{{ $isWeekend ? 'weekend' : '' }}">{{ $code == '-' ? '' : $code }}</td>
                    @endforeach

                    <td>{{ $summaryData[$guru->id]['Hadir'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; width: 100%; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 200px;">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <br><br><br>
            <p>Ansori, M.Pd., Gr.</p>
        </div>
    </div>

</body>
</html>