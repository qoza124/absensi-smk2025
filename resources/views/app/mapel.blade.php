@extends('layout.master')

@section('title', 'Data Mata Pelajaran - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Mata Pelajaran</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mapel</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Mapel</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                    <i class="bi bi-plus-lg"></i> Tambah Mapel
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Nama Mata Pelajaran</th>
                                <th style="width: 20%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mapel as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="text-align: center;">
                                        <a href="#" class="btn btn-sm btn-warning text-white"
                                            data-bs-toggle="modal" data-bs-target="#modaledit"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"> 
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('mapel') }}/{{ $item->id }}" method="POST" style="display: inline;">
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

    @include('app.modal.tambahmapel')
    @include('app.modal.editmapel')
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
        var modal = $(this);
        
        var actionUrl = '{{ url('mapel') }}/' + id;
        modal.find('#editForm').attr('action', actionUrl);
        modal.find('#name').val(nama);
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