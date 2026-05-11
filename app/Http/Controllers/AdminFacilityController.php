<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class AdminFacilityController extends Controller
{
    // Menampilkan daftar fasilitas di dashboard
    public function index() {
        $facilities = Facility::latest()->get();
        return view('admin.dashboard', compact('facilities'));
    }

    // Menampilkan form tambah
    public function create() {
        return view('admin.create');
    }

    // Menyimpan data ke PostgreSQL
    public function store(Request $request) {
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'alamat' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        Facility::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Lokasi berhasil ditambahkan!');
    }
    public function destroy($id)
    {
        $facility = Facility::findOrFail($id);
        $facility->delete();

        return redirect()->route('dashboard')->with('success', 'Data lokasi berhasil dihapus!');
    }
}