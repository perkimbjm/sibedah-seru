<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $casts = [
           'geom' => 'geometry', // Menentukan kolom geom sebagai tipe geometry
       ];

    protected $fillable = ['geom']; // Menentukan kolom yang bisa diisi
}