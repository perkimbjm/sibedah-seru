<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AduanDitanggapiNotification extends Notification
{
    use Queueable;

    private int $aduanId;
    private ?string $responsePreview;

    public function __construct(int $aduanId, ?string $responsePreview = null)
    {
        $this->aduanId = $aduanId;
        $this->responsePreview = $responsePreview ? mb_substr($responsePreview, 0, 140) : null;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $aduan = \App\Models\Aduan::with(['district', 'village'])->find($this->aduanId);

        return [
            'title' => 'Pengaduan Ditanggapi',
            'message' => 'Pengaduan Anda telah ditanggapi oleh admin.',
            'type' => 'aduan_ditanggapi',
            'action' => route('aduan.show', $this->aduanId),
            'aduan_id' => $this->aduanId,
            'kode_tiket' => $aduan?->kode_tiket ?? null,
            'response_preview' => $this->responsePreview,
        ];
    }
}


