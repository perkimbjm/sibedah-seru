@extends('layouts.main')

@php
    $menuName = 'Tambah Data Foto RTLH';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container">
    <h4>Foto Rumah : {{ $rtlh->name }}</h4>

    <form action="{{ route('app.rutilahu.store', $rtlh->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')


        <div class="mb-3">
            <label for="photo" class="form-label">Foto</label>
            <img id="photoPreview" src="#" alt="Preview Foto" style="display:none; max-width: 300px; margin-top: 10px;"/>
            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" required>
            <small class="text-muted">Maksimal 3 MB. Resolusi akan otomatis diubah menjadi 600x450 piksel.</small>
            @error('photo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>


        <!-- Deskripsi -->
        <div class="form-group">
            <label for="description">Deskripsi:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('app.rutilahu.index', $rtlh->id) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
@parent
<script>
    document.getElementById('photo').addEventListener('change', function() {
        const file = this.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
        
        if (file) {
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.value = ''; // Kosongkan input file
                document.getElementById('photoPreview').style.display = 'none';
                return;
            }

            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Tipe file tidak didukung. Gunakan format JPG, PNG, atau GIF.');
                this.value = '';
                document.getElementById('photoPreview').style.display = 'none';
                return;
            }

            // Preview foto
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photoPreview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection