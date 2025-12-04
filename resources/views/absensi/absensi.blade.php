@extends('layout.master')

@section('title', 'Jadwal Mengajar - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Absensi & Jadwal</h3>
                    <p class="text-subtitle text-muted">Kelola kehadiran harian dan kegiatan mengajar Anda.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Absensi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        
        {{-- === 1. KARTU ABSENSI HARIAN === --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-white mb-0">Absensi Kehadiran (Harian)</h5>
                        <small>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
                    </div>
                    <div class="card-body py-4 text-center">
                        
                        @if($absen_harian)
                            {{-- KONDISI SUDAH ABSEN (TAMPILKAN STATUS) --}}
                            <div class="alert alert-success d-inline-block px-5">
                                <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> {{ $absen_harian->status }}</h4>
                                <p class="mb-0">{{ $absen_harian->ket ?? 'Tercatat dalam sistem' }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($absen_harian->created_at)->format('H:i') }} WIB</small>
                            </div>
                            <div class="mt-3">
                                 <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalIzin">
                                    <i class="bi bi-pencil"></i> Ubah / Ralat
                                </button>
                            </div>
                        @else
                            {{-- KONDISI BELUM ABSEN --}}
                            
                            @if($jadwal->isEmpty())
                                {{-- JIKA TIDAK ADA JADWAL: Tampilkan Pesan Umum --}}
                                <p class="card-text mb-4">Anda tidak memiliki jadwal mengajar hari ini. Silakan lakukan absen datang atau ajukan izin.</p>
                            @else
                                {{-- JIKA ADA JADWAL: Tampilkan Pesan Fokus Mengajar --}}
                                <p class="card-text mb-4">Anda memiliki jadwal mengajar hari ini. Silakan lakukan absen per kelas di tabel bawah atau ajukan izin jika berhalangan hadir seharian.</p>
                            @endif
                            
                            <div class="d-flex justify-content-center gap-3">
                                
                                {{-- LOGIKA TAMPILAN TOMBOL ABSEN DATANG --}}
                                {{-- Hanya muncul jika TIDAK punya jadwal mengajar --}}
                                @if($jadwal->isEmpty())
                                    <form id="formAbsenHarian" action="{{ route('absensi.harian') }}" method="POST">
                                        @csrf
                                        <button type="button" class="btn btn-lg btn-primary" id="btnAbsenHarian">
                                            <i class="bi bi-geo-alt-fill"></i> Absen Datang
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Izin (Selalu Muncul) --}}
                                <button class="btn btn-lg btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalIzin">
                                    <i class="bi bi-envelope-paper"></i> Izin / Sakit
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- === 2. TABEL JADWAL MENGAJAR === --}}
        @if($jadwal->isNotEmpty())
            {{-- JIKA PUNYA JADWAL: Tampilkan Tabel --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Jadwal Mengajar Hari Ini</h4>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="table">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Jam</th>
                                    <th style="width: 15%; text-align: center;">Status</th>
                                    <th style="width: 20%; text-align: center;" data-orderable="false">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwal as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->mapel->name }}</td>
                                        <td>{{ $item->kelas->name }}</td>
                                        <td><span class="badge bg-light-primary text-primary">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</span></td>
                                        
                                        <td style="text-align: center;">
                                            @if ($sudah_absen_ids->contains($item->id))
                                                <span class="badge bg-success">Sudah Absen</span>
                                            @else
                                                <span class="badge bg-danger">Belum Absen</span>
                                            @endif
                                        </td>

                                        <td style="text-align: center;">
                                            @if ($sudah_absen_ids->contains($item->id))
                                                <a href="{{ route('absensi.ambil', ['jadwal_id' => $item->id]) }}"
                                                    class="btn btn-sm btn-warning text-white">
                                                    <i class="bi bi-pencil-square"></i> Ubah
                                                </a>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-sm btn-primary btn-mulai-absen"
                                                    data-url="{{ route('absensi.ambil', ['jadwal_id' => $item->id]) }}"
                                                    data-jadwal-id="{{ $item->id }}">
                                                    <i class="bi bi-geo-alt-fill"></i> Mulai Absen
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            {{-- JIKA TIDAK PUNYA JADWAL: Tampilkan Alert --}}
            <div class="alert alert-light-secondary color-secondary">
                <i class="bi bi-info-circle me-2"></i> Anda tidak memiliki jadwal mengajar pada hari ini.
            </div>
        @endif

    </section>

    {{-- Modal Form Izin (Sama seperti sebelumnya) --}}
    <div class="modal fade" id="modalIzin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Pengajuan Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('absensi.izin') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Status Kehadiran</label>
                            <select name="status" class="form-select" required>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Keterangan / Alasan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Berikan keterangan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable hanya jika tabel ada
            if ($('#table').length) {
                $('#table').DataTable({
                    "language": { "url": "{{ asset('assets/modules/datatables/id.json') }}" },
                    "columnDefs": [{ "type": "num", "targets": 0 }]
                });
            }

            // SCRIPT ABSEN HARIAN (Datang)
            $('#btnAbsenHarian').on('click', function(e) {
                e.preventDefault();
                let form = $('#formAbsenHarian');
                let validationUrl = "{{ route('absensi.cek_lokasi_harian') }}";
                let csrfToken = "{{ csrf_token() }}";

                Swal.fire({
                    title: 'Mendeteksi Lokasi...',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                if (!navigator.geolocation) {
                    Swal.fire("Gagal!", "Browser tidak mendukung GPS.", "error");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        $.ajax({
                            url: validationUrl,
                            type: 'POST',
                            data: {
                                _token: csrfToken,
                                lat: position.coords.latitude,
                                long: position.coords.longitude
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    form.submit(); 
                                });
                            },
                            error: function(jqXHR) {
                                let msg = jqXHR.responseJSON?.message || "Lokasi tidak valid.";
                                Swal.fire("Gagal!", msg, "error");
                            }
                        });
                    },
                    function(error) {
                        Swal.fire("Gagal!", "Pastikan GPS aktif.", "error");
                    }
                );
            });

            // SCRIPT ABSEN MENGAJAR (Per Jadwal)
            $('.btn-mulai-absen').on('click', function(e) {
                e.preventDefault();
                let targetUrl = $(this).data('url');
                let jadwalId = $(this).data('jadwal-id');
                let csrfToken = "{{ csrf_token() }}";
                let validationUrl = "{{ route('absensi.cek_lokasi') }}"; // Route cek lokasi jadwal

                Swal.fire({
                    title: 'Mendeteksi Lokasi...',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                if (!navigator.geolocation) {
                    Swal.fire("Gagal!", "Browser Anda tidak mendukung Geolocation.", "error");
                    return;
                }
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        $.ajax({
                            url: validationUrl,
                            type: 'POST',
                            data: {
                                _token: csrfToken,
                                lat: position.coords.latitude,
                                long: position.coords.longitude,
                                jadwal_id: jadwalId
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Lokasi Terverifikasi!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = targetUrl;
                                });
                            },
                            error: function(jqXHR) {
                                let msg = jqXHR.responseJSON?.message || "Gagal verifikasi lokasi.";
                                Swal.fire("Absensi Gagal!", msg, "error");
                            }
                        });
                    },
                    function(error) {
                        Swal.fire("Gagal!", "Pastikan GPS aktif.", "error");
                    }
                );
            });
        });
    </script>
@endpush