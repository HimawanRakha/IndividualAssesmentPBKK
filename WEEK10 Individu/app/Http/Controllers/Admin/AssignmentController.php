<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Tabel riwayat tugas kuliah di panel admin: pencarian, filter mata kuliah, dan hapus data.
 */
class AssignmentController extends Controller implements HasMiddleware
{
    /**
     * Middleware controller: Staf hanya boleh melihat, hapus khusus pemegang 'hapus data'.
     *
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat tugas', only: ['index']),
            new Middleware('permission:hapus data', only: ['destroy']),
        ];
    }

    /**
     * Tampilkan tabel riwayat tugas dengan filter kondisional when().
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'mata_kuliah' => ['nullable', 'string', 'max:100'],
        ]);

        $assignments = Assignment::query()
            ->with('user:id,name,nrp')
            ->when($filter['q'] ?? null, fn (Builder $query, string $q) => $query->cari($q))
            ->when(
                $filter['mata_kuliah'] ?? null,
                fn (Builder $query, string $mataKuliah) => $query->where('mata_kuliah', $mataKuliah),
            )
            ->latest('dikumpulkan_pada')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tugas.index', [
            'assignments' => $assignments,
            'daftarMataKuliah' => Assignment::distinct()->orderBy('mata_kuliah')->pluck('mata_kuliah'),
        ]);
    }

    /**
     * Hapus riwayat tugas. Selain middleware permission, peran Admin dicek ulang di server.
     */
    public function destroy(Request $request, Assignment $assignment): RedirectResponse
    {
        abort_unless($request->user()->hasRole('Admin'), 403, 'Hanya Admin yang boleh menghapus data.');

        $assignment->delete();

        return back()->with('status', "Tugas \"{$assignment->judul}\" berhasil dihapus.");
    }
}
