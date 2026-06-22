<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Produk
 *
 * @property int $id_produk
 * @property string $nama_produk
 * @property string|null $deskripsi
 * @property int $harga
 * @property int $stok
 * @property string|null $foto
 * @property string $kategori
 * @property string|null $ram
 * @property string|null $storage
 * @property string|null $vga
 * @property string|null $cpu
 * @property string|null $merek
 * @property array|null $galeri_foto
 * @property-read string|null $foto_url
 * @property-read array $galeri_foto_urls
 */
class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'produk';
    protected $primaryKey = 'id_produk';

    // Definisikan fillable secara eksplisit untuk keamanan (hindari mass assignment attack)
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'foto',
        'kategori',
        'ram',
        'storage',
        'vga',
        'cpu',
        'merek',
        'galeri_foto',
    ];

    protected $casts = [
        'galeri_foto' => 'array',
    ];

    // ------------------------------------------------
    // Relasi
    // ------------------------------------------------

    /**
     * Produk bisa masuk dalam banyak detail pesanan.
     */
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }

    /**
     * Produk bisa masuk dalam keranjang banyak pengguna.
     */
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'id_produk', 'id_produk');
    }

    // ------------------------------------------------
    // Accessor
    // ------------------------------------------------

    /**
     * Otomatis mengembalikan URL lengkap foto produk.
     * Jika tidak ada foto, kembalikan null.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    /**
     * Otomatis mengembalikan URL lengkap daftar foto galeri produk.
     */
    public function getGaleriFotoUrlsAttribute(): array
    {
        if ($this->galeri_foto && is_array($this->galeri_foto)) {
            return array_map(function ($path) {
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }
                return asset('storage/' . $path);
            }, $this->galeri_foto);
        }
        return [];
    }

    protected $appends = ['foto_url', 'galeri_foto_urls'];
}
