<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder peran dan hak akses panel admin (tabel roles, permissions, role_has_permissions).
 *
 * Admin memegang seluruh hak akses, Staf hanya boleh melihat data.
 * Memakai firstOrCreate + syncPermissions sehingga aman dijalankan berulang kali.
 */
class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seluruh hak akses panel admin.
     */
    public const PERMISSIONS = [
        'lihat dashboard',
        'lihat proposal',
        'lihat tugas',
        'hapus data',
        'kelola pengguna',
    ];

    /**
     * Hak akses terbatas milik peran Staf (hanya membaca data).
     */
    private const PERMISSIONS_STAF = [
        'lihat dashboard',
        'lihat proposal',
        'lihat tugas',
    ];

    /**
     * Buat peran Admin dan Staf beserta hak aksesnya.
     */
    public function run(): void
    {
        // Cache hak akses Spatie dibersihkan agar perubahan langsung berlaku.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web'])
            ->syncPermissions(self::PERMISSIONS);

        Role::firstOrCreate(['name' => 'Staf', 'guard_name' => 'web'])
            ->syncPermissions(self::PERMISSIONS_STAF);
    }
}
