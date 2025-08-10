@extends('layouts.main')

@php
    $menuName = 'Tanggapi Pengaduan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('title', 'Tanggapi Pengaduan')

@section('content')
<div class="mr-4 ml-4">
    <div class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">Tanggapi Pengaduan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('aduan.index') }}">Pengaduan</a></li>
                        <li class="breadcrumb-item active">Tanggapi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Tanggapan Pengaduan</h3>
                        </div>
                        <div class="card-body">
                            <form id="responForm" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Informasi Pengaduan</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Kode Tiket:</strong></td>
                                                <td>{{ $aduan->kode_tiket }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nama:</strong></td>
                                                <td>{{ $aduan->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Lokasi:</strong></td>
                                                <td>{{ $aduan->village->name }}, {{ $aduan->district->name }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Isi Pengaduan</h5>
                                        <div class="p-3 rounded bg-light">
                                            {{ $aduan->complain }}
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label for="respon">Tanggapan Admin:</label>
                                    <textarea name="respon" id="respon" class="form-control" rows="5" placeholder="Masukkan tanggapan Anda..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ $aduan->status == 'pending' ? 'selected' : '' }}>Menunggu Tanggapan</option>
                                        <option value="process" {{ $aduan->status == 'process' || $aduan->status == 'pending' ? 'selected' : '' }}>Sedang Diproses</option>
                                        {{-- Admin tidak boleh mengubah status ke completed --}}
                                        @if($aduan->status == 'completed')
                                            <option value="completed" selected disabled>Selesai (Hanya bisa diubah oleh pengadu)</option>
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Status "Selesai" hanya dapat diubah oleh pengadu melalui sistem evaluasi.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-primary">Kirim Tanggapan</button>
                                <a href="{{ route('aduan.index') }}" class="btn btn-secondary">Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('responForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    // Validasi client-side
    const responTextarea = document.getElementById('respon');
    const responValue = responTextarea.value.trim();

    if (responValue === '') {
        alert('Tanggapan tidak boleh kosong!');
        responTextarea.focus();
        return;
    }

    // Tambahkan loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

    const formData = new FormData(this);

    fetch('{{ route("aduan.update", $aduan) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
    })
    .then(response => {
        // Debug: tampilkan response status
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        if (!response.ok) {
            // Try to get error text for debugging
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}...`);
            });
        }

        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Tampilkan success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Berhasil!</strong> ${data.message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            `;

            // Insert alert di bagian atas form
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);

            // Redirect setelah 2 detik
            setTimeout(() => {
                window.location.href = '{{ route("aduan.show", $aduan) }}';
            }, 2000);
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Tampilkan error message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <strong>Gagal!</strong> ${error.message}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        `;

        // Insert alert di bagian atas form
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);

        // Kembalikan button state
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});
</script>
@endsection
