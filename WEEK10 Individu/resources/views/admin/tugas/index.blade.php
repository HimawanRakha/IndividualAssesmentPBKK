@extends('adminlte::page')

@section('title', 'Riwayat Tugas')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Riwayat Tugas</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tugas</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @include('admin.partials.status')

    <x-adminlte-card theme="warning" theme-mode="outline" body-class="p-0">
        <x-slot name="headerSlot">
            @include('admin.partials.search-form', [
                'action' => route('admin.tugas.index'),
                'placeholder' => 'Cari judul, mata kuliah, nama, atau NRP...',
                'filterName' => 'mata_kuliah',
                'filterLabel' => 'Mata Kuliah',
                'filterOptions' => $daftarMataKuliah->combine($daftarMataKuliah),
            ])
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px">#</th>
                        <th>Judul</th>
                        <th>Mata Kuliah</th>
                        <th>Mahasiswa</th>
                        <th class="text-center">Nilai</th>
                        <th>Dikumpulkan</th>
                        @role('Admin')
                            <th class="text-end">Aksi</th>
                        @endrole
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr>
                            <td>{{ $assignments->firstItem() + $loop->index }}</td>
                            <td>{{ Str::limit($assignment->judul, 60) }}</td>
                            <td>{{ $assignment->mata_kuliah }}</td>
                            <td>
                                {{ $assignment->user->name }}
                                @if ($assignment->user->nrp)
                                    <div class="small text-body-secondary">{{ $assignment->user->nrp }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($assignment->nilai === null)
                                    <span class="badge text-bg-secondary">Belum dinilai</span>
                                @else
                                    <span @class([
                                        'badge',
                                        'text-bg-success' => $assignment->nilai >= 85,
                                        'text-bg-primary' => $assignment->nilai >= 75 && $assignment->nilai < 85,
                                        'text-bg-warning' => $assignment->nilai < 75,
                                    ])>{{ $assignment->nilai }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $assignment->dikumpulkan_pada->translatedFormat('d M Y') }}</td>
                            @role('Admin')
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.tugas.destroy', $assignment) }}"
                                        onsubmit="return confirm('Hapus riwayat tugas ini secara permanen?')">
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
                            <td colspan="7" class="text-center text-body-secondary py-4">
                                Tidak ada tugas yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footerSlot">
            {{-- Teks "Menampilkan x sampai y dari z data" berasal dari view pagination + lang/id.json. --}}
            {{ $assignments->links('pagination::bootstrap-5') }}
        </x-slot>
    </x-adminlte-card>
@stop
