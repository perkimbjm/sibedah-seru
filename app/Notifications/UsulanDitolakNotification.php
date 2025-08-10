<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UsulanDitolakNotification extends Notification
{
    use Queueable;

    protected $usulanId;
    protected $catatan;

    public function __construct($usulanId, $catatan = null)
    {
        $this->usulanId = $usulanId;
        $this->catatan = $catatan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $usulan = \App\Models\Usulan::with(['user', 'district', 'village'])->find($this->usulanId);

        $message = 'Usulan Anda belum memenuhi syarat.';
        if ($this->catatan) {
            $message .= ' Catatan: ' . $this->catatan;
        }

        $villageName = $usulan?->village?->name ?? '';
        $districtName = $usulan?->district?->name ?? '';
        $lokasi = implode(', ', array_filter([$villageName, $districtName]));

        return [
            'title' => 'Usulan Ditolak',
            'message' => $message,
            'type' => 'usulan_ditolak',
            'action' => route('usulan.proposals.show', $this->usulanId),
            'usulan_id' => $this->usulanId,
            'catatan' => $this->catatan,
            'nama_diusulkan' => $usulan->nama ?? '',
            'nama_pengusul' => $usulan?->user?->name ?? 'Pengguna',
            'nik' => $usulan->nik ?? '',
            'jenis_usulan' => $usulan->jenis_usulan_label ?? '',
            'lokasi' => $lokasi,
        ];
    }
}
