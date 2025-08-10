@extends('layouts.main')

@php
    $menuName = 'Lihat Detail Usulan Masyarakat';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('styles')
    <!-- Pastikan CSS Leaflet dimuat dengan benar -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <style>
        /* Pastikan container peta memiliki tinggi yang pasti */
        #map {
            height: 400px !important;
            width: 100% !important;
            z-index: 1;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Usulan</h3>
                    <div class="card-tools">
                        @can('usulan_edit')
                            <a href="{{ route('usulan.proposals.edit', $usulan->id) }}" class="btn btn-primary btn-sm">
                                <i class="mr-1 fas fa-edit"></i>Edit
                            </a>
                        @endcan
                        <a href="{{ route('usulan.proposals.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mr-1 fas fa-arrow-left"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Lengkap:</strong></td>
                                    <td>{{ $usulan->nama }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIK:</strong></td>
                                    <td>{{ $usulan->nik }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor KK:</strong></td>
                                    <td>{{ $usulan->nomor_kk }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor HP:</strong></td>
                                    <td>{{ $usulan->nomor_hp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kecamatan:</strong></td>
                                    <td>{{ $usulan->district->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kelurahan/Desa:</strong></td>
                                    <td>{{ $usulan->village->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat Lengkap:</strong></td>
                                    <td>{{ $usulan->alamat_lengkap }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Jenis Usulan:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $usulan->jenis_usulan == 'RTLH' ? 'primary' : 'secondary' }}">
                                            {{ $usulan->jenis_usulan_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $usulan->status == 'pending' ? 'warning' : ($usulan->status == 'verified' ? 'info' : ($usulan->status == 'accepted' ? 'success' : 'danger')) }}">
                                            {{ $usulan->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Usulan:</strong></td>
                                    <td>{{ $usulan->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Diupdate:</strong></td>
                                    <td>{{ $usulan->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($usulan->latitude && $usulan->longitude)
                                <tr>
                                    <td><strong>Koordinat:</strong></td>
                                    <td>{{ $usulan->latitude }}, {{ $usulan->longitude }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($usulan->foto_rumah)
                    <div class="mt-3 row">
                        <div class="col-12">
                            <h5>Foto Rumah:</h5>
                            <img src="{{ $usulan->foto_rumah_url }}" alt="Foto Rumah" class="img-fluid" style="max-width: 400px;">
                        </div>
                    </div>
                    @endif

                    @if($usulan->latitude && $usulan->longitude)
                    <div class="mt-3 row">
                        <div class="col-12">
                            <h5>Lokasi di Peta:</h5>
                            <div id="map"></div>
                        </div>
                    </div>
                    @endif

                    @if($usulan->verifikasi)
                    <div class="mt-3 row">
                        <div class="col-12">
                            <h5>Informasi Verifikasi:</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="200"><strong>Verifikator:</strong></td>
                                                    <td>{{ $usulan->verifikasi->verifikator->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Hasil Verifikasi:</strong></td>
                                                    <td>
                                                        <span class="badge badge-{{ $usulan->verifikasi->hasil_verifikasi == 'diterima' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($usulan->verifikasi->hasil_verifikasi) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal Verifikasi:</strong></td>
                                                    <td>{{ $usulan->verifikasi->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Kriteria Verifikasi:</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-{{ $usulan->verifikasi->kesesuaian_tata_ruang ? 'check text-success' : 'times text-danger' }}"></i> Kesesuaian Tata Ruang</li>
                                                <li><i class="fas fa-{{ $usulan->verifikasi->tidak_dalam_sengketa ? 'check text-success' : 'times text-danger' }}"></i> Tidak Dalam Sengketa</li>
                                                <li><i class="fas fa-{{ $usulan->verifikasi->memiliki_alas_hak ? 'check text-success' : 'times text-danger' }}"></i> Memiliki Alas Hak</li>
                                                <li><i class="fas fa-{{ $usulan->verifikasi->satu_satunya_rumah ? 'check text-success' : 'times text-danger' }}"></i> Satu-satunya Rumah</li>
                                                <li><i class="fas fa-{{ $usulan->verifikasi->belum_pernah_bantuan ? 'check text-success' : 'times text-danger' }}"></i> Belum Pernah Bantuan</li>
                                                <li><i class="fas fa-{{ $usulan->verifikasi->berpenghasilan_rendah ? 'check text-success' : 'times text-danger' }}"></i> Berpenghasilan Rendah</li>
                                            </ul>
                                        </div>
                                    </div>
                                    @if($usulan->verifikasi->catatan_verifikator)
                                    <div class="mt-3 row">
                                        <div class="col-12">
                                            <h6>Catatan Verifikator:</h6>
                                            <p class="text-muted">{{ $usulan->verifikasi->catatan_verifikator }}</p>
                                        </div>
                                    </div>
                                    @endif
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
@endsection

@section('scripts')
<!-- Pastikan JavaScript Leaflet dimuat -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

@if($usulan->latitude && $usulan->longitude)
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');

    // Fungsi untuk inisialisasi peta
    function initializeMap() {
        console.log('Attempting to initialize map...');

        // Periksa apakah elemen map ada
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            return false;
        }

        // Periksa apakah Leaflet sudah dimuat
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            return false;
        }

        try {
            // Koordinat dari database
            const lat = parseFloat({{ $usulan->latitude }});
            const lng = parseFloat({{ $usulan->longitude }});

            console.log('Coordinates:', lat, lng);

            // Validasi koordinat
            if (isNaN(lat) || isNaN(lng)) {
                console.error('Invalid coordinates:', lat, lng);
                return false;
            }

            // Hapus instance peta yang sudah ada
            if (window.usulanMap) {
                window.usulanMap.remove();
                delete window.usulanMap;
            }

            // Buat peta baru
            const map = L.map('map', {
                center: [lat, lng],
                zoom: 15,
                zoomControl: true,
                attributionControl: true
            });

            // Simpan referensi global
            window.usulanMap = map;

            // Tambahkan tile layer
            const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            });

            tileLayer.addTo(map);

            // Tambahkan marker
            const marker = L.marker([lat, lng])
                .addTo(map)
                .bindPopup(`
                    <div style="min-width: 200px;">
                        <strong>{{ addslashes($usulan->nama) }}</strong><br>
                        <small>{{ addslashes($usulan->alamat_lengkap) }}</small><br>
                        <em>Koordinat: ${lat}, ${lng}</em>
                    </div>
                `)
                .openPopup();

            // Force resize setelah beberapa saat
            setTimeout(function() {
                map.invalidateSize();
                console.log('Map resized');
            }, 100);

            // Event listener untuk resize window
            window.addEventListener('resize', function() {
                setTimeout(function() {
                    if (window.usulanMap) {
                        window.usulanMap.invalidateSize();
                    }
                }, 100);
            });

            console.log('Map initialized successfully');
            return true;

        } catch (error) {
            console.error('Error initializing map:', error);

            // Tampilkan pesan error di container map
            mapContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Gagal memuat peta. Error: ${error.message}
                </div>
            `;
            return false;
        }
    }

    // Coba inisialisasi dengan beberapa attempt
    let attempts = 0;
    const maxAttempts = 10;

    function tryInitialize() {
        attempts++;
        console.log(`Initialization attempt ${attempts}/${maxAttempts}`);

        if (initializeMap()) {
            console.log('Map initialization successful');
            return;
        }

        if (attempts < maxAttempts) {
            setTimeout(tryInitialize, 200);
        } else {
            console.error('Failed to initialize map after maximum attempts');

            // Tampilkan fallback
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-map-marked-alt"></i>
                        Peta tidak dapat dimuat. Koordinat lokasi: {{ $usulan->latitude }}, {{ $usulan->longitude }}
                        <br>
                        <a href="https://www.google.com/maps?q={{ $usulan->latitude }},{{ $usulan->longitude }}"
                           target="_blank" class="mt-2 btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt"></i> Buka di Google Maps
                        </a>
                    </div>
                `;
            }
        }
    }

    // Mulai inisialisasi setelah delay singkat
    setTimeout(tryInitialize, 500);
});
</script>
@endif
@endsection
