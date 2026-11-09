<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto-Login Akun Uji
    |--------------------------------------------------------------------------
    |
    | Tombol login instan di halaman login untuk demo kelas (Admin vs Staf).
    | Hanya aktif bila DEMO_LOGIN_ENABLED=true dan aplikasi tidak berjalan
    | di environment production. Server hanya menerima kunci akun yang
    | terdaftar di bawah, jadi tombol ini tidak bisa dipakai login sebagai
    | akun lain.
    |
    */

    'enabled' => (bool) env('DEMO_LOGIN_ENABLED', false),

    'accounts' => [
        'admin' => [
            'name' => 'Administrator Portal',
            'email' => 'admin@its.ac.id',
            'role' => 'Admin',
            'description' => 'Akses penuh: lihat, hapus data, dan kelola peran pengguna.',
        ],
        'staf' => [
            'name' => 'Staf Akademik',
            'email' => 'staf@its.ac.id',
            'role' => 'Staf',
            'description' => 'Akses terbatas: hanya melihat dashboard dan tabel data.',
        ],
        'mahasiswa' => [
            'name' => 'Pemilik Portofolio',
            'email' => env('PORTFOLIO_OWNER_EMAIL', '5025241055@student.its.ac.id'),
            'role' => null,
            'description' => 'Tanpa peran: panel admin menolak akses (403).',
        ],
    ],

];
