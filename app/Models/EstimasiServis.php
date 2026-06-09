<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimasiServis extends Model
{
    use HasFactory;

    protected $table      = 'estimasi_servis';
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $guarded    = ['id'];

    /**
     * Kolom-kolom tabel estimasi_servis:
     * - id_estimasi
     * - jenis_kerusakan
     * - harga_estimasi
     * - is_aktif
     */
}
