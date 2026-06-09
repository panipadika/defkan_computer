<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    protected $guarded = ['id_detail'];

    // Relasi ke Produk (Gunakan withTrashed agar riwayat invoice tidak rusak walau produk dihapus)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk')->withTrashed();
    }
}
