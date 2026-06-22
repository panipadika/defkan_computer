<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ulasan
 *
 * @property int    $id
 * @property int    $pengguna_id
 * @property string $tipe          'produk' | 'servis'
 * @property int|null $id_pesanan
 * @property int|null $id_produk
 * @property int|null $id_servis
 * @property int    $rating        1-5
 * @property string|null $komentar
 * @property array|null  $foto_bukti
 * @property bool   $is_visible
 */
class Ulasan extends Model
{
    use HasFactory;

    protected $table = 'ulasan';

    protected $fillable = [
        'pengguna_id',
        'tipe',
        'id_pesanan',
        'id_produk',
        'id_servis',
        'rating',
        'komentar',
        'foto_bukti',
        'is_visible',
    ];

    protected $casts = [
        'foto_bukti' => 'array',
        'is_visible' => 'boolean',
        'rating'     => 'integer',
    ];

    protected $appends = ['foto_bukti_urls'];

    // ------------------------------------------------
    // Relasi
    // ------------------------------------------------

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'id_pengguna');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function servis()
    {
        return $this->belongsTo(Servis::class, 'id_servis', 'id');
    }

    // ------------------------------------------------
    // Accessors
    // ------------------------------------------------

    /**
     * Generate full URL untuk setiap foto bukti.
     */
    public function getFotoBuktiUrlsAttribute(): array
    {
        if (! $this->foto_bukti || ! is_array($this->foto_bukti)) {
            return [];
        }

        return array_map(function ($path) {
            if (str_starts_with($path, 'http')) {
                return $path;
            }
            return asset('storage/' . ltrim($path, '/'));
        }, $this->foto_bukti);
    }

    // ------------------------------------------------
    // Scopes
    // ------------------------------------------------

    /**
     * Hanya ulasan yang visible (belum dihapus admin).
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Filter by tipe: 'produk' atau 'servis'.
     */
    public function scopeByTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    /**
     * Ulasan publik: visible dan rating >= 4 (untuk ditampilkan di beranda).
     */
    public function scopePublik($query)
    {
        return $query->where('is_visible', true)->where('rating', '>=', 4);
    }
}
