<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility; // Pastikan model ini di-import

class MapController extends Controller
{
    /**
     * Menampilkan halaman peta utama dengan data koordinat RS & Damkar.
     */
    public function index()
    {
        // 1. Ambil semua data fasilitas dari database PostgreSQL
        $facilities = Facility::all();

        // 2. Kirim data ke view 'map.blade.php'
        return view('map', compact('facilities'));
    }

    /**
     * (Opsional) Fungsi jika kedepannya kamu ingin memfilter kategori via AJAX
     */
    public function getByCategory($kategori)
    {
        $facilities = Facility::where('kategori', $kategori)->get();
        return response()->json($facilities);
    }
}