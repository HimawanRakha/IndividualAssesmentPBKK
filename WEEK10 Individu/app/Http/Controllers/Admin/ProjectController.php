<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Tabel proposal Agentic AI di panel admin: pencarian, filter tema, dan hapus data.
 */
class ProjectController extends Controller implements HasMiddleware
{
    /**
     * Middleware controller: Staf hanya boleh melihat, hapus khusus pemegang 'hapus data'.
     *
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat proposal', only: ['index']),
            new Middleware('permission:hapus data', only: ['destroy']),
        ];
    }

    /**
     * Tampilkan tabel proposal.
     *
     * Filter dipasang lewat when(), sehingga kondisi hanya ditambahkan bila
     * parameternya diisi. withQueryString() menjaga ?q= dan ?tema= tetap
     * terbawa di link pagination.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tema' => ['nullable', 'string', 'max:50'],
        ]);

        $projects = Project::query()
            ->with('user:id,name,nrp')
            ->when($filter['q'] ?? null, fn (Builder $query, string $q) => $query->cari($q))
            ->when($filter['tema'] ?? null, fn (Builder $query, string $tema) => $query->where('tema_agent', $tema))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.proposal.index', [
            'projects' => $projects,
            'daftarTema' => Project::distinct()->orderBy('tema_agent')->pluck('tema_agent'),
        ]);
    }

    /**
     * Hapus proposal. Selain middleware permission, peran Admin dicek ulang di server.
     */
    public function destroy(Request $request, Project $project): RedirectResponse
    {
        abort_unless($request->user()->hasRole('Admin'), 403, 'Hanya Admin yang boleh menghapus data.');

        $project->delete();

        return back()->with('status', "Proposal \"{$project->judul}\" berhasil dihapus.");
    }
}
