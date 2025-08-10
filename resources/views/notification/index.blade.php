@extends('layouts.main')

@php
    $menuName = 'Notifikasi';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('title', 'Notifikasi')

@section('content')
<div class="container py-4">
    <div class="mb-3 d-flex align-items-center justify-content-between">
        <h4 class="mb-0">Semua Notifikasi</h4>
        <div class="gap-2 d-flex">
            <button id="mark-all-read" class="btn btn-sm btn-outline-primary">Tandai semua dibaca</button>
            <button id="clear-all" class="btn btn-sm btn-outline-danger">Hapus semua</button>
        </div>
    </div>
    <div id="notification-message"></div>

    <ul class="mb-3 nav nav-pills">
        @php $filter = request('filter'); $type = request('type'); @endphp
        <li class="nav-item"><a class="nav-link {{ $filter===null ? 'active' : '' }}" href="{{ route('usulan.notifications.index') }}">Semua</a></li>
        <li class="nav-item"><a class="nav-link {{ $filter==='unread' ? 'active' : '' }}" href="{{ route('usulan.notifications.index', ['filter' => 'unread', 'type' => $type]) }}">Belum dibaca</a></li>
        <li class="nav-item"><a class="nav-link {{ $filter==='read' ? 'active' : '' }}" href="{{ route('usulan.notifications.index', ['filter' => 'read', 'type' => $type]) }}">Sudah dibaca</a></li>
    </ul>
    <div class="list-group">
        @forelse($notifications as $notification)
            @php
                // Fallback multi-sumber agar notifikasi lama tetap informatif
                $usulanObj = method_exists($notification, 'getUsulan') ? $notification->getUsulan() : null;
                $namaDiusulkan = $notification->nama_diusulkan
                    ?? ($notification->data['nama_diusulkan'] ?? ($usulanObj->nama ?? null));
                $nikDiusulkan = $notification->nik_diusulkan
                    ?? ($notification->data['nik'] ?? null);
                $namaPengusul = $notification->nama_pengusul
                    ?? ($notification->data['nama_pengusul'] ?? ($usulanObj->user->name ?? null));
                $jenisUsulanText = $notification->jenis_usulan_text
                    ?? ($notification->data['jenis_usulan'] ?? ($usulanObj->jenis_usulan_label ?? null));
                $lokasiText = $notification->lokasi_text
                    ?? ($notification->data['lokasi'] ?? null);
            @endphp
            <div class="list-group-item d-flex align-items-start {{ $notification->isUnread() ? 'bg-light' : '' }}" style="border-left: 4px solid {{ $notification->type_color }};">
                <div class="mt-1 me-3">
                    <i class="{{ $notification->type_icon }}" style="color: {{ $notification->type_color }}"></i>
                </div>
                <div class="flex-grow-1">
                    @php
                        $headingTitle = $notification->data['title'] ?? $notification->type_label;
                    @endphp
                    <div class="fw-semibold {{ $notification->isUnread() ? 'text-dark' : '' }}">
                        {{ $headingTitle }}
                        {{-- Hilangkan badge jenis bila sama dengan judul --}}
                        @php $showTypeBadge = $headingTitle !== $notification->type_label; @endphp
                        @if($showTypeBadge)
                            <span class="badge bg-light text-muted ms-2">{{ $notification->type_label }}</span>
                        @endif
                        @if($notification->isUnread())
                            <span class="badge bg-primary ms-2">Belum dibaca</span>
                        @endif
                    </div>
                    @if($namaDiusulkan || $nikDiusulkan)
                        <div class="mt-1">
                            <span class="fw-bold">{{ $namaDiusulkan ?? '-' }}</span>
                            @if($nikDiusulkan)
                                <span class="badge bg-light text-dark ms-1">{{ $nikDiusulkan }}</span>
                            @endif
                        </div>
                    @endif
                    @if(!empty($notification->data['message']))
                        <div class="text-muted small">{{ $notification->data['message'] }}</div>
                    @endif

                    @if(isset($notification->data['hasil']))
                        <div class="mt-1">
                            <span class="badge {{ ($notification->data['hasil'] === 'diterima') ? 'bg-success' : 'bg-warning text-dark' }}">
                                Hasil Verifikasi: {{ ucfirst($notification->data['hasil']) }}
                            </span>
                        </div>
                    @endif

                    <div class="mt-1 row row-cols-1 row-cols-md-2 g-1">
                        @if($namaDiusulkan)
                            <div class="col text-muted small">Nama Diusulkan: <strong>{{ $namaDiusulkan }}</strong></div>
                        @endif
                        @if($nikDiusulkan)
                            <div class="col text-muted small">NIK Diusulkan: <strong>{{ $nikDiusulkan }}</strong></div>
                        @endif
                        @if($namaPengusul)
                            <div class="col text-muted small">Pengusul: <strong>{{ $namaPengusul }}</strong></div>
                        @endif
                        @if($jenisUsulanText)
                            <div class="col text-muted small">Jenis Usulan: <strong>{{ $jenisUsulanText }}</strong></div>
                        @endif
                        @if($lokasiText)
                            <div class="col text-muted small">Lokasi: <strong>{{ $lokasiText }}</strong></div>
                        @endif
                        @if(isset($notification->data['catatan']) && $notification->data['catatan'])
                            <div class="col-12 text-muted small">Catatan Verifikator: <em>{{ $notification->data['catatan'] }}</em></div>
                        @endif
                    </div>

                    <div class="gap-2 mt-1 d-flex align-items-center text-muted small">
                        <span>{{ $notification->created_at->translatedFormat('d M Y, H:i') }} ({{ $notification->created_at->diffForHumans() }})</span>
                        @if(isset($notification->data['action']))
                            <a class="btn btn-sm btn-outline-primary ms-2" href="{{ $notification->data['action'] }}">Lihat Usulan</a>
                        @endif
                    </div>
                </div>
                <div class="ms-3 text-end">
                    <form method="POST" action="{{ route('usulan.notifications.destroy', $notification) }}" class="mb-1 delete-notification" data-id="{{ $notification->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn" aria-label="Hapus notifikasi"><i class="fas fa-trash"></i></button>
                    </form>
                    @if($notification->isUnread())
                        <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="{{ $notification->id }}" data-url="{{ route('usulan.notifications.mark-read', $notification) }}">Tandai dibaca</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-muted">Tidak ada notifikasi.</div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mark all as read
    const markAllBtn = document.getElementById('mark-all-read');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch("{{ route('usulan.notifications.mark-all-read') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.list-group-item.bg-light').forEach(el => el.classList.remove('bg-light'));
                    Swal.fire('Sukses', data.message, 'success');
                }
            })
            .catch(() => Swal.fire('Gagal', 'Tidak dapat menandai semua sebagai dibaca.', 'error'));
        });
    }

    // Clear all
    const clearAllBtn = document.getElementById('clear-all');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Hapus semua notifikasi?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (!res.isConfirmed) return;
                fetch("{{ route('usulan.notifications.clear-all') }}", {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.list-group').innerHTML = '<div class="text-muted">Tidak ada notifikasi.</div>';
                        Swal.fire('Dihapus', data.message, 'success');
                    }
                })
                .catch(() => Swal.fire('Gagal', 'Tidak dapat menghapus semua notifikasi.', 'error'));
            });
        });
    }

    // Mark single as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const row = this.closest('.list-group-item');
            fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        row.classList.remove('bg-light');
                        // remove badge "Belum dibaca"
                        const badge = row.querySelector('.badge.bg-primary');
                        if (badge) badge.remove();
                    }
                })
                .catch(() => Swal.fire('Gagal', 'Tidak dapat menandai sebagai dibaca.', 'error'));
        });
    });
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const notificationId = form.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Notifikasi?',
                text: "Anda tidak dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the notification from the DOM
                            form.closest('.list-group-item').remove();

                            // Show success message
                            Swal.fire(
                                'Dihapus!',
                                data.message,
                                'success'
                            );

                            // Check if there are no more notifications
                            if (document.querySelectorAll('.list-group-item').length === 0) {
                                document.querySelector('.list-group').innerHTML = '<div class="text-muted">Tidak ada notifikasi.</div>';
                            }
                        } else {
                            Swal.fire(
                                'Gagal!',
                                data.message || 'Terjadi kesalahan saat menghapus notifikasi.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghubungi server.',
                            'error'
                        );
                    });
                }
            });
        });
    });
});
</script>
@endsection

