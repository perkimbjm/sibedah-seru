<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Usulan extends Model
{
    use HasFactory;

    protected $table = 'usulan';

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'nomor_kk',
        'nomor_hp',
        'district_id',
        'village_id',
        'alamat_lengkap',
        'jenis_usulan',
        'foto_rumah',
        'latitude',
        'longitude',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Sudah Diverifikasi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    public function getJenisUsulanLabelAttribute()
    {
        return match($this->jenis_usulan) {
            'RTLH' => 'Rumah Tidak Layak Huni',
            'Rumah Korban Bencana' => 'Rumah Korban Bencana',
            default => 'Tidak Diketahui'
        };
    }

    public function getFotoRumahUrlAttribute()
    {
        if ($this->foto_rumah) {
            return Storage::url($this->foto_rumah);
        }
        return null;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOlderThanDays($query, $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function canBeEditedBy($user)
    {
        // Admin dan TFL bisa edit semua usulan
        if ($user->hasRole(['Super Admin', 'Admin', 'tfl'])) {
            return true;
        }

        // User biasa hanya bisa edit usulan miliknya yang masih pending
        return $this->user_id === $user->id && $this->isPending();
    }

    public function canBeDeletedBy($user)
    {
        // Admin bisa hapus semua usulan
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // User biasa hanya bisa hapus usulan miliknya yang masih pending
        return $this->user_id === $user->id && $this->isPending();
    }

    public function canBeVerifiedBy($user)
    {
        return $user->hasRole(['Super Admin', 'Admin', 'tfl']);
    }
}
