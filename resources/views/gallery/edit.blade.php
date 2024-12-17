@extends('layouts.main')

@php
    $menuName = 'Edit Data Foto Bedah Rumah';
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
    <h4>Edit Data Foto Bedah Rumah</h4>

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

    <form action="{{ route('app.gallery.update', [$house->id, $photo->id]) }}" method="POST" enctype="multipart/form-data">
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

        <!-- Progres -->
        <div class="form-group">
            <label for="progres">Progres Renovasi:</label>
            <select name="progres" id="progres" class="form-control">
                <option value="0" {{ $photo->progres == 0 ? 'selected' : '' }}>0%</option>
                <option value="30" {{ $photo->progres == 30 ? 'selected' : '' }}>30%</option>
                <option value="50" {{ $photo->progres == 50 ? 'selected' : '' }}>50%</option>
                <option value="80" {{ $photo->progres == 80 ? 'selected' : '' }}>80%</option>
                <option value="100" {{ $photo->progres == 100 ? 'selected' : '' }}>100%</option>
            </select>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="description">Deskripsi:</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $photo->description }}</textarea>
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
                           {{ old('is_primary', $photo->is_primary) ? 'checked' : '' }} onclick="toggleLabel('is_primary')">
                    <label for="is_primary" class="toggle-switch relative inline-block w-10 h-6 bg-gray-300 rounded-full cursor-pointer">
                        <span class="w-16 h-10 flex items-center flex-shrink-0 ml-4 p-1 bg-gray-300 rounded-full duration-300 ease-in-out 
                                     peer-checked:bg-green-400 
                                     after:w-8 after:h-8 after:bg-white after:rounded-full after:shadow-md after:duration-300 
                                     {{ old('is_primary', $photo->is_primary) ? 'peer-checked:after:translate-x-6' : '' }}">
                        </span>
                    </label>
                    <span id="toggleLabel_is_primary" class="ml-3 text-sm font-medium text-gray-900">
                        {{ old('is_primary', $photo->is_primary) ? 'Ya' : 'Tidak' }}
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
                           {{ old('is_best', $photo->is_best) ? 'checked' : '' }} onclick="toggleLabel('is_best')">
                    <label for="is_best" class="toggle-switch relative inline-block w-10 h-6 bg-gray-300 rounded-full cursor-pointer">
                        <span class="w-16 h-10 flex items-center flex-shrink-0 ml-4 p-1 bg-gray-300 rounded-full duration-300 ease-in-out 
                                     peer-checked:bg-green-400 
                                     after:w-8 after:h-8 after:bg-white after:rounded-full after:shadow-md after:duration-300 
                                     {{ old('is_best', $photo->is_best) ? 'peer-checked:after:translate-x-6' : '' }}">
                        </span>
                    </label>
                    <span id="toggleLabel_is_best" class="ml-3 text-sm font-medium text-gray-900">
                        {{ old('is_best', $photo->is_best) ? 'Ya' : 'Tidak' }}
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
</script> 
@endsection
