@extends('layout.master')
@section('title', 'Jadwal Pelajaran - SI Absensi')
@section('content')
    {{-- STYLE TAMBAHAN UNTUK DARK MODE & KARTU --}}
    <style>
        /* Header Tabel & Kolom Jam (Light Mode) */
        .header-jadwal th { background-color: #f8f9fa; color: #000; vertical-align: middle; }
        .jam-cell { background-color: #f8f9fa; color: #000; font-weight: bold; vertical-align: middle; }

        /* Kartu Jadwal Base */
        .card-jadwal { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 1px solid transparent; }
        .card-jadwal:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }

        /* --- DARK MODE ADJUSTMENTS --- */
        [data-bs-theme="dark"] .header-jadwal th {
            background-color: #1e1e2d !important; color: #dee2e6 !important; border-color: #3f3f4e !important;
        }
        [data-bs-theme="dark"] .jam-cell {
            background-color: #1e1e2d !important; color: #dee2e6 !important; border-color: #3f3f4e !important;
        }
        [data-bs-theme="dark"] .table-bordered, 
        [data-bs-theme="dark"] .table-bordered th, 
        [data-bs-theme="dark"] .table-bordered td {
            border-color: #3f3f4e !important;
        }
        
        /* Penyesuaian Warna Kartu di Dark Mode agar tidak terlalu terang */
        [data-bs-theme="dark"] .bg-light-primary { background-color: rgba(67, 94, 190, 0.2) !important; color: #adc0ff !important; border-color: rgba(67, 94, 190, 0.4) !important; }
        [data-bs-theme="dark"] .bg-light-success { background-color: rgba(25, 135, 84, 0.2) !important; color: #75b798 !important; border-color: rgba(25, 135, 84, 0.4) !important; }
        [data-bs-theme="dark"] .bg-light-warning { background-color: rgba(255, 193, 7, 0.2) !important; color: #ffda6a !important; border-color: rgba(255, 193, 7, 0.4) !important; }
        [data-bs-theme="dark"] .bg-light-danger  { background-color: rgba(220, 53, 69, 0.2) !important; color: #ea868f !important; border-color: rgba(220, 53, 69, 0.4) !important; }
        [data-bs-theme="dark"] .bg-light-info    { background-color: rgba(13, 202, 240, 0.2) !important; color: #6edff6 !important; border-color: rgba(13, 202, 240, 0.4) !important; }
        [data-bs-theme="dark"] .bg-light-secondary{ background-color: rgba(108, 117, 125, 0.2) !important; color: #a7acb1 !important; border-color: rgba(108, 117, 125, 0.4) !important; }
        
        /* Warna Teks Guru di Dark Mode */
        [data-bs-theme="dark"] .text-guru { color: #dcdcdc !important; }
        .text-guru { color: #6c757d; } /* Default Light Mode */
    </style>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Jadwal Mengajar</h3>
                    <p class="text-subtitle text-muted">Jadwal pelajaran per kelas (Grid View).</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jadwal</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    {{-- Filter Kelas --}}
                    <form action="{{ url('jadwal') }}" method="GET" class="d-flex align-items-center">
                        <label for="kelas_select" class="me-2 mb-0 fw-bold">Pilih Kelas:</label>
                        <select name="kelas_id" id="kelas_select" class="form-select" onchange="this.form.submit()" style="min-width: 200px;">
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Tombol Tambah --}}
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                        <i class="bi bi-plus-lg"></i> Tambah Jadwal
                    </button>
                </div>
            </div>

            <div class="card-body">
                @if ($selectedKelas)
                    <div class="table-responsive">
                        <table class="table table-bordered text-center table-hover align-middle">
                            <thead class="header-jadwal">
                                <tr>
                                    <th style="width: 60px;">Jam</th>
                                    @foreach ($days as $day)
                                        <th style="min-width: 160px;">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($timeSlots as $jam)
                                    <tr>
                                        {{-- Kolom Nomor Jam --}}
                                        <td class="jam-cell">{{ $jam }}</td>
                                        
                                        @foreach ($days as $day)
                                            <td class="p-1">
                                                @php
                                                    // Ambil data jadwal di hari & jam ini
                                                    $jadwalItem = $scheduleMatrix[$day][$jam] ?? null;
                                                @endphp

                                                @if ($jadwalItem)
                                                    @php
                                                        // --- LOGIKA WARNA DINAMIS ---
                                                        // Array warna bootstrap/mazer
                                                        $colors = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
                                                        
                                                        // Pilih warna berdasarkan ID Mapel agar konsisten
                                                        $colorIndex = $jadwalItem->mapel_id % count($colors);
                                                        $theme = $colors[$colorIndex];
                                                        
                                                        $bgClass = 'bg-light-' . $theme;
                                                        $borderClass = 'border-' . $theme;
                                                        $textClass = 'text-' . $theme;
                                                    @endphp

                                                    <div class="card card-jadwal {{ $bgClass }} {{ $borderClass }} mb-0 shadow-sm h-100"
                                                         onclick="openActionModal(
                                                             {{ $jadwalItem->id }},
                                                             '{{ $jadwalItem->mapel->name }}',
                                                             '{{ $jadwalItem->user->name }}',
                                                             '{{ $jadwalItem->tahun_id }}',
                                                             '{{ $jadwalItem->users_id }}',
                                                             '{{ $jadwalItem->kelas_id }}',
                                                             '{{ $jadwalItem->mapel_id }}',
                                                             '{{ $jadwalItem->hari }}',
                                                             '{{ $jadwalItem->jam_mulai }}',
                                                             '{{ $jadwalItem->jam_selesai }}'
                                                         )"
                                                         title="Klik untuk Aksi">
                                                        
                                                        <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                            
                                                            {{-- NAMA MAPEL --}}
                                                            <div class="fw-bold {{ $textClass }}" style="font-size: 0.85rem; line-height: 1.2;">
                                                                {{ $jadwalItem->mapel->name }}
                                                            </div>
                                                            
                                                            {{-- NAMA GURU (Ditambahkan Ikon) --}}
                                                            <div class="text-guru mt-1 d-flex align-items-center justify-content-center gap-1" style="font-size: 0.75rem;">
                                                                 
                                                                <span class="text-truncate">
                                                                    {{ $jadwalItem->user->name }}
                                                                </span>
                                                            </div>

                                                            {{-- JAM --}}
                                                            <div class="mt-1">
                                                                <span class="badge bg-white {{ $textClass }} {{ $borderClass }} border" style="font-size: 0.65rem;">
                                                                    {{ $jadwalItem->jam_mulai }} - {{ $jadwalItem->jam_selesai }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- SLOT KOSONG --}}
                                                    <span class="text-muted small" style="opacity: 0.3;">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> Silakan pilih kelas terlebih dahulu untuk melihat jadwal.
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- MODAL AKSI --}}
    <div class="modal fade" id="modalAction" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary py-2">
                    <h6 class="modal-title text-white">Aksi Jadwal</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-4">
                    <h6 id="actionMapelName" class="fw-bold mb-1">Nama Mapel</h6>
                    <p class="text-muted small mb-4">
                        <i class="bi bi-person-fill"></i> <span id="actionGuruName">Nama Guru</span>
                    </p>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-warning text-white" id="btnTriggerEdit">
                            <i class="bi bi-pencil-square"></i> Edit Jadwal
                        </button>
                        
                        <form id="formDeleteAction" action="" method="POST" class="d-grid">
                            @csrf
                            @method('delete')
                            <button type="button" class="btn btn-danger btn-hapus">
                                <i class="bi bi-trash"></i> Hapus Jadwal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('app.modal.tambahjadwal')
    @include('app.modal.editjadwal')

@endsection

@push('script')
<script>
    let currentJadwalData = {};

    function openActionModal(id, mapelName, guruName, tahunId, usersId, kelasId, mapelId, hari, jamMulai, jamSelesai) {
        // Isi Modal Aksi
        $('#actionMapelName').text(mapelName);
        $('#actionGuruName').text(guruName);

        // Simpan data
        currentJadwalData = {
            id: id, tahun_id: tahunId, users_id: usersId, kelas_id: kelasId, 
            mapel_id: mapelId, hari: hari, jam_mulai: jamMulai, jam_selesai: jamSelesai
        };

        let deleteUrl = '{{ url('jadwal') }}/' + id;
        $('#formDeleteAction').attr('action', deleteUrl);
        $('#modalAction').modal('show');
    }

    $(document).ready(function() {
        // Edit Action
        $('#btnTriggerEdit').on('click', function() {
            $('#modalAction').modal('hide');
            let modalEdit = $('#modaledit');
            let urlEdit = '{{ url('jadwal') }}/' + currentJadwalData.id;
            
            // Populate form edit
            modalEdit.find('#editForm').attr('action', urlEdit);
            modalEdit.find('#tahun_id').val(currentJadwalData.tahun_id);
            modalEdit.find('#users_id').val(currentJadwalData.users_id);
            modalEdit.find('#kelas_id').val(currentJadwalData.kelas_id);
            modalEdit.find('#mapel_id').val(currentJadwalData.mapel_id);
            modalEdit.find('#hari').val(currentJadwalData.hari);
            
            // Trigger change untuk jam mulai agar jam selesai menyesuaikan
            modalEdit.find('#jam_mulai').val(currentJadwalData.jam_mulai).trigger('change');
            
            setTimeout(() => {
                modalEdit.find('#jam_selesai').val(currentJadwalData.jam_selesai);
            }, 100);

            modalEdit.modal('show');
        });

        // Delete Action
        $('.btn-hapus').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            $('#modalAction').modal('hide');
            
            Swal.fire({
                title: 'Hapus Jadwal?', 
                text: 'Data tidak dapat dikembalikan!', 
                icon: 'warning',
                showCancelButton: true, 
                confirmButtonColor: '#3085d6', 
                cancelButtonColor: '#d33', 
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });

        // Dynamic Jam Selesai Option
        $('#modaledit').on('change', '#jam_mulai', function() {
            var selectedMulai = parseInt($(this).val());
            var jamSelesaiSelect = $(this).closest('form').find('#jam_selesai');
            jamSelesaiSelect.empty();
            jamSelesaiSelect.append('<option value="" hidden>-- Pilih --</option>');
            if(selectedMulai) {
                for (var i = selectedMulai; i <= 10; i++) {
                     jamSelesaiSelect.append('<option value="' + i + '">' + i + '</option>');
                }
            }
        });
    });
</script>
@endpush