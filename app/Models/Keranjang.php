<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $table      = 'keranjang';
    protected $primaryKey = 'id'; // Sesuai dengan migration awal yang menggunakan $table->id()

    protected $fillable = [
        'pengguna_id',
        'id_produk',
        'jumlah',
    ];

    // ------------------------------------------------
    // Relasi
    // ------------------------------------------------

    /**
     * Item keranjang milik seorang pengguna.
     */
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'id_pengguna');
    }

    /**
     * Item keranjang terhubung ke satu produk.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
