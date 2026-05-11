<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar Laravel mengizinkan data ini masuk ke PostgreSQL
    protected $fillable = [
        'nama',
        'kategori',
        'alamat',
        'lat',
        'lng'
    ];
}