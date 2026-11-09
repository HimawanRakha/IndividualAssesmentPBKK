<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder akun uji panel admin: satu Admin (akses penuh) dan satu Staf (akses terbatas).
 *
 * Email akun diambil dari config/demo.php, sama dengan yang dipakai tombol auto-login.
 * Memakai updateOrCreate + syncRoles sehingga aman dijalankan berulang kali.
 */
class AdminPanelUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Buat atau perbarui akun Admin dan Staf.
     */
    public function run(): void
    {
        foreach (config('demo.accounts') as $akun) {
            if ($akun['role'] === null) {
                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['name'],
                    'program_studi' => 'Teknik Informatika',
                    'bio' => $akun['description'],
                    'password' => 'password', // Di-hash otomatis oleh cast 'hashed' di model User.
                ],
            );

            // email_verified_at tidak ada di $fillable, jadi diisi terpisah agar lolos middleware 'verified'.
            $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()])->save();

            // Peran disimpan di tabel model_has_roles, bukan di kolom users.
            $user->syncRoles([$akun['role']]);
        }
    }
}
