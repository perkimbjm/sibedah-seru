@extends('layouts.apps')

@push('after-style')
@include('components.leaflet')
<link rel="stylesheet" href= "{{ asset('css/leaflet.plugin.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
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
        <a href="#" aria-label="current-page">Detail</a>
      </li>
    </ul>
  </div>
</section>
<!-- END: BREADCRUMB -->

<!-- START: DETAILS -->
<section class="container mx-auto">
  <div class="d-flex flex-wrap my-4 my-md-12">
    <div class="flex-1">
      <div class="slider">
        <div class="thumbnail">
          @php $i = 0; @endphp
          @foreach ($gallery->galleries as $item)
              <div class="px-2">
                <div
                  class="gallery-item item
                  {{ $loop->first ? 'selected' : '' }}"
                  data-img="{{ Storage::url($item->url) }}"
                >
                <a href="#slide{{ $i++ }}" class="gallery-lightbox">
                  <img
                    src="{{ Storage::url($item->url) }}"
                    alt="front"
                    class="object-cover w-100 h-100 rounded-lg"
                  />
              </a>
            </div>
          </div>
          @endforeach

        </div>
        <div class="preview">
          <div class="item rounded-lg h-100 overflow-hidden">
            <img
              src="{{ $gallery->galleries()->exists() ? Storage::url($gallery->galleries->first()->url) : "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}}"
              alt="front"
              class="object-cover w-100 h-100 rounded-lg"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="flex-1 px-4 p-md-6">
      <div class="w-100 d-lg-block">
        <h4 class="fw-bold">{{ $gallery->categories->type }}</h4>
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
            <th>Kecamatan</th>
            <td>{{ $gallery->kecamatans->name }}</td>
          </tr>
          <tr>
            <th>Tahun Pembangunan</th>
            <td>{{ $gallery->year ?? '-'}}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>{{ $gallery->status }}</td>
          </tr>
          <tr>
            <th>Sumber Dana</th>
            <td>{{ $gallery->funded }}</td>
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

    
    <div class="col-12">
      <h4 class="fw-bold">Peta</h4>
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
        <p>Licensed Under by : <a href="https://opendatacommons.org/licenses/odbl/1-0/">Open Data Commons Open Database License (ODbL) v1.0</a>
          <br>Source Route : <a href="http://project-osrm.org/">OSRM</a></p>
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
<script src="{{ asset('js/glightbox.min.js') }}"></script>
<script>

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