@extends('layouts.main')

@php
    $menuName = 'Manajemen Pengaduan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('title', 'Manajemen Pengaduan')

@section('content')
<div class="mr-4 ml-4">
    <section class="content">

            <!-- Statistics Cards -->
            <div class="mb-4 row">
                <div class="col-lg-3 col-md-6">
                    <div class="text-white card bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 card-title">{{ $stats['total'] }}</h3>
                                    <p class="card-text">
                                        @if($isAdmin) Total Pengaduan @else Pengaduan Saya @endif
                                    </p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-comments fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-white card bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 card-title">{{ $stats['pending'] }}</h3>
                                    <p class="card-text">Menunggu Tanggapan</p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-white card bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 card-title">{{ $stats['process'] }}</h3>
                                    <p class="card-text">Sedang Diproses</p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-cogs fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-white card bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 card-title">{{ $stats['completed'] }}</h3>
                                    <p class="card-text">Selesai</p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4 row">
                <div class="col-12">
                    @can('aduan_create')
                    <a href="{{ route('aduan.create') }}" class="mb-3 btn btn-primary">
                        <i class="mr-2 fas fa-plus"></i>Tambah Aduan
                    </a>
                    @endcan

                    @if(!$isAdmin && $hasLinkableComplaints)
                    <button id="linkComplaintsBtn" class="mb-3 btn btn-outline-info">
                        <i class="mr-2 fas fa-link"></i>Kaitkan Pengaduan Lama
                    </button>
                    <small id="link-message" class="ml-2"></small>
                    @endif
                </div>
            </div>

            <!-- Filter Section -->
            <div class="mb-4 shadow-sm card">
                <div class="card-header bg-light">
                    <h5 class="mb-0 card-title">
                        <i class="mr-2 fas fa-filter"></i>Filter Pengaduan
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET">
                        <div class="row">
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        <i class="fas fa-clock"></i> Menunggu Tanggapan
                                    </option>
                                    <option value="process" {{ request('status') == 'process' ? 'selected' : '' }}>
                                        <i class="fas fa-cogs"></i> Sedang Diproses
                                    </option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-3">
                                <label class="form-label">Kecamatan</label>
                                <select name="district_id" class="form-control">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="search" class="form-control" placeholder="Nama atau Kode Tiket" value="{{ request('search') }}">
                            </div>
                            <div class="mb-3 col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="gap-2 d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('aduan.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Cards -->
            <div class="shadow-sm card">
                <div class="card-header bg-light">
                    <h5 class="mb-0 card-title">
                        <i class="mr-2 fas fa-list"></i>Daftar Pengaduan
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($aduan as $item)
                        <div class="card mb-3 border-left-4
                            @if($item->status == 'pending') border-warning
                            @elseif($item->status == 'process') border-info
                            @else border-success @endif">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <h6 class="mb-1 text-primary">{{ $item->kode_tiket }}</h6>
                                            <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mb-1">{{ $item->name }}</h6>
                                        @if($item->user)
                                            <small class="badge badge-secondary">User Terdaftar</small>
                                        @endif
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $item->village->name }}, {{ $item->district->name }}
                                        </p>

                                        @if($item->foto)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $item->foto) }}"
                                                     alt="Foto Pengaduan"
                                                     class="img-thumbnail"
                                                     style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                     onclick="openPhotoModal('{{ asset('storage/' . $item->foto) }}')">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        @if($item->status == 'pending')
                                            <span class="p-2 badge badge-warning">
                                                <i class="fas fa-clock"></i> Menunggu Tanggapan
                                            </span>
                                        @elseif($item->status == 'process')
                                            <span class="p-2 badge badge-info">
                                                <i class="fas fa-cogs"></i> Sedang Diproses
                                            </span>
                                        @else
                                            <span class="p-2 badge badge-success">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            @if($item->expect)
                                                <div class="mb-1">
                                                    @for($i = 1; $i <= $item->expect; $i++)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @endfor
                                                    @for($i = $item->expect + 1; $i <= 4; $i++)
                                                        <i class="far fa-star text-muted"></i>
                                                    @endfor
                                                </div>
                                                <small class="text-muted">Evaluasi</small>
                                            @else
                                                <small class="text-muted">Belum ada evaluasi</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        @if($isAdmin)
                                            {{-- Admin actions --}}
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('aduan.show', $item) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('aduan.edit', $item) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger" onclick="deleteAduan({{ $item->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                        @else
                                            {{-- User actions - hanya lihat detail --}}
                                            <div class="text-center">
                                                <a href="{{ route('aduan.show', $item) }}" class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-eye"></i> Lihat Detail
                                                </a>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        Klik untuk melihat riwayat percakapan
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-5 text-center">
                            <i class="mb-3 fas fa-comments fa-3x text-muted"></i>
                            <h5 class="text-muted">Tidak ada data pengaduan</h5>
                            <p class="text-muted">Belum ada pengaduan yang masuk ke sistem</p>
                        </div>
                    @endforelse
                </div>
                @if($aduan->hasPages())
                    <div class="card-footer">
                        {{ $aduan->links() }}
                    </div>
                @endif
            </div>

    </section>
</div>

<style>
.border-left-4 {
    border-left: 4px solid !important;
}
.border-warning {
    border-left-color: #ffc107 !important;
}
.border-info {
    border-left-color: #17a2b8 !important;
}
.border-success {
    border-left-color: #28a745 !important;
}
.card-icon {
    opacity: 0.7;
}
.btn-group .btn {
    border-radius: 0.25rem;
}
</style>

<script>
function deleteAduan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')) {
        fetch(`/aduan/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    }
}

function openPhotoModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    $('#photoModal').modal('show');
}
</script>

<!-- Photo Modal -->
<div id="photoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Foto Pengaduan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Foto Pengaduan" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const linkButton = document.getElementById('linkComplaintsBtn');
        if (linkButton) {
            linkButton.addEventListener('click', function() {
                this.disabled = true;
                this.innerHTML = '<i class="mr-2 fas fa-spinner fa-spin"></i>Mengaitkan...';

                fetch('{{ route('aduan.link-previous') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const messageEl = document.getElementById('link-message');
                    if (data.success) {
                        messageEl.textContent = data.message;
                        messageEl.className = 'ml-2 text-success';

                        // Sembunyikan tombol setelah berhasil
                        linkButton.style.display = 'none';

                        // Refresh halaman setelah 2 detik untuk menampilkan data terbaru
                        setTimeout(() => {
                            location.reload();
                        }, 2000);

                    } else {
                        messageEl.textContent = 'Gagal: ' + data.message;
                        messageEl.className = 'ml-2 text-danger';
                        this.disabled = false;
                        this.innerHTML = '<i class="mr-2 fas fa-link"></i>Kaitkan Pengaduan Lama';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageEl = document.getElementById('link-message');
                    messageEl.textContent = 'Terjadi kesalahan jaringan.';
                    messageEl.className = 'ml-2 text-danger';
                    this.disabled = false;
                    this.innerHTML = '<i class="mr-2 fas fa-link"></i>Kaitkan Pengaduan Lama';
                });
            });
        }
    });
</script>
@endpush

