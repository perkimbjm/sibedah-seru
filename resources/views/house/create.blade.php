@extends('layouts.main')

@section('styles')
    <x-leaflet></x-leaflet>
@endsection

@php
    $menuName = 'Penerima Bantuan Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        Tambah Data Bedah Rumah
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("app.houses.store") }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label class="required" for="nik">NIK</label>
                <input class="form-control {{ $errors->has('nik') ? 'is-invalid' : '' }}" type="number" name="nik" id="nik" value="{{ old('nik', '') }}" required pattern="[0-9]{16}" placeholder="Isi Nomor Identitas yang Valid (16 digit)">
                <small id="nikAlert" class="text-success" style="display:none;">NIK ada di RTLH</small>
            
                @if($errors->has('nik'))
                    <span class="text-danger">{{ $errors->first('nik') }}</span>
                @endif
            </div>
            
            <!-- Tombol Isi Data -->
            <button type="button" class="btn btn-sm btn-info mb-3" id="fillDataButton" style="display:none;">Isi Data</button>

            <div class="form-group">
                <label class="required" for="name">Nama</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required placeholder="Masukkan nama Penerima Bantuan">
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
                <label class="required" for="year">Tahun</label>
                <input class="form-control {{ $errors->has('year') ? 'is-invalid' : '' }}" type="number" name="year" id="year" value="{{ old('year', date('Y')) }}" required min="2020" max="2029" oninput="this.value = this.value.slice(0, 4);">
                @if($errors->has('year'))
                    <span class="text-danger">{{ $errors->first('year') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="type">Jenis Bantuan</label>
                <input class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" type="text" name="type" id="type" value="{{ old('type', '') }}" required placeholder="Jenis Bantuan / Kegiatan">
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="source">Sumber Dana</label>
                <input class="form-control {{ $errors->has('source') ? 'is-invalid' : '' }}" type="text" name="source" id="source" value="{{ old('source', '') }}" required placeholder="Contoh : APBD / APBN / APBD Prov / CSR/ Lainnya">
                @if($errors->has('source'))
                    <span class="text-danger">{{ $errors->first('source') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="note">Catatan</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note" placeholder="Catatan atau keterangan tambahan, jika ada">{{ old('note', '') }}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
            </div>

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
        url: "{{ route('app.houses.getKecamatan') }}?village_id=" + selectedValue,
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

$(document).ready(function() {
    $('#nik').on('input', function() {
        let nik = $(this).val();

        // Hanya lakukan pengecekan jika NIK memiliki 16 karakter
        if (nik.length === 16) {
            $.ajax({
                url: '{{ route("app.houses.checkNIK") }}',
                type: 'GET',
                data: { nik: nik },
                success: function(response) {
                    if (response.exists) {
                        $('#nikAlert').show();
                        $('#fillDataButton').show();

                        $('#fillDataButton').data('houseData', response.house);
                    } else {
                      
                        $('#nikAlert').hide();
                        $('#fillDataButton').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                }
            });
        } else {
            $('#nikAlert').hide();
            $('#fillDataButton').hide();
        }
    });

    $('#fillDataButton').click(function() {
        let houseData = $(this).data('houseData');

        $('#name').val(houseData.name);
        $('#address').val(houseData.address);
        $('#lat').val(houseData.lat);
        $('#lng').val(houseData.lng);
    });
});
</script>
    <x-coordinate></x-coordinate>
@endsection

