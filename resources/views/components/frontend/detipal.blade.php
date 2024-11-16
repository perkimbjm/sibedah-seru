@extends('layouts.apps')

@push('after-style')
@include('components.leaflet')
<link rel="stylesheet" href= "{{ asset('css/leaflet.plugin.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/glightbox.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}" />
@endpush

@section('header')
<x-header></x-header>
@endsection

@section('content')

<!-- START: BREADCRUMB -->
<section class="bg-light py-8 px-4">
  <div class="container mx-auto">
    <ul class="breadcrumb py-2">
      <li>
        <a href="/">{{ trans('frontend.home') }}</a>
      </li>
      <li>
        <a href="#" aria-label="current-page">Detail IPALD</a>
      </li>
    </ul>
  </div>
</section>
<!-- END: BREADCRUMB -->

<!-- START: DETAILS -->
<section class="container mx-auto">
      
    <div class="flex-1 px-4 p-md-6 my-4">
      <div class="w-100 d-lg-block">
        <h4 class="fw-bold">{{ $gallery->name}}</h4>
      </div>
      <h6>{{ $gallery->address }}</h6>
      <hr class="my-8" />
      <div class="table-responsive table-responsive-lg table-responsive-sm table-responsive-xl table-responsive-md overflow-auto">
      <table class="table table-responsive">
        <tbody>
          <tr>
            <th>Kelurahan</th>
            <td>{{ $gallery->kelurahans->name }}</td>
          </tr>
          <tr>
            <th>Tahun Mulai Operasi</th>
            <td>{{ $gallery->year ?? '-'}}</td>
          </tr>
          <tr>
            <th>Kapasitas</th>
            <td>{{ $gallery->capacity}}&nbsp;m<sup>3</sup>/hari</td>
          </tr>
          <tr>
            <th>Area Terlayani</th>
            <td>
              @foreach($gallery->services as $key => $service)
                  <span class="label label-info">{{ $service->name }},&nbsp;</span>
              @endforeach
          </td>
          </tr>
          <tr>
            <th>Koordinat</th>
            <td><a href="https://maps.google.com/?q={{ $gallery->lat }},{{ $gallery->lng }}" target="_blank">
              {{ $gallery->lat }}, {{ $gallery->lng }}</a></td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>

    
    <div class="flex-1 flex-wrap">
      <div class="flex justify-center mb-2">
        <h3 class="text-2xl">
          Gallery
        </h3>
      </div>
      <div class="owl-carousel owl-theme">
        <div class="card">
          <div class="rounded-xl card-shadow" style="width: 320px; height: 200px">
            <div class="item">
              <img src="https://via.placeholder.com/1x1.png?text=No+Image" alt="foto" title="No Image Found" class="object-cover h-100 w-100"/>
              </div>
          </div>
        </div>         
          @foreach ($ipal as $data)
            <div class="card">
            <div class="rounded-xl card-shadow mx-3" style="width: 320px; height: 200px">
              <div class="item">
                 <a href="{{ Storage::url('') }}{{ $data->idm }}/{{ $data->file_name }}" class="gallery-lightbox">
                  <img src="{{ Storage::url('') }}{{ $data->idm }}/{{ $data->file_name }}" alt="foto" class="object-cover h-100 w-100">
                </a>
                </div>
            </div>
          </div>
          @endforeach
      </div>
    </div>

    
    <div class="col-12 mt-4">
      <div class="flex justify-center mb-2">
        <h3 class="text-2xl">
          Peta
        </h3>
      </div>
      <div class="row my-2">
        <div class="col-md-4 mb-1">
          <input class="form-control" type="text" name="lat" id="lat">
        </div>
        <div class="col-md-4 mb-1">
          <input class="form-control" type="text" name="lng" id="lng">
        </div>
        <div class="col-md-4">
          <button class="btn btn-sm btn-info" onclick="return fromhere()"><i class="fa fa-map-marker"></i> Posisi Saat Ini</button>
        </div>
      </div>
      <div id="map" style="width: 100%; height: 75vh;"></div>
    </div>
  </div>
</section>
<!-- END: DETAILS -->

@endsection


@section('footer')
@include('components.frontend.footer')
@endsection

@push('after-script')
<script src="{{ asset('js/L.Control.ZoomBar.js')}}"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="{{ asset('js/glightbox.min.js') }}"></script>
<script>

$('.owl-carousel').owlCarousel({
    margin:10,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
})

const galleryLightbox = GLightbox({
  selector: '.gallery-lightbox',
  touchNavigation: true,
  openEffect: 'zoom',
  closeEffect: 'fade',
  cssEfects: {
     // This are some of the animations included, no need to overwrite
    fade: { in: 'fadeIn', out: 'fadeOut' },
    zoom: { in: 'zoomIn', out: 'zoomOut' }
  }
  });
</script>

<script>
  let map = L.map('map', {
		center: [{{ $gallery->lat }}, {{ $gallery->lng }}],
    zoomControl: false,
		zoom: 19,
	});

getLocation();
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
  $("#lat").val(position.coords.latitude)
  $("#lng").val(position.coords.longitude)
}

  var build = L.marker([{{ $gallery->lat }}, {{ $gallery->lng }}], {
          icon: L.icon({
                  iconUrl: '{{ asset( Storage::url($gallery->categories->icon) ) }}',
                  iconSize: [24, 28],
                  iconAnchor: [12, 28],
                  popupAnchor: [0, -25]
              })
          }).bindPopup("{{ $gallery->address }}" + "<br><button class='btn btn-sm btn-info' onClick='return here({{ $gallery->lat }}, {{ $gallery->lng }})'>Rute kesini</button>").addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let routeControl = L.Routing.control({
      waypoints: [
        L.latLng(),
      ],
      position: "bottomleft",
      collapse: true,
      geocoder: L.Control.Geocoder.nominatim(),
      routeWhileDragging: true,
      showAlternatives: true,
      altLineOptions: {
        styles: [
          {color: 'black', opacity: 0.15, weight: 9},
          {color: 'white', opacity: 0.8, weight: 6},
          {color: 'blue', opacity: 0.5, weight: 2}
        ]
      },
      createMarker: function (i, waypoint, n) {
            let urlIcon;
            if(i==0) {
              urlIcon = '{{ asset('img/pin-user.svg') }}';
            }
            else if((i+1)==n) {
              urlIcon = '{{ asset( Storage::url($gallery->categories->icon) ) }}'; 
            }
            else {
              urlIcon = '{{ asset('img/favicon/favicon-76.png') }}';
            }
            const marker = L.marker(waypoint.latLng, {
              bounceOnAdd: false,
              bounceOnAddOptions: {
                duration: 1000,
                height: 800,
                function() {
                  (bindPopup(myPopup).openOn(map))
                }
              },
              icon: L.icon({
                iconUrl: urlIcon,
                iconSize: [25, 28],
                iconAnchor: [12, 28],
                popupAnchor: [-3, -76],
                shadowAnchor: [22, 94]
              })
            });
            return marker;
          }
    })
    routeControl.addTo(map);

    L.Routing.errorControl(routeControl).addTo(map);
    
    function here(lat, lng) {
      let latlng = L.latLng(lat, lng);
      routeControl.spliceWaypoints(routeControl.getWaypoints().length - 1, 1, latlng);
    }

    function fromhere(lat, lng) {
      let latlng = [$("#lat").val(), $("#lng").val()];
      routeControl.spliceWaypoints(0, 1, latlng);
      map.panTo(latlng);
    }


    let zoom_bar = new L.Control.ZoomBar({
            position: 'topleft'
        }).addTo(map);
 
  
    let scaleControl = L.control.scale({imperial: false}).addTo(map);

    let locateControl = L.control.locate({
    position: "bottomright",
    drawCircle: true,
    follow: true,
    setView: true,
    keepCurrentZoomLevel: true,
    markerStyle: {
        weight: 1,
        opacity: 0.8,
        fillOpacity: 0.8
    },
    circleStyle: {
        weight: 1,
        clickable: false
    },
    icon: "fa fa-location-arrow",
    metric: true,
    strings: {
        title: "Lokasiku",
        popup: "Lokasimu {distance} {unit} dari titik ini",
        outsideMapBoundsMsg: "Kamu tampaknya berada di luar jangkauan peta"
    },
    locateOptions: {
        maxZoom: 18,
        watch: true,
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 10000
    }
    }).addTo(map);
</script>
@endpush