<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * GET /api/produk
     * Tampilkan semua produk (dengan pagination).
     */
    public function index(Request $request)
    {
        $query = Produk::query();

        // Filter berdasarkan kategori (opsional)
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan harga maksimal (opsional)
        if ($request->has('harga_max')) {
            $query->where('harga', '<=', $request->harga_max);
        }

        // Filter stok tersedia (opsional - backwards compatibility)
        if ($request->boolean('tersedia')) {
            $query->where('stok', '>', 0);
        }

        // Filter status stok (tersedia/habis) dari admin
        if ($request->has('stok_status')) {
            if ($request->stok_status === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->stok_status === 'habis') {
                $query->where('stok', '=', 0);
            }
        }

        // Search nama produk (opsional)
        if ($request->has('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Sort
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'nama_asc':
                    $query->orderBy('nama_produk', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama_produk', 'desc');
                    break;
                case 'terlama':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'harga_tertinggi':
                    $query->orderBy('harga', 'desc');
                    break;
                case 'harga_terendah':
                    $query->orderBy('harga', 'asc');
                    break;
                case 'terbaru':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 5);
        $produk = $query->paginate($perPage);

        $stats = [
            'total' => \App\Models\Produk::count(),
            'tersedia' => \App\Models\Produk::where('stok', '>', 0)->count(),
            'habis' => \App\Models\Produk::where('stok', '=', 0)->count(),
            'kategori' => \App\Models\Produk::distinct('kategori')->count('kategori'),
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil daftar produk',
            'data'    => $produk,
            'stats'   => $stats,
        ], 200);
    }

    /**
     * GET /api/produk/{id}
     * Tampilkan detail satu produk.
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil detail produk',
            'data'    => $produk,
        ], 200);
    }

    /**
     * POST /api/produk  (Admin only — support multipart/form-data untuk upload foto)
     * Tambah produk baru beserta foto.
     */
    public function storeWithUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'kategori'    => 'required|string|max:100',
            'merek'       => 'nullable|string|max:100',
            'ram'         => 'nullable|integer|min:0',
            'storage'     => 'nullable|integer|min:0',
            'vga'         => 'nullable|string|max:100',
            'cpu'         => 'nullable|string|max:200',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'galeri'      => 'nullable|array|max:10',
            'galeri.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $fotoPath = null;

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('produk', 'public');
        }

        $galeriPaths = [];
        if ($request->hasFile('galeri')) {
            foreach ($request->file('galeri') as $file) {
                $galeriPaths[] = $file->store('produk_galeri', 'public');
            }
        }

        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'kategori'    => $request->kategori,
            'merek'       => $request->merek,
            'ram'         => $request->ram,
            'storage'     => $request->storage,
            'vga'         => $request->vga,
            'cpu'         => $request->cpu,
            'foto'        => $fotoPath,
            'galeri_foto' => $galeriPaths,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $produk,
        ], 201);
    }

    /**
     * PUT /api/produk/{id}  (Admin only)
     * Update data produk, termasuk ganti foto.
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_produk' => 'sometimes|required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'sometimes|required|numeric|min:0',
            'stok'        => 'sometimes|required|integer|min:0',
            'kategori'    => 'sometimes|required|string|max:100',
            'merek'       => 'nullable|string|max:100',
            'ram'         => 'nullable|integer|min:0',
            'storage'     => 'nullable|integer|min:0',
            'vga'         => 'nullable|string|max:100',
            'cpu'         => 'nullable|string|max:200',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'galeri'      => 'nullable|array|max:10',
            'galeri.*'    => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $dataUpdate = $request->only([
            'nama_produk', 'deskripsi', 'harga', 'stok',
            'kategori', 'merek', 'ram', 'storage', 'vga', 'cpu',
        ]);

        // Ganti foto jika ada upload baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama dari storage
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $dataUpdate['foto'] = $request->file('foto')->store('produk', 'public');
        }

        // Ganti galeri jika ada upload baru
        if ($request->hasFile('galeri')) {
            // Hapus galeri lama dari storage
            if ($produk->galeri_foto && is_array($produk->galeri_foto)) {
                foreach ($produk->galeri_foto as $oldFile) {
                    if (Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }
            $galeriPaths = [];
            foreach ($request->file('galeri') as $file) {
                $galeriPaths[] = $file->store('produk_galeri', 'public');
            }
            $dataUpdate['galeri_foto'] = $galeriPaths;
        }

        $produk->update($dataUpdate);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui',
            'data'    => $produk->fresh(),
        ], 200);
    }

    /**
     * DELETE /api/produk/{id}  (Admin only)
     * Hapus produk beserta fotonya.
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        // Hapus foto dari storage dihilangkan karena menggunakan fitur SoftDeletes.
        // File fisik tetap dipertahankan agar riwayat pesanan (invoice) tidak rusak.

        $produk->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus',
        ], 200);
    }

    /**
     * GET /api/produk/export (Admin only)
     * Export daftar produk ke CSV.
     */
    public function export()
    {
        $produks = Produk::orderBy('created_at', 'desc')->get();
        $csvHeader = ['ID', 'Nama Produk', 'Merek', 'Kategori', 'Harga', 'Stok', 'RAM', 'Storage', 'VGA', 'CPU', 'Dibuat Pada'];
        
        $callback = function() use ($produks, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($produks as $p) {
                fputcsv($file, [
                    $p->id_produk,
                    $p->nama_produk,
                    $p->merek,
                    $p->kategori,
                    $p->harga,
                    $p->stok,
                    $p->ram,
                    $p->storage,
                    $p->vga,
                    $p->cpu,
                    $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'Daftar_Produk_Defkan.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Daftar_Produk_Defkan.csv"',
        ]);
    }
}