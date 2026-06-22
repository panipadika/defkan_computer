<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Complaint
 *
 * @property int    $id
 * @property int    $pengguna_id
 * @property string $tipe           'pesanan' | 'servis'
 * @property int    $id_referensi   id_pesanan atau id servis
 * @property string $judul
 * @property string $deskripsi
 * @property array|null $foto_bukti
 * @property string $status         'menunggu' | 'diproses' | 'selesai' | 'ditolak'
 * @property string|null $respons_admin
 * @property \Carbon\Carbon|null $respons_at
 */
class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaint';

    protected $fillable = [
        'pengguna_id',
        'tipe',
        'id_referensi',
        'judul',
        'deskripsi',
        'foto_bukti',
        'status',
        'respons_admin',
        'respons_at',
    ];

    protected $casts = [
        'foto_bukti' => 'array',
        'respons_at' => 'datetime',
    ];

    protected $appends = ['foto_bukti_urls', 'status_label', 'referensi_info'];

    // Warna badge untuk setiap status
    const STATUS_COLORS = [
        'menunggu' => 'warning',
        'diproses' => 'info',
        'selesai'  => 'success',
        'ditolak'  => 'danger',
    ];

    // ------------------------------------------------
    // Relasi
    // ------------------------------------------------

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'id_pengguna');
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

    /**
     * Label status yang lebih ramah pengguna.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Respon',
            'diproses' => 'Sedang Diproses',
            'selesai'  => 'Selesai',
            'ditolak'  => 'Ditolak',
            default    => ucfirst($this->status),
        };
    }

    /**
     * Info referensi (judul pesanan/servis).
     */
    public function getReferensiInfoAttribute(): array
    {
        if ($this->tipe === 'pesanan') {
            $pesanan = Pesanan::find($this->id_referensi);
            return [
                'label' => 'Pesanan #' . $this->id_referensi,
                'status' => $pesanan?->status ?? '-',
            ];
        }

        if ($this->tipe === 'servis') {
            $servis = Servis::find($this->id_referensi);
            return [
                'label' => 'Servis ' . ($servis?->kode_servis ?? '#' . $this->id_referensi),
                'status' => $servis?->status ?? '-',
            ];
        }

        return ['label' => '-', 'status' => '-'];
    }

    public function getFotoBuktiUrls(): array
    {
        return $this->foto_bukti_urls;
    }

    // ------------------------------------------------
    // Scopes
    // ------------------------------------------------

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeByTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }
}
