<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeranjangController extends Controller
{
    /**
     * GET /api/keranjang
     * Lihat isi keranjang belanja milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $items = Keranjang::with('produk')
            ->where('pengguna_id', $pengguna->id_pengguna)
            ->get();

        $totalHarga = $items->sum(function ($item) {
            return $item->produk ? $item->produk->harga * $item->jumlah : 0;
        });

        return response()->json([
            'status'      => 'success',
            'message'     => 'Berhasil mengambil isi keranjang',
            'data'        => $items,
            'total_harga' => $totalHarga,
            'total_item'  => $items->count(),
        ], 200);
    }

    /**
     * POST /api/keranjang
     * Tambahkan produk ke keranjang.
     * Jika produk sudah ada di keranjang, jumlah akan ditambahkan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        // Cek apakah stok produk cukup
        $produk = Produk::find($request->id_produk);
        if ($produk->stok < $request->jumlah) {
            return response()->json([
                'status'  => 'error',
                'message' => "Stok produk '{$produk->nama_produk}' tidak mencukupi (Tersisa: {$produk->stok})",
            ], 400);
        }

        // Cek apakah produk sudah ada di keranjang → tambah jumlah
        $existing = Keranjang::where('pengguna_id', $pengguna->id_pengguna)
            ->where('id_produk', $request->id_produk)
            ->first();

        if ($existing) {
            $totalJumlah = $existing->jumlah + $request->jumlah;
            if ($produk->stok < $totalJumlah) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Stok tidak cukup. Anda sudah punya {$existing->jumlah} di keranjang, stok tersisa {$produk->stok}.",
                ], 400);
            }
            $existing->update(['jumlah' => $totalJumlah]);
            $item = $existing->fresh()->load('produk');
        } else {
            $item = Keranjang::create([
                'pengguna_id' => $pengguna->id_pengguna,
                'id_produk'   => $request->id_produk,
                'jumlah'      => $request->jumlah,
            ]);
            $item->load('produk');
        }

        return response()->json([
            'status'  => 'success',
            'message' => "'{$produk->nama_produk}' berhasil ditambahkan ke keranjang",
            'data'    => $item,
        ], 201);
    }

    /**
     * PATCH /api/keranjang/{id}
     * Ubah jumlah item di keranjang.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $item = Keranjang::where('id', $id)
            ->where('pengguna_id', $pengguna->id_pengguna)
            ->first();

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Item keranjang tidak ditemukan',
            ], 404);
        }

        // Cek stok
        $produk = $item->produk;

        if (!$produk) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk ini sudah tidak tersedia atau telah dihapus.',
            ], 400);
        }

        if ($produk->stok < $request->jumlah) {
            return response()->json([
                'status'  => 'error',
                'message' => "Stok produk '{$produk->nama_produk}' tidak mencukupi (Tersisa: {$produk->stok})",
            ], 400);
        }

        $item->update(['jumlah' => $request->jumlah]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Jumlah item berhasil diperbarui',
            'data'    => $item->fresh()->load('produk'),
        ], 200);
    }

    /**
     * DELETE /api/keranjang/{id}
     * Hapus satu item dari keranjang.
     */
    public function destroy(Request $request, $id)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $item = Keranjang::where('id', $id)
            ->where('pengguna_id', $pengguna->id_pengguna)
            ->first();

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Item keranjang tidak ditemukan',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Item berhasil dihapus dari keranjang',
        ], 200);
    }

    /**
     * DELETE /api/keranjang
     * Kosongkan seluruh keranjang belanja.
     */
    public function clear(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        Keranjang::where('pengguna_id', $pengguna->id_pengguna)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Keranjang belanja berhasil dikosongkan',
        ], 200);
    }
}
