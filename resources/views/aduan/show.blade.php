@extends('layouts.main')

@php
    $menuName = 'Detail Pengaduan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('title', 'Detail Pengaduan')

@section('content')
<div class="mr-4 ml-4">


    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Info Pengadu -->
                <div class="col-md-4">
                    <div class="mb-4 shadow-sm card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 card-title">
                                <i class="mr-2 fas fa-user"></i>Informasi Pengadu
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <div class="text-center col-12">
                                    <div class="mb-2">
                                        <span class="p-2 badge badge-primary" style="font-size: 1.1em;">
                                            {{ $aduan->kode_tiket }}
                                        </span>
                                    </div>
                                    <h5 class="mb-1">{{ $aduan->name }}</h5>
                                    @if($aduan->user)
                                        <small class="badge badge-secondary">User Terdaftar</small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 info-item">
                                <div class="row">
                                    <div class="col-4">
                                        <i class="fas fa-envelope text-primary"></i>
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-8">
                                        {{ $aduan->email ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 info-item">
                                <div class="row">
                                    <div class="col-4">
                                        <i class="fas fa-phone text-primary"></i>
                                        <strong>Kontak:</strong>
                                    </div>
                                    <div class="col-8">
                                        {{ $aduan->contact ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 info-item">
                                <div class="row">
                                    <div class="col-4">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <strong>Lokasi:</strong>
                                    </div>
                                    <div class="col-8">
                                        {{ $aduan->village->name }}<br>
                                        <small class="text-muted">{{ $aduan->district->name }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 info-item">
                                <div class="row">
                                    <div class="col-4">
                                        <i class="fas fa-calendar text-primary"></i>
                                        <strong>Tanggal:</strong>
                                    </div>
                                    <div class="col-8">
                                        {{ $aduan->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 info-item">
                                <div class="row">
                                    <div class="col-4">
                                        <i class="fas fa-flag text-primary"></i>
                                        <strong>Status:</strong>
                                    </div>
                                    <div class="col-8">
                                        @if($aduan->status == 'pending')
                                            <span class="p-2 badge badge-warning">
                                                <i class="fas fa-clock"></i> Menunggu Tanggapan
                                            </span>
                                        @elseif($aduan->status == 'process')
                                            <span class="p-2 badge badge-info">
                                                <i class="fas fa-cogs"></i> Sedang Diproses
                                            </span>
                                        @else
                                            <span class="p-2 badge badge-success">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($aduan->expect)
                    <div class="mb-4 shadow-sm card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 card-title">
                                <i class="mr-2 fas fa-star"></i>Evaluasi Layanan
                            </h5>
                        </div>
                        <div class="text-center card-body">
                            <div class="mb-3">
                                @for($i = 1; $i <= $aduan->expect; $i++)
                                    <i class="fas fa-star text-warning fa-2x"></i>
                                @endfor
                                @for($i = $aduan->expect + 1; $i <= 4; $i++)
                                    <i class="far fa-star text-muted fa-2x"></i>
                                @endfor
                            </div>
                            <div class="alert alert-info">
                                <strong>
                                    @if($aduan->expect == 1) Tidak ada tanggapan
                                    @elseif($aduan->expect == 2) Ada, tapi tidak berfungsi
                                    @elseif($aduan->expect == 3) Berfungsi tapi kurang maksimal
                                    @else Dikelola dengan baik
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Riwayat Percakapan -->
                <div class="col-md-8">
                    <div class="shadow-sm card">
                        <div class="card-header bg-light">
                                                         <div class="d-flex justify-content-between align-items-center">
                                 <h5 class="mb-0 card-title">
                                     <i class="mr-2 fas fa-comments"></i>Riwayat Percakapan
                                 </h5>
                                 @if($isAdmin)
                                     <a href="{{ route('aduan.edit', $aduan) }}" class="btn btn-primary">
                                         <i class="fas fa-reply"></i> Beri Tanggapan
                                     </a>
                                 @else
                                     <span class="p-2 badge badge-info">
                                         <i class="fas fa-info-circle"></i> Pengaduan Anda
                                     </span>
                                 @endif
                             </div>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Pengaduan Awal -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge badge-info">Pengaduan</span>
                                            <strong>{{ $aduan->name }}</strong>
                                            <small class="float-right text-muted">
                                                <i class="fas fa-clock"></i> {{ $aduan->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="alert alert-light">
                                                {{ $aduan->complain }}
                                            </div>

                                            @if($aduan->foto)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $aduan->foto) }}"
                                                         alt="Foto Pengaduan"
                                                         class="img-fluid rounded shadow-sm"
                                                         style="max-width: 300px; cursor: pointer;"
                                                         onclick="openPhotoModal('{{ asset('storage/' . $aduan->foto) }}')">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Respon 1 -->
                                @if($aduan->respon)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge badge-success">Tanggapan Admin</span>
                                            <small class="float-right text-muted">
                                                <i class="fas fa-clock"></i> {{ $aduan->updated_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="alert alert-success">
                                                {{ $aduan->respon }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Pengaduan 2 -->
                                @if($aduan->complain2)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge badge-info">Pengaduan Lanjutan</span>
                                            <strong>{{ $aduan->name }}</strong>
                                            <small class="float-right text-muted">
                                                <i class="fas fa-clock"></i> {{ $aduan->updated_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="alert alert-light">
                                                {{ $aduan->complain2 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Respon 2 -->
                                @if($aduan->respon2)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge badge-success">Tanggapan Admin</span>
                                            <small class="float-right text-muted">
                                                <i class="fas fa-clock"></i> {{ $aduan->updated_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="alert alert-success">
                                                {{ $aduan->respon2 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Pengaduan 3 -->
                                @if($aduan->complain3)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge badge-info">Pengaduan Lanjutan</span>
                                            <strong>{{ $aduan->name }}</strong>
                                            <small class="float-right text-muted">
                                                <i class="fas fa-clock"></i> {{ $aduan->updated_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="alert alert-light">
                                                {{ $aduan->complain3 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Respon 3 -->
                                @if($aduan->respon3)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <span class="badge badge-success">Tanggapan Admin (Final)</span>
                                            <small class="float-right text-muted">
                                                <i class="fas fa-clock"></i> {{ $aduan->updated_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="alert alert-success">
                                                {{ $aduan->respon3 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 15px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    z-index: 1;
}

.timeline-content {
    background: #fff;
    padding: 0;
    border-radius: 8px;
}

.timeline-header {
    padding: 15px 20px 10px;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
    border-bottom: 1px solid #dee2e6;
}

.timeline-body {
    padding: 15px 20px;
}

.timeline-body .alert {
    margin-bottom: 0;
    border-radius: 6px;
}

.info-item {
    padding: 8px 0;
    border-bottom: 1px solid #f1f1f1;
}

.info-item:last-child {
    border-bottom: none;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.alert-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
</style>

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

<script>
function openPhotoModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    $('#photoModal').modal('show');
}
</script>

@endsection
