@extends('layout.master')

@section('title', 'Rekap Absensi Siswa')

@section('content')
    <style>
        /* --- CSS TABLE UMUM --- */
        .table-rekap {
            border-collapse: collapse; /* Pastikan garis menyatu */
        }

        .table-rekap th, .table-rekap td { 
            text-align: center; 
            vertical-align: middle; 
            font-size: 0.8rem; 
            padding: 4px; 
            
            /* PERBAIKAN: Garis 1px Solid Abu-abu Terang (Seragam) */
            border: 1px solid #dee2e6 !important;
        }
        
        /* Sticky Column (Nama Siswa) */
        .th-nama { 
            min-width: 180px; 
            max-width: 180px;
            text-align: left !important; 
            padding-left: 10px !important;
            position: sticky; 
            left: 0; 
            z-index: 5; 
            background-color: #ffffff !important; /* Fix Transparan Light Mode */
            
            /* Border kanan sticky column */
            border-right: 1px solid #dee2e6 !important;
        }

        /* --- STYLE BADGE (Rounded Box) --- */
        .status-badge {
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.75rem;
            text-align: center;
        }

        /* Warna Badge (Light Mode) */
        .badge-h { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; } 
        .badge-i { background-color: #cff4fc; color: #055160; border: 1px solid #b6effb; } 
        .badge-s { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; } 
        .badge-a { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        
        /* Warna Teks Strip (-) */
        .text-strip { color: #adb5bd; font-weight: bold; }

        /* Tanggal Merah (Background Sel & Header) */
        .weekend { 
            background-color: #ffebeb !important; 
            color: #d8000c !important;
            font-weight: bold;
        }

        /* Header Custom (Light Mode) */
        .thead-custom th {
            background-color: #f8f9fa;
            color: #000;
        }

        /* --- DARK MODE OVERRIDES --- */
        
        /* 1. Header Tabel Gelap */
        [data-bs-theme="dark"] .thead-custom th {
            background-color: #1e1e2d !important;
            color: #dee2e6 !important;
            /* PERBAIKAN: Warna garis disamakan dengan Light Mode */
            border-color: #dee2e6 !important;
        }

        /* 2. Sticky Column Gelap */
        [data-bs-theme="dark"] .th-nama {
            background-color: #1e1e2d !important;
            /* PERBAIKAN: Warna garis disamakan dengan Light Mode */
            border-right: 1px solid #dee2e6 !important;
            color: #dee2e6 !important;
        }

        /* 3. Border Seragam Gelap (PENTING) */
        [data-bs-theme="dark"] .table-rekap th,
        [data-bs-theme="dark"] .table-rekap td {
            border: 1px solid #dee2e6 !important;
        }

        /* 4. Tanggal Merah Gelap */
        [data-bs-theme="dark"] .weekend {
            background-color: #2c0b0e !important;
            color: #ea868f !important;
            border-color: #dee2e6 !important;
        }

        /* 5. Warna Badge Gelap */
        [data-bs-theme="dark"] .badge-h { background-color: #0f5132; color: #d1e7dd; border-color: #146c43; }
        [data-bs-theme="dark"] .badge-i { background-color: #055160; color: #cff4fc; border-color: #087990; }
        [data-bs-theme="dark"] .badge-s { background-color: #664d03; color: #fff3cd; border-color: #997404; }
        [data-bs-theme="dark"] .badge-a { background-color: #842029; color: #f8d7da; border-color: #b02a37; }

        /* 6. Teks Strip Gelap */
        [data-bs-theme="dark"] .text-strip { color: #495057; }
    </style>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Rekap Absensi Siswa (Bulanan)</h3>
                    <p class="text-subtitle text-muted">Laporan kehadiran siswa per kelas.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rekap Siswa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        {{-- Card Filter --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filter Data</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('rekap.siswa.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas_list as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bulan & Tahun</label>
                        <input type="month" name="bulan" class="form-control" value="{{ $selectedMonthYear }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-filter"></i> Tampilkan
                        </button>
                        @if($selectedKelasId)
                            <button type="button" class="btn btn-success btn-print-siswa" 
                                data-url="{{ route('rekap.siswa.cetak', ['kelas_id' => $selectedKelasId, 'bulan' => $selectedMonthYear]) }}">
                                <i class="bi bi-printer"></i> Cetak PDF
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($selectedKelasId && $siswas->count() > 0)
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Periode: {{ $date->translatedFormat('F Y') }}</h4>
                <div class="small">
                    <span class="badge bg-success">H: Hadir</span>
                    <span class="badge bg-info">I: Izin</span>
                    <span class="badge bg-warning">S: Sakit</span>
                    <span class="badge bg-danger">A: Alpha</span>
                </div>
            </div>
            
            {{-- Padding Card Body --}}
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-rekap mb-0">
                        <thead class="thead-custom">
                            <tr>
                                <th rowspan="2" style="width: 40px;">No</th>
                                <th rowspan="2" class="th-nama">Nama Siswa</th>
                                <th colspan="{{ $dates->count() }}">Tanggal</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                @foreach($dates as $dt)
                                    {{-- Header Tanggal (Merah jika libur) --}}
                                    <th class="{{ ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '' }}">
                                        {{ $dt->format('j') }}
                                    </th>
                                @endforeach
                                <th class="text-success">H</th> 
                                <th class="text-info">I</th> 
                                <th class="text-warning">S</th> 
                                <th class="text-danger">A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $siswa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="th-nama">{{ $siswa->name }}</td>
                                    @foreach($dates as $dt)
                                        @php 
                                            $day = $dt->format('j');
                                            $st = $rekapData[$siswa->id][$day] ?? '-';
                                            
                                            // Background sel hanya berubah jika weekend
                                            $isW = ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '';
                                            
                                            // Tentukan Class Badge
                                            $badgeClass = '';
                                            if($st == 'H') $badgeClass = 'badge-h';
                                            elseif($st == 'I') $badgeClass = 'badge-i';
                                            elseif($st == 'S') $badgeClass = 'badge-s';
                                            elseif($st == 'A') $badgeClass = 'badge-a';
                                        @endphp
                                        
                                        {{-- Sel Tabel (Hanya class weekend jika libur) --}}
                                        <td class="{{ $isW }}">
                                            @if($st && $st != '-')
                                                <span class="status-badge {{ $badgeClass }}">{{ $st }}</span>
                                            @else
                                                <span class="text-strip">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    {{-- Total --}}
                                    <td><strong>{{ $summaryData[$siswa->id]['H'] }}</strong></td>
                                    <td><strong>{{ $summaryData[$siswa->id]['I'] }}</strong></td>
                                    <td><strong>{{ $summaryData[$siswa->id]['S'] }}</strong></td>
                                    <td><strong>{{ $summaryData[$siswa->id]['A'] }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif($selectedKelasId)
            <div class="alert alert-warning m-3">
                <i class="bi bi-exclamation-triangle"></i> Tidak ada data siswa di kelas ini.
            </div>
        @endif

    </section>
@endsection

@push('script')
<script>
    document.addEventListener('click', function(e) {
        if(e.target && e.target.closest('.btn-print-siswa')) {
            var btn = e.target.closest('.btn-print-siswa');
            var url = btn.dataset.url;
            
            Swal.fire({
                title: 'Sedang Memproses...',
                text: 'Menyiapkan halaman cetak',
                timer: 1000,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading() }
            });

            var oldFrame = document.getElementById('printFrame');
            if (oldFrame) oldFrame.remove();

            var iframe = document.createElement('iframe');
            iframe.id = 'printFrame';
            iframe.src = url;
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch(e) {
                    console.error(e);
                }
            };
        }
    });
</script>
@endpush