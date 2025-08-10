@extends('layouts.main')

@php
    $menuName = 'Edit Verifikasi Usulan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Verifikasi untuk Usulan: <strong>{{ $verifikasi->usulan->nama }}</strong> (NIK: {{ $verifikasi->usulan->nik }})</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('usulan.verifikasi-management.verifikasi.update', $verifikasi->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informasi Usulan -->
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="mr-2 fas fa-user-check"></i>Informasi Pengusul</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-4">Nama</dt>
                                            <dd class="col-sm-8">{{ $verifikasi->usulan->nama }}</dd>

                                            <dt class="col-sm-4">NIK</dt>
                                            <dd class="col-sm-8">{{ $verifikasi->usulan->nik }}</dd>

                                            <dt class="col-sm-4">Alamat</dt>
                                            <dd class="col-sm-8">{{ $verifikasi->usulan->alamat_lengkap }}, {{ $verifikasi->usulan->village->name }}, {{ $verifikasi->usulan->district->name }}</dd>

                                            <dt class="col-sm-4">Jenis Usulan</dt>
                                            <dd class="col-sm-8">{{ $verifikasi->usulan->jenis_usulan_label }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <!-- Kriteria Verifikasi -->
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="mr-2 fas fa-tasks"></i>Kriteria Verifikasi</h3>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $criteria = [
                                                'kesesuaian_tata_ruang' => 'Kesesuaian dengan Tata Ruang',
                                                'tidak_dalam_sengketa' => 'Tidak dalam Sengketa',
                                                'memiliki_alas_hak' => 'Memiliki Alas Hak yang Sah',
                                                'satu_satunya_rumah' => 'Merupakan Satu-satunya Rumah',
                                                'belum_pernah_bantuan' => 'Belum Pernah Menerima Bantuan Sejenis',
                                                'berpenghasilan_rendah' => 'Termasuk Masyarakat Berpenghasilan Rendah (MBR)',
                                            ];
                                        @endphp
                                        @foreach($criteria as $key => $label)
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="{{ $key }}" value="0">
                                                    <input type="checkbox" class="custom-control-input kriteria-checkbox" id="{{ $key }}" name="{{ $key }}" value="1" {{ old($key, $verifikasi->$key) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $key }}">{{ $label }}</label>
                                                </div>
                                            </div>
                                        @endforeach


                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hasil Verifikasi -->
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="mr-2 fas fa-clipboard-check"></i>Hasil Verifikasi</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="required">Hasil Verifikasi</label>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> <strong>Hasil verifikasi akan ditentukan otomatis berdasarkan kriteria yang dipilih di atas.</strong>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="diterima" value="diterima" {{ old('hasil_verifikasi', $verifikasi->hasil_verifikasi) == 'diterima' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label text-success" for="diterima">
                                            <strong>Diterima</strong> (Jika semua kriteria terpenuhi)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="ditolak" value="belum_memenuhi_syarat" {{ old('hasil_verifikasi', $verifikasi->hasil_verifikasi) == 'belum_memenuhi_syarat' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label text-danger" for="ditolak">
                                            <strong>Ditolak</strong> (Jika ada kriteria yang tidak terpenuhi)
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="catatan_verifikator" class="required">Catatan Verifikator</label>
                                    <textarea class="form-control @error('catatan_verifikator') is-invalid @enderror" name="catatan_verifikator" id="catatan_verifikator" rows="4" placeholder="Berikan catatan jika usulan ditolak atau ada informasi tambahan" required>{{ old('catatan_verifikator', $verifikasi->catatan_verifikator) }}</textarea>
                                    @error('catatan_verifikator')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mr-2 fas fa-save"></i>Update Verifikasi
                            </button>
                            <a href="{{ route('usulan.verifikasi-management.verifikasi.index') }}" class="btn btn-secondary">
                                <i class="mr-2 fas fa-arrow-left"></i>Batal
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
<script>
$(document).ready(function() {
    // Fungsi untuk update status verifikasi berdasarkan kriteria
    function updateVerifikasiStatus() {
        const criteria = [
            'kesesuaian_tata_ruang',
            'tidak_dalam_sengketa',
            'memiliki_alas_hak',
            'satu_satunya_rumah',
            'belum_pernah_bantuan',
            'berpenghasilan_rendah'
        ];

        let allCriteriaMet = true;
        let uncheckedCriteria = [];

        criteria.forEach(function(criterion) {
            const checkbox = $('#' + criterion);
            if (!checkbox.is(':checked')) {
                allCriteriaMet = false;
                uncheckedCriteria.push(criterion);
            }
        });

        // Update tampilan radio button
        if (allCriteriaMet) {
            $('#diterima').prop('checked', true);
            $('#ditolak').prop('checked', false);
            $('.alert-info').removeClass('alert-warning alert-danger').addClass('alert-success')
                .html('<i class="fas fa-check-circle"></i> <strong>Status: DITERIMA</strong> - Semua kriteria terpenuhi.');
        } else {
            $('#diterima').prop('checked', false);
            $('#ditolak').prop('checked', true);
            $('.alert-info').removeClass('alert-success alert-warning').addClass('alert-danger')
                .html('<i class="fas fa-times-circle"></i> <strong>Status: DITOLAK</strong> - Kriteria yang tidak terpenuhi: ' + uncheckedCriteria.join(', '));
        }
    }

    // Event listener untuk checkbox kriteria
    $('input[type="checkbox"]').on('change', updateVerifikasiStatus);

    // Update status saat halaman dimuat
    updateVerifikasiStatus();

    // Pastikan form mengirim data dengan benar
    $('form').on('submit', function(e) {
        // Hapus semua localStorage yang mungkin menyimpan state checkbox
        const criteria = [
            'kesesuaian_tata_ruang',
            'tidak_dalam_sengketa',
            'memiliki_alas_hak',
            'satu_satunya_rumah',
            'belum_pernah_bantuan',
            'berpenghasilan_rendah'
        ];

        criteria.forEach(function(criterion) {
            localStorage.removeItem('verifikasi_' + criterion);
        });

        // Log form data untuk debug
        console.log('Form submitting with data:');
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    });
});
</script>
@endpush

