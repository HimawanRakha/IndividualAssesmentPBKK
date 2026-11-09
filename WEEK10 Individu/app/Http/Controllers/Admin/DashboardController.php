<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ChatSession;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Beranda panel admin (AdminLTE): widget statistik agregat dari kueri count().
 */
class DashboardController extends Controller implements HasMiddleware
{
    /**
     * Middleware controller: hanya pemegang hak 'lihat dashboard' yang boleh masuk.
     *
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat dashboard'),
        ];
    }

    /**
     * Tampilkan beranda panel admin.
     */
    public function index(Request $request): View
    {
        $totalProposal = Project::count();

        return view('admin.dashboard', [
            'statistik' => [
                'mahasiswa' => User::where('email', 'like', '%@student.its.ac.id')->count(),
                'proposal' => $totalProposal,
                'tugas' => Assignment::count(),
                'obrolan' => ChatSession::count(),
            ],
            'totalProposal' => $totalProposal,
            'perTema' => Project::select('tema_agent')
                ->selectRaw('count(*) as jumlah')
                ->groupBy('tema_agent')
                ->orderByDesc('jumlah')
                ->get(),
            'rataRataNilai' => round((float) Assignment::avg('nilai'), 1),
            'perPeran' => Role::withCount('users')->orderBy('name')->get(),
            'proposalTerbaru' => Project::with('user:id,name,nrp')
                ->latest()
                ->take(5)
                ->get(['id', 'user_id', 'judul', 'tema_agent', 'created_at']),
            'peranSaya' => $request->user()->getRoleNames()->implode(', '),
            'hakAksesSaya' => $request->user()->getAllPermissions()->pluck('name'),
        ]);
    }
}
