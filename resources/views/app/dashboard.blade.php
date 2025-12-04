@extends('layout.master')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-heading">
    <h3>Dashboard Statistik</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="row">
                
                {{-- BARIS 1: DATA MASTER --}}
                
                {{-- 1. Data Kelas --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon purple mb-2">
                                        <i class="fas fa-chalkboard-teacher text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Data Kelas</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countKelas }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Total Siswa --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Siswa</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countSiswa }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Total Guru --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2">
                                        <i class="fas fa-user-tie text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Guru</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countGuru }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Mata Pelajaran --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon red mb-2">
                                        <i class="fas fa-book text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Mapel</h6>
                                    <h6 class="font-extrabold mb-0">{{ $countMapel }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BARIS 2: STATISTIK ABSENSI HARI INI --}}

                {{-- 5. Guru Hadir --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon bg-success mb-2">
                                        <i class="fas fa-check-circle text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Guru Hadir</h6>
                                    <h6 class="font-extrabold mb-0">{{ $guruHadirToday }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6. Guru Belum Absen --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon bg-secondary mb-2">
                                        <i class="fas fa-user-clock text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Guru Belum</h6>
                                    <h6 class="font-extrabold mb-0">{{ $guruBelumAbsen }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 7. Siswa Hadir --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="fas fa-user-graduate text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Siswa Hadir</h6>
                                    <h6 class="font-extrabold mb-0">{{ $siswaHadirToday }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 8. Siswa Belum Absen --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon bg-danger mb-2">
                                        <i class="fas fa-user-slash text-white"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Siswa Belum</h6>
                                    <h6 class="font-extrabold mb-0">{{ $siswaBelumAbsen }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection