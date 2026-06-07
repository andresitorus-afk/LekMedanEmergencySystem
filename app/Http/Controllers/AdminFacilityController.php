<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFacilityController extends Controller
{
    public function index()
    {
        $facilities = DB::table('facilities')->get();
        return view('dashboard', compact('facilities'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'alamat' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'telepon' => 'nullable|string',
        ]);

        DB::table('facilities')->insert([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'alamat' => $request->alamat,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'telepon' => $request->telepon,
            'geom' => DB::raw("ST_SetSRID(ST_MakePoint(" . floatval($request->lng) . ", " . floatval($request->lat) . "), 4326)"),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('dashboard')->with('success', 'Fasilitas baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        DB::table('facilities')->where('id', $id)->delete();
        return redirect()->route('dashboard')->with('success', 'Fasilitas berhasil dihapus dari database!');
    }
}