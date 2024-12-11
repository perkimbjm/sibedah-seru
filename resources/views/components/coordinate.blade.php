<script src="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.min.js"></script>
<script>
    
    let mapCenter = [{{ request('lat', config('leaflet.map_center_latitude')) }}, {{ request('lng', config('leaflet.map_center_longitude')) }}];
    let map = L.map('mapid').setView(mapCenter, {{ config('leaflet.zoom_level') }});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    let marker = L.marker(mapCenter, {
        draggable: true,
        autoPan: true
    }).addTo(map);
    function updateMarker(lat, lng) {
        marker
        .setLatLng([lat, lng])
        .bindPopup("Lokasi :  " + marker.getLatLng().toString())
        .openPopup();
        return false;
    };

    marker.on('dragend', function (e) {
        document.getElementById('lat').value = marker.getLatLng().lat.toFixed(5);
        document.getElementById('lng').value = marker.getLatLng().lng.toFixed(5);
    });

    map.on('click', function(e) {
        let latitude = e.latlng.lat.toString().substring(0, 8);
        let longitude = e.latlng.lng.toString().substring(0, 8);
        $('#lat').val(latitude);
        $('#lng').val(longitude);
        updateMarker(latitude, longitude);
    });
    let updateMarkerByInputs = function() {
        return updateMarker( $('#lat').val() , $('#lng').val());
    }
    $('#lat').on('input', updateMarkerByInputs);
    $('#lng').on('input', updateMarkerByInputs);

    /* GPS enabled geolocation control set to follow the user's location */
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
        title: "Lokasi",
        popup: "Tingkat Akurasi  {distance} {unit} dari titik ini",
        outsideMapBoundsMsg: "Di luar jangkauan area",
    },
    locateOptions: {
        maxZoom: 21,
        watch: true,
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 10000
    }
    }).addTo(map);
</script>