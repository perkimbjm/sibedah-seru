<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class VerifikasiSelesaiNotification extends Notification
{
    use Queueable;

    protected $usulanId;
    protected $hasil;

    public function __construct($usulanId, $hasil)
    {
        $this->usulanId = $usulanId;
        $this->hasil = $hasil;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $usulan = \App\Models\Usulan::with(['user', 'district', 'village'])->find($this->usulanId);

        $message = $this->hasil === 'diterima'
            ? 'Usulan dari ' . ($usulan?->user?->name ?? 'Pengguna') . ' telah diverifikasi dan diterima.'
            : 'Usulan dari ' . ($usulan?->user?->name ?? 'Pengguna') . ' telah diverifikasi namun belum memenuhi syarat.';

        $villageName = $usulan?->village?->name ?? '';
        $districtName = $usulan?->district?->name ?? '';
        $lokasi = implode(', ', array_filter([$villageName, $districtName]));

        return [
            'title' => 'Verifikasi Selesai',
            'message' => $message,
            'type' => 'verifikasi_selesai',
            'action' => route('usulan.proposals.show', $this->usulanId),
            'usulan_id' => $this->usulanId,
            'hasil' => $this->hasil,
            'nama_diusulkan' => $usulan->nama ?? '',
            'nama_pengusul' => $usulan?->user?->name ?? 'Pengguna',
            'nik' => $usulan->nik ?? '',
            'jenis_usulan' => $usulan->jenis_usulan_label ?? '',
            'lokasi' => $lokasi,
        ];
    }
}
