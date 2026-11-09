<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Kelola peran pengguna (khusus Admin). Perubahan peran langsung tersimpan
 * ke tabel model_has_roles milik Spatie.
 */
class UserRoleController extends Controller implements HasMiddleware
{
    /**
     * Middleware controller: seluruh halaman hanya untuk pemegang 'kelola pengguna'.
     *
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kelola pengguna'),
        ];
    }

    /**
     * Tampilkan daftar pengguna beserta perannya.
     */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'peran' => ['nullable', 'string', Rule::in(['Admin', 'Staf', 'tanpa'])],
        ]);

        $users = User::query()
            ->with('roles:id,name')
            ->when($filter['q'] ?? null, fn (Builder $query, string $q) => $query->where(
                fn (Builder $sub) => $sub
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('nrp', 'like', "%{$q}%"),
            ))
            ->when($filter['peran'] ?? null, fn (Builder $query, string $peran) => $peran === 'tanpa'
                ? $query->doesntHave('roles')
                : $query->role($peran))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengguna.index', [
            'users' => $users,
            'daftarPeran' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Ganti peran seorang pengguna (atau cabut semua peran).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->hasRole('Admin'), 403, 'Hanya Admin yang boleh mengubah peran.');

        $data = $request->validate([
            'peran' => ['nullable', 'string', Rule::exists('roles', 'name')],
        ]);

        // Admin tidak boleh mencabut peran Admin miliknya sendiri agar panel tidak terkunci.
        if ($user->is($request->user()) && ($data['peran'] ?? null) !== 'Admin') {
            return back()->withErrors(['peran' => 'Anda tidak bisa mencabut peran Admin milik sendiri.']);
        }

        $user->syncRoles(array_filter([$data['peran'] ?? null]));

        return back()->with('status', "Peran {$user->name} diperbarui.");
    }
}
