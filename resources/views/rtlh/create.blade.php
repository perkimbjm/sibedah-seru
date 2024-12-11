@extends('layouts.main')

@section('styles')
    <x-leaflet></x-leaflet>
@endsection

@php
    $menuName = 'RTLH Kab. Balangan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        Tambah Data RTLH
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("app.rtlh.store") }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Identitas Diri dan Lokasi Rumah -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">Identitas Diri dan Lokasi Rumah</div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="required" for="nik">NIK</label>
                        <input class="form-control {{ $errors->has('nik') ? 'is-invalid' : '' }}" type="number" name="nik" id="nik" value="{{ old('nik', '') }}" required pattern="[0-9]{16}" placeholder="Isi Nomor Identitas yang Valid (16 digit)">           
                        @if($errors->has('nik'))
                            <span class="text-danger">{{ $errors->first('nik') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required" for="kk">KK</label>
                        <input class="form-control {{ $errors->has('kk') ? 'is-invalid' : '' }}" type="number" name="kk" id="kk" value="{{ old('kk', '') }}" required pattern="[0-9]" placeholder="Isi Nomor Kartu Keluarga yang Valid">           
                        @if($errors->has('kk'))
                            <span class="text-danger">{{ $errors->first('kk') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required" for="name">Nama</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required placeholder="Masukkan Nama">
                        @if($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required" for="address">Alamat</label>
                        <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" value="{{ old('address', '') }}" required placeholder="Masukkan Alamat (dengan nama desa)">
                        @if($errors->has('address'))
                            <span class="text-danger">{{ $errors->first('address') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required" for="village_id">Kel / Desa</label>
                        <select class="form-control select2 {{ $errors->has('village_id') ? 'is-invalid' : '' }}" name="village_id" id="village_id" required>
                            @foreach($villages as $id => $village)
                                <option value="{{ $id }}" {{ old('village_id') == $id? 'selected' : '' }}>{{ $village }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('village_id'))
                            <span class="text-danger">{{ $errors->first('village_id') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required" for="district_id">Kecamatan</label>
                        <input type="text" class="form-control" id="district_name" readonly>
                        <input type="hidden" name="district_id" id="district_id" required>
                    </div>

                    <label>Geser Marker pada Peta</label>
                    <div id="mapid" class="mb-3 mx-auto" style="min-height: 350px"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lat" class="control-label">Latitude</label>
                                <input class="form-control {{ $errors->has('lat') ? 'is-invalid' : '' }}" type="text" name="lat" id="lat" value="{{ old('lat', request('lat')) }}" onfocus="this.value='-2.';">
                                @if($errors->has('lat'))
                                    <span class="text-danger">{{ $errors->first('lat') }}</span>
                                @endif
                                <span class="help-block text-sm">Masukkan garis Latitude / Lintang yang benar (format -2.xxx)</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lng" class="control-label">Longitude</label>
                                <input class="form-control {{ $errors->has('lng') ? 'is-invalid' : '' }}" type="text" name="lng" id="lng" value="{{ old('lng', request('lng')) }}" onfocus="this.value='115.';">
                                @if($errors->has('lng'))
                                    <span class="text-danger">{{ $errors->first('lng') }}</span>
                                @endif
                                <span class="help-block text-sm">Masukkan garis Longitude / Bujur yang benar (format 115.xxx)</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required" for="people">Jumlah Penghuni</label>
                        <input class="form-control {{ $errors->has('people') ? 'is-invalid' : '' }}" type="number" name="people" id="people" value="{{ old('people', '') }}" required>
                        @if($errors->has('people'))
                            <span class="text-danger">{{ $errors->first('people') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required" for="area">Luas Bangunan Rumah (p x l x jumlah lantai)</label>
                        <input class="form-control {{ $errors->has('area') ? 'is-invalid' : '' }}" type="number" name="area" id="area" value="{{ old('area', '') }}" required>
                        @if($errors->has('area'))
                            <span class="text-danger">{{ $errors->first('area') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Struktural -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">Struktural</div>
                <div class="card-body">
                    <x-condition-select name="pondasi" label="Pondasi" :options="$kelayakanOptions" :errors="$errors" />
                    <x-condition-select name="kolom_blk" label="Kolom / Balok" :options="$kelayakanOptions" :errors="$errors" />
                    <x-condition-select name="rngk_atap" label="Rangka Atap" :options="$kelayakanOptions" :errors="$errors" />
                </div>
            </div>

            <!-- Non Struktural -->
            <div class="card mb-3">
                <div class="card-header bg-warning text-white">Non Struktural</div>
                <div class="card-body">
                    <x-condition-select name="atap" label="Atap" :options="$kelayakanOptions" :errors="$errors" />
                    <x-condition-select name="dinding" label="Dinding" :options="$kelayakanOptions" :errors="$errors" />
                    <x-condition-select name="lantai" label="Lantai" :options="$kelayakanOptions" :errors="$errors" />
                </div>
            </div>

            <!-- Air Bersih -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Air Bersih</div>
                <div class="card-body">
                    <x-condition-select name="air" label="Sumber Air Minum" :options="$airOptions" :errors="$errors" />
                    <x-condition-select name="jarak_tinja" label="Jarak Sumber Air Minum ke TPA Tinja" :options="$jarakTinjaOptions" :errors="$errors" />
                </div>
            </div>

            <!-- Sanitasi -->
            <div class="card mb-3">
                <div class="card-header bg-danger text-white">Sanitasi</div>
                <div class="card-body">
                    <x-condition-select name="wc" label="Kepemilikan WC" :options="$wcOptions" :errors="$errors" />
                    <x-condition-select name="jenis_wc" label="Jenis Kloset / WC" :options="$jenisWcOptions" :errors="$errors" />
                    <x-condition-select name="tpa_tinja" label="TPA Tinja" :options="$tpaTinjaOptions" :errors="$errors" />
                </div>
            </div>

            <!-- Hasil Penilaian -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Hasil Penilaian</div>
                <div class="card-body">
                    <x-condition-select name="status_safety" label="Hasil Penilaian Keselamatan Bangunan" :options="$kelayakanOptions" :errors="$errors" uppercase="true" />
                    <x-condition-select name="status" label="Hasil Penilaian Seluruh Aspek" :options="$kelayakanOptions" :errors="$errors" uppercase="true" />
                </div>
            </div>

            <!-- Catatan -->
            <div class="form-group">
                <label for="note">Catatan</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note" placeholder="Catatan atau keterangan tambahan, jika ada">{{ old('note', '') }}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
            </div>

            <!-- Tombol Submit -->
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    Simpan
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-link">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $("#village_id").change(function(){
    let selectedValue = $(this).val();
    $.ajax({
        url: "{{ route('app.rtlh.getKecamatan') }}?village_id=" + selectedValue,
        type: 'GET',
        success: function (data) {
            if (data.district_name) {
                $('#district_name').val(data.district_name);
                $('#district_id').val(data.district_id);
            } else {
                $('#district_name').val('');
                $('#district_id').val('');
                alert('Kecamatan tidak ditemukan.');
            }
        },
        error: function (xhr, status, error) {
            console.log('Terjadi kesalahan: ' + error);
        }
    });
});
</script>
    <x-coordinate></x-coordinate>
@endsection
