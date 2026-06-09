<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananEkspedisi extends Model
{
    use HasFactory;

    protected $table      = 'layanan_ekspedisi';
    protected $primaryKey = 'id_layanan_ekspedisi';

    protected $fillable = [
        'nama_layanan',
        'kode_layanan',
        'biaya_ongkir',
        'is_aktif',
    ];

    protected $casts = [
        'biaya_ongkir' => 'decimal:2',
        'is_aktif'     => 'boolean',
    ];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_layanan_ekspedisi', 'id_layanan_ekspedisi');
    }
}
