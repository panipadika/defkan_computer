<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servis extends Model
{
    use HasFactory;

    protected $table = 'servis';

    protected $guarded = ['id'];

    protected $appends = [
        'bukti_pembayaran_url',
        'total_biaya_final',
        'status_pembayaran_label',
        'metode_pembayaran_label',
    ];

    protected $casts = [
        'estimasi_biaya' => 'integer',
        'total_biaya' => 'integer',
        'waktu_pembayaran' => 'datetime',
        'tanggal_masuk' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'id_pengguna');
    }

    // Relasi ke Ulasan (satu servis, satu ulasan per pengguna)
    public function ulasan()
    {
        return $this->hasOne(Ulasan::class, 'id_servis', 'id');
    }

    // Relasi ke Complaint (satu servis bisa punya satu complaint)
    public function complaint()
    {
        return $this->hasOne(Complaint::class, 'id_referensi')
            ->where('tipe', 'servis');
    }

    public function estimasi()
    {
        return $this->belongsTo(EstimasiServis::class, 'jenis_kerusakan', 'jenis_kerusakan');
    }

    public function getBuktiPembayaranUrlAttribute()
    {
        if (empty($this->bukti_pembayaran)) {
            return null;
        }

        if (str_starts_with($this->bukti_pembayaran, 'http')) {
            return $this->bukti_pembayaran;
        }

        return asset('storage/' . ltrim($this->bukti_pembayaran, '/'));
    }

    public function getTotalBiayaFinalAttribute()
    {
        return $this->total_biaya
            ?? $this->biaya_total
            ?? $this->biaya_akhir
            ?? $this->estimasi_biaya
            ?? 0;
    }

    public function getStatusPembayaranLabelAttribute()
    {
        $status = strtolower($this->status_pembayaran ?? 'pending');

        return match ($status) {
            'dibayar', 'paid', 'lunas', 'success', 'settlement', 'sukses' => 'Dibayar',
            'gagal', 'ditolak', 'failed' => 'Ditolak',
            default => 'Belum Dibayar',
        };
    }

    public function getMetodePembayaranLabelAttribute()
    {
        return $this->metode_pembayaran ?: 'Belum dipilih';
    }
}
