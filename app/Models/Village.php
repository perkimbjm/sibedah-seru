<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = [
        'code',
        'name',
        'slug',
        'district_id',
        'geom'
    ];

    // Relasi many-to-one dengan district
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // Method untuk mendapatkan GeoJSON
    public function getGeoJsonAttribute()
    {
        return DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM villages WHERE id = ?", [$this->id])[0]->geojson;
    }
}