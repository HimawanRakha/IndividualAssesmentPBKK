@extends('adminlte::page')

@section('title', 'Proposal Agentic AI')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Proposal Agentic AI</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Proposal</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @include('admin.partials.status')

    <x-adminlte-card theme="primary" theme-mode="outline" body-class="p-0">
        <x-slot name="headerSlot">
            @include('admin.partials.search-form', [
                'action' => route('admin.proposal.index'),
                'placeholder' => 'Cari judul, tema, nama, atau NRP pemilik...',
                'filterName' => 'tema',
                'filterLabel' => 'Tema',
                'filterOptions' => $daftarTema->combine($daftarTema),
            ])
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px">#</th>
                        <th>Judul</th>
                        <th>Pemilik</th>
                        <th>Tema</th>
                        <th>Dibuat</th>
                        {{-- Kolom aksi hapus hanya dirender untuk Admin. --}}
                        @role('Admin')
                            <th class="text-end">Aksi</th>
                        @endrole
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td>{{ $projects->firstItem() + $loop->index }}</td>
                            <td>{{ Str::limit($project->judul, 70) }}</td>
                            <td>
                                {{ $project->user->name }}
                                @if ($project->user->nrp)
                                    <div class="small text-body-secondary">{{ $project->user->nrp }}</div>
                                @endif
                            </td>
                            <td><span class="badge text-bg-info">{{ $project->tema_agent }}</span></td>
                            <td class="text-nowrap">{{ $project->created_at->translatedFormat('d M Y') }}</td>
                            @role('Admin')
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.proposal.destroy', $project) }}"
                                        onsubmit="return confirm('Hapus proposal ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-nowrap">
                                            <i class="bi bi-trash" aria-hidden="true"></i> Hapus Data
                                        </button>
                                    </form>
                                </td>
                            @endrole
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-4">
                                Tidak ada proposal yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footerSlot">
            {{-- Teks "Menampilkan x sampai y dari z data" berasal dari view pagination + lang/id.json. --}}
            {{ $projects->links('pagination::bootstrap-5') }}
        </x-slot>
    </x-adminlte-card>
@stop
