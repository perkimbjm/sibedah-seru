<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AduanBaruNotification extends Notification
{
    use Queueable;

    private int $aduanId;

    public function __construct(int $aduanId)
    {
        $this->aduanId = $aduanId;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $aduan = \App\Models\Aduan::with(['district', 'village', 'user'])->find($this->aduanId);

        $districtName = $aduan?->district?->name ?? '';
        $villageName = $aduan?->village?->name ?? '';
        $lokasi = implode(', ', array_filter([$villageName, $districtName]));

        return [
            'title' => 'Pengaduan Baru Masuk',
            'message' => 'Ada pengaduan baru dari ' . ($aduan?->name ?? 'Pengguna') . (empty($lokasi) ? '' : ' - ' . $lokasi) . '.',
            'type' => 'aduan_baru',
            'action' => route('aduan.show', $this->aduanId),
            'aduan_id' => $this->aduanId,
            'nama_pelapor' => $aduan?->name ?? '-',
            'email_pelapor' => $aduan?->email ?? '-',
            'lokasi' => $lokasi,
            'kode_tiket' => $aduan?->kode_tiket ?? null,
        ];
    }
}


