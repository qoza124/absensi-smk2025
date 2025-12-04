@extends('layout.master')

@section('title', 'Rekap Absensi Mapel')

@section('content')
<style>
    .table-rekap th, .table-rekap td { text-align: center; vertical-align: middle; font-size: 0.8rem; padding: 4px; }
    .th-nama { 
        min-width: 200px; text-align: left !important; 
        position: sticky; left: 0; background: #fff; z-index: 5; border-right: 2px solid #eee; 
    }
    .weekend { background-color: #ffebeb !important; color: #d8000c; }
    .bg-h { background-color: #d1e7dd; color: #0f5132; font-weight: bold; }
    .bg-i { background-color: #cff4fc; color: #055160; font-weight: bold; }
    .bg-s { background-color: #fff3cd; color: #664d03; font-weight: bold; }
    .bg-a { background-color: #f8d7da; color: #842029; font-weight: bold; }
</style>

<div class="page-heading">
    <h3>Rekap Absensi Per Mata Pelajaran</h3>
    <p class="text-subtitle text-muted">Laporan kehadiran siswa khusus mata pelajaran Anda.</p>
</div>

<section class="section">
    {{-- Filter Card --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('rekap.mapel.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Pilih Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pilih Mapel</label>
                    <select name="mapel_id" class="form-select" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($listMapel as $m)
                            <option value="{{ $m->id }}" {{ $selectedMapelId == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <input type="month" name="bulan" class="form-control" value="{{ $selectedMonthYear }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                    @if($selectedKelasId && $selectedMapelId)
                        <button type="button" class="btn btn-success btn-print-mapel"
                            data-url="{{ route('rekap.mapel.cetak') }}?kelas_id={{ $selectedKelasId }}&mapel_id={{ $selectedMapelId }}&bulan={{ $selectedMonthYear }}">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($selectedKelasId && $selectedMapelId)
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="card-title">Periode: {{ $date->translatedFormat('F Y') }}</h5>
            <div class="small">
                <span class="badge bg-success">H: Hadir</span>
                <span class="badge bg-info">I: Izin</span>
                <span class="badge bg-warning">S: Sakit</span>
                <span class="badge bg-danger">A: Alpha</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-rekap mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2" class="th-nama">Nama Siswa</th>
                            <th colspan="{{ $dates->count() }}">Tanggal Pertemuan</th>
                            <th colspan="4">Total</th>
                        </tr>
                        <tr>
                            @foreach($dates as $dt)
                                <th class="{{ ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '' }}">
                                    {{ $dt->format('j') }}
                                </th>
                            @endforeach
                            <th>H</th><th>I</th><th>S</th><th>A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="th-nama">{{ $siswa->name }}</td>
                                @foreach($dates as $dt)
                                    @php
                                        $day = $dt->format('j');
                                        $st = $rekapData[$siswa->id][$day] ?? '';
                                        
                                        $cls = '';
                                        if($st == 'H') $cls = 'bg-h';
                                        elseif($st == 'I') $cls = 'bg-i';
                                        elseif($st == 'S') $cls = 'bg-s';
                                        elseif($st == 'A') $cls = 'bg-a';
                                        
                                        $isW = ($dt->isSaturday() || $dt->isSunday()) ? 'weekend' : '';
                                    @endphp
                                    <td class="{{ $isW }} {{ $cls }}">{{ $st }}</td>
                                @endforeach
                                <td>{{ $summaryData[$siswa->id]['H'] }}</td>
                                <td>{{ $summaryData[$siswa->id]['I'] }}</td>
                                <td>{{ $summaryData[$siswa->id]['S'] }}</td>
                                <td>{{ $summaryData[$siswa->id]['A'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="40">Tidak ada data siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</section>
@endsection

@push('script')
<script>
    document.addEventListener('click', function(e) {
        if(e.target && e.target.closest('.btn-print-mapel')) {
            var btn = e.target.closest('.btn-print-mapel');
            var url = btn.dataset.url;
            
            Swal.fire({
                title: 'Memproses Cetak...',
                timer: 1000, showConfirmButton: false, didOpen: () => { Swal.showLoading() }
            });

            var oldFrame = document.getElementById('printFrame');
            if (oldFrame) oldFrame.remove();

            var iframe = document.createElement('iframe');
            iframe.id = 'printFrame';
            iframe.src = url;
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try { iframe.contentWindow.focus(); iframe.contentWindow.print(); } catch(e) {}
            };
        }
    });
</script>
@endpush