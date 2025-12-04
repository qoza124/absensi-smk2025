@extends('layout.master')

@section('title', 'Rekap Harian Siswa')

@section('content')
    <style>
        /* --- CSS TABLE UMUM --- */
        .table-rekap {
            border-collapse: collapse; /* Pastikan garis menyatu */
        }

        .table-rekap th, .table-rekap td { 
            vertical-align: middle; 
            font-size: 0.8rem; 
            padding: 4px; 
            height: 40px; /* Tinggi minimum baris */
            
            /* DEFAULT (LIGHT MODE): Garis 1px Solid Abu-abu Terang */
            border: 1px solid #dee2e6 !important;
        }
        
        /* Sticky Column (Nama Siswa) */
        .th-nama { 
            min-width: 200px; 
            max-width: 200px;
            text-align: left !important; 
            padding-left: 10px !important;
            position: sticky; 
            left: 0; 
            z-index: 5; 
            background-color: #ffffff !important; /* Fix Transparan Light Mode */
            
            /* Border kanan sticky column */
            border-right: 1px solid #dee2e6 !important; 
        }

        /* --- STYLE BADGE --- */
        .status-badge {
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
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

        /* Sel Kosong (Tidak Ada Jadwal) */
        .cell-empty {
            background-color: #f8f9fa; 
            color: #ccc;
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
            /* PERBAIKAN: Warna garis disamakan dengan Light Mode (#dee2e6) */
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
        /* Kita paksa semua sel di Dark Mode menggunakan warna garis Light Mode (#dee2e6) */
        [data-bs-theme="dark"] .table-rekap th, 
        [data-bs-theme="dark"] .table-rekap td {
            border: 1px solid #dee2e6 !important;
        }

        /* 4. Warna Badge Gelap */
        [data-bs-theme="dark"] .badge-h { background-color: #0f5132; color: #d1e7dd; border-color: #146c43; }
        [data-bs-theme="dark"] .badge-i { background-color: #055160; color: #cff4fc; border-color: #087990; }
        [data-bs-theme="dark"] .badge-s { background-color: #664d03; color: #fff3cd; border-color: #997404; }
        [data-bs-theme="dark"] .badge-a { background-color: #842029; color: #f8d7da; border-color: #b02a37; }

        /* 5. Sel Kosong Gelap */
        [data-bs-theme="dark"] .cell-empty {
            background-color: #2b3035;
            color: #495057;
        }
    </style>

    <div class="page-heading">
        <h3>Rekap Absensi Harian (Per Jam)</h3>
        <p class="text-subtitle text-muted">Detail kehadiran siswa jam ke-1 s.d ke-10.</p>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('rekap.siswa.harian') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas_list as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $selectedDate->format('Y-m-d') }}"
                            onchange="this.form.submit()">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success btn-print-harian"
                            data-url="{{ route('rekap.siswa.harian.cetak') }}?kelas_id={{ $selectedKelasId }}&tanggal={{ $selectedDate->format('Y-m-d') }}">
                            <i class="bi bi-printer"></i> Cetak PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedKelasId)
            <div class="card">
                <div class="card-header">
                    <h4>Hari: {{ $selectedDate->translatedFormat('l, d F Y') }}</h4>
                    <small class="text-muted">Ket: Arahkan kursor ke status untuk melihat Mapel.</small>
                </div>
                
                {{-- Jarak Padding Tabel --}}
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-rekap mb-0">
                            <thead class="thead-custom">
                                <tr>
                                    <th rowspan="2" style="width: 50px;" class="text-center">No</th>
                                    <th rowspan="2" class="th-nama">Nama Siswa</th>
                                    {{-- Header Rata Tengah --}}
                                    <th colspan="10" class="text-center">Jam Pelajaran</th>
                                </tr>
                                <tr>
                                    @for($i = 1; $i <= 10; $i++)
                                        {{-- Angka Jam Rata Tengah --}}
                                        <th class="text-center">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswas as $siswa)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="th-nama">{{ $siswa->name }}</td>
                                        @for($i = 1; $i <= 10; $i++)
                                            @php
                                                $data = $matrixHarian[$siswa->id][$i];
                                                $isSchedule = $data['ada_jadwal'];
                                                $status = $data['status'];
                                                
                                                $cellClass = $isSchedule ? '' : 'cell-empty';

                                                $badgeClass = '';
                                                if ($status == 'H') $badgeClass = 'badge-h';
                                                elseif ($status == 'I') $badgeClass = 'badge-i';
                                                elseif ($status == 'S') $badgeClass = 'badge-s';
                                                elseif ($status == 'A') $badgeClass = 'badge-a';
                                            @endphp

                                            {{-- Isi Tabel Rata Tengah --}}
                                            <td class="{{ $cellClass }} text-center"
                                                title="{{ $isSchedule ? $data['mapel'] : 'Tidak ada jadwal' }}"
                                                data-bs-toggle="tooltip">

                                                @if($isSchedule)
                                                    @if($status && $status != '-')
                                                        <span class="status-badge {{ $badgeClass }}">{{ $status }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">Tidak ada data siswa.</td>
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
        document.addEventListener('click', function(e) {
            if(e.target && e.target.closest('.btn-print-harian')) {
                var btn = e.target.closest('.btn-print-harian');
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
@endsection