<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    {{-- CSS Mazer --}}
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">

    {{-- Font Awesome (Simpan ini agar icon lama tidak rusak) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- DataTables Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        /* Penyesuaian Custom */
        .fontawesome-icons .fas { margin-right: 10px; }
    </style>
    @stack('css')
</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        
        {{-- SIDEBAR --}}
        @include('layout.sidebar')

        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
                 @include('layout.navbar')
            </header>

            <div id="main-content">
                <div class="page-heading">
                    {{-- Judul Halaman bisa diletakkan disini atau di dalam content view --}}
                    {{-- <h3>@yield('title')</h3> --}}
                </div>

                <div class="page-content">
                    @yield('content')
                </div>

                @include('layout.footer')
            </div>
        </div>
    </div>

    {{-- JS Mazer --}}
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>

    {{-- JQuery (Wajib untuk DataTables & Script lama Anda) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    {{-- DataTables BS5 --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Global Script untuk Notifikasi --}}
    <script>
        // Definisi Toast SweetAlert2 (Gaya Mazer)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        // Menangani Session Flash Message (Sukses)
        @if(session('sukses'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('sukses') }}'
            })
        @endif

        // Menangani Session Flash Message (Gagal/Error)
        @if(session('gagal'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('gagal') }}'
            })
        @endif
        
        // Menangani Session Flash Message (Error dari Auth/Validasi standar Laravel)
        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            })
        @endif
    </script>

    @include('sweetalert::alert')
    @stack('script')
</body>

</html>