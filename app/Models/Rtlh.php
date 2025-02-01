<?php

namespace App\Models;

use DB;
use App\Models\House;
use App\Models\Village;
use App\Models\District;
use App\Models\HousePhoto;
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
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // Relasi dengan Village
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function housePhotos()
    {
        return $this->hasMany(HousePhoto::class, 'house_id');
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


    // Hitung nilai aspek keselamatan (Safety Score)
    public function calculateSafetyScore()
    {
        $structural = $this->getScore($this->pondasi, [
            'Layak' => 20,
            'Kurang Layak' => 10,
            'Tidak Layak' => 0,
        ]) + $this->getScore($this->kolom_blk, [
            'Layak' => 25,
            'Kurang Layak' => 15,
            'Tidak Layak' => 0,
        ]) + $this->getScore($this->rngk_atap, [
            'Layak' => 20,
            'Kurang Layak' => 10,
            'Tidak Layak' => 0,
        ]);

        $nonStructural = $this->getScore($this->atap, [
            'Layak' => 15,
            'Kurang Layak' => 7.5,
            'Tidak Layak' => 0,
        ]) + $this->getScore($this->dinding, [
            'Layak' => 10,
            'Kurang Layak' => 5,
            'Tidak Layak' => 0,
        ]) + $this->getScore($this->lantai, [
            'Layak' => 10,
            'Kurang Layak' => 5,
            'Tidak Layak' => 0,
        ]);

        return $structural + $nonStructural;
    }

    // Hitung nilai aspek kecukupan ruang
    public function calculateSpaceScore()
    {
        return ($this->area / $this->people > 7) ? 100 : 0;
    }

    public function calculateCleanWaterScore()
    {
        $airScore = $this->getScore($this->air, [
            'PDAM' => 50,
            'Isi Ulang' => 50,
            'Air Kemasan' => 50,
            'Sumur' => 40,
            'Pamsimas' => 40,
            'Mata Air' => 30,
            'Air Hujan' => 20,
        ], 10);

        $jarakTinjaScore = $this->jarak_tinja === '≥ 10 Meter' ? 50 : 0;

        return $airScore + $jarakTinjaScore;
    }

    public function calculateSanitationScore()
    {
        $wcScore = $this->getScore($this->wc, [
            'Milik Sendiri' => 50,
            'Bersama' => 25,
            'Milik bersama/ komunal' => 25,
        ], 0);

        $jenisWcScore = $this->getScore($this->jenis_wc, [
            'Leher Angsa' => 25,
            'Plengsengan' => 12.5,
        ], 0);

        $tpaTinjaScore = $this->getScore($this->tpa_tinja, [
            'Tangki Septik' => 25,
            'IPAL' => 25,
        ], 0);

        return $wcScore + $jenisWcScore + $tpaTinjaScore;
    }

    public function calculateFinalScore()
    {
        $safetyScore = $this->calculateSafetyScore();
        $spaceScore = $this->calculateSpaceScore();
        $cleanWaterScore = $this->calculateCleanWaterScore();
        $sanitationScore = $this->calculateSanitationScore();

        $total = $safetyScore + $spaceScore + $cleanWaterScore + $sanitationScore;

        return round($total / 4, 2); // Rata-rata dari 4 aspek
    }

    public function calculateOverallScore()
    {
        $scores = [
            $this->calculateSafetyScore(),
            $this->calculateRoomAdequacyScore(),
            $this->calculateCleanWaterScore(),
            $this->calculateSanitationScore(),
        ];

        return array_sum($scores) / count($scores);
    }

    public function determineCategory($score)
    {
        if ($score > 80) return 'LAYAK';
        if ($score >= 60) return 'MENUJU LAYAK';
        if ($score >= 35) return 'KURANG LAYAK';
        return 'TIDAK LAYAK';
    }

    public function getConditionStatusSafety()
    {
        $safetyScore = $this->calculateSafetyScore();

        return $safetyScore > 80 ? 'LAYAK' :
               ($safetyScore >= 60 ? 'MENUJU LAYAK' :
               ($safetyScore >= 35 ? 'KURANG LAYAK' : 'TIDAK LAYAK'));
    }

    // // Accessor untuk kategori Status (Final Score)
    public function getConditionStatus()
    {
        $finalScore = $this->calculateFinalScore();

        return $finalScore > 80 ? 'LAYAK' :
               ($finalScore >= 60 ? 'MENUJU LAYAK' :
               ($finalScore >= 35 ? 'KURANG LAYAK' : 'TIDAK LAYAK'));
    }

    public function getScore($value, $map, $default = 0)
    {
        return $map[$value] ?? $default;
    }

}
