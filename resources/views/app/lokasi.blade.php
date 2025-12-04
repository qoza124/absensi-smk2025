@extends('layout.master')

@section('title', 'Lokasi Sekolah - SI Absensi')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.5rem;
            z-index: 1; /* Agar tidak menutupi modal/dropdown */
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pengaturan Lokasi</h3>
                    <p class="text-subtitle text-muted">Atur koordinat dan radius absensi sekolah.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lokasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Peta & Koordinat</h4>
            </div>
            
            <form action="{{ route('lokasi.simpan') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="sekolah_lat" class="form-label">Latitude</label>
                                <input type="text" id="sekolah_lat" name="sekolah_lat"
                                    class="form-control @error('sekolah_lat') is-invalid @enderror"
                                    value="{{ old('sekolah_lat', $sekolah_lat) }}" required>
                                @error('sekolah_lat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="sekolah_long" class="form-label">Longitude</label>
                                <input type="text" id="sekolah_long" name="sekolah_long"
                                    class="form-control @error('sekolah_long') is-invalid @enderror"
                                    value="{{ old('sekolah_long', $sekolah_long) }}" required>
                                @error('sekolah_long') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="sekolah_radius" class="form-label">Radius (meter)</label>
                                <input type="number" id="sekolah_radius" name="sekolah_radius"
                                    class="form-control @error('sekolah_radius') is-invalid @enderror"
                                    value="{{ old('sekolah_radius', $sekolah_radius) }}" required>
                                @error('sekolah_radius') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="alert alert-light-primary color-primary">
                                <i class="bi bi-info-circle"></i> Klik pada peta atau geser marker untuk menentukan lokasi sekolah.
                            </div>
                            <button class="btn btn-primary w-100 mt-2">
                                <i class="bi bi-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                        
                        <div class="col-md-8">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        $(document).ready(function () {
            let latInput = $('#sekolah_lat');
            let longInput = $('#sekolah_long');
            let radiusInput = $('#sekolah_radius');

            // Default fallback jika kosong (misal: Monas, Jakarta)
            let defaultLat = -6.175392;
            let defaultLong = 106.827153;
            
            let currentLat = parseFloat(latInput.val()) || defaultLat;
            let currentLong = parseFloat(longInput.val()) || defaultLong;
            let currentRadius = parseInt(radiusInput.val()) || 100;

            let map = L.map('map').setView([currentLat, currentLong], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '' }).addTo(map);

            let marker = L.marker([currentLat, currentLong], { draggable: true }).addTo(map);
            
            let circle = L.circle([currentLat, currentLong], {
                color: '#435ebe', // Warna primary Mazer
                fillColor: '#435ebe',
                fillOpacity: 0.2,
                radius: currentRadius
            }).addTo(map);

            function updateUI(lat, lng) {
                latInput.val(lat.toFixed(6));
                longInput.val(lng.toFixed(6));
                marker.setLatLng([lat, lng]);
                circle.setLatLng([lat, lng]);
            }

            marker.on('dragend', function (e) {
                let pos = e.target.getLatLng();
                updateUI(pos.lat, pos.lng);
            });

            map.on('click', function (e) {
                updateUI(e.latlng.lat, e.latlng.lng);
            });

            radiusInput.on('input', function () {
                let val = parseInt($(this).val());
                if (val > 0) circle.setRadius(val);
            });

            // Fix peta tidak render sempurna saat load
            setTimeout(function () { map.invalidateSize(); }, 500);
        });
    </script>
@endpush