@extends('adminlte::page')

@section('title', 'Pengguna & Peran')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Pengguna &amp; Peran</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @include('admin.partials.status')

    <x-adminlte-card theme="danger" theme-mode="outline" body-class="p-0">
        <x-slot name="headerSlot">
            @include('admin.partials.search-form', [
                'action' => route('admin.pengguna.index'),
                'placeholder' => 'Cari nama, email, atau NRP...',
                'filterName' => 'peran',
                'filterLabel' => 'Peran',
                'filterOptions' => $daftarPeran->combine($daftarPeran)->put('tanpa', 'Tanpa peran'),
            ])
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran Saat Ini</th>
                        <th class="text-end" style="width: 280px">Ubah Peran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                {{ $user->name }}
                                @if ($user->nrp)
                                    <div class="small text-body-secondary">{{ $user->nrp }}</div>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span @class([
                                        'badge',
                                        'text-bg-danger' => $role->name === 'Admin',
                                        'text-bg-warning' => $role->name !== 'Admin',
                                    ])>{{ $role->name }}</span>
                                @empty
                                    <span class="badge text-bg-light border">Tanpa peran</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.pengguna.update', $user) }}"
                                    class="d-flex gap-2 justify-content-end">
                                    @csrf
                                    @method('PATCH')
                                    <select name="peran" class="form-select form-select-sm" style="max-width: 150px"
                                        aria-label="Peran untuk {{ $user->name }}">
                                        <option value="">Tanpa peran</option>
                                        @foreach ($daftarPeran as $peran)
                                            <option value="{{ $peran }}" @selected($user->roles->contains('name', $peran))>
                                                {{ $peran }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-body-secondary py-4">
                                Tidak ada pengguna yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot name="footerSlot">
            {{-- Teks "Menampilkan x sampai y dari z data" berasal dari view pagination + lang/id.json. --}}
            {{ $users->links('pagination::bootstrap-5') }}
        </x-slot>
    </x-adminlte-card>
@stop
