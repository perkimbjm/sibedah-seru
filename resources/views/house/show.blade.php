@extends('layouts.main')

@php
    $menuName = 'Penerima Bantuan Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<x-leaflet></x-leaflet>
<style>
    .main-image img {
        width: 80%;
        height: auto;
        aspect-ratio: 16 / 9;
    }
    .thumbnail-grid .item img {
        height: 150px;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .thumbnail-grid .item img {
            height: 100px;
        }
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="mb-4">
        <div class="main-image">
            {{-- Foto utama berdasarkan is_primary --}}
            @php
                $mainPhoto = $house->renovatedHousePhotos()->where('is_primary', true)->first();
            @endphp
            @if ($mainPhoto)
                <img id="mainImage" src="{{ asset($mainPhoto->photo_url) }}" alt="rumah">
            @else
                <a href="{{ route('app.gallery.index', $house) }}">
                    <img class="w-50" id="mainImage" src="https://placehold.co/100x75/EEE/31343C?font=open-sans&text=Belum+Ada+Foto" alt="No Image Available">
                </a>
            @endif
        </div>
    </div>

    <!-- Thumbnail Grid -->
    <div class="owl-carousel owl-theme row thumbnail-grid mb-4">
        @foreach ($house->renovatedHousePhotos()->get() as $photo)
            <div class="item">
                <img src="{{ asset($photo->photo_url) }}" alt="progres" class="w-100 rounded" onclick="changeMainImage(this.src)">
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-8">
            <h3 class="mt-4 mb-3">Data Bantuan</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-start mb-2">    
                        <p class="font-bold text-lg"><i class="fas fa-handshake fa-lg" aria-hidden="true"></i> {{ $house->type }}</p>
                    </div>
                    <div class="d-flex align-items-start mb-2">
                        <p class="font-bold text-lg"><i class="fa fa-university fa-lg" aria-hidden="true"></i> Dibiayai Oleh {{ $house->source }}</p>
                    </div>
                    <div class="d-flex align-items-start mb-2">
                        <p class="font-bold text-lg"><i class="fas fa-calendar-alt fa-lg" aria-hidden="true"></i> Tahun {{ $house->year }}</p>
                    </div>
                </div>
            </div>

            <!-- Lokasi -->
            <h3 class="mt-4 mb-3">Lokasi</h3>
            <div class="rounded mb-3">
                <div id="mapid" class="mb-3 mx-auto" style="min-height: 350px"></div>
            </div>
            <p><i class="fas fa-map-marker-alt" style="color: #358dd0;"></i> {{ ucwords($house->address) }} Kec. {{ ucwords($house->district->name) }} Kab. Balangan Kalimantan Selatan</p>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 my-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Penerima Manfaat</h4>
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h5 class="mb-1 text-lg">ID : {{ $house->id }}</h5>
                            <div class="d-flex align-items-center"><i class="fas fa-user-check fa-lg" aria-hidden="true"></i><p class="ml-2 mb-1 text-lg">{{ $house->name }}</p></div>
                            <div class="d-flex align-items-center"><i class="fa fa-id-badge" aria-hidden="true"></i><p class="ml-2 mb-1 text-muted">NIK {{ $house->nik }}</p></div>
                        </div>
                    </div>

                    <h4 class="mt-4 mb-3">Lokasi</h4>
                    <div class="d-flex align-items-center"><i class="fas fa-street-view" aria-hidden="true"></i><p class="ml-2 mb-1 text-md">{{ $house->address }}</p></div>
                    <p class="font-bold mb-1 text-md"><i class="fas fa-map"></i> Kec. {{ $house->district ? $house->district->name : 'Tidak ada kecamatan' }}</p>
                    <p class="font-bold mb-1 text-md"><i class="fas fa-map-pin"></i> Lat {{ $house->lat }} ,Lng {{ $house->lng }}</p>
                    <a target='_blank' href='https://maps.google.com/maps?q={{ $house->lat }},{{ $house->lng }}&z=20&ll={{ $house->lat }},s{{ $house->lng }}'><button class='btn btn-outline-success btn-block'>Google Maps</button></a>

                    @if($house->rtlh_id != null)
                        <h4 class="mt-4 mb-3">Detail Rumah</h4>
                        <div class="d-flex align-items-center"><i class="fas fa-home" aria-hidden="true"></i><p class="ml-2 mb-1 text-md">Luas Rumah : {{ $house->rtlh->area }} m<sup>2</sup></p>
                        </div>
                        <p class="mb-1 text-md"><i class="fas fa-users"></i> Jumlah Penghuni : {{ $house->rtlh->people }} Orang</p>
                        <p class="mb-1 text-md"><i class="fas fa-water"></i> Air Minum : {{ $house->rtlh->air }}</p>
                        <p class="mb-1 text-md"><i class="fas fa-toilet"></i> WC : {{ $house->rtlh->wc }}</p>
                    @endif
                </div>
            </div>
            <div class="col-md-2 my-3 d-flex justify-content-start">
                <a href="{{ route('app.houses.edit', $house->id) }}" class="btn btn-warning mx-1">Edit</a>
                <a href="{{ route('app.houses.index') }}" class="btn btn-secondary mx-1">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
$(document).ready(function(){
    $('.owl-carousel').owlCarousel({
        loop: false,
        margin:6,
        responsive:{
            0:{ items:2 },
            600:{ items:3 },
            1000:{ items:4 }
        }
    });

    let lat = {{ $house->lat ?? config('leaflet.map_center_latitude') }};
    let lng = {{ $house->lng ?? config('leaflet.map_center_longitude') }};
    let mapCenter = [lat, lng];
    let map = L.map('mapid').setView(mapCenter, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let marker = L.marker(mapCenter, { autoPan: true }).addTo(map);
});

function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
}
</script>
@endsection
