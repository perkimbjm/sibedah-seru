setTimeout(function () {
    map.invalidateSize();
}, 1000);

/*Zoom Extends*/
L.easyButton({
    states: [
        {
            stateName: "zoom-to-default",
            icon: "fa-home fa-lg",
            title: "zoom to Balangan",
            onClick: function (btn, map) {
                map.setView(mapCenter, mapZoom);
                btn.state("zoom-to-default");
            },
        },
    ],
}).addTo(map);

/* GPS enabled geolocation control set to follow the user's location */
const locateControl = L.control
    .locate({
        position: "topleft",
        drawCircle: true,
        follow: true,
        setView: true,
        keepCurrentZoomLevel: true,
        markerStyle: {
            weight: 1,
            opacity: 0.8,
            fillOpacity: 0.8,
        },
        circleStyle: {
            weight: 0.8,
            clickable: true,
        },
        icon: "fa fa-location-arrow",
        metric: true,
        strings: {
            title: "Lokasiku",
            outsideMapBoundsMsg: "Kamu tampaknya berada di luar jangkauan peta",
        },
        locateOptions: {
            maxZoom: 18,
            watch: true,
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000,
        },
    })
    .addTo(map);

/*Scale Map*/
L.control.scale({ imperial: false }).addTo(map);

/*Basemap*/
const baseMapIcons = {
    greyscale: "/img/basemap/greyscale.png",
    rbi: "/img/basemap/rbi.png",
    googleMaps: "/img/basemap/googleMaps.png",
    googleSatellite: "/img/basemap/googleSatellite.png",
    streetMap: "/img/basemap/streetmap.png",
    topoMap: "/img/basemap/topomap.png",
    dark: "/img/basemap/dark.png",
    cartoVoyager: "/img/basemap/voyager.png",
};

const iconLayers = [
    { title: "Positron", layer: cartoLight, icon: baseMapIcons.greyscale },
    { title: "Dark", layer: cartoDark, icon: baseMapIcons.dark },
    {
        title: "Voyager",
        layer: cartoVoyager,
        icon: baseMapIcons.cartoVoyager,
    },
    { title: "RBI", layer: rbi, icon: baseMapIcons.rbi },
    {
        title: "Google Maps",
        layer: googleMaps,
        icon: baseMapIcons.googleMaps,
    },
    {
        title: "Google Imagery",
        layer: googleSatellite,
        icon: baseMapIcons.googleSatellite,
    },
    { title: "OSM", layer: osm, icon: baseMapIcons.streetMap },
    { title: "Topo Map", layer: otopomap, icon: baseMapIcons.topoMap },
];

let iconLayersControl = new L.Control.IconLayers(iconLayers, {
    position: "bottomright",
    maxLayersInRow: 4,
});

iconLayersControl.addTo(map);

// define drawtoolbar options
let options = {
    position: "topleft",
    drawMarker: true,
    drawPolyline: true,
    drawRectangle: true,
    drawPolygon: true,
    drawCircle: true,
    cutPolygon: true,
    editMode: true,
    removalMode: true,
};

L.easyButton({
    states: [
        {
            stateName: "draw",
            icon: "fa fa-wrench fa-lg",
            title: "draw this map",
            onClick: function (btn, map) {
                togglePMToolbar();
            },
        },
    ],
}).addTo(map);

// Fungsi untuk menampilkan atau menyembunyikan tombol-tombol leaflet.pm
function togglePMToolbar() {
    let pmToolbar = document.querySelector(".leaflet-pm-toolbar");
    if (pmToolbar) {
        if (
            pmToolbar.style.display === "none" ||
            pmToolbar.style.display === ""
        ) {
            pmToolbar.style.display = "block";
        } else {
            pmToolbar.style.display = "none";
        }
    }
}

// Aktifkan toolbar leaflet.pm dan sembunyikan saat pertama kali peta dimuat
map.pm.addControls({
    position: "topleft",
    drawCircle: false,
    // Anda bisa mengatur opsi leaflet.pm lainnya di sini...
});

let pmToolbar = document.querySelector(".leaflet-pm-toolbar");
if (pmToolbar) {
    pmToolbar.style.display = "none";
}

let ctlMeasure = L.control.polylineMeasure({ position: "topleft" }).addTo(map);
