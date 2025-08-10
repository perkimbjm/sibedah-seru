@extends('layouts.main')

@section('styles')
    <x-leaflet></x-leaflet>
@endsection

@php
    $menuName = 'Tambah Usulan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Usulan RTLH</h3>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('usulan.proposals.store') }}" enctype="multipart/form-data" id="usulan-form">
            @csrf

            <!-- Identitas Diri -->
            <div class="mb-3 card">
                <div class="text-white card-header bg-primary">
                    <h5 class="mb-0"><i class="mr-2 fas fa-user"></i>Identitas Diri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama" class="required">Nama Lengkap</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                       id="nama" name="nama" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nik" class="required">NIK (16 digit)</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                       id="nik" name="nik" value="{{ old('nik') }}"
                                       maxlength="16" pattern="[0-9]{16}" required>
                                <div id="nik-feedback" class="invalid-feedback"></div>
                                @error('nik')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_kk" class="required">Nomor KK (16 digit)</label>
                                <input type="text" class="form-control @error('nomor_kk') is-invalid @enderror"
                                       id="nomor_kk" name="nomor_kk" value="{{ old('nomor_kk') }}"
                                       maxlength="16" pattern="[0-9]{16}" required>
                                <div id="kk-feedback" class="invalid-feedback"></div>
                                @error('nomor_kk')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_hp">Nomor HP</label>
                                <input type="tel" class="form-control @error('nomor_hp') is-invalid @enderror"
                                       id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp') }}"
                                       placeholder="628xxxxxxxxxx">
                                @error('nomor_hp')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Lokasi -->
            <div class="mb-3 card">
                <div class="text-white card-header bg-success">
                    <h5 class="mb-0"><i class="mr-2 fas fa-map-marker-alt"></i>Lokasi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="district_id" class="required">Kecamatan</label>
                                <select class="form-control @error('district_id') is-invalid @enderror"
                                        id="district_id" name="district_id" required>
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($districts as $id => $name)
                                        <option value="{{ $id }}" {{ old('district_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="village_id" class="required">Kelurahan/Desa</label>
                                <select class="form-control @error('village_id') is-invalid @enderror"
                                        id="village_id" name="village_id" required>
                                    <option value="">Pilih Kelurahan/Desa</option>
                                </select>
                                @error('village_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat_lengkap" class="required">Alamat Lengkap</label>
                        <textarea class="form-control @error('alamat_lengkap') is-invalid @enderror"
                                  id="alamat_lengkap" name="alamat_lengkap" rows="3" required
                                  placeholder="RT/RW, Nama Jalan, Nomor Rumah">{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Jenis Usulan -->
            <div class="mb-3 card">
                <div class="text-white card-header bg-warning">
                    <h5 class="mb-0"><i class="mr-2 fas fa-home"></i>Jenis Usulan</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="required">Pilih Jenis Usulan</label>
                        <div class="form-check">
                            <input class="form-check-input @error('jenis_usulan') is-invalid @enderror"
                                   type="radio" name="jenis_usulan" id="rtlh" value="RTLH"
                                   {{ old('jenis_usulan') == 'RTLH' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="rtlh">
                                <strong>RTLH (Rumah Tidak Layak Huni)</strong><br>
                                <small class="text-muted">Untuk rumah yang tidak memenuhi standar kelayakan huni</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('jenis_usulan') is-invalid @enderror"
                                   type="radio" name="jenis_usulan" id="bencana" value="Rumah Korban Bencana"
                                   {{ old('jenis_usulan') == 'Rumah Korban Bencana' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="bencana">
                                <strong>Rumah Korban Bencana</strong><br>
                                <small class="text-muted">Untuk rumah yang rusak akibat bencana alam</small>
                            </label>
                        </div>
                        @error('jenis_usulan')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Foto Rumah -->
            <div class="mb-3 card">
                <div class="text-white card-header bg-info">
                    <h5 class="mb-0"><i class="mr-2 fas fa-camera"></i>Foto Rumah (Opsional)</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="foto_rumah">Upload Foto Rumah</label>
                        <input type="file" class="form-control @error('foto_rumah') is-invalid @enderror"
                               id="foto_rumah" name="foto_rumah" accept="image/*">
                        <small class="form-text text-muted">
                            Format: JPG, PNG, WEBP. Maksimal 2MB. Foto akan membantu proses verifikasi.
                        </small>
                        @error('foto_rumah')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div id="foto-preview" class="mt-3" style="display: none;">
                        <img id="preview-img" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                </div>
            </div>

            <!-- Koordinat -->
            <div class="mb-3 card">
                <div class="text-white card-header bg-secondary">
                    <h5 class="mb-0"><i class="mr-2 fas fa-map"></i>Koordinat Lokasi (Opsional)</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mr-2 fas fa-info-circle"></i>
                        <strong>Petunjuk:</strong> Geser marker pada peta untuk menentukan koordinat lokasi rumah Anda.
                        Koordinat akan membantu tim verifikasi menemukan lokasi dengan tepat.
                    </div>

                    <div id="map" style="height: 400px; width: 100%;" class="mb-3"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                                @error('latitude')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
                                @error('longitude')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konfirmasi -->
            <div class="mb-3 card">
                <div class="text-white card-header bg-dark">
                    <h5 class="mb-0"><i class="mr-2 fas fa-check-circle"></i>Konfirmasi</h5>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="konfirmasi" required>
                        <label class="form-check-label" for="konfirmasi">
                            Saya menyatakan bahwa data yang saya berikan adalah benar dan dapat dipertanggungjawabkan.
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="mr-2 fas fa-paper-plane"></i>Kirim Usulan
                </button>
                <a href="{{ route('usulan.proposals.index') }}" class="btn btn-secondary">
                    <i class="mr-2 fas fa-arrow-left"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('after-script')
<script>
$(document).ready(function() {
    // Initialize map - Paringin, Balangan coordinates
    var map = L.map('map').setView([-2.3357, 115.474], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([-2.3357, 115.474], {draggable: true}).addTo(map);

    // Update coordinates when marker is dragged
    marker.on('dragend', function(event) {
        var position = event.target.getLatLng();
        $('#latitude').val(position.lat.toFixed(8));
        $('#longitude').val(position.lng.toFixed(8));
    });

    // Load villages when district changes
    $('#district_id').change(function() {
        var districtId = $(this).val();
        var villageSelect = $('#village_id');

        villageSelect.html('<option value="">Pilih Kelurahan/Desa</option>');

        if (districtId) {
            $.get('{{ route("usulan.villages-by-district") }}', {district_id: districtId})
                .done(function(data) {
                    data.forEach(function(village) {
                        villageSelect.append('<option value="' + village.id + '">' + village.name + '</option>');
                    });
                })
                .fail(function() {
                    alert('Gagal memuat data kelurahan/desa');
                });
        }
    });

    // Real-time NIK validation
    $('#nik').on('blur', function() {
        var nik = $(this).val();
        if (nik.length === 16) {
            $.post('{{ route("usulan.validate-nik") }}', {
                nik: nik,
                _token: '{{ csrf_token() }}'
            })
            .done(function(data) {
                if (data.valid) {
                    $('#nik').removeClass('is-invalid').addClass('is-valid');
                    $('#nik-feedback').removeClass('invalid-feedback').addClass('valid-feedback').text(data.message);
                } else {
                    $('#nik').removeClass('is-valid').addClass('is-invalid');
                    $('#nik-feedback').removeClass('valid-feedback').addClass('invalid-feedback').text(data.message);
                }
            })
            .fail(function() {
                $('#nik').removeClass('is-valid is-invalid');
                $('#nik-feedback').removeClass('valid-feedback invalid-feedback').text('');
            });
        }
    });

    // Real-time KK validation
    $('#nomor_kk').on('blur', function() {
        var kk = $(this).val();
        if (kk.length === 16) {
            $.post('{{ route("usulan.validate-kk") }}', {
                nomor_kk: kk,
                _token: '{{ csrf_token() }}'
            })
            .done(function(data) {
                if (data.valid) {
                    $('#nomor_kk').removeClass('is-invalid').addClass('is-valid');
                    $('#kk-feedback').removeClass('invalid-feedback').addClass('valid-feedback').text(data.message);
                } else {
                    $('#nomor_kk').removeClass('is-valid').addClass('is-invalid');
                    $('#kk-feedback').removeClass('valid-feedback').addClass('invalid-feedback').text(data.message);
                }
            })
            .fail(function() {
                $('#nomor_kk').removeClass('is-valid is-invalid');
                $('#kk-feedback').removeClass('valid-feedback invalid-feedback').text('');
            });
        }
    });

    // Photo preview
    $('#foto_rumah').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#foto-preview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#foto-preview').hide();
        }
    });

    // Form submission
    $('#usulan-form').submit(function(e) {
        if (!$('#konfirmasi').is(':checked')) {
            e.preventDefault();
            alert('Anda harus menyetujui pernyataan konfirmasi terlebih dahulu.');
            return false;
        }

        $('#submit-btn').prop('disabled', true).html('<i class="mr-2 fas fa-spinner fa-spin"></i>Mengirim...');
    });
});
</script>
@endpush
