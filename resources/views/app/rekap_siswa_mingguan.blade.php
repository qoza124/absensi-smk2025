@extends('layout.master')

@section('title', 'Rekap Mingguan Siswa')

@section('content')
    <style>
        /* --- CSS TABLE UMUM --- */
        .table-mingguan {
            border-collapse: collapse;
        }

        .table-mingguan th,
        .table-mingguan td {
            font-size: 0.8rem;
            vertical-align: top;
            
            /* PERBAIKAN: Garis 1px Solid Abu-abu Terang (Seragam) */
            border: 1px solid #dee2e6 !important;
        }

        .item-mapel {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding: 2px 0;
        }

        .item-mapel:last-child {
            border-bottom: none;
        }

        .badge-status {
            width: 20px;
            display: inline-block;
            text-align: center;
            border-radius: 3px;
            font-weight: bold;
        }

        /* --- MODE TERANG (DEFAULT) --- */
        .bg-h { background: #d1e7dd; color: #0f5132; }
        .bg-i { background: #cff4fc; color: #055160; }
        .bg-s { background: #fff3cd; color: #664d03; }
        .bg-a { background: #f8d7da; color: #842029; }
        .bg-miss { background: #f0f0f0; color: #666; } 
        
        /* --- MODE GELAP (DARK MODE) --- */
        
        /* 1. Override Header Tabel */
        [data-bs-theme="dark"] .table-light th {
            background-color: #1e1e2d !important;
            color: #dee2e6 !important;
            /* PERBAIKAN: Warna garis disamakan dengan Light Mode */
            border-color: #dee2e6 !important;
        }

        /* 2. Border Seragam Gelap (PENTING) */
        [data-bs-theme="dark"] .table-mingguan th,
        [data-bs-theme="dark"] .table-mingguan td {
            border: 1px solid #dee2e6 !important;
        }

        /* 3. Override Warna Status */
        [data-bs-theme="dark"] .bg-h { background: #0f5132; color: #d1e7dd; }
        [data-bs-theme="dark"] .bg-i { background: #055160; color: #cff4fc; }
        [data-bs-theme="dark"] .bg-s { background: #664d03; color: #fff3cd; }
        [data-bs-theme="dark"] .bg-a { background: #842029; color: #f8d7da; }
        [data-bs-theme="dark"] .bg-miss { background: #2b3035; color: #888; }

        /* 4. Override Border Item Mapel */
        [data-bs-theme="dark"] .item-mapel { border-bottom: 1px solid #dee2e6; }

        @media print {
            .no-print { display: none; }
            .page-heading { display: none; }
            .card { border: none; box-shadow: none; }
            .table-responsive { overflow: visible; }
        }
    </style>

    <div class="page-heading">
        <h3>Rekap Absensi Mingguan</h3>
        <p class="text-subtitle text-muted">Detail kehadiran per Mapel dalam satu minggu (Senin - Jumat).</p>
    </div>

    <section class="section">
        <div class="card no-print">
            <div class="card-body">
                <form method="GET" action="{{ route('rekap.siswa.mingguan') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas_list as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Tampilkan</button>
                        <button type="button" class="btn btn-success btn-print-mingguan"
                            data-url="{{ route('rekap.siswa.mingguan.cetak') }}?kelas_id={{ $selectedKelasId }}&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}">
                            <i class="bi bi-printer"></i> Cetak PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedKelasId)
            <div class="card">
                <div class="card-header text-center">
                    <h4>Laporan Absensi Mingguan</h4>
                    <p>Periode: {{ $startDate->translatedFormat('d M Y') }} s/d {{ $endDate->translatedFormat('d M Y') }}</p>
                </div>
                
                {{-- Padding Card Body --}}
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-mingguan mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 200px;">Nama Siswa</th>
                                    @foreach($dates as $dt)
                                        {{-- SEMBUNYIKAN SABTU MINGGU --}}
                                        @if($dt->isSaturday() || $dt->isSunday()) @continue @endif
                                        
                                        <th>
                                            {{ $dt->translatedFormat('l') }}<br>
                                            <small>{{ $dt->format('d/m') }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswas as $siswa)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $siswa->name }}</td>

                                        @foreach($dates as $dt)
                                            {{-- SEMBUNYIKAN SABTU MINGGU --}}
                                            @if($dt->isSaturday() || $dt->isSunday()) @continue @endif

                                            @php
                                                $dateStr = $dt->format('Y-m-d');
                                                $items = $rekapMingguan[$siswa->id][$dateStr] ?? [];
                                            @endphp
                                            
                                            <td>
                                                @if(count($items) > 0)
                                                    @foreach($items as $item)
                                                        @php
                                                            $bg = 'bg-miss';
                                                            if ($item['status'] == 'H') $bg = 'bg-h';
                                                            if ($item['status'] == 'I') $bg = 'bg-i';
                                                            if ($item['status'] == 'S') $bg = 'bg-s';
                                                            if ($item['status'] == 'A') $bg = 'bg-a';
                                                        @endphp
                                                        <div class="item-mapel">
                                                            <span style="font-size: 0.75rem;">{{ $item['mapel'] }}</span>
                                                            <span class="badge-status {{ $bg }}">{{ $item['status'] }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted" style="font-size: 0.7rem;">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </section>
    
    @push('script')
        <script>
            document.addEventListener('click', function (e) {
                if (e.target && e.target.closest('.btn-print-mingguan')) {
                    var btn = e.target.closest('.btn-print-mingguan');
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

                    iframe.onload = function () {
                        try {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                        } catch (e) {
                            console.error(e);
                        }
                    };
                }
            });
        </script>
    @endpush
@endsection