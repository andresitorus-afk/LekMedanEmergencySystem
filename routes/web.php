<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController; 
use App\Http\Controllers\AdminFacilityController; 
use App\Http\Controllers\ReportApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MapController::class, 'index']);

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [AdminFacilityController::class, 'index'])->name('dashboard');
    
    Route::get('/admin/create', [AdminFacilityController::class, 'create'])->name('admin.create');
    Route::post('/admin/store', [AdminFacilityController::class, 'store'])->name('admin.store');
    Route::delete('/admin/destroy/{id}', [AdminFacilityController::class, 'destroy'])->name('admin.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/api/reports', [ReportApiController::class, 'index']);
Route::post('/api/reports', [ReportApiController::class, 'store']);
Route::post('/api/reports/resolve/{id}', [ReportApiController::class, 'resolve']);

require __DIR__.'/auth.php';