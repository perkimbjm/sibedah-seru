@extends('layouts.main')

@php
    $menuName = 'Peta Sebaran Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp


@section('content')
<div class="card">
    <div class="card-body m-1" id="mapid"></div>
</div>
@endsection

@section('styles')
<x-leaflet></x-leaflet>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
    #mapid { height: 85vh; }
</style>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    var map = L.map('mapid').setView([{{ config('leaflet.map_center_latitude') }}, {{ config('leaflet.map_center_longitude') }}], {{ config('leaflet.zoom_level') }});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    var markers = L.markerClusterGroup();
    
    fetch('{{ route('api.bedah.general') }}')
    .then((response) => {
        if (!response.ok) {
            throw new Error("HTTP error " + response.status);
        }
        return response.json();
    })
    .then((data) => {
        if (!Array.isArray(data.data)) {
            throw new Error("Data yang diterima bukan array");
        }

        // Konversi data ke format GeoJSON
        let geoJsonData = {
            type: "FeatureCollection",
            features: data.data.map((item) => ({
                type: "Feature",
                geometry: {
                    type: "Point",
                    coordinates: [item.lng, item.lat],
                },
                properties: {
                    id: item.id,
                    name: item.name,
                    address: item.address,
                    district: item.district.name,
                    year: item.year,
                },
            })),
        };

        var marker = L.geoJSON(geoJsonData, {
            pointToLayer: function(geoJsonPoint, latlng) {
                return L.marker(latlng).bindPopup(function (layer) {
                    return layer.feature.properties.name + '<br>ID: ' + layer.feature.properties.id + '<br>Alamat: ' + layer.feature.properties.address + '<br>Kecamatan: ' + layer.feature.properties.district + '<br>Tahun: ' + layer.feature.properties.year;
                });
            }
        });
        markers.addLayer(marker);
        map.addLayer(markers);
    })
    .catch((error) => {
        console.log(error);
    });
    
    {{-- /* GPS enabled geolocation control set to follow the user's location */
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
    }).addTo(map); --}}
</script>
@endsection