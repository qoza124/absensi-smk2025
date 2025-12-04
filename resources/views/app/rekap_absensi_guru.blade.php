@extends('layout.master')

@section('title', 'Rekap Absensi Guru - SI Absensi')

@section('content')
    <style>
        /* --- CSS TABLE UMUM --- */
        .table-bulanan {
            border-collapse: collapse;
            min-width: 1500px; /* Agar bisa discroll horizontal */
        }

        .table-bulanan th, .table-bulanan td { 
            text-align: center; 
            vertical-align: middle; 
            font-size: 0.85rem; 
            padding: 6px;
            height: 45px; 
            
            /* GARIS SERAGAM (Light Mode) */
            border: 1px solid #dee2e6 !important;
        }
        
        /* --- STICKY COLUMNS --- */
        /* Kolom 1: Nama Guru */
        .guru-name { 
            position: sticky; 
            left: 0; 
            z-index: 5; 
            min-width: 200px; 
            text-align: left !important; 
            padding-left: 15px !important;
            
            /* Background & Border Light Mode */
            background-color: #ffffff !important; 
            border-right: 1px solid #dee2e6 !important; 
        }

        /* Kolom 2: Aksi (Cetak) */
        .guru-action { 
            position: sticky; 
            left: 200px; /* Sesuai min-width guru-name */
            z-index: 5; 
            min-width: 80px; 
            
            /* Background & Border Light Mode */
            background-color: #ffffff !important; 
            border-right: 1px solid #dee2e6 !important; 
        }

        /* Fix z-index untuk header sticky agar di atas body sticky */
        thead .guru-name, thead .guru-action {
            z-index: 6; 
            background-color: #f8f9fa !important; /* Warna Header Light */
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
        .badge-h { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; } /* Hadir */
        .badge-i { background-color: #cff4fc; color: #055160; border: 1px solid #b6effb; } /* Izin */
        .badge-s { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; } /* Sakit */
        .badge-a { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; } /* Alpha */
        
        /* Warna Strip */
        .text-strip { color: #adb5bd; font-weight: bold; }

        /* Tanggal Merah (Light Mode) */
        .weekend { 
            background-color: #ffebeb !important; 
            color: #d8000c !important;
            font-weight: bold;
        }

        /* Header Custom */
        .thead-custom th {
            background-color: #f8f9fa;
            color: #000;
        }

        /* --- DARK MODE OVERRIDES --- */
        
        /* 1. Header Tabel & Sticky Header Gelap */
        [data-bs-theme="dark"] .thead-custom th,
        [data-bs-theme="dark"] thead .guru-name, 
        [data-bs-theme="dark"] thead .guru-action {
            background-color: #1e1e2d !important;
            color: #dee2e6 !important;
            border-color: #dee2e6 !important;
        }

        /* 2. Sticky Body Gelap */
        [data-bs-theme="dark"] .guru-name, 
        [data-bs-theme="dark"] .guru-action {
            background-color: #1e1e2d !important;
            border-right: 1px solid #dee2e6 !important;
            color: #dee2e6 !important;
        }

        /* 3. Border Seragam Gelap */
        [data-bs-theme="dark"] .table-bulanan th,
        [data-bs-theme="dark"] .table-bulanan td {
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

        /* 6. Strip Gelap */
        [data-bs-theme="dark"] .text-strip { color: #495057; }
    </style>

    <div class="page-heading no-print">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Rekap Absensi Guru</h3>
                    <p class="text-subtitle text-muted">Laporan kehadiran guru bulanan.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rekap Guru</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section no-print">
        {{-- Filter Card --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filter Data</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('rekap.absensi_guru') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="bulan" class="form-label">Bulan & Tahun</label>
                        <input type="month" class="form-control" id="bulan" name="bulan" value="{{ $selectedMonthYear }}">
                    </div>
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-filter"></i> Tampilkan
                        </button>
                        <button type="button" class="btn btn-success btn-print-all"
                            data-url="{{ route('rekap.absensi_guru.cetak_semua', ['bulan' => $selectedMonthYear]) }}">
                            <i class="bi bi-printer"></i> Cetak Rekap Bulanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Data Card --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Data: {{ $selectedDate->translatedFormat('F Y') }}</h4>
                <div class="small">
                    <span class="badge bg-success me-1">H: Hadir</span>
                    <span class="badge bg-info me-1">I: Izin</span>
                    <span class="badge bg-warning me-1">S: Sakit</span>
                    <span class="badge bg-danger">A: Alpha</span>
                </div>
            </div>
            
            {{-- PERBAIKAN: Padding p-4 --}}
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 table-bulanan">
                        <thead class="thead-custom">
                            <tr>
                                <th class="guru-name">Nama Guru</th>
                                <th class="guru-action">Cetak</th>
                                @foreach ($dates as $date)
                                    <th class="{{ ($date->isSaturday() || $date->isSunday()) ? 'weekend' : '' }}">
                                        {{ $date->format('j') }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gurus as $guru)
                                <tr>
                                    <td class="guru-name">{{ $guru->name }}</td>
                                    <td class="guru-action">
                                        <button class="btn btn-sm btn-secondary btn-print-detail"
                                            data-url="{{ route('rekap.absensi_guru.print_detail', ['user_id' => $guru->id, 'bulan' => $selectedMonthYear]) }}"
                                            title="Cetak Detail">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                    </td>
                                    @foreach ($dates as $date)
                                        @php
                                            $dayNum = $date->format('j');
                                            $status = $rekapData[$guru->id][$dayNum] ?? null;
                                            
                                            // Tentukan Badge Class
                                            $badgeClass = '';
                                            $label = '-';
                                            
                                            if ($status == 'Hadir') { $badgeClass = 'badge-h'; $label = 'H'; }
                                            elseif ($status == 'Izin') { $badgeClass = 'badge-i'; $label = 'I'; }
                                            elseif ($status == 'Sakit') { $badgeClass = 'badge-s'; $label = 'S'; }
                                            elseif ($status == 'Alpha') { $badgeClass = 'badge-a'; $label = 'A'; }
                                            
                                            // Cek Weekend untuk sel body
                                            $isWeekend = ($date->isSaturday() || $date->isSunday()) ? 'weekend' : '';
                                        @endphp
                                        
                                        <td class="{{ $isWeekend }}">
                                            @if($status)
                                                <span class="status-badge {{ $badgeClass }}">{{ $label }}</span>
                                            @else
                                                <span class="text-strip">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $dates->count() + 2 }}" class="text-center p-4">Tidak ada data guru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Script Cetak --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function printViaIframe(url) {
                const oldFrame = document.getElementById('printFrame');
                if (oldFrame) oldFrame.remove();

                const iframe = document.createElement('iframe');
                iframe.id = 'printFrame';
                iframe.src = url;
                iframe.style.display = 'none';
                document.body.appendChild(iframe);

                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        alert("Gagal mencetak.");
                    }
                };
            }

            const btnPrintAll = document.querySelector('.btn-print-all');
            if(btnPrintAll){
                btnPrintAll.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Memuat...',
                        timer: 1000,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading() }
                    });
                    printViaIframe(this.dataset.url);
                });
            }

            document.querySelectorAll('.btn-print-detail').forEach(button => {
                button.addEventListener('click', function() {
                    printViaIframe(this.dataset.url);
                });
            });
        });
    </script>
@endsection