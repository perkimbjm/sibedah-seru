<?php

namespace App\Models;

use DB;
use App\Models\House;
use App\Models\Village;
use App\Models\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rtlh extends Model
{
    use HasFactory;

    // Tabel terkait
    protected $table = 'rtlh';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'name',
        'nik',
        'kk',
        'address',
        'people',
        'lat',
        'lng',
        'area',
        'pondasi',
        'kolom_blk',
        'rngk_atap',
        'atap',
        'dinding',
        'lantai',
        'air',
        'jarak_tinja',
        'wc',
        'jenis_wc',
        'tpa_tinja',
        'status_safety',
        'status',
        'is_renov',
        'district_id',
        'village_id',
        'note',
        'geom',
        'slug',
    ];

    // Relasi ke tabel houses
    public function house()
    {
        return $this->hasOne(House::class, 'rtlh_id');
    }

    // Relasi dengan District
    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // Relasi dengan Village
    public function villages()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    // Mengubah koordinat menjadi Point POSTGIS saat menyimpan
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($rtlh) {
            if ($rtlh->lat && $rtlh->lng) {
                $rtlh->geom = DB::raw("ST_SetSRID(ST_MakePoint($rtlh->lng, $rtlh->lat), 4326)");
            }
        });

        static::updating(function ($rtlh) {
            if ($rtlh->isDirty(['lat', 'lng'])) {
                $rtlh->geom = DB::raw("ST_SetSRID(ST_MakePoint($rtlh->lng, $rtlh->lat), 4326)");
            }
        });
    }

    // Method untuk mendapatkan GeoJSON
    public function getGeoJsonAttribute()
    {
        return DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM rtlh WHERE id = ?", [$this->id])[0]->geojson;
    }

    
}
