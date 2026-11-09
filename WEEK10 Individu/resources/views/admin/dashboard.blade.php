@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Dashboard</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('portal') }}">Portal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <x-adminlte-callout theme="{{ auth()->user()->hasRole('Admin') ? 'primary' : 'warning' }}"
        title="Selamat datang, {{ auth()->user()->name }}">
        Anda login sebagai <strong>{{ $peranSaya }}</strong>.
        Hak akses:
        @foreach ($hakAksesSaya as $hak)
            <span class="badge text-bg-secondary">{{ $hak }}</span>
        @endforeach
    </x-adminlte-callout>

    {{-- Widget statistik: angka berasal dari kueri count() di controller. --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            @can('kelola pengguna')
                <x-adminlte-small-box title="{{ $statistik['mahasiswa'] }}" text="Mahasiswa Terdaftar"
                    icon="bi bi-mortarboard-fill" theme="primary"
                    url="{{ route('admin.pengguna.index') }}" url-text="Kelola pengguna" />
            @else
                <x-adminlte-small-box title="{{ $statistik['mahasiswa'] }}" text="Mahasiswa Terdaftar"
                    icon="bi bi-mortarboard-fill" theme="primary" />
            @endcan
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="{{ $statistik['proposal'] }}" text="Proposal Agentic AI"
                icon="bi bi-lightbulb-fill" theme="success"
                url="{{ route('admin.proposal.index') }}" url-text="Lihat proposal" />
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="{{ $statistik['tugas'] }}" text="Riwayat Tugas"
                icon="bi bi-journal-check" theme="warning"
                url="{{ route('admin.tugas.index') }}" url-text="Lihat tugas" />
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="{{ $statistik['obrolan'] }}" text="Sesi Obrolan AI"
                icon="bi bi-chat-dots-fill" theme="danger" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <x-adminlte-card title="Proposal Terbaru" icon="bi bi-clock-history" theme="primary" theme-mode="outline"
                body-class="p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Pemilik</th>
                                <th>Tema</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proposalTerbaru as $project)
                                <tr>
                                    <td>{{ Str::limit($project->judul, 55) }}</td>
                                    <td>{{ $project->user->name }}</td>
                                    <td><span class="badge text-bg-info">{{ $project->tema_agent }}</span></td>
                                    <td class="text-nowrap">{{ $project->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-body-secondary py-4">Belum ada proposal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        <div class="col-lg-5">
            <x-adminlte-card title="Proposal per Tema Agent" icon="bi bi-bar-chart" theme="success" theme-mode="outline">
                @foreach ($perTema as $tema)
                    @php($persen = $totalProposal > 0 ? round($tema->jumlah / $totalProposal * 100) : 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small">
                            <span>{{ $tema->tema_agent }}</span>
                            <span class="fw-semibold">{{ $tema->jumlah }} ({{ $persen }}%)</span>
                        </div>
                        <div class="progress" role="progressbar" aria-label="{{ $tema->tema_agent }}"
                            aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px">
                            <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                @endforeach
            </x-adminlte-card>

            <x-adminlte-info-box title="Rata-rata Nilai Tugas" text="{{ $rataRataNilai }}" icon="bi bi-award"
                icon-theme="warning" progress="{{ (int) $rataRataNilai }}" progress-theme="warning"
                description="Dari seluruh tugas yang sudah dinilai" />

            {{-- Ringkasan peran hanya dirender untuk Admin. --}}
            @role('Admin')
                <x-adminlte-card title="Pengguna per Peran (Spatie)" icon="bi bi-shield-lock" theme="danger"
                    theme-mode="outline">
                    <ul class="list-group list-group-flush">
                        @foreach ($perPeran as $role)
                            <li class="list-group-item d-flex justify-content-between px-0">
                                {{ $role->name }}
                                <span class="badge text-bg-danger rounded-pill">{{ $role->users_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-adminlte-card>
            @endrole
        </div>
    </div>
@stop
