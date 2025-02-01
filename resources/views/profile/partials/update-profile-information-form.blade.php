<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="border-0 shadow-sm card">
                <div class="p-4 card-body">
                   <!-- Profile Section -->
                    <div class="row">
                        <!-- Left Column - Photo -->
                        <div class="mt-5 mb-2 text-center col-md-4 mb-md-1">
                            <div class="position-relative d-inline-block">
                                <!-- Camera Icon Overlay -->
                                <label for="photo-upload" class="p-2 cursor-pointer position-absolute bg-primary rounded-circle"
                                       style="top: 50%; left: 50%; transform: translate(-50%, -50%); transition: all 0.3s; opacity: 0; z-index: 2;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-white" width="24" height="24" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                </label>

                                <!-- Profile Image Container -->
                                <div class="image-container">
                                    <img src="{{ $user->profile_photo_url }}"
                                         alt="{{ $user->name }}"
                                         class="border rounded-circle"
                                         style="width: 180px; height: 180px; object-fit: cover; cursor: pointer;"
                                         id="preview-photo">
                                    <!-- Dark Overlay -->
                                    <div class="image-overlay"
                                         style="position: absolute; top: 0; left: 0; width: 180px; height: 180px;
                                                background: rgba(0,0,0,0.5); border-radius: 50%; opacity: 0;
                                                transition: all 0.3s; cursor: pointer;"></div>
                                </div>

                                <!-- Photo Upload Form -->
                                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photo-form">
                                    @csrf
                                    <input type="file" name="photo" id="photo-upload" class="d-none" accept="image/*" onchange="showPreview(event)">
                                    <button type="submit" class="mt-2 btn btn-info btn-sm d-none" id="upload-btn">
                                        <i class="fas fa-save me-1"></i> Simpan Foto
                                    </button>
                                </form>

                                @if($user->profile_photo_path)
                                <form action="{{ route('profile.photo.delete') }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i> Hapus Foto
                                    </button>
                                </form>
                                @endif

                                @error('photo')
                                    <span class="mt-2 text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column - Form -->
                        <div class="col-md-8">
                            <h5 class="mb-4 text-primary">Data Diri</h5>
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label text-muted small">Nama Lengkap</label>
                                    <input id="name"
                                           type="text"
                                           name="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="form-control bg-light @error('name') is-invalid @enderror"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label text-muted small">Email</label>
                                    <input id="email"
                                           type="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="form-control bg-light @error('email') is-invalid @enderror"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="form-label text-muted small">Nomor Telepon</label>
                                    <input id="phone"
                                           type="tel"
                                           name="phone"
                                           value="{{ old('phone', $user->phone) }}"
                                           class="form-control bg-light @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="px-4 btn btn-primary">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
function showPreview(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-photo').src = e.target.result;
        }
        reader.readAsDataURL(file);
        document.getElementById('upload-btn').classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const imageContainer = document.querySelector('.image-container');
    const cameraLabel = document.querySelector('label[for="photo-upload"]');
    const overlay = document.querySelector('.image-overlay');

    imageContainer.addEventListener('mouseenter', function() {
        cameraLabel.style.opacity = '1';
        overlay.style.opacity = '1';
    });

    imageContainer.addEventListener('mouseleave', function() {
        cameraLabel.style.opacity = '0';
        overlay.style.opacity = '0';
    });

    overlay.addEventListener('click', function() {
        document.getElementById('photo-upload').click();
    });
});
</script>
@endsection
