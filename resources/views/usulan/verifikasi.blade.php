@extends('layouts.main')

@php
    $menuName = 'Verifikasi Usulan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="mr-2 fas fa-check-circle"></i>Verifikasi Usulan
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('usulan.proposals.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mr-1 fas fa-arrow-left"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informasi Usulan -->
                    <div class="mb-4 row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5 class="alert-heading">
                                    <i class="mr-2 fas fa-info-circle"></i>Informasi Pengusul
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td width="200" class="font-weight-bold bg-light">Nama Lengkap:</td>
                                        <td>{{ $usulan->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">NIK:</td>
                                        <td>{{ $usulan->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Nomor KK:</td>
                                        <td>{{ $usulan->nomor_kk }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Nomor HP:</td>
                                        <td>{{ $usulan->nomor_hp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Alamat:</td>
                                        <td>{{ $usulan->alamat_lengkap }}, {{ $usulan->village->name }}, {{ $usulan->district->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Jenis Usulan:</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $usulan->jenis_usulan_label }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Tanggal Usulan:</td>
                                        <td>{{ $usulan->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($usulan->foto_rumah)
                    <div class="mb-4 row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5 class="alert-heading">
                                    <i class="mr-2 fas fa-camera"></i>Foto Rumah
                                </h5>
                            </div>
                            <div class="text-center">
                                <img src="{{ $usulan->foto_rumah_url }}" alt="Foto Rumah" class="rounded shadow img-fluid" style="max-width: 500px;">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Form Verifikasi -->
                    <form method="POST" action="{{ route('usulan.verifikasi.store', $usulan->id) }}" id="verifikasi-form">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h5 class="alert-heading">
                                        <i class="mr-2 fas fa-clipboard-check"></i>Kriteria Verifikasi
                                    </h5>
                                    <p class="mb-0">Pilih kriteria yang sesuai dengan kondisi usulan:</p>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="mr-1 fas fa-info-circle"></i>
                                            <strong>Penting:</strong> Untuk memilih "Diterima", semua kriteria harus tercentang.
                                        </small>
                                    </div>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" id="criteria-progress" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted" id="criteria-status">
                                            <span id="checked-count">0</span> dari <span id="total-count">6</span> kriteria tercentang
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="kesesuaian_tata_ruang" id="kesesuaian_tata_ruang" value="1">
                                                <label class="custom-control-label" for="kesesuaian_tata_ruang">
                                                    <i class="mr-2 fas fa-map-marked-alt"></i>Kesesuaian Lokasi Rumah dengan Tata Ruang
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="tidak_dalam_sengketa" id="tidak_dalam_sengketa" value="1">
                                                <label class="custom-control-label" for="tidak_dalam_sengketa">
                                                    <i class="mr-2 fas fa-gavel"></i>Tanah/Rumah Tidak Dalam Sengketa
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="memiliki_alas_hak" id="memiliki_alas_hak" value="1">
                                                <label class="custom-control-label" for="memiliki_alas_hak">
                                                    <i class="mr-2 fas fa-file-contract"></i>Memiliki Alas Hak atau Bukti yang sah
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="satu_satunya_rumah" id="satu_satunya_rumah" value="1">
                                                <label class="custom-control-label" for="satu_satunya_rumah">
                                                    <i class="mr-2 fas fa-home"></i>Satu-satunya Rumah
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="belum_pernah_bantuan" id="belum_pernah_bantuan" value="1">
                                                <label class="custom-control-label" for="belum_pernah_bantuan">
                                                    <i class="mr-2 fas fa-hand-holding-heart"></i>Belum Pernah Menerima Bantuan Perumahan dari Pemerintah dalam 10 Tahun terakhir
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="berpenghasilan_rendah" id="berpenghasilan_rendah" value="1">
                                                <label class="custom-control-label" for="berpenghasilan_rendah">
                                                    <i class="mr-2 fas fa-coins"></i>Berpenghasilan Rendah
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hasil_verifikasi" class="font-weight-bold">
                                        <i class="mr-2 fas fa-clipboard-list"></i>Hasil Verifikasi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-lg @error('hasil_verifikasi') is-invalid @enderror"
                                            id="hasil_verifikasi" name="hasil_verifikasi" required>
                                        <option value="">Pilih Hasil Verifikasi</option>
                                        <option value="diterima" class="text-success">
                                            <i class="fas fa-check-circle"></i> Diterima
                                        </option>
                                        <option value="ditolak" class="text-danger">
                                            <i class="fas fa-times-circle"></i> Ditolak
                                        </option>
                                    </select>
                                    @error('hasil_verifikasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Pilih hasil verifikasi berdasarkan kriteria yang telah dicek
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="catatan_verifikator" class="font-weight-bold">
                                        <i class="mr-2 fas fa-sticky-note"></i>Catatan Verifikator <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('catatan_verifikator') is-invalid @enderror"
                                              id="catatan_verifikator" name="catatan_verifikator"
                                              rows="4" placeholder="Masukkan catatan verifikasi..." required></textarea>
                                    @error('catatan_verifikator')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Berikan catatan untuk menjelaskan hasil verifikasi
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="mr-2 fas fa-info-circle"></i>Petunjuk Verifikasi
                                    </h6>
                                    <ul class="mb-0">
                                        <li>Centang kriteria yang sesuai dengan kondisi usulan</li>
                                        <li>Pilih hasil verifikasi berdasarkan kriteria yang telah dicek</li>
                                        <li>Berikan catatan jika diperlukan untuk menjelaskan hasil verifikasi</li>
                                        <li>Pastikan semua informasi telah diperiksa dengan teliti</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="mr-2 fas fa-save"></i>Simpan Verifikasi
                            </button>
                            <a href="{{ route('usulan.proposals.index') }}" class="btn btn-secondary btn-lg">
                                <i class="mr-2 fas fa-times"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-style')
<style>
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}

.custom-control-input:checked ~ .custom-control-label::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3e%3c/svg%3e");
}

.form-control-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

.alert-heading {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endpush

@push('after-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validasi form
    const form = document.getElementById('verifikasi-form');
    const hasilVerifikasi = document.getElementById('hasil_verifikasi');

    // Ambil semua checkbox kriteria
    const kriteriaCheckboxes = [
        'kesesuaian_tata_ruang',
        'tidak_dalam_sengketa',
        'memiliki_alas_hak',
        'satu_satunya_rumah',
        'belum_pernah_bantuan',
        'berpenghasilan_rendah'
    ];

    // Function untuk mengecek apakah semua kriteria tercentang
    function checkAllCriteria() {
        return kriteriaCheckboxes.every(criteria => {
            const checkbox = document.getElementById(criteria);
            return checkbox && checkbox.checked;
        });
    }

    // Function untuk update opsi hasil verifikasi
    function updateVerifikasiOptions() {
        const allCriteriaChecked = checkAllCriteria();
        const diterimaOption = hasilVerifikasi.querySelector('option[value="diterima"]');

        if (allCriteriaChecked) {
            // Jika semua kriteria tercentang, enable opsi "Diterima"
            diterimaOption.disabled = false;
            diterimaOption.classList.remove('text-muted');
            diterimaOption.classList.add('text-success');
        } else {
            // Jika ada kriteria yang tidak tercentang, disable opsi "Diterima"
            diterimaOption.disabled = true;
            diterimaOption.classList.remove('text-success');
            diterimaOption.classList.add('text-muted');

            // Jika saat ini "Diterima" terpilih, ubah ke "Ditolak"
            if (hasilVerifikasi.value === 'diterima') {
                hasilVerifikasi.value = 'ditolak';
                showCriteriaWarning();
            }
        }

        // Update progress indicator
        updateProgressIndicator();
    }

    // Function untuk update progress indicator
    function updateProgressIndicator() {
        const checkedCount = kriteriaCheckboxes.filter(criteria => {
            const checkbox = document.getElementById(criteria);
            return checkbox && checkbox.checked;
        }).length;

        const totalCount = kriteriaCheckboxes.length;
        const progressPercentage = (checkedCount / totalCount) * 100;

        // Update progress bar
        const progressBar = document.getElementById('criteria-progress');
        if (progressBar) {
            progressBar.style.width = progressPercentage + '%';
            progressBar.setAttribute('aria-valuenow', progressPercentage);
        }

        // Update counter
        const checkedCountElement = document.getElementById('checked-count');
        const totalCountElement = document.getElementById('total-count');

        if (checkedCountElement) {
            checkedCountElement.textContent = checkedCount;
        }
        if (totalCountElement) {
            totalCountElement.textContent = totalCount;
        }

        // Update progress bar color
        if (progressBar) {
            if (progressPercentage === 100) {
                progressBar.className = 'progress-bar bg-success';
            } else if (progressPercentage >= 50) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-danger';
            }
        }
    }

    // Function untuk menampilkan peringatan
    function showCriteriaWarning() {
        Swal.fire({
            icon: 'warning',
            title: 'Kriteria Belum Lengkap',
            text: 'Untuk memilih "Diterima", semua kriteria verifikasi harus tercentang. Hasil verifikasi diubah menjadi "Ditolak".',
            confirmButtonText: 'OK'
        });
    }

    // Event listener untuk setiap checkbox kriteria
    kriteriaCheckboxes.forEach(criteria => {
        const checkbox = document.getElementById(criteria);
        if (checkbox) {
            checkbox.addEventListener('change', updateVerifikasiOptions);
        }
    });

    // Event listener untuk hasil verifikasi
    hasilVerifikasi.addEventListener('change', function() {
        if (this.value === 'diterima' && !checkAllCriteria()) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Dapat Memilih "Diterima"',
                text: 'Semua kriteria verifikasi harus tercentang untuk memilih "Diterima".',
                confirmButtonText: 'OK'
            });
            this.value = '';
            return;
        }
    });

    // Initialize pada load
    updateVerifikasiOptions();

    form.addEventListener('submit', function(e) {
        if (!hasilVerifikasi.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih hasil verifikasi terlebih dahulu!',
                confirmButtonText: 'OK'
            });
            hasilVerifikasi.focus();
            return false;
        }

        // Validasi tambahan untuk "Diterima"
        if (hasilVerifikasi.value === 'diterima' && !checkAllCriteria()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Untuk memilih "Diterima", semua kriteria verifikasi harus tercentang.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Konfirmasi sebelum submit
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Verifikasi',
            text: 'Apakah Anda yakin ingin menyimpan hasil verifikasi ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Auto-save draft (opsional)
    let autoSaveTimer;
    const formInputs = form.querySelectorAll('input, select, textarea');

    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Simpan draft ke localStorage
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                localStorage.setItem('verifikasi_draft', JSON.stringify(data));
            }, 2000);
        });
    });

    // Load draft jika ada
    const draft = localStorage.getItem('verifikasi_draft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = data[key] === '1';
                } else {
                    input.value = data[key];
                }
            }
        });
        // Update opsi setelah load draft
        updateVerifikasiOptions();
    }

    // Clear draft setelah submit berhasil
    form.addEventListener('submit', function() {
        localStorage.removeItem('verifikasi_draft');
    });
});
</script>
@endpush
