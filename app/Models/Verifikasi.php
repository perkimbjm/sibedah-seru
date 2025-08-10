<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Verifikasi extends Model
{
    use HasFactory;

    protected $table = 'verifikasi';

    protected $fillable = [
        'usulan_id',
        'verifikator_id',
        'kesesuaian_tata_ruang',
        'tidak_dalam_sengketa',
        'memiliki_alas_hak',
        'satu_satunya_rumah',
        'belum_pernah_bantuan',
        'berpenghasilan_rendah',
        'hasil_verifikasi',
        'catatan_verifikator'
    ];

    protected $casts = [
        'kesesuaian_tata_ruang' => 'boolean',
        'tidak_dalam_sengketa' => 'boolean',
        'memiliki_alas_hak' => 'boolean',
        'satu_satunya_rumah' => 'boolean',
        'belum_pernah_bantuan' => 'boolean',
        'berpenghasilan_rendah' => 'boolean',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function usulan()
    {
        return $this->belongsTo(Usulan::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    // Accessors
    public function getHasilVerifikasiLabelAttribute()
    {
        return match($this->hasil_verifikasi) {
            'diterima' => 'Diterima',
            'belum_memenuhi_syarat' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    // Methods
    public function isAccepted()
    {
        return $this->hasil_verifikasi === 'diterima';
    }

    public function isRejected()
    {
        return $this->hasil_verifikasi === 'belum_memenuhi_syarat';
    }

    public function getKriteriaVerifikasi()
    {
        return [
            'kesesuaian_tata_ruang' => $this->kesesuaian_tata_ruang,
            'tidak_dalam_sengketa' => $this->tidak_dalam_sengketa,
            'memiliki_alas_hak' => $this->memiliki_alas_hak,
            'satu_satunya_rumah' => $this->satu_satunya_rumah,
            'belum_pernah_bantuan' => $this->belum_pernah_bantuan,
            'berpenghasilan_rendah' => $this->berpenghasilan_rendah,
        ];
    }

    public function getKriteriaMemenuhiSyarat()
    {
        $kriteria = $this->getKriteriaVerifikasi();
        return array_filter($kriteria);
    }

    public function getKriteriaTidakMemenuhiSyarat()
    {
        $kriteria = $this->getKriteriaVerifikasi();
        return array_filter($kriteria, function($value) {
            return !$value;
        });
    }

    public function getJumlahKriteriaMemenuhiSyarat()
    {
        return count($this->getKriteriaMemenuhiSyarat());
    }

    public function getJumlahKriteriaTidakMemenuhiSyarat()
    {
        return count($this->getKriteriaTidakMemenuhiSyarat());
    }

    public function getPersentaseMemenuhiSyarat()
    {
        $totalKriteria = count($this->getKriteriaVerifikasi());
        $memenuhiSyarat = $this->getJumlahKriteriaMemenuhiSyarat();

        return $totalKriteria > 0 ? round(($memenuhiSyarat / $totalKriteria) * 100, 2) : 0;
    }

    // Authorization methods
    public function canBeEditedBy($user)
    {
        // Hanya verifikator yang bisa edit verifikasinya sendiri
        return $user->hasRole(['Super Admin', 'Admin', 'tfl']) && $this->verifikator_id === $user->id;
    }

    public function canBeDeletedBy($user)
    {
        // Hanya admin yang bisa hapus verifikasi
        return $user->hasRole(['Super Admin', 'Admin']);
    }
}
