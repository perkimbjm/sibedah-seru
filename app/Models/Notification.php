<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use App\Models\Usulan;

class Notification extends DatabaseNotification
{


    // Accessors
    public function getTypeLabelAttribute()
    {
        $type = $this->data['type'] ?? $this->type;
        return match($type) {
            'aduan_baru' => 'Pengaduan Baru',
            'aduan_ditanggapi' => 'Pengaduan Ditanggapi',
            'usulan_baru' => 'Usulan Baru',
            'verifikasi_selesai' => 'Verifikasi Selesai',
            'usulan_diterima' => 'Usulan Diterima',
            'usulan_ditolak' => 'Usulan Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    public function getTypeIconAttribute()
    {
        $type = $this->data['type'] ?? $this->type;
        return match($type) {
            'aduan_baru' => 'fas fa-envelope-open-text',
            'aduan_ditanggapi' => 'fas fa-reply',
            'usulan_baru' => 'fas fa-file-alt',
            'verifikasi_selesai' => 'fas fa-check-circle',
            'usulan_diterima' => 'fas fa-thumbs-up',
            'usulan_ditolak' => 'fas fa-times-circle',
            default => 'fas fa-bell'
        };
    }

    public function getTypeColorAttribute()
    {
        $type = $this->data['type'] ?? $this->type;
        return match($type) {
            'aduan_baru' => 'teal',
            'aduan_ditanggapi' => 'purple',
            'usulan_baru' => 'blue',
            'verifikasi_selesai' => 'yellow',
            'usulan_diterima' => 'green',
            'usulan_ditolak' => 'red',
            default => 'gray'
        };
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        // Kompatibel dengan tabel standar Laravel: notifiable_id dan notifiable_type
        return $query->where('notifiable_id', $userId)
            ->where('notifiable_type', \App\Models\User::class);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }

    public function isUnread()
    {
        return is_null($this->read_at);
    }

    // Static methods for creating notifications are no longer needed as we use Laravel's notification system

    // ---- Augmented data accessors dari relasi Usulan (fallback bila payload tidak lengkap)
    protected ?Usulan $cachedUsulan = null;

    public function getUsulan(): ?Usulan
    {
        if ($this->cachedUsulan !== null) {
            return $this->cachedUsulan;
        }
        $usulanId = $this->data['usulan_id'] ?? null;
        if (!$usulanId) {
            return null;
        }
        $this->cachedUsulan = Usulan::with(['user', 'village', 'district'])->find($usulanId);
        return $this->cachedUsulan;
    }

    public function getNamaDiusulkanAttribute(): ?string
    {
        return $this->data['nama_diusulkan'] ?? ($this->getUsulan()?->nama);
    }

    public function getNikDiusulkanAttribute(): ?string
    {
        return $this->data['nik'] ?? ($this->getUsulan()?->nik);
    }

    public function getNamaPengusulAttribute(): ?string
    {
        return $this->data['nama_pengusul'] ?? ($this->getUsulan()?->user?->name);
    }

    public function getJenisUsulanTextAttribute(): ?string
    {
        return $this->data['jenis_usulan'] ?? ($this->getUsulan()?->jenis_usulan_label);
    }

    public function getLokasiTextAttribute(): ?string
    {
        if (!empty($this->data['lokasi'])) {
            return $this->data['lokasi'];
        }
        $usulan = $this->getUsulan();
        if (!$usulan) {
            return null;
        }
        $village = $usulan->village?->name ?? '';
        $district = $usulan->district?->name ?? '';
        $parts = array_filter([$village, $district]);
        return empty($parts) ? null : implode(', ', $parts);
    }
}
