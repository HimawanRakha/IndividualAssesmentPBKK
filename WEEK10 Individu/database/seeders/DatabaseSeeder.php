<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Orkestrasi seeding: peran & hak akses Spatie, akun panel admin (Admin dan Staf),
 * 30 mahasiswa (pemilik portofolio + 29 acak dari Faker, masing-masing 2 project
 * dan 3 tugas), 1 akun demo penguji, lalu 2 sesi obrolan Agentic AI per user.
 *
 * Aman dijalankan berulang kali tanpa error duplikasi.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Peran dibuat lebih dulu agar akun panel admin bisa langsung diberi peran.
            RolePermissionSeeder::class,
            AdminPanelUserSeeder::class,
            // Pemilik portofolio dibuat lebih dulu agar ikut terhitung sebagai salah satu dari 30 mahasiswa.
            PortfolioOwnerSeeder::class,
            StudentSeeder::class,
            DemoUserSeeder::class,
            // Dijalankan terakhir agar semua user di atas ikut mendapat riwayat obrolan.
            ChatSeeder::class,
        ]);
    }
}
