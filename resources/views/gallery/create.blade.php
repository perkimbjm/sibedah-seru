@extends('layouts.main')

@php
    $menuName = 'Tambah Data Foto Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('styles')
<style>
    input[type="checkbox"] {
        display: none;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    input:checked + .toggle-switch::before {
        background-color: #2196F3;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 4px;
        left: 4px;
        width: 26px;
        height: 26px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
    }

    input:checked + .toggle-switch::after {
        transform: translateX(26px);
    }
    </style>
@endsection

@section('content')
<div class="container">
    <h4>Tambah Data Foto Bedah Rumah</h4>


    <form action="{{ route('app.gallery.store', $house->id) }}" method="POST" enctype="multipart/form-data">
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

        <!-- Progres -->
        <div class="form-group">
            <label for="progres">Progres Bedah Rumah:</label>
            <select name="progres" id="progres" class="form-control" required>
                <option value="100">100%</option>
                <option value="0">0%</option>
                <option value="30">30%</option>
                <option value="50">50%</option>
                <option value="80">80%</option>
            </select>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="description">Deskripsi:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <!-- Is Primary -->
        <div class="form-group mb-4 md:flex md:justify-between">
            <div class="md:w-1/2">
                <label for="is_primary" class="text-gray-700 mb-2 flex justify-between items-center p-2">
                    Jadikan Foto Utama
                </label>
                <div class="flex items-center">
                    <input type="hidden" name="is_primary" value="0">
                    <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                           class="appearance-none peer" 
                           {{ old('is_primary', '') ? 'checked' : '' }} onclick="toggleLabel('is_primary')">
                    <label for="is_primary" class="toggle-switch relative inline-block w-10 h-6 bg-gray-300 rounded-full cursor-pointer">
                        <span class="w-16 h-10 flex items-center flex-shrink-0 ml-4 p-1 bg-gray-300 rounded-full duration-300 ease-in-out 
                                     peer-checked:bg-green-400 
                                     after:w-8 after:h-8 after:bg-white after:rounded-full after:shadow-md after:duration-300 
                                     {{ old('is_primary', '') ? 'peer-checked:after:translate-x-6' : '' }}">
                        </span>
                    </label>
                    <span id="toggleLabel_is_primary" class="ml-3 text-sm font-medium text-gray-900">
                        {{ old('is_primary', '') ? 'Ya' : 'Tidak' }}
                    </span>
                </div>
            </div>
            <div class="md:w-1/2">
                <label for="is_best" class="text-gray-700 mb-2 flex justify-between items-center p-2">
                    Jadikan Foto Best Practices
                </label>
                <div class="flex items-center">
                    <input type="hidden" name="is_best" value="0">
                    <input type="checkbox" name="is_best" id="is_best" value="1" 
                           class="appearance-none peer" 
                           {{ old('is_best', '') ? 'checked' : '' }} onclick="toggleLabel('is_best')">
                    <label for="is_best" class="toggle-switch relative inline-block w-10 h-6 bg-gray-300 rounded-full cursor-pointer">
                        <span class="w-16 h-10 flex items-center flex-shrink-0 ml-4 p-1 bg-gray-300 rounded-full duration-300 ease-in-out 
                                     peer-checked:bg-green-400 
                                     after:w-8 after:h-8 after:bg-white after:rounded-full after:shadow-md after:duration-300 
                                     {{ old('is_best', '') ? 'peer-checked:after:translate-x-6' : '' }}">
                        </span>
                    </label>
                    <span id="toggleLabel_is_best" class="ml-3 text-sm font-medium text-gray-900">
                        {{ old('is_best', '') ? 'Ya' : 'Tidak' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn btn-primary mb-4">Simpan Perubahan</button>
        <a href="{{ route('app.gallery.index', $house->id) }}" class="btn btn-secondary mb-4">Kembali</a>
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