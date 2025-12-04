<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Harian</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 4px 0; }
        
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 9pt; vertical-align: middle; }
        th { background-color: #f0f0f0; font-weight: bold; }
        
        .text-left { text-align: left !important; padding-left: 8px; }
        
        /* Warna Status */
        .bg-h { background-color: #d1e7dd; } /* Hijau Muda */
        .bg-i { background-color: #cff4fc; } /* Biru Muda */
        .bg-s { background-color: #fff3cd; } /* Kuning Muda */
        .bg-a { background-color: #f8d7da; } /* Merah Muda */
        .bg-empty { background-color: #f9f9f9; color: #ccc; }

        /* Footer Tanda Tangan */
        .footer-sign { margin-top: 30px; width: 100%; display: flex; justify-content: flex-end; }
        .sign-box { width: 250px; text-align: center; float: right; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>REKAP ABSENSI HARIAN SISWA (PER JAM)</h2>
        <h3>Kelas: {{ $kelas->name }} | Tanggal: {{ $selectedDate->translatedFormat('l, d F Y') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="40">No</th>
                <th rowspan="2" width="250">Nama Siswa</th>
                <th colspan="10">Jam Pelajaran Ke-</th>
            </tr>
            <tr>
                @for($i=1; $i<=10; $i++)
                    <th width="50">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $siswa->name }}</td>
                    @for($i=1; $i<=10; $i++)
                        @php
                            $data = $matrixHarian[$siswa->id][$i];
                            $cls = 'bg-empty';
                            $text = '';

                            if ($data['ada_jadwal']) {
                                $cls = ''; 
                                if($data['status'] == 'H') $cls = 'bg-h';
                                elseif($data['status'] == 'I') $cls = 'bg-i';
                                elseif($data['status'] == 'S') $cls = 'bg-s';
                                elseif($data['status'] == 'A') $cls = 'bg-a';
                                
                                $text = $data['status'] ?: '-';
                            }
                        @endphp
                        <td class="{{ $cls }}">{{ $text }}</td>
                    @endfor
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