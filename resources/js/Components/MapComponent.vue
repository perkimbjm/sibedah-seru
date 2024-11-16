<template>
    <div>
        <div id="map" ref="mapContainer"></div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from "vue";
import L from "leaflet";

import "leaflet/dist/leaflet.css";
import "@fortawesome/fontawesome-free/css/fontawesome.css";
import "leaflet.locatecontrol/dist/L.Control.Locate.min.css";
import "leaflet-easybutton/src/easy-button.css";
import "leaflet.fullscreen/Control.FullScreen.css";
import "leaflet-iconlayers/dist/iconLayers.css";
import "leaflet.pm/dist/leaflet.pm.css";
import "leaflet.polylinemeasure/Leaflet.PolylineMeasure.css";
import "leaflet-toolbar/dist/leaflet.toolbar.min.css";
import "leaflet.sidepanel/dist/style.css";

// Import plugins
import "@fortawesome/fontawesome-free";
import "leaflet.locatecontrol";
import "leaflet-easybutton";
import "leaflet.fullscreen";
import "leaflet-iconlayers";
import "leaflet.pm";
import "leaflet.polylinemeasure";
import "leaflet-toolbar";
import "leaflet.sidepanel";

// Refs
const mapContainer = ref(null);
const map = ref(null);

// Constants
const MAP_CENTER = [-2.3167, 115.3667];
const MAP_ZOOM = 11.5;

// Initialize Base Layers
const initializeBaseLayers = () => {
    // OSM Base Layer
    const osmLayer = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            maxZoom: 19,
            attribution: "© OpenStreetMap contributors",
        }
    );

    // CartoDB Light
    const cartoLight = L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
        {
            attribution: "©OpenStreetMap, ©CartoDB",
            subdomains: "abcd",
            maxZoom: 19,
        }
    );

    // CartoDB Dark
    const cartoDark = L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
        {
            attribution: "©OpenStreetMap, ©CartoDB",
            subdomains: "abcd",
            maxZoom: 19,
        }
    );

    // CartoDB Voyager
    const cartoVoyager = L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
        {
            attribution: "©OpenStreetMap, ©CartoDB",
            subdomains: "abcd",
            maxZoom: 19,
        }
    );

    // Google Maps Layer
    const googleMaps = L.tileLayer(
        "https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}",
        {
            maxZoom: 20,
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "©Google Maps",
        }
    );

    // Google Satellite Layer
    const googleSatellite = L.tileLayer(
        "https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
        {
            maxZoom: 20,
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "©Google Maps",
        }
    );

    // OpenTopoMap Layer
    const otopomap = L.tileLayer(
        "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
        {
            maxZoom: 17,
            attribution: "©OpenTopoMap",
        }
    );

    return {
        osm: osmLayer,
        cartoLight,
        cartoDark,
        cartoVoyager,
        googleMaps,
        googleSatellite,
        otopomap,
    };
};

// Base Map Icons
const baseMapIcons = {
    greyscale: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fas fa-layer-group"></i>',
    }),
    dark: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fas fa-moon"></i>',
    }),
    cartoVoyager: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fas fa-map"></i>',
    }),
    googleMaps: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fab fa-google"></i>',
    }),
    googleSatellite: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fas fa-satellite"></i>',
    }),
    streetMap: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fas fa-road"></i>',
    }),
    topoMap: L.divIcon({
        className: "custom-div-icon",
        html: '<i class="fas fa-mountain"></i>',
    }),
};

// Initialize Map
const initializeMap = () => {
    const baseLayers = initializeBaseLayers();

    map.value = L.map(mapContainer.value, {
        zoom: MAP_ZOOM,
        center: MAP_CENTER,
        fullscreenControl: {
            pseudoFullscreen: true,
        },
        layers: [baseLayers.osm], // Set default layer
    });

    setTimeout(() => {
        map.value.invalidateSize();
    }, 1000);

    // Add controls after map initialization
    addMapControls();
    initializeBasemaps(baseLayers);
    initializeDrawTools();
    initializeSidePanels();
};

// Add Map Controls
const addMapControls = () => {
    // Home button using standard Leaflet control instead of easyButton
    const homeButton = L.control({ position: "topleft" });
    homeButton.onAdd = function () {
        const div = L.DomUtil.create("div", "leaflet-control leaflet-bar");
        const button = L.DomUtil.create("a", "home-button", div);
        button.innerHTML = '<i class="fas fa-home fa-lg"></i>';
        button.href = "#";
        button.title = "Zoom to Balangan";

        L.DomEvent.on(button, "click", function (e) {
            L.DomEvent.preventDefault(e);
            map.value.setView(MAP_CENTER, MAP_ZOOM);
        });

        return div;
    };
    homeButton.addTo(map.value);

    // Locate control
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
                outsideMapBoundsMsg:
                    "Kamu tampaknya berada di luar jangkauan peta",
            },
            locateOptions: {
                maxZoom: 18,
                watch: true,
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000,
            },
        })
        .addTo(map.value);

    // Scale control
    L.control.scale({ imperial: false }).addTo(map.value);

    // Measure control
    L.control.polylineMeasure({ position: "topleft" }).addTo(map.value);
};

// Initialize Basemaps
const initializeBasemaps = (baseLayers) => {
    const iconLayers = [
        {
            title: "Positron",
            layer: baseLayers.cartoLight,
            icon: baseMapIcons.greyscale,
        },
        { title: "Dark", layer: baseLayers.cartoDark, icon: baseMapIcons.dark },
        {
            title: "Voyager",
            layer: baseLayers.cartoVoyager,
            icon: baseMapIcons.cartoVoyager,
        },
        {
            title: "Google Maps",
            layer: baseLayers.googleMaps,
            icon: baseMapIcons.googleMaps,
        },
        {
            title: "Google Imagery",
            layer: baseLayers.googleSatellite,
            icon: baseMapIcons.googleSatellite,
        },
        { title: "OSM", layer: baseLayers.osm, icon: baseMapIcons.streetMap },
        {
            title: "Topo Map",
            layer: baseLayers.otopomap,
            icon: baseMapIcons.topoMap,
        },
    ];

    new L.Control.IconLayers(iconLayers, {
        position: "bottomright",
        maxLayersInRow: 4,
    }).addTo(map.value);
};

// Initialize Draw Tools
const initializeDrawTools = () => {
    // Create custom draw control
    const drawControl = L.control({ position: "topleft" });
    drawControl.onAdd = function () {
        const div = L.DomUtil.create("div", "leaflet-control leaflet-bar");
        const button = L.DomUtil.create("a", "draw-button", div);
        button.innerHTML = '<i class="fas fa-wrench fa-lg"></i>';
        button.href = "#";
        button.title = "Draw tools";

        L.DomEvent.on(button, "click", function (e) {
            L.DomEvent.preventDefault(e);
            togglePMToolbar();
        });

        return div;
    };
    drawControl.addTo(map.value);

    // Initialize PM controls
    map.value.pm.addControls({
        position: "topleft",
        drawCircle: false,
    });

    // Hide PM toolbar initially
    nextTick(() => {
        const pmToolbar = document.querySelector(".leaflet-pm-toolbar");
        if (pmToolbar) {
            pmToolbar.style.display = "none";
        }
    });
};

// Toggle PM Toolbar
const togglePMToolbar = () => {
    const pmToolbar = document.querySelector(".leaflet-pm-toolbar");
    if (pmToolbar) {
        pmToolbar.style.display =
            pmToolbar.style.display === "none" || pmToolbar.style.display === ""
                ? "block"
                : "none";
    }
};

// Initialize Side Panels
const initializeSidePanels = () => {
    L.control
        .sidepanel("mySidepanelLeft", {
            tabsPosition: "left",
            startTab: "tab-5",
        })
        .addTo(map.value);

    L.control
        .sidepanel("mySidepanelRight", {
            panelPosition: "right",
            tabsPosition: "top",
            pushControls: true,
            darkMode: true,
            startTab: 2,
        })
        .addTo(map.value);
};

// Lifecycle Hooks
onMounted(() => {
    initializeMap();
});

onBeforeUnmount(() => {
    if (map.value) {
        map.value.remove();
    }
});

// Expose methods and map instance if needed
defineExpose({
    map,
    togglePMToolbar,
});
</script>

<style scoped>
#map {
    width: 100%;
    height: 100vh;
}

.custom-div-icon {
    background: #fff;
    border: 2px solid rgba(0, 0, 0, 0.2);
    border-radius: 4px;
    text-align: center;
    line-height: 30px;
    width: 30px !important;
    height: 30px !important;
}

.home-button,
.draw-button {
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    text-decoration: none;
    color: #000;
    display: block;
}

.home-button:hover,
.draw-button:hover {
    background-color: #f4f4f4;
}
</style>
