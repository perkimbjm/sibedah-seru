@extends('layouts.main')

@php
    $menuName = 'Data RTLH';
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
            @php
                $mainPhoto = $rtlh->housePhotos()->first();
            @endphp
            @if ($mainPhoto)
                <img id="mainImage" src="{{ asset($mainPhoto->photo_url) }}" alt="rumah">
            @else
                <a href="{{ route('app.rutilahu.index', $rtlh) }}">
                    <img class="w-50" id="mainImage" src="https://placehold.co/100x75/EEE/31343C?font=open-sans&text=Belum+Ada+Foto" alt="No Image Available">
                </a>
            @endif
        </div>
    </div>

    <!-- Thumbnail Grid -->
    <div class="owl-carousel owl-theme row thumbnail-grid mb-4">
        @foreach ($rtlh->housePhotos()->get() as $photo)
            <div class="item">
                <img src="{{ asset($photo->photo_url) }}" alt="rtlh" class="w-100 rounded" onclick="changeMainImage(this.src)">
            </div>
        @endforeach
    </div>


    <div class="row">
        <div class="col-md-8">

            <h4 class="mt-4 mb-3">Data RTLH</h4>
            <div class="card">
                <div class="card-body">
                    <h4 class="font-bold">ASPEK KESELAMATAN</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-bold">Struktural</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">Pondasi : {{ $rtlh->pondasi }}</p>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <p class="text-md font-bold">Kolom / Balok : {{ $rtlh->kolom_blk }}</p>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <p class="text-md font-bold">Rangka Atap : {{ $rtlh->rngk_atap }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-bold">Non Struktural</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">Atap : {{ $rtlh->atap }}</p>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <p class="text-md font-bold">Dinding : {{ $rtlh->dinding }}</p>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <p class="text-md font-bold">Lantai : {{ $rtlh->lantai }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="font-bold">ASPEK AIR BERSIH</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-bold">Sumber Air Bersih</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">{{ $rtlh->air }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-bold">Jarak Air Ke Pembuangan Tinja</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">{{ $rtlh->jarak_tinja }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="font-bold">ASPEK SANITASI</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="font-bold">Kepemilikan WC</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">{{ $rtlh->wc }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5 class="font-bold">Jenis WC</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">{{ $rtlh->jenis_wc }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h5 class="font-bold">TPA Tinja</h5>
                            <div class="d-flex align-items-start mb-2">    
                                <p class="text-md font-bold">{{ $rtlh->tpa_tinja }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Aspek</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pondasi</td>
                        <td>{{ $rtlh->getScore($rtlh->pondasi, ['Layak' => 20, 'Kurang Layak' => 10, 'Tidak Layak' => 0]) }}</td>
                    </tr>
                    <tr>
                        <td>Kolom / Balok</td>
                        <td>{{ $rtlh->getScore($rtlh->kolom_blk, ['Layak' => 25, 'Kurang Layak' => 15, 'Tidak Layak' => 0]) }}</td>
                    </tr>
                    <tr>
                        <td>Rangka Atap</td>
                        <td>{{ $rtlh->getScore($rtlh->rngk_atap, ['Layak' => 20, 'Kurang Layak' => 10, 'Tidak Layak' => 0]) }}</td>
                    </tr>
                    <tr>
                        <td>Atap</td>
                        <td>{{ $rtlh->getScore($rtlh->atap, ['Layak' => 15, 'Kurang Layak' => 7.5, 'Tidak Layak' => 0]) }}</td>
                    </tr>
                    <tr>
                        <td>Dinding</td>
                        <td>{{ $rtlh->getScore($rtlh->dinding, ['Layak' => 10, 'Kurang Layak' => 5, 'Tidak Layak' => 0]) }}</td>
                    </tr>
                    <tr>
                        <td>Lantai</td>
                        <td>{{ $rtlh->getScore($rtlh->lantai, ['Layak' => 10, 'Kurang Layak' => 5, 'Tidak Layak' => 0]) }}</td>
                    </tr>
                    <tr>
                        <td>Kecukupan Ruang</td>
                        <td>{{ $rtlh->calculateSpaceScore() }}</td>
                    </tr>
                    <tr>
                        <td>Air Bersih</td>
                        <td>{{ $rtlh->calculateCleanWaterScore() }}</td>
                    </tr>
                    <tr>
                        <td>Sanitasi</td>
                        <td>{{ $rtlh->calculateSanitationScore() }}</td>
                    </tr>
                    <tr>
                        <td><strong>HASIL SKORING KATEGORI ASPEK KESELAMATAN</strong></td>
                        <td><strong>{{ $rtlh->calculateSafetyScore() }} </strong></td>
                        <td><strong>{{ $rtlh->status_safety }} </strong></td>
                    </tr>
                    <tr>
                        <td><strong>HASIL SKORING KATEGORI KESELURUHAN (ASPEK KESELAMATAN, KECUKUPAN RUANG, AIR BERSIH & SANITASI)</strong></td>
                        <td><strong>{{ $rtlh->calculateFinalScore() }}</strong></td>
                        <td><strong>{{ $rtlh->status }} </strong></td>
                    </tr>
                </tbody>
            </table>

            

            <!-- Lokasi -->
            <h4 class="mt-4 mb-3">Lokasi</h4>
            <div class="rounded mb-3">
                <div id="mapid" class="mb-3 mx-auto" style="min-height: 350px"></div>
            </div>
            <p><i class="fas fa-map-marker-alt" style="color: #358dd0;"></i> {{ ucwords($rtlh->address) }} Kec. {{ ucwords($rtlh->district->name) }} Kab. Balangan Kalimantan Selatan</p>
        </div>

        

        <!-- Sidebar -->
        <div class="col-md-4 my-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Penghuni</h4>
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h5 class="mb-1 text-lg">ID : {{ $rtlh->id }}</h5>
                            <div class="d-flex align-items-center"><i class="fas fa-user-check fa-lg" aria-hidden="true"></i><p class="ml-2 mb-1 text-lg">{{ $rtlh->name }}</p></div>
                            <div class="d-flex align-items-center"><i class="fa fa-id-badge" aria-hidden="true"></i><p class="ml-2 mb-1 text-muted">NIK {{ $rtlh->nik }}</p></div>
                            <div class="d-flex align-items-center"><i class="fa fa-id-badge" aria-hidden="true"></i><p class="ml-2 mb-1 text-muted">KK {{ $rtlh->kk }}</p></div>
                        </div>
                    </div>

                    <h4 class="mt-4 mb-3">Lokasi</h4>
                    <div class="d-flex align-items-center"><i class="fas fa-street-view" aria-hidden="true"></i><p class="ml-2 mb-1 text-md">{{ $rtlh->address }}</p></div>
                    <p class="font-bold mb-1 text-md"><i class="fas fa-map"></i> Kec. {{ $rtlh->district ? $rtlh->district->name : 'Tidak ada kecamatan' }}</p>
                    <p class="font-bold mb-1 text-md"><i class="fas fa-map-pin"></i> Lat {{ $rtlh->lat }} ,Lng {{ $rtlh->lng}}</p>
                    <a target='_blank' href='https://maps.google.com/maps?q={{ $rtlh->lat }},{{ $rtlh->lng }}&z=20&ll={{ $rtlh->lat }},s{{ $rtlh->lng }}'><button class='btn btn-outline-success btn-block'>Google Maps</button></a>

                    <h4 class="mt-4 mb-3">Kecukupan Ruang</h4>
                    <div class="d-flex align-items-center"><i class="fas fa-home" aria-hidden="true"></i><p class="ml-2 mb-1 text-md">Luas Bangunan Rumah : {{ $rtlh->area }} m<sup>2</sup></p></div>
                    <p class="mb-1 text-md"><i class="fas fa-users"></i> Jumlah Penghuni : {{ $rtlh->people }} Orang</p>
                    <p class="mb-1 text-md"><i class="fas fa-users"></i> Luas Lantai Per Kapita : {{ $rtlh->people > 0 ? round($rtlh->area / $rtlh->people, 2) : '-' }} m<sup>2</sup></p>
                    
                </div>
            </div>
            <div class="col-md-2 my-3 d-flex justify-content-start">
                <a href="{{ route('app.rtlh.edit', $rtlh->id) }}" class="btn btn-warning mx-1">Edit</a>
                <a href="{{ route('app.rtlh.index') }}" class="btn btn-secondary mx-1">Kembali</a>
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
        0:{
            items:2
        },
        600:{
            items:3
        },
        1000:{
            items:4
        }
    }
    });


        // Mendapatkan nilai lat dan lng dari properti komponen
        let lat = {{ $rtlh->lat ?? config('leaflet.map_center_latitude') }};
        let lng = {{ $rtlh->lng ?? config('leaflet.map_center_longitude') }};
        
        let mapCenter = [lat, lng];
        
        let map = L.map('mapid').setView(mapCenter, 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        let marker = L.marker(mapCenter, {
            autoPan: true
        }).addTo(map);
        
       
        
        
});

function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
}

</script>
@endsection