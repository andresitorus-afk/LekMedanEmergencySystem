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
       Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori');
            $table->string('alamat');
            $table->decimal('lat', 10, 8); // Garis lintang
            $table->decimal('lng', 11, 8); // Garis bujur
            $table->string('telepon')->nullable();
            
            // PERBAIKAN DI SINI: Pakai geometry() agar kompatibel dengan PostGIS bawaan Laravel
            $table->geometry('geom', 'point', 4326);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};