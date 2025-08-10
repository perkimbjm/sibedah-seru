<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UsulanDiterimaNotification extends Notification
{
    use Queueable;

    protected $usulanId;

    public function __construct($usulanId)
    {
        $this->usulanId = $usulanId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $usulan = \App\Models\Usulan::with(['user', 'district', 'village'])->find($this->usulanId);

        $villageName = $usulan?->village?->name ?? '';
        $districtName = $usulan?->district?->name ?? '';
        $lokasi = implode(', ', array_filter([$villageName, $districtName]));

        return [
            'title' => 'Usulan Diterima',
            'message' => 'Selamat! Usulan Anda telah diterima dan akan diproses. Terima kasih atas kontribusi Anda dalam program bedah rumah.',
            'type' => 'usulan_diterima',
            'action' => route('usulan.proposals.show', $this->usulanId),
            'usulan_id' => $this->usulanId,
            'nama_diusulkan' => $usulan->nama ?? '',
            'nama_pengusul' => $usulan?->user?->name ?? 'Pengguna',
            'nik' => $usulan->nik ?? '',
            'jenis_usulan' => $usulan->jenis_usulan_label ?? '',
            'lokasi' => $lokasi,
        ];
    }
}
