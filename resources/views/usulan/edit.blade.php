@extends('layouts.main')

@php
    $menuName = 'Edit Usulan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Usulan</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('usulan.proposals.update', $usulan->id) }}" enctype="multipart/form-data" id="usulan-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap *</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                           id="nama" name="nama" value="{{ old('nama', $usulan->nama) }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nik">NIK (16 digit) *</label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                           id="nik" name="nik" value="{{ old('nik', $usulan->nik) }}"
                                           maxlength="16" pattern="[0-9]{16}" required>
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_kk">Nomor KK (16 digit) *</label>
                                    <input type="text" class="form-control @error('nomor_kk') is-invalid @enderror"
                                           id="nomor_kk" name="nomor_kk" value="{{ old('nomor_kk', $usulan->nomor_kk) }}"
                                           maxlength="16" pattern="[0-9]{16}" required>
                                    @error('nomor_kk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_hp">Nomor HP</label>
                                    <input type="tel" class="form-control @error('nomor_hp') is-invalid @enderror"
                                           id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $usulan->nomor_hp) }}"
                                           placeholder="628xxxxxxxxxx">
                                    @error('nomor_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="district_id">Kecamatan *</label>
                                    <select class="form-control @error('district_id') is-invalid @enderror"
                                            id="district_id" name="district_id" required>
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($districts as $id => $name)
                                            <option value="{{ $id }}" {{ old('district_id', $usulan->district_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('district_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="village_id">Kelurahan/Desa *</label>
                                    <select class="form-control @error('village_id') is-invalid @enderror"
                                            id="village_id" name="village_id" required>
                                        <option value="">Pilih Kelurahan/Desa</option>
                                        @foreach($villages as $id => $name)
                                            <option value="{{ $id }}" {{ old('village_id', $usulan->village_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('village_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat_lengkap">Alamat Lengkap *</label>
                            <textarea class="form-control @error('alamat_lengkap') is-invalid @enderror"
                                      id="alamat_lengkap" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap', $usulan->alamat_lengkap) }}</textarea>
                            @error('alamat_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Jenis Usulan *</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_usulan"
                                       id="jenis_rtlh" value="RTLH"
                                       {{ old('jenis_usulan', $usulan->jenis_usulan) == 'RTLH' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="jenis_rtlh">
                                    RTLH (Rumah Tidak Layak Huni)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_usulan"
                                       id="jenis_bencana" value="Rumah Korban Bencana"
                                       {{ old('jenis_usulan', $usulan->jenis_usulan) == 'Rumah Korban Bencana' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="jenis_bencana">
                                    Rumah Korban Bencana
                                </label>
                            </div>
                            @error('jenis_usulan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="foto_rumah">Foto Rumah (Opsional)</label>
                            <input type="file" class="form-control @error('foto_rumah') is-invalid @enderror"
                                   id="foto_rumah" name="foto_rumah" accept="image/*">
                            @if($usulan->foto_rumah)
                                <small class="form-text text-muted">
                                    Foto saat ini: <a href="{{ $usulan->foto_rumah_url }}" target="_blank">Lihat Foto</a>
                                </small>
                            @endif
                            @error('foto_rumah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                           id="latitude" name="latitude" value="{{ old('latitude', $usulan->latitude) }}"
                                           placeholder="-2.123456">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                           id="longitude" name="longitude" value="{{ old('longitude', $usulan->longitude) }}"
                                           placeholder="115.123456">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="map">Peta Lokasi (Opsional)</label>
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            <small class="form-text text-muted">Klik pada peta untuk mengatur koordinat atau gunakan tombol "Share Lokasi"</small>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-info" onclick="getCurrentLocation()">
                                <i class="mr-2 fas fa-location-arrow"></i>Share Lokasi
                            </button>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mr-2 fas fa-save"></i>Update Usulan
                            </button>
                            <a href="{{ route('usulan.proposals.index') }}" class="btn btn-secondary">
                                <i class="mr-2 fas fa-arrow-left"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
let map, marker;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map - Paringin, Balangan coordinates as default
    map = L.map('map').setView([{{ $usulan->latitude ?? -2.3357 }}, {{ $usulan->longitude ?? 115.474 }}], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add marker if coordinates exist
    @if($usulan->latitude && $usulan->longitude)
        marker = L.marker([{{ $usulan->latitude }}, {{ $usulan->longitude }}]).addTo(map);
    @endif

    // Handle map clicks
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }
    });

    // Handle district change
    document.getElementById('district_id').addEventListener('change', function() {
        const districtId = this.value;
        const villageSelect = document.getElementById('village_id');

        // Clear villages
        villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';

        if (districtId) {
            fetch(`/usulan/villages-by-district?district_id=${districtId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(village => {
                        const option = document.createElement('option');
                        option.value = village.id;
                        option.textContent = village.name;
                        villageSelect.appendChild(option);
                    });
                });
        }
    });

    // Real-time NIK validation
    document.getElementById('nik').addEventListener('blur', function() {
        const nik = this.value;
        if (nik.length === 16) {
            fetch('/usulan/validate-nik', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nik: nik })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    this.setCustomValidity(data.message);
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });

    // Real-time KK validation
    document.getElementById('nomor_kk').addEventListener('blur', function() {
        const kk = this.value;
        if (kk.length === 16) {
            fetch('/usulan/validate-kk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nomor_kk: kk })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    this.setCustomValidity(data.message);
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });
});

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }

            map.setView([lat, lng], 15);
        }, function(error) {
            alert('Tidak dapat mendapatkan lokasi: ' + error.message);
        });
    } else {
        alert('Geolokasi tidak didukung oleh browser ini.');
    }
}
</script>
@endpush
