<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller {
    
    // Ambil semua laporan yang statusnya masih aktif
    public function index() {
        return response()->json(DB::table('reports')->where('status', 'aktif')->get());
    }

    // Simpan laporan baru dari warga (SUDAH DI-FIX DENGAN GEOM POSTGIS)
    public function store(Request $request) {
        try {
            DB::table('reports')->insert([
                'id' => $request->id,
                'tipe' => $request->tipe,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'status' => 'aktif',
                
                // PERBAIKAN UTAMA: Konversi koordinat lat/lng dari HP/Web menjadi tipe data spasial PostGIS
                'geom' => DB::raw("ST_SetSRID(ST_MakePoint(" . floatval($request->lng) . ", " . floatval($request->lat) . "), 4326)"),
                
                'created_at' => now(),
                'updated_at' => now() // Tambahkan updated_at sekalian biar lengkap standar Laravel
            ]);
            
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Jika database menolak, respon ini akan membantu kita melihat erornya di tab Inspect Element -> Network
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Ubah status jadi selesai (Hapus marker)
    public function resolve($id) {
        DB::table('reports')->where('id', $id)->update([
            'status' => 'selesai',
            'updated_at' => now()
        ]);
        return response()->json(['success' => true]);
    }
}