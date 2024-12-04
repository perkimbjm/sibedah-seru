<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'code',
        'name',
        'slug',
        'geom'
    ];

    // Relasi one-to-many dengan villages
    public function villages()
    {
        return $this->hasMany(Village::class);
    }

    // Method untuk mendapatkan GeoJSON
    public function getGeoJsonAttribute()
    {
        return DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM districts WHERE id = ?", [$this->id])[0]->geojson;
    }
}