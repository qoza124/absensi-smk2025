@extends('layout.master')

@section('title', 'Data Jadwal - SI Absensi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Jadwal Mengajar</h3>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Jadwal</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                    <i class="bi bi-calendar-plus"></i> Tambah Jadwal
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Tahun Ajaran</th>
                                <th style="width: 20%">Guru</th>
                                <th>Kelas</th>
                                <th>Mapel</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th style="width: 15%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->tahun->tahun }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->kelas->name }}</td>
                                    <td>{{ $item->mapel->name }}</td>
                                    <td>{{ $item->hari }}</td>
                                    <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                                    <td style="text-align: center;">
                                        <a href="#" class="btn btn-sm btn-warning text-white"
                                            data-bs-toggle="modal" data-bs-target="#modaledit" 
                                            data-id="{{ $item->id }}"
                                            data-tahun_id="{{ $item->tahun_id }}"
                                            data-users_id="{{ $item->users_id }}"
                                            data-kelas_id="{{ $item->kelas_id }}"
                                            data-mapel_id="{{ $item->mapel_id }}"
                                            data-hari="{{ $item->hari }}"
                                            data-jam_mulai="{{ $item->jam_mulai }}"
                                            data-jam_selesai="{{ $item->jam_selesai }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('jadwal') }}/{{ $item->id }}" method="POST" style="display: inline;">
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

    @include('app.modal.tambahjadwal')
    @include('app.modal.editjadwal')
@endsection

@push('script')
<script>
$(document).ready(function () {
    $('#table').DataTable({
        "language": { "url": "{{ asset('assets/modules/datatables/id.json') }}" },
        "columnDefs": [{ "type": "num", "targets": 0 }]
    });

    // Helper: Update opsi jam selesai
    function updateJamSelesaiOptions(modal, selectedJamMulai) {
        var jamSelesaiSelect = modal.find('#jam_selesai');
        var mulai = parseInt(selectedJamMulai, 10);
        jamSelesaiSelect.prop('disabled', false);

        jamSelesaiSelect.find('option').each(function() {
            var $option = $(this);
            var optionValue = parseInt($option.val(), 10);
            if (isNaN(optionValue)) return;

            if (optionValue < mulai) {
                $option.prop('disabled', true).hide();
            } else {
                $option.prop('disabled', false).show();
            }
        });
    }

    $('#modaledit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        // Ambil data
        var id = button.data('id');
        var tahun_id = button.data('tahun_id');
        var users_id = button.data('users_id');
        var kelas_id = button.data('kelas_id');
        var mapel_id = button.data('mapel_id');
        var hari = button.data('hari');
        var jam_mulai = button.data('jam_mulai');
        var jam_selesai = button.data('jam_selesai');

        // Set action & value
        modal.find('#editForm').attr('action', '{{ url('jadwal') }}/' + id);
        modal.find('#tahun_id').val(tahun_id);
        modal.find('#users_id').val(users_id);
        modal.find('#kelas_id').val(kelas_id);
        modal.find('#mapel_id').val(mapel_id);
        modal.find('#hari').val(hari);

        // Logika Jam
        modal.find('#jam_mulai').val(jam_mulai);
        updateJamSelesaiOptions(modal, jam_mulai);
        modal.find('#jam_selesai').val(jam_selesai);
    });

    $('#modaledit').on('change', '#jam_mulai', function() {
        var modal = $(this).closest('.modal');
        var selectedJamMulai = $(this).val();
        updateJamSelesaiOptions(modal, selectedJamMulai);
        modal.find('#jam_selesai').val(selectedJamMulai);
    });

    $('.btn-hapus').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Jadwal?',
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