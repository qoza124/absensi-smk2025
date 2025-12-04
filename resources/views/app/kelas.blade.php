@extends('layout.master')

@section('title', 'Data Kelas - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Kelas</h3>
                    <p class="text-subtitle text-muted">Daftar kelas yang terdaftar dalam sistem.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kelas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Tabel Kelas</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                    <i class="bi bi-plus-lg"></i> Tambah Kelas
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 20%">Nama Kelas</th>
                                <th style="width: 15%; text-align: center;">ID Kelas</th>
                                <th>Wali Kelas</th>
                                <th style="width: 20%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kelas as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="text-align: center;">{{ $item->id }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <a href="#" class="btn btn-sm btn-warning text-white"
                                            data-bs-toggle="modal" data-bs-target="#modaledit" 
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}" 
                                            data-users_id="{{ $item->users_id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('kelas') }}/{{ $item->id }}" method="POST"
                                            style="display: inline;">
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

    @include('app.modal.tambahkelas')
    @include('app.modal.editkelas')
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable
            $('#table').DataTable({
                "language": {
                    "url": "{{ asset('assets/modules/datatables/id.json') }}"
                },
                "columnDefs": [{ "type": "num", "targets": 0 }]
            });

            // Handle Modal Edit
            $('#modaledit').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nama = button.data('name');
                var users_id = button.data('users_id');
                var modal = $(this);

                var actionUrl = '{{ url('kelas') }}/' + id;
                modal.find('#editForm').attr('action', actionUrl);
                modal.find('#name').val(nama);
                modal.find('#users_id').val(users_id);
            });

            // Handle Submit Edit via AJAX (Sesuai kode lama Anda)
            $('#editForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        $('#modaledit').modal('hide');
                        Toast.fire({ icon: 'success', title: response.success });
                        setTimeout(function () { location.reload(); }, 1500);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                Toast.fire({ icon: 'error', title: value[0] });
                            });
                        } else {
                            Toast.fire({ icon: 'error', title: 'Terjadi kesalahan server.' });
                        }
                    }
                });
            });

            // Handle Hapus
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
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush