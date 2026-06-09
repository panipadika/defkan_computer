<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory;

    // Konstanta Role
    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    // Tabel pengguna tidak punya kolom updated_at
    const UPDATED_AT = null;

    protected $table      = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'google_id',
        'role',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'google_id',
    ];

    protected $casts = [
        'role' => 'string',
    ];

    // ------------------------------------------------
    // Helper Methods
    // ------------------------------------------------

    /**
     * Cek apakah pengguna ini adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // ------------------------------------------------
    // Relasi
    // ------------------------------------------------

    /**
     * Pengguna bisa memiliki banyak pesanan.
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Pengguna bisa mendaftarkan banyak servis.
     */
    public function servis()
    {
        return $this->hasMany(Servis::class, 'pengguna_id', 'id_pengguna');
    }

    /**
     * Pengguna bisa memiliki item di keranjang belanja.
     */
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'pengguna_id', 'id_pengguna');
    }
}
