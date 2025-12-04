{{-- Tambahkan Style ini di bagian paling atas file sidebar.blade.php --}}
<style>
    /* Paksa Sidebar agar bisa discroll secara vertikal */
    .sidebar-wrapper {
        overflow-y: auto !important; /* Mengaktifkan scroll */
        height: 100vh !important;    /* Memastikan tinggi memenuhi layar */
        padding-bottom: 50px;        /* Memberi ruang di bawah agar menu terakhir tidak terpotong */
    }

    /* Opsional: Menyembunyikan tampilan scrollbar fisik agar lebih rapi (tetap bisa discroll) */
    .sidebar-wrapper::-webkit-scrollbar {
        width: 0px;
        background: transparent;
    }
</style>

<div id="sidebar">
  <div class="sidebar-wrapper active">
    {{-- ... sisa kode sidebar Anda ... --}}
    <div class="sidebar-header position-relative">
      <div class="d-flex justify-content-between align-items-center">
        <div class="logo">
          {{-- Logo Aplikasi --}}
          <a href="{{ url('/') }}"><span class="fs-4">SI Absensi</span></a>
        </div>

        {{-- === MULAI KODE SWITCH DARK MODE === --}}
        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
            role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet"
            viewBox="0 0 21 21">
            <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path
                d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                opacity=".3"></path>
              <g transform="translate(-210 -1)">
                <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                <circle cx="220.5" cy="11.5" r="4"></circle>
                <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
              </g>
            </g>
          </svg>
          <div class="form-check form-switch fs-6">
            <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
            <label class="form-check-label"></label>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
            role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
            viewBox="0 0 24 24">
            <path fill="currentColor"
              d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
            </path>
          </svg>
        </div>
        {{-- === AKHIR KODE SWITCH DARK MODE === --}}

        <div class="sidebar-toggler x">
          <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
        </div>
      </div>
    </div>

    {{-- Menu sidebar lainnya tetap sama... --}}
    <div class="sidebar-menu">
      <ul class="menu">
        <li class="sidebar-title"><b>Menu</b></li>

        {{-- ================= ADMIN ================= --}}
        @if (auth()->user()->role == "Admin")
          <li class="sidebar-item {{ Request::is('dashboard*') ? 'active' : '' }}">
            <a href="{{url('/')}}" class='sidebar-link'>
              <i class="bi bi-grid-fill"></i> <span>Dashboard</span>
            </a>
          </li>

          <li class="sidebar-title"><b>Manajemen Data</b></li>

          <li class="sidebar-item {{ Request::is('kelas*') ? 'active' : '' }}">
            <a href="{{url('/kelas')}}" class='sidebar-link'>
              <i class="fas fa-chalkboard-teacher"></i> <span>Data Kelas</span>
            </a>
          </li>
          <li class="sidebar-item {{ Request::is('mapel*') ? 'active' : '' }}">
            <a href="{{url('/mapel')}}" class='sidebar-link'>
              <i class="fas fa-book-open"></i> <span>Data Mapel</span>
            </a>
          </li>
          <li class="sidebar-item {{ Request::is('siswa*') ? 'active' : '' }}">
            <a href="{{url('/siswa')}}" class='sidebar-link'>
              <i class="fas fa-users"></i> <span>Data Siswa</span>
            </a>
          </li>
          <li class="sidebar-item {{ Request::is('user*') ? 'active' : '' }}">
            <a href="{{url('/user')}}" class='sidebar-link'>
              <i class="fas fa-user-cog"></i> <span>Data Pengguna</span>
            </a>
          </li>
          <li class="sidebar-item {{ Request::is('tahun*') ? 'active' : '' }}">
            <a href="{{url('/tahun')}}" class='sidebar-link'>
              <i class="fas fa-calendar"></i> <span>Tahun Ajaran</span>
            </a>
          </li>

          <li class="sidebar-title"><b>Manajemen Absensi</b></li>

          <li class="sidebar-item {{ Request::is('jadwal*') ? 'active' : '' }}">
            <a href="{{url('/jadwal')}}" class='sidebar-link'>
              <i class="fas fa-book-reader"></i> <span>Jadwal Mengajar</span>
            </a>
          </li>
          <li class="sidebar-item {{ Request::is('lokasi*') ? 'active' : '' }}">
            <a href="{{url('/lokasi')}}" class='sidebar-link'>
              <i class="fas fa-map-marked-alt"></i> <span>Pengaturan Lokasi</span>
            </a>
          </li>
          <li class="sidebar-item {{ Request::is('rekap-absensi-guru*') ? 'active' : '' }}">
            <a href="{{url('/rekap-absensi-guru')}}" class='sidebar-link'>
              <i class="fas fa-server"></i> <span>Rekap Guru</span>
            </a>
          </li>
          <li class="sidebar-item has-sub {{ Request::is('rekap-absensi-siswa*') ? 'active' : '' }}">
            <a href="#" class='sidebar-link'>
              <i class="fas fa-users-cog"></i> <span>Rekap Siswa</span>
            </a>
            <ul class="submenu {{ Request::is('rekap-absensi-siswa*') ? 'active' : '' }}">


              <li class="submenu-item {{ Request::routeIs('rekap.siswa.harian') ? 'active' : '' }}">
                <a href="{{ route('rekap.siswa.harian') }}" class="submenu-link">Harian</a> {{-- Tambahkan class
                submenu-link --}}
              </li>
              <li class="submenu-item {{ Request::routeIs('rekap.siswa.mingguan') ? 'active' : '' }}">
                <a href="{{ route('rekap.siswa.mingguan') }}" class="submenu-link">Mingguan</a> {{-- Tambahkan class
                submenu-link --}}
              </li>
              <li class="submenu-item {{ Request::routeIs('rekap.siswa.index') ? 'active' : '' }}">
                <a href="{{ route('rekap.siswa.index') }}" class="submenu-link">Bulanan</a> {{-- Tambahkan class
                submenu-link --}}
              </li>
            </ul>
          </li>

          {{-- ================= WALI KELAS ================= --}}
        @elseif (auth()->user()->role == "Wali Kelas")
          <li class="sidebar-item {{ Request::is('absensi*') ? 'active' : '' }}">
            <a href="{{url('/absensi')}}" class='sidebar-link'>
              <i class="fas fa-calendar-check"></i> <span>Absensi</span>
            </a>
          </li>
          <li class="sidebar-title"><b>Manajemen Guru Mapel</b></li>
          <li class="sidebar-item {{ Request::is('rekap-mapel*') ? 'active' : '' }}">
            <a href="{{ route('rekap.mapel.index') }}" class='sidebar-link'>
              <i class="fas fa-clipboard-list"></i> <span>Rekap Mapel Saya</span>
            </a>
          </li>
          <li class="sidebar-title"><b>Manajemen Wali Kelas</b></li>
          <li class="sidebar-item {{ Request::is('siswa*') ? 'active' : '' }}">
            <a href="{{url('/siswa')}}" class='sidebar-link'>
              <i class="fas fa-users"></i> <span>Data Siswa</span>
            </a>
          </li>
          <li class="sidebar-item has-sub {{ Request::is('rekap-absensi-siswa*') ? 'active' : '' }}">
            <a href="#" class='sidebar-link'>
              <i class="fas fa-users-cog"></i> <span>Rekap Siswa</span>
            </a>
            <ul class="submenu {{ Request::is('rekap-absensi-siswa*') ? 'active' : '' }}">
              <li class="submenu-item {{ Request::routeIs('rekap.siswa.harian') ? 'active' : '' }}">
                <a href="{{ route('rekap.siswa.harian') }}">Harian</a>
              </li>
              <li class="submenu-item {{ Request::routeIs('rekap.siswa.mingguan') ? 'active' : '' }}">
                <a href="{{ route('rekap.siswa.mingguan') }}">Mingguan</a>
              </li>
              <li class="submenu-item {{ Request::routeIs('rekap.siswa.index') ? 'active' : '' }}">
                <a href="{{ route('rekap.siswa.index') }}">Bulanan</a>
              </li>
            </ul>
          </li>

          {{-- ================= GURU / KESISWAAN ================= --}}
        @elseif (auth()->user()->role == "Guru" || auth()->user()->role == "Kesiswaan")
          {{-- Tambahkan menu sesuai kebutuhan di sini --}}
          <li class="sidebar-item">
            <a href="{{url('/absensi')}}" class='sidebar-link'>
              <i class="fas fa-calendar-check"></i> <span>Absensi</span>
            </a>
          </li>
          <li class="sidebar-title">Manajemen Guru Mapel</li>
          <li class="sidebar-item {{ Request::is('rekap-mapel*') ? 'active' : '' }}">
            <a href="{{ route('rekap.mapel.index') }}" class='sidebar-link'>
              <i class="fas fa-clipboard-list"></i> <span>Rekap Mapel Saya</span>
            </a>
          </li>
        @endif


      </ul>
    </div>
  </div>
</div>