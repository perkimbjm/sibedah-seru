@extends('layouts.main')

@php
    $menuName = 'Edit Data Foto RTLH';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container">
    <h4>Rumah : {{ $rtlh->name }}</h4>

    <!-- Notifikasi error -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('app.rutilahu.update', [$rtlh->id, $photo->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Thumbnail Foto Lama -->
        <div class="form-group">
            <label for="thumbnail">Thumbnail Foto Lama:</label>
            <div>
                <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Foto Lama" style="max-width: 200px; max-height: 150px;">
            </div>
        </div>

        <!-- Upload Foto Baru (Opsional) -->
        <div class="form-group">
            <label for="photo">Foto Baru (Opsional):</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" optional>
            <small class="text-muted">Maksimal 3 MB. Resolusi akan otomatis diubah menjadi 600x450 piksel. Jika tidak ada foto baru yang diupload, foto lama akan tetap digunakan.</small>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="description">Deskripsi:</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $photo->description }}</textarea>
        </div>


        <!-- Tombol Submit -->
        <button type="submit" class="btn btn-primary mb-4">Simpan Perubahan</button>
        <a href="{{ route('app.rutilahu.index', $rtlh->id) }}" class="btn btn-secondary mb-4">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
@parent
<script>
    function toggleLabel(id) {
        const checkbox = document.getElementById(id);
        const label = document.getElementById('toggleLabel_' + id);
        if (label) {
            label.textContent = checkbox.checked ? 'Ya' : 'Tidak';
        }
    }
</script> 
@endsection
