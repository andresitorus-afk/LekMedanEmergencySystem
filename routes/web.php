<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController; // <-- Wajib ada untuk peta
use App\Http\Controllers\AdminFacilityController; // <-- Wajib ada agar tidak merah!
use Illuminate\Support\Facades\Route;

// 1. Halaman Peta Utama (Bisa diakses siapa saja tanpa login)
Route::get('/', [MapController::class, 'index']);

// 2. Halaman Dashboard & Admin (Hanya untuk yang sudah login)
Route::middleware(['auth', 'verified'])->group(function () {
    // Menampilkan daftar lokasi di dashboard
    Route::get('/dashboard', [AdminFacilityController::class, 'index'])->name('dashboard');
    
    // Form tambah lokasi baru
    Route::get('/admin/create', [AdminFacilityController::class, 'create'])->name('admin.create');
    
    // Proses simpan lokasi ke database
    Route::post('/admin/store', [AdminFacilityController::class, 'store'])->name('admin.store');
    Route::delete('/admin/destroy/{id}', [AdminFacilityController::class, 'destroy'])->name('admin.destroy');
});

// 3. Pengaturan Profil Bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';