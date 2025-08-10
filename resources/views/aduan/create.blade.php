@extends('layouts.main')

@php
    $menuName = 'Tambah Pengaduan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('title', 'Tambah Pengaduan')

@section('content')
<div class="mr-4 ml-4">
    <section class="content">
        <div class="mb-4 shadow-sm card">
            <div class="card-header bg-primary">
                <h5 class="mb-0 text-white card-title">
                    <i class="mr-2 fas fa-plus"></i>Tambah Pengaduan Baru
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mr-2 fas fa-info-circle"></i>
                    Form khusus untuk menambah pengaduan dari sistem internal. Silakan isi form berikut:
                </div>

                <form id="aduanForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Nama (User yang login) -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Nama *</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" readonly>
                            <small class="text-muted">Nama akan diisi otomatis dari akun yang login</small>
                        </div>
                    </div>

                    <!-- Email (User yang login) -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" readonly>
                            <small class="text-muted">Email akan diisi otomatis dari akun yang login</small>
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Nomor HP *</label>
                        <div class="col-sm-10">
                            <input type="tel" class="form-control" name="contact" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Kecamatan -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Kecamatan *</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="district_id" id="district_id" required>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Desa -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Desa *</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="village_id" id="village_id" required>
                                <option value="">Pilih Desa</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Isi Pengaduan -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Isi Pengaduan *</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="complain" rows="5" required
                                placeholder="Tuliskan isi pengaduan dengan jelas..."></textarea>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Minimal 10 karakter, maksimal 2000 karakter</small>
                        </div>
                    </div>

                    <!-- Foto Pendukung -->
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Foto Pendukung</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="foto" accept="image/*">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Opsional. Format: JPEG, PNG, JPG, GIF, WEBP. Maksimal 3MB.</small>

                            <!-- Preview Foto -->
                            <div id="fotoPreview" class="mt-2" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                <button type="button" class="mt-2 btn btn-sm btn-outline-danger" onclick="removeFoto()">
                                    <i class="fas fa-trash"></i> Hapus Foto
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mb-3 row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="mr-2 fas fa-paper-plane"></i>Kirim Pengaduan
                            </button>
                            <a href="{{ route('aduan.index') }}" class="btn btn-secondary">
                                <i class="mr-2 fas fa-arrow-left"></i>Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Mengirim pengaduan...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load villages when district changes
    $('#district_id').change(function() {
        const districtId = $(this).val();
        const villageSelect = $('#village_id');

        villageSelect.html('<option value="">Pilih Desa</option>');

        if (districtId) {
            $.get(`/aduan/villages/${districtId}`, function(villages) {
                villages.forEach(function(village) {
                    villageSelect.append(`<option value="${village.id}">${village.name}</option>`);
                });
            }).fail(function() {
                alert('Gagal memuat data desa');
            });
        }
    });

    // Preview foto
    $('input[name="foto"]').change(function() {
        const file = this.files[0];
        if (file) {
            // Validasi ukuran file (3MB)
            if (file.size > 3 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 3MB.');
                $(this).val('');
                return;
            }

            // Preview gambar
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#fotoPreview').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    $('#aduanForm').submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Show loading
        $('#loadingModal').modal('show');
        $('#submitBtn').prop('disabled', true);

        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $.ajax({
            url: '{{ route("aduan.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingModal').modal('hide');

                if (response.success) {
                    alert('Pengaduan berhasil dikirim!\n\nKode Tiket: ' + response.data.kode_tiket);
                    window.location.href = '{{ route("aduan.index") }}';
                } else {
                    alert('Gagal mengirim pengaduan: ' + response.message);
                    $('#submitBtn').prop('disabled', false);
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                $('#submitBtn').prop('disabled', false);

                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Server error'));
                }
            }
        });
    });
});

function removeFoto() {
    $('input[name="foto"]').val('');
    $('#fotoPreview').hide();
    $('#previewImage').attr('src', '');
}
</script>
@endsection
