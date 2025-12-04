{{-- ISI FILE: resources/views/app/modal/importsiswa.blade.php --}}

<div class="modal fade" tabindex="-1" role="dialog" id="modal-import">
    <div class="modal-dialog modal-lg" role="document"> {{-- Dibuat modal-lg agar lebih lebar --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Siswa (Multi-Kelas)</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Form diubah action-nya dan wajib ada enctype --}}
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="alert alert-info p-3">
                        <strong>Petunjuk Pengisian:</strong>
                        <ol class_mt-2>
                            <li>Unduh template file excel (CSV) menggunakan tombol di bawah.</li>
                            <li>Isi kolom <strong>nama_siswa</strong>.</li>
                            <li>Isi kolom <strong>kelas_id</strong> sesuai dengan ID Kelas pada tabel referensi di bawah.</li>
                            <li>Simpan file, lalu upload menggunakan form di bawah ini.</li>
                        </ol>
                        <a href="{{ route('siswa.template') }}" class="btn btn-sm btn-danger">
                            <i class="fa fa-file-excel"></i> Download Template Import
                        </a>
                    </div>
                    
                    <hr>

                    {{-- HANYA INPUT FILE, TIDAK ADA PILIHAN KELAS --}}
                    <div class="form-group">
                        <label>Upload File Excel (XLSX / CSV)</label>
                        <input type="file" name="file_excel" class="form-control" required>
                    </div>

                    <hr>

                    {{-- TABEL REFERENSI ID KELAS --}}
                    <h5><i class="fa fa-list-alt"></i> Referensi Daftar ID Kelas</h5>
                    <p>Gunakan 'ID Kelas' dari tabel di bawah ini untuk mengisi kolom `kelas_id` di file Excel Anda.</p>
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    
                                    <th>Nama Kelas</th>
                                    <th>ID Kelas</th>

                                    <th>Wali Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Kita gunakan variabel $kelas yang dikirim dari controller --}}
                                @forelse ($kelas as $k)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $k->name }}</td>

                                        <td><strong>{{ $k->id }}</strong></td>
                                        <td>{{ $k->user->name ?? '(Belum diatur)' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada data kelas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>