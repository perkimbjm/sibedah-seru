<?php

namespace App\Models;

use DB;
use App\Models\Rtlh;
use App\Models\Village;
use App\Models\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class House extends Model
{
    protected $fillable = [
        'name',
        'nik',
        'address',
        'district',
        'lat',
        'lng',
        'geom',
        'year',
        'type',
        'source',
        'slug',
        'rtlh_id',
        'district_id',
        'village_id',
        'note'
    ];


    // Relasi dengan rtlh
    public function rtlh()
    {
        return $this->belongsTo(Rtlh::class, 'rtlh_id');
    }

    // Relasi dengan District
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // Relasi dengan Village
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function renovatedHousePhotos()
    {
        return $this->hasMany(RenovatedHousePhoto::class, 'renovated_house_id');
    }


    // Mengubah koordinat menjadi Point POSTGIS saat menyimpan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($house) {
            if ($house->lat && $house->lng) {
                $house->geom = DB::raw("ST_SetSRID(ST_MakePoint($house->lng, $house->lat), 4326)");
            }
        });

        static::updating(function ($house) {
            if ($house->isDirty(['lat', 'lng'])) {
                $house->geom = DB::raw("ST_SetSRID(ST_MakePoint($house->lng, $house->lat), 4326)");
            }
        });
    }

    // Method untuk mendapatkan GeoJSON
    public function getGeoJsonAttribute()
    {
        return DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM houses WHERE id = ?", [$this->id])[0]->geojson;
    }

    public $appends = [
        'coordinate', 'map_popup_content',
    ];


    public function getNameLinkAttribute()
    {
        $title = __('Lihat Detail');
        $link = '<a href="' . route('app.houses.show', $this) . '"';
        $link .= ' title="' . $title . '">';
        $link .= $this->id;
        $link .= '</a>';

        return $link;
    }

    public function getCoordinateAttribute()
    {
        if ($this->lat && $this->lng) {
            return $this->lat . ', ' . $this->lng;
        }
    }

    public function getMapPopupContentAttribute()
    {
        $mapPopupContent = '';
        $mapPopupContent .= '<div class="my-2"><strong>' . __('rumah') . ':</strong><br>' . $this->name_link . '</div>';
        $mapPopupContent .= '<div class="my-2"><strong>' . __('koordinat') . ':</strong><br>' . $this->coordinate . '</div>';

        return $mapPopupContent;
    }
}
