@extends('layout.master')

@section('title', 'Input Absensi - SI Absensi')

@section('content')
    {{-- CSS KHUSUS HALAMAN INI --}}
    <style>
        /* Membuat font tabel lebih kecil dan padding lebih rapat */
        .table-absen thead th {
            font-size: 0.85rem; /* Font Header Kecil */
            padding: 0.5rem;
            vertical-align: middle;
        }
        .table-absen tbody td {
            font-size: 0.85rem; /* Font Body Kecil */
            padding: 0.4rem 0.5rem; /* Jarak atas-bawah diperkecil */
            vertical-align: middle;
        }
        /* Memperkecil tombol pilihan status */
        .btn-group-absen .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        /* Memperkecil input form text */
        .form-control-xs {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.785rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
    </style>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Input Absensi</h3>
                    <p class="text-subtitle text-muted">
                        Kelas: <b>{{ $jadwal->kelas->name }}</b> | Mapel: <b>{{ $jadwal->mapel->name }}</b>
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('absensi') }}">Absensi</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Input</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                @if ($sudah_absen)
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <b>Mode Edit:</b> Sedang mengubah data yang tersimpan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @else
                    <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <b>Belum Absensi:</b> Silakan ambil absensi hari ini.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header py-3">
                        <h4 class="card-title m-0" style="font-size: 1.1rem">Daftar Siswa</h4>
                    </div>
                    
                    <div class="card-body p-0"> {{-- p-0 agar tabel mepet ke pinggir card --}}
                        @if ($siswa->isEmpty())
                            <div class="alert alert-warning m-3">
                                <i class="bi bi-exclamation-triangle"></i> Belum ada data siswa di kelas ini.
                            </div>
                        @else
                            @if ($errors->any())
                                <div class="alert alert-danger m-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('absensi.simpan') }}" method="POST">
                                @csrf
                                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

                                <div class="table-responsive">
                                    {{-- TAMBAHKAN CLASS 'table-absen' DI SINI --}}
                                    <table class="table table-hover table-bordered mb-0 table-absen" id="table-siswa">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px; min-width: 40px;" class="text-center">No</th>
                                                <th style="min-width: 200px;">Nama Siswa</th>
                                                <th style="text-align: center; min-width: 180px;">Status</th>
                                                <th style="min-width: 150px;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($siswa as $item)
                                                @php
                                                    $data_absen = $absensi_hari_ini->get($item->id);
                                                    $status = $data_absen ? $data_absen->status : 'Hadir';
                                                    $ket = $data_absen ? $data_absen->ket : '';
                                                @endphp

                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="fw-bold">{{ $item->name }}</td>
                                                    <td class="text-center">
                                                        {{-- Gunakan class 'btn-group-absen' dan 'btn-sm' --}}
                                                        <div class="btn-group w-100 btn-group-absen" role="group">
                                                            
                                                            <input type="radio" class="btn-check" name="status[{{ $item->id }}][stat]" id="h_{{ $item->id }}" value="Hadir" {{ $status == 'Hadir' ? 'checked' : '' }} autocomplete="off">
                                                            <label class="btn btn-sm btn-outline-success" for="h_{{ $item->id }}">H</label>
                                                          
                                                            <input type="radio" class="btn-check" name="status[{{ $item->id }}][stat]" id="i_{{ $item->id }}" value="Izin" {{ $status == 'Izin' ? 'checked' : '' }} autocomplete="off">
                                                            <label class="btn btn-sm btn-outline-info" for="i_{{ $item->id }}">I</label>
                                                          
                                                            <input type="radio" class="btn-check" name="status[{{ $item->id }}][stat]" id="s_{{ $item->id }}" value="Sakit" {{ $status == 'Sakit' ? 'checked' : '' }} autocomplete="off">
                                                            <label class="btn btn-sm btn-outline-warning" for="s_{{ $item->id }}">S</label>

                                                            <input type="radio" class="btn-check" name="status[{{ $item->id }}][stat]" id="a_{{ $item->id }}" value="Alpha" {{ $status == 'Alpha' ? 'checked' : '' }} autocomplete="off">
                                                            <label class="btn btn-sm btn-outline-danger" for="a_{{ $item->id }}">A</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{-- Gunakan class 'form-control-xs' --}}
                                                        <input type="text" name="status[{{ $item->id }}][ket]"
                                                            class="form-control form-control-xs"
                                                            placeholder="Ket..."
                                                            value="{{ $ket }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-12 d-flex justify-content-end p-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save-fill"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection