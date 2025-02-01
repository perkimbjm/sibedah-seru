@extends('layouts.main')

@php
    $menuName = 'Import Data';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Import Data Bedah Rumah</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('app.houses.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">File Excel</label>
                            <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" name="file" required>
                            <small class="form-text text-muted">
                                Format yang didukung: .xlsx, .xls<br>
                                Kolom yang wajib ada dan diisi : NAMA, NIK, ALAMAT, KECAMATAN, DESA, LATITUDE, LONGITUDE, TAHUN, JENIS BANTUAN
                            </small>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Import</button>
                            <a href="{{ route('app.houses.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 card">
                <div class="card-header">
                    <h5 class="mb-0">Format Excel</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="{{ route('app.houses.download-template') }}" class="btn btn-success">Download Template Excel</a>
                    </div>
                    <div class="mb-1">Contoh Pengisian :</div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NAMA</th>
                                    <th>NIK</th>
                                    <th>ALAMAT</th>
                                    <th>KECAMATAN</th>
                                    <th>DESA</th>
                                    <th>LATITUDE</th>
                                    <th>LONGITUDE</th>
                                    <th>TAHUN</th>
                                    <th>JENIS BANTUAN</th>
                                    <th>SUMBER</th>
                                    <th>CATATAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>AHMAD MUHAMMAD</td>
                                    <td>6311033456789012</td>
                                    <td>Tebing Tinggi RT. 03 No. 123</td>
                                    <td>Tebing Tinggi</td>
                                    <td>Dayak Pitap</td>
                                    <td>-2.123456</td>
                                    <td>115.123456</td>
                                    <td>2024</td>
                                    <td>BRS</td>
                                    <td>APBD</td>
                                    <td>Perlu Perbaikan Koordinat</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
