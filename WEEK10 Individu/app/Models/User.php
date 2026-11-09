<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Akun pengguna portal: mahasiswa (@student.its.ac.id) atau dosen/penguji.
 *
 * Peran panel admin (Admin/Staf) beserta hak aksesnya disimpan di tabel Spatie Permission.
 */
#[Fillable(['name', 'email', 'password', 'nrp', 'program_studi', 'bio', 'minat', 'keahlian'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'minat' => 'array',
            'keahlian' => 'array',
        ];
    }

    /**
     * Apakah akun ini milik mahasiswa (berdomain email student ITS).
     */
    public function isMahasiswa(): bool
    {
        return str_ends_with($this->email, '@student.its.ac.id');
    }

    /**
     * Apakah akun ini boleh membuka panel admin (memegang peran Admin atau Staf).
     */
    public function bisaAksesPanelAdmin(): bool
    {
        return $this->hasAnyRole(['Admin', 'Staf']);
    }

    /**
     * Keterangan di menu user AdminLTE (nama method ditentukan paket AdminLTE).
     */
    public function adminlte_desc(): string
    {
        return 'Peran: '.($this->getRoleNames()->implode(', ') ?: 'tanpa peran');
    }

    /**
     * Apakah akun ini pemilik portofolio publik (lihat config/portfolio.php).
     */
    public function isPemilikPortofolio(): bool
    {
        return $this->email === config('portfolio.owner_email');
    }

    /**
     * Inisial nama (maks. 2 huruf) untuk avatar, mis. "PR".
     */
    public function inisial(): string
    {
        return collect(explode(' ', $this->name))
            ->take(2)
            ->map(fn (string $kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
            ->implode('');
    }

    /**
     * Proposal ide proyek Agentic AI milik user ini.
     *
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Riwayat tugas kuliah milik user ini.
     *
     * @return HasMany<Assignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Sesi obrolan user ini dengan agent AI.
     *
     * @return HasMany<ChatSession, $this>
     */
    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }
}
