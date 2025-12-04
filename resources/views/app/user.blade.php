@extends('layout.master')

@section('title', 'Data Pengguna - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Pengguna</h3>
                    <p class="text-subtitle text-muted">Manajemen pengguna (Guru, Admin, dll).</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">User</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Pengguna</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                    <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Username</th>
                                <th>Nama Pengguna</th>
                                <th>Peran</th>
                                <th>Tanggal Dibuat</th>
                                <th style="width: 25%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td><span class="badge bg-info">{{ $item->role }}</span></td>
                                    <td>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                    <td style="text-align: center;">
                                        <a href="#" class="btn btn-sm btn-warning text-white"
                                            data-bs-toggle="modal" data-bs-target="#modaledit" 
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}" 
                                            data-username="{{ $item->username }}"
                                            data-role="{{ $item->role }}"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('user') }}/reset/{{ $item->id }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-secondary btn-reset" type="button" title="Reset Password">
                                                <i class="bi bi-key-fill"></i>
                                            </button>
                                        </form>

                                        <form action="{{ url('user') }}/{{ $item->id }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-danger btn-hapus" type="button" title="Hapus">
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

    @include('app.modal.tambahuser')
    @include('app.modal.edituser')
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
        var username = button.data('username');
        var role = button.data('role');
        var modal = $(this);
        var actionUrl = '{{ url('user') }}/' + id;

        modal.find('#editForm').attr('action', actionUrl);
        modal.find('#name').val(nama);
        modal.find('#username').val(username);
        modal.find('#role').val(role);
    });

    $('.btn-reset').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Reset Password?',
            text: 'Password akan dikembalikan ke default (123456*)!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('.btn-hapus').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Pengguna?',
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