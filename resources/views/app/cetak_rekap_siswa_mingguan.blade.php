<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Mingguan</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 4px 0; }
        
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 8pt; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; vertical-align: middle; }
        
        .text-left { text-align: left !important; padding-left: 5px; }
        
        /* Item Mapel dalam Cell */
        .item-mapel { 
            display: flex; 
            justify-content: space-between; 
            border-bottom: 1px solid #ddd; 
            padding: 2px 0; 
            font-size: 7.5pt;
        }
        .item-mapel:last-child { border-bottom: none; }
        
        .status-box { 
            font-weight: bold; 
            width: 15px; 
            text-align: center; 
            display: inline-block;
        }

        /* Footer */
        .footer-sign { margin-top: 20px; width: 100%; }
        .sign-box { width: 250px; text-align: center; float: right; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>REKAP ABSENSI MINGGUAN SISWA (PER MAPEL)</h2>
        <h3>Kelas: {{ $kelas->name }} | Periode: {{ $startDate->translatedFormat('d M Y') }} s/d {{ $endDate->translatedFormat('d M Y') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="150">Nama Siswa</th>
                @foreach($dates as $dt)
                    <th>{{ $dt->translatedFormat('l') }}<br><small>{{ $dt->format('d/m') }}</small></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $siswa->name }}</td>
                    @foreach($dates as $dt)
                        @php
                            $dateStr = $dt->format('Y-m-d');
                            $items = $rekapMingguan[$siswa->id][$dateStr] ?? [];
                            $bgCell = ($dt->isSaturday() || $dt->isSunday()) ? '#fafafa' : '#fff';
                        @endphp
                        <td style="background-color: {{ $bgCell }}">
                            @foreach($items as $item)
                                <div class="item-mapel">
                                    <span style="text-align: left; width: 80%;">{{ Str::limit($item['mapel'], 12, '..') }}</span>
                                    <span class="status-box">{{ $item['status'] }}</span>
                                </div>
                            @endforeach
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Mengetahui,<br>Wali Kelas {{ $kelas->name }}</p>
            <br><br><br><br>
            <p><b>{{ $kelas->user->name ?? '.........................' }}</b></p>
        </div>
    </div>

</body>
</html>