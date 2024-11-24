<template>
    <MainLayout>
        <Head title="Peta Digital" />
        <div class="relative">
            <div
                id="map"
                role="map"
                class="h-[87vh] w-full mx-auto overflow-hidden relative z-5"
            >
                <SidePanel />
            </div>
            <MapModals />
        </div>
    </MainLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import SidePanel from "@/Components/SidePanel.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import { ref, onMounted, onBeforeUnmount } from "vue";
import MapModals from "@/Components/MapModals.vue";
import { loadMapStyles } from "@/styles/map-styles";

const mapInstance = ref(null);
const loadedScripts = ref([]);

// Definisi semua tile layers
const TILE_LAYERS = {
    osm: {
        url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        options: {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 21,
            detectRetina: true,
            tileSize: 256,
            zoomOffset: 0,
        },
    },
    cartoLight: {
        url: "https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{r}.png",
        options: {
            maxZoom: 21,
            detectRetina: true,
            attribution:
                '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="https://cartodb.com/attributions">CartoDB</a>',
        },
    },
    googleSatellite: {
        url: "https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
        options: {
            maxZoom: 23,
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            detectRetina: true,
            attribution:
                "Maps Data: Google, Citra 2021 TerraMetrics, Data peta &copy; 2021",
        },
    },
    googleMaps: {
        url: "https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}",
        options: {
            maxZoom: 23,
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            detectRetina: true,
            attribution: "Powered by Google , Data Peta: &copy; 2021",
        },
    },
    cartoDark: {
        url: "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
        options: {
            maxZoom: 21,
            subdomains: "abcd",
            detectRetina: true,
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        },
    },
    otopomap: {
        url: "//{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
        options: {
            attribution: "© OpenStreetMap contributors. OpenTopoMap.org",
            detectRetina: true,
        },
    },
    cartoVoyager: {
        url: "https://cartodb-basemaps-1.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}{r}.png",
        options: {
            maxZoom: 23,
            detectRetina: true,
            attribution:
                '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: "abcd",
        },
    },
    rbi: {
        url: "https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}",
        options: {
            attribution:
                "Tiles &copy; RBI Indonesia Geospasial Portal, Badan Informasi Geospasial",
            maxZoom: 21,
            subdomains: ["server", "services"],
            tileSize: 512,
            zoomOffset: -1,
            opacity: 1,
            zIndex: 0,
            detectRetina: true,
        },
    },
};

const SCRIPTS = [
    {
        src: "https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js",
        critical: true,
    },
    {
        src: "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js",
        critical: true,
    },
    { src: "https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js" },
    {
        src: "https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js",
    },
    {
        src: "https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js",
    },
    {
        src: "https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.min.js",
    },
    {
        src: "https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js",
    },
    { src: "/js/Control.Geocoder.js" },
    { src: "/js/leaflet.groupedlayercontrol.js" },
    { src: "/js/iconLayers.js" },
    { src: "/js/Leaflet.PolylineMeasure.js" },
    { src: "/js/leaflet.toolbar.js" },
    { src: "/js/leaflet.pm.min.js" },
    { src: "/js/leaflet-sidepanel.min.js" },
];

const loadScript = (src, critical = false) => {
    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = src;
        script.defer = !critical;
        script.async = !critical;
        script.onload = () => resolve(script);
        script.onerror = () => reject(new Error(`Failed to load ${src}`));
        document.head.appendChild(script);
    });
};

const loadAllScripts = async () => {
    try {
        const criticalScripts = await Promise.all(
            SCRIPTS.filter((s) => s.critical).map((s) =>
                loadScript(s.src, true)
            )
        );
        loadedScripts.value.push(...criticalScripts);

        const nonCriticalScripts = await Promise.all(
            SCRIPTS.filter((s) => !s.critical).map((s) => loadScript(s.src))
        );
        loadedScripts.value.push(...nonCriticalScripts);
    } catch (error) {
        console.error("Error loading scripts:", error);
        throw error;
    }
};

const initMap = async () => {
    if (typeof L === "undefined") return;

    await new Promise((resolve) => setTimeout(resolve, 300));

    try {
        // Inisialisasi semua tile layers
        const layers = {};
        for (const [key, layer] of Object.entries(TILE_LAYERS)) {
            layers[key] = L.tileLayer(layer.url, layer.options);
        }

        // Simpan di window object untuk akses dari core.js
        window.cartoLight = layers.cartoLight;
        window.googleSatellite = layers.googleSatellite;
        window.googleMaps = layers.googleMaps;
        window.cartoDark = layers.cartoDark;
        window.otopomap = layers.otopomap;
        window.osm = layers.osm;
        window.cartoVoyager = layers.cartoVoyager;
        window.rbi = layers.rbi;

        const mapCenter = [-2.33668, 115.46028];
        const mapZoom = 18;

        mapInstance.value = L.map("map", {
            zoom: mapZoom,
            center: mapCenter,
            fullscreenControl: { pseudoFullscreen: true },
            layers: [layers.osm],
        });

        window.map = mapInstance.value;

        const response = await fetch("/js/core.js");
        const coreJsContent = await response.text();
        eval(coreJsContent);

        const sidepanelLeft = L.control
            .sidepanel("mySidepanelLeft", {
                tabsPosition: "left",
                startTab: "tab-1",
            })
            .addTo(map);
        console.log("Sidepanel kiri berhasil dimuat");

        const sidepanelRight = L.control
            .sidepanel("mySidepanelRight", {
                panelPosition: "right",
                tabsPosition: "top",
                darkMode: true,
                startTab: "tab-2",
            })
            .addTo(map);
        console.log("Sidepanel kanan berhasil dimuat");

        window.dispatchEvent(new Event("resize"));
    } catch (error) {
        console.error("Error initializing map:", error);
    }
};

onMounted(async () => {
    console.log("Component mounted");
    try {
        await loadMapStyles();
        await loadAllScripts();
        await new Promise((resolve) => setTimeout(resolve, 100));

        await initMap();
    } catch (error) {
        console.error("Error during initialization:", error);
    }
});

onBeforeUnmount(() => {
    if (mapInstance.value) {
        // Hapus semua kontrol dan layer
        mapInstance.value.eachLayer((layer) => {
            mapInstance.value.removeLayer(layer);
        });

        // Hapus semua kontrol
        for (const [, control] of Object.entries(
            mapInstance.value._controlCorners
        )) {
            control.innerHTML = "";
        }

        // Hapus instance map
        mapInstance.value.remove();
        mapInstance.value = null;
    }

    // Hapus semua script yang telah dimuat
    loadedScripts.value.forEach((script) => {
        if (script && script.parentNode) {
            script.parentNode.removeChild(script);
        }
    });

    // Reset semua referensi
    loadedScripts.value = [];

    // Hapus referensi map dari window object
    window.map = undefined;
    window.cartoLight = undefined;
    window.googleSatellite = undefined;
    window.googleMaps = undefined;
    window.cartoDark = undefined;
    window.otopomap = undefined;
    window.osm = undefined;
    window.cartoVoyager = undefined;
    window.rbi = undefined;

    loadedScripts.value = [];
    // Reset fungsi dan nilai
    loadMapStyles.value = null;
    loadAllScripts.value = null;

    // Bersihkan elemen DOM map
    const mapElement = document.getElementById("map");
    if (mapElement) {
        mapElement.innerHTML = "";
    }
});
</script>

<style>
:deep(.leaflet-control-layers) {
    z-index: 999;
}

.leaflet-control-layers-selector {
    top: 0;
    border-radius: 3px;
}

.leaflet-control-layers-selector:hover {
    background-color: #199900;
}

[type="checkbox"]:checked,
[type="checkbox"]:checked:hover,
[type="checkbox"]:checked:focus,
[type="radio"]:checked:hover,
[type="radio"]:checked:focus {
    border-color: transparent;
    background-color: #199900;
}

.leaflet-control-layers-group-name {
    font-size: small;
    font-weight: 600;
    margin-bottom: 0.2em;
    margin-left: 3px;
}
.leaflet-control-layers-group {
    margin-bottom: 0.5em;
}

.leaflet-control-layers label {
    font-size: 1em;
    top: 0;
    transition: font-size 0.5s ease, color 0.5s ease;
}

.leaflet-control-layers label:hover {
    font-size: 1.025em;
    color: #199900;
}

.leaflet-control-layers-scrollbar {
    overflow-y: scroll;
    padding-right: 10px;
}

.leaflet-tooltip.no-background {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: none;
    text-shadow: 1px 1px 0px #fff, -1px -1px 0px #fff, 1px -1px 0px #fff,
        -1px 1px 0px #fff;
}
</style>
