<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
     Schema::create('reports', function (Blueprint $table) {
        $table->string('id')->primary(); // ID unik berbasis timestamp
        $table->string('tipe');         // Jenis hambatan (Banjir, Kecelakaan, dll)
        $table->decimal('lat', 10, 8);   // Garis lintang desimal
        $table->decimal('lng', 11, 8);   // Garis bujur desimal
        $table->string('status')->default('aktif'); // Status 'aktif' atau 'selesai'
        
        // TAMBAHKAN BARIS INI: Biar tabel reports punya kolom spasial PostGIS
        $table->geometry('geom', 'point', 4326); 
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
