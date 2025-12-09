@extends('layout.master')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Jadwal Pelajaran Per Kelas</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('jadwal.perkelas') }}" method="GET" class="form-inline mb-4">
                                <label for="kelas_select" class="mr-2">Pilih Kelas:</label>
                                <select name="kelas_id" id="kelas_select" class="form-control" onchange="this.form.submit()">
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}" {{ $selectedKelas && $k->id == $selectedKelas->id ? 'selected' : '' }}>
                                            {{ $k->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            
                            @if ($selectedKelas)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr class="bg-light">
                                                <th style="width: 120px;">Sesi</th>
                                                @foreach ($days as $day)
                                                    <th>{{ $day }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($standardTimeSlots as $slotNumber => $jamMulai)
                                                <tr>
                                                    <td class="align-middle font-weight-bold">
                                                        Jam ke-{{ $slotNumber }}
                                                    </td>
                                                    @foreach ($days as $day)
                                                        <td class="align-middle">
                                                            @php
                                                                $schedule = $scheduleForSelectedKelas[$day][$jamMulai] ?? null;
                                                            @endphp

                                                            @if ($schedule)
                                                                <div class="p-1">
                                                                    <span class="badge badge-primary" style="font-size: 1rem;">
                                                                        {{ $schedule->mapel->name ?? 'N/A' }}
                                                                    </span>
                                                                    <br>
                                                                    <small class="text-dark">{{ $schedule->user->name ?? '-' }}</small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection