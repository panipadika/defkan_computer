<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    protected $guarded = ['id_pesanan'];
    protected $appends = ['bukti_pembayaran_url'];

    public function getBuktiPembayaranUrlAttribute()
    {
        if ($this->bukti_pembayaran) {
            return asset('storage/' . $this->bukti_pembayaran);
        }
        return null;
    }

    // Relasi ke DetailPesanan
    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi ke LayananEkspedisi
    public function ekspedisi()
    {
        return $this->belongsTo(LayananEkspedisi::class, 'id_layanan_ekspedisi', 'id_layanan_ekspedisi');
    }

    // Relasi ke Pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
