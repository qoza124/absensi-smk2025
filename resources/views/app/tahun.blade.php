@extends('layout.master')

@section('title', 'Data Tahun Ajaran - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Tahun Ajaran</h3>
                    <p class="text-subtitle text-muted">Manajemen tahun pelajaran sekolah.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tahun Ajaran</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Tahun Ajaran</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                    <i class="bi bi-plus-lg"></i> Tambah Tahun
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Tahun Pelajaran</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th style="width: 20%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        {{-- GANTI BAGIAN <tbody> PADA TABEL MENJADI SEPERTI INI --}}
                        <tbody>
                            @foreach ($tahun as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->tahun }}
                                        @if($item->is_active)
                                            <span class="badge bg-success ms-2">Aktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->mulai }}</td>
                                    <td>{{ $item->selesai }}</td>
                                    <td style="text-align: center;">
                                        @if(!$item->is_active)
                                            {{-- Tombol Aktifkan --}}
                                            <form action="{{ url('tahun/aktifkan') }}/{{ $item->id }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-sm btn-success" type="submit" title="Aktifkan Tahun Ini">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="#" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal"
                                            data-bs-target="#modaledit" data-id="{{ $item->id }}"
                                            data-name="{{ $item->tahun ?? $item->tahun }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('tahun') }}/{{ $item->id }}" method="POST"
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

    @include('app.modal.tambahtahun')
    {{-- Pastikan Anda memiliki modal edit tahun jika diperlukan, atau sesuaikan include ini --}}
    {{-- @include('app.modal.edittahun') --}}
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "language": { "url": "{{ asset('assets/modules/datatables/id.json') }}" },
                "columnDefs": [{ "type": "num", "targets": 0 }]
            });

            // Sesuaikan logika modal edit ini dengan struktur modal Anda yang sebenarnya
            $('#modaledit').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nama = button.data('name');
                var modal = $(this);

                // Perhatikan URL actionnya
                var actionUrl = '{{ url('tahun') }}/' + id;
                modal.find('#editForm').attr('action', actionUrl);
                // Sesuaikan ID input di dalam modal edit
                modal.find('#tahun').val(nama);
            });

            $('.btn-hapus').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Tahun Ajaran?',
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