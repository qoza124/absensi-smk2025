@extends('layout.master')

@section('title', 'My Profile - SI Absensi')

@section('content')
@push('css')
{{-- CSS Cropper.js --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<style>
    /* Style agar gambar di dalam modal tidak melebar */
    .img-container {
        max-height: 400px; /* Batasi tinggi area crop */
        width: 100%;
        overflow: hidden;
    }
    .img-container img {
        max-width: 100%;
    }
</style>
@endpush
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Profil Saya</h3>
                    <p class="text-subtitle text-muted">Kelola informasi akun dan kata sandi Anda.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profil</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            {{-- KOLOM KIRI: KARTU PROFIL --}}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <div class="avatar avatar-2xl mb-3">
                                {{-- Logika Menampilkan Gambar --}}
                                @if(Auth::user()->foto)
                                    <img src="{{ asset('storage/profil/' . Auth::user()->foto) }}" alt="Avatar" style="object-fit: cover; width: 100%; height: 100%;">
                                @else
                                    <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                @endif
                            </div>

                            <h3 class="mt-1">{{ Auth::user()->name }}</h3>
                            <p class="text-small">{{ Auth::user()->role }}</p>

                            {{-- FORM UPLOAD FOTO --}}
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalGantiFoto">
                                <i class="bi bi-camera"></i> Ganti Foto
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Info Tambahan (Read Only) --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Detail Akun</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="text-muted">Username</label>
                            <p class="fw-bold">{{ Auth::user()->username }}</p>
                        </div>
                        <div class="form-group">
                            <label class="text-muted">Role</label>
                            <p class="fw-bold"><span class="badge bg-primary">{{ Auth::user()->role }}</span></p>
                        </div>
                        <div class="form-group">
                            <label class="text-muted">Terdaftar Sejak</label>
                            <p class="fw-bold">{{ Auth::user()->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: FORM EDIT --}}
            <div class="col-12 col-lg-8">
                
                {{-- Form Ganti Identitas --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Informasi Pribadi</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('myprofil.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama Lengkap" value="{{ Auth::user()->name }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ Auth::user()->username }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Form Ganti Password --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Ganti Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('myprofil.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password lama">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Minimal 6 karakter">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            </div>

                            {{-- Fitur Lihat Password --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="showPassword">
                                <label class="form-check-label" for="showPassword">
                                    Tampilkan Password
                                </label>
                            </div>

                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-danger">Ubah Password</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    {{-- Modal Ganti Foto --}}
{{-- Modal Ganti Foto dengan Cropper --}}
<div class="modal fade" id="modalGantiFoto" tabindex="-1" aria-labelledby="modalFotoLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- Pakai modal-lg agar lebih luas --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFotoLabel">Ganti Foto Profil</h5>
                <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                {{-- Langkah 1: Pilih File --}}
                <div class="mb-3" id="step-upload">
                    <label for="inputImage" class="form-label">Pilih Foto (Max 2MB)</label>
                    <input class="form-control" type="file" id="inputImage" accept="image/*">
                </div>

                {{-- Langkah 2: Area Crop (Awalnya sembunyi) --}}
                <div class="img-container" id="crop-area" style="display: none;">
                    <img id="image-preview" src="" alt="Picture">
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal" data-bs-dismiss="modal">Batal</button>
                {{-- Tombol ini hanya muncul setelah pilih foto --}}
                <button type="button" class="btn btn-primary" id="btn-crop-save" style="display: none;">
                    <i class="bi bi-scissors"></i> Potong & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Form Tersembunyi untuk Mengirim Data ke Controller --}}
{{-- Kita memanipulasi upload via AJAX/JS, tapi mengirimnya via form biasa agar Controller tidak perlu diubah --}}
<form id="formUploadReal" action="{{ route('myprofil.foto') }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    @method('PUT')
    <input type="file" name="foto" id="foto_real">
</form>
@endsection

@push('script')
<script>
    // Script untuk Toggle Lihat Password
    document.getElementById('showPassword').addEventListener('change', function() {
        var currentPass = document.getElementById('current_password');
        var newPass = document.getElementById('new_password');
        var confirmPass = document.getElementById('new_password_confirmation');
        
        if (this.checked) {
            currentPass.type = 'text';
            newPass.type = 'text';
            confirmPass.type = 'text';
        } else {
            currentPass.type = 'password';
            newPass.type = 'password';
            confirmPass.type = 'password';
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var inputImage = document.getElementById('inputImage');
        var image = document.getElementById('image-preview');
        var cropArea = document.getElementById('crop-area');
        var stepUpload = document.getElementById('step-upload');
        var btnSave = document.getElementById('btn-crop-save');
        var formReal = document.getElementById('formUploadReal');
        var inputReal = document.getElementById('foto_real');
        var cropper;

        // 1. Saat user memilih file
        inputImage.addEventListener('change', function (e) {
            var files = e.target.files;
            var done = function (url) {
                inputImage.value = ''; // Reset input agar bisa pilih file yg sama jika gagal
                image.src = url;
                
                // Tampilkan area crop, sembunyikan input file awal
                stepUpload.style.display = 'none';
                cropArea.style.display = 'block';
                btnSave.style.display = 'block';
                
                // Inisialisasi Cropper
                if(cropper) { 
                    cropper.destroy(); 
                }
                cropper = new Cropper(image, {
                    aspectRatio: 1 / 1, // WAJIB: Agar hasil selalu persegi
                    viewMode: 1,        // Agar crop box tidak keluar dari gambar
                    minCropBoxWidth: 100,
                    minCropBoxHeight: 100,
                });
            };

            if (files && files.length > 0) {
                var file = files[0];
                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // 2. Saat tombol "Potong & Simpan" diklik
        btnSave.addEventListener('click', function () {
            // Ubah tombol jadi loading
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
            btnSave.disabled = true;

            if (cropper) {
                // Ambil hasil crop sebagai Blob (File object)
                cropper.getCroppedCanvas({
                    width: 500, // Resize hasil crop biar tidak terlalu besar (opsional)
                    height: 500,
                }).toBlob(function (blob) {
                    
                    // Buat File baru dari Blob hasil crop
                    var fileBaru = new File([blob], "avatar-cropped.jpg", { type: "image/jpeg" });

                    // Masukkan file ke input file tersembunyi menggunakan DataTransfer
                    // (Ini trik karena kita tidak bisa set value input file secara langsung)
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(fileBaru);
                    inputReal.files = dataTransfer.files;

                    // Submit form asli
                    formReal.submit();
                }, 'image/jpeg', 0.9); // Kualitas 90%
            }
        });

        // 3. Reset modal saat ditutup
        var closeButtons = document.querySelectorAll('.btn-close-modal');
        closeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Kembalikan tampilan modal ke awal
                stepUpload.style.display = 'block';
                cropArea.style.display = 'none';
                btnSave.style.display = 'none';
                if(cropper) { cropper.destroy(); cropper = null; }
                image.src = "";
            });
        });
    });
</script>
@endpush