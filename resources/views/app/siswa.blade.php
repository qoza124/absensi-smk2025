@extends('layout.master')

@section('title', 'Data Siswa - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Siswa</h3>
                    <p class="text-subtitle text-muted">Manajemen data siswa per kelas.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Siswa</li>
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
                    {{-- Filter Form (Kiri) --}}
                    <div>
                        @if (Auth::user()->role == 'Admin')
                        <form action="{{ url('siswa') }}" method="GET" class="d-flex align-items-center">
                            <label class="me-2 mb-0 fw-bold">Filter:</label>
                            <select name="kelas_id" class="form-select form-select-sm" style="min-width: 200px;" onchange="this.form.submit()">
                                <option value="">-- Tampilkan Semua Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}" {{ isset($selectedKelasId) && $selectedKelasId == $k->id ? 'selected' : '' }}>
                                        {{ $k->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @else
                           <h4 class="card-title">Daftar Siswa</h4>
                        @endif
                    </div>

                    {{-- Tombol Aksi (Kanan) --}}
                    <div>
                        @if (Auth::user()->role == 'Admin')
                            <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modal-import">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Import Excel
                            </button>
                        @endif
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                            <i class="bi bi-plus-lg"></i> Tambah Siswa
                        </button>
                    </div>
                </div>
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
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th style="width: 20%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siswa as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->kelas->name }}</td>
                                    <td style="text-align: center;">
                                        <a href="#" class="btn btn-sm btn-warning text-white"
                                            data-bs-toggle="modal" data-bs-target="#modaledit"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-kelas_id="{{ $item->kelas_id }}"> 
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('siswa') }}/{{ $item->id }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-danger btn-hapus" type="button">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('app.modal.tambahsiswa')
    @include('app.modal.editsiswa')
    @include('app.modal.importsiswa')
@endsection

@push('script')
<script>
$(document).ready(function() {
    $('#table').DataTable({
        "language": { "url": "{{ asset('assets/modules/datatables/id.json') }}" },
        "columnDefs": [{ "type": "num", "targets": 0 }]
    });
    
    $('#modaledit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var nama = button.data('name'); 
        var kelas_id = button.data('kelas_id'); 
        var modal = $(this);
        
        var actionUrl = '{{ url('siswa') }}/' + id;
        modal.find('#editForm').attr('action', actionUrl);
        modal.find('#name').val(nama);
        modal.find('#kelas_id').val(kelas_id);
    });

    $('.btn-hapus').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush