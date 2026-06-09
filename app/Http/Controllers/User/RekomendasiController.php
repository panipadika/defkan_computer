<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    public function getRekomendasi(Request $request)
    {
        $software = $request->query('software'); // cth: 'premiere', 'office', 'autocad'
        
        $query = Produk::query();

        // Logika sederhana: Filter berdasarkan spek yang dibutuhkan software
        if ($software == 'premiere' || $software == 'autocad') {
            $query->where('ram', '>=', 16)->where('vga', '!=', 'Integrated');
        } elseif ($software == 'office') {
            $query->where('ram', '>=', 4);
        }

        $rekomendasi = $query->get();
        return response()->json($rekomendasi);
    }
}
