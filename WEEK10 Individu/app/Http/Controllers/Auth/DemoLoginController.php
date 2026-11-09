<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Tombol login instan akun uji untuk demo kelas (lihat config/demo.php).
 */
class DemoLoginController extends Controller
{
    /**
     * Login sebagai salah satu akun uji yang terdaftar.
     *
     * Ditolak (404) bila fitur dimatikan atau berjalan di production, dan
     * hanya kunci akun dari config yang diterima, bukan email bebas.
     */
    public function store(Request $request, string $akun): RedirectResponse
    {
        abort_unless(config('demo.enabled') && ! app()->isProduction(), 404);

        $email = config("demo.accounts.{$akun}.email");
        abort_if($email === null, 404);

        $user = User::where('email', $email)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->bisaAksesPanelAdmin() ? route('admin.dashboard') : route('dashboard'));
    }
}
