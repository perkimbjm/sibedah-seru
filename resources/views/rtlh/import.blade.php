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
                    <h5 class="mb-0">Import Data RTLH</h5>
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

                    <form action="{{ route('app.rtlh.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">File Excel</label>
                            <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" name="file" required>
                            <small class="form-text text-muted">
                                Format yang didukung: .xlsx, .xls<br>
                                Kolom yang wajib ada dan diisi : NAMA, NIK, KK, KECAMATAN, DESA, LATITUDE, LONGITUDE
                            </small>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Import</button>
                            <a href="{{ route('app.rtlh.index') }}" class="btn btn-secondary">Kembali</a>
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
                        <a href="{{ asset('storage/template/template_import_rtlh.xlsx') }}" class="btn btn-success">Download Template Excel</a>
                    </div>
                    <div class="form-text">
                        <strong>Ketentuan Pengisian Kolom:</strong><br>
                        <ul>
                            <li><strong>Pondasi, Kolom atau Balok, Rangka Atap, Atap, Dinding, dan Lantai :</strong> Harus Diisi salah satu dari <strong>Layak, Kurang Layak, Tidak Layak</strong>.</li>
                            <li><strong>Sumber Air Bersih:</strong> Pilih salah satu dari <strong>Air Kemasan, Pamsimas, Mata Air, PDAM, Air Hujan, Sungai, Sumur, Lainnya</strong>.</li>
                            <li><strong>Jarak ke Pembuang Tinja:</strong> Pilih salah satu dari <strong>≥ 10 Meter, ≤ 10 Meter</strong>.</li>
                            <li><strong>Fasilitas BAB:</strong> Pilih salah satu dari <strong>Tidak Ada, Bersama, Milik Sendiri</strong>.</li>
                            <li><strong>Jenis :</strong> Pilih salah satu dari <strong>Tidak Ada, Plengsengan, Leher Angsa, Cemplung/Cubluk</strong>.</li>
                            <li><strong>TPA Tinja:</strong> Pilih salah satu dari <strong>Tangki Septik, Lubang Tanah, IPAL, Kolam/Sawah/Sungai/Danau/Laut, Pantai/Tanah Lapang/Kebun</strong>.</li>
                            <li><strong>Status Penilaian :</strong> Harus Diisi (huruf besar semua) salah satu dari <strong>LAYAK, MENUJU LAYAK, KURANG LAYAK, TIDAK LAYAK</strong>.</li>
                        </ul>
                        <blockquote>Perhatikan penulisan huruf kapital. Jika tidak diisi sesuai ketentuan, berpotensi gagal impor data</blockquote>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
