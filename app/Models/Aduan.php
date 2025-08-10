<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Aduan extends Model
{
    use HasFactory;

    protected $table = 'aduan';

    protected $fillable = [
        'name',
        'email',
        'contact',
        'district_id',
        'village_id',
        'complain',
        'foto',
        'complain2',
        'complain3',
        'respon',
        'respon2',
        'respon3',
        'expect',
        'kode_tiket',
        'user_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'complain2_at' => 'datetime',
        'complain3_at' => 'datetime',
        'respon_at' => 'datetime',
        'respon2_at' => 'datetime',
        'respon3_at' => 'datetime',
    ];

    // Relationships
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Generate unique ticket code
    public static function generateKodeTicket()
    {
        do {
            $kode = 'ADU-' . strtoupper(Str::random(6)) . '-' . now()->format('md');
        } while (self::where('kode_tiket', $kode)->exists());

        return $kode;
    }

    // Boot method untuk auto-generate kode tiket
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aduan) {
            if (empty($aduan->kode_tiket)) {
                $aduan->kode_tiket = self::generateKodeTicket();
            }
        });
    }

    // Accessor untuk status readable
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Tanggapan',
            'process' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'closed' => 'Ditutup',
            default => 'Unknown'
        };
    }

    // Accessor untuk URL foto
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    // Check if this complaint can receive additional comments
    public function canAddComplaint()
    {
        if (empty($this->complain2)) return true;
        if (empty($this->complain3)) return true;
        return false;
    }

    // Check if admin can add response
    public function canAddResponse()
    {
        if ($this->status === 'completed') return false;
        if (empty($this->respon)) return true;
        if (empty($this->respon2) && !empty($this->complain2)) return true;
        if (empty($this->respon3) && !empty($this->complain3)) return true;
        return false;
    }

    // Get next complaint field
    public function getNextComplaintField()
    {
        if (empty($this->complain2)) return 'complain2';
        if (empty($this->complain3)) return 'complain3';
        return null;
    }

    // Get next response field
    public function getNextResponseField()
    {
        if (empty($this->respon)) return 'respon';
        if (empty($this->respon2)) return 'respon2';
        if (empty($this->respon3)) return 'respon3';
        return null;
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan district
    public function scopeByDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    // Scope untuk filter berdasarkan village
    public function scopeByVillage($query, $villageId)
    {
        return $query->where('village_id', $villageId);
    }
}
