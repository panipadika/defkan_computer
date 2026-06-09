<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SoftwareController extends Controller
{
    /**
     * GET /api/software
     * Ambil semua software beserta requirement spesifikasinya.
     */
    public function index()
    {
        $software = DB::table('software')
            ->join('requirement_software', 'software.id', '=', 'requirement_software.software_id')
            ->select(
                'software.id',
                'software.nama',
                'software.icon',
                'software.kategori',
                'requirement_software.ram_min',
                'requirement_software.storage_min',
                'requirement_software.vga_min',
                'requirement_software.cpu_min'
            )
            ->orderBy('software.kategori')
            ->orderBy('software.nama')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil daftar software',
            'data'    => $software,
        ], 200);
    }
}
