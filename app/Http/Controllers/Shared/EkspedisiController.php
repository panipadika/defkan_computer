<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;

use App\Models\LayananEkspedisi;
use Illuminate\Http\Request;

class EkspedisiController extends Controller
{
    /**
     * GET /api/ekspedisi
     * Ambil semua layanan ekspedisi yang aktif.
     */
    public function index()
    {
        $list = LayananEkspedisi::where('is_aktif', true)
            ->orderBy('biaya_ongkir', 'asc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil layanan ekspedisi',
            'data'    => $list,
        ], 200);
    }
}
