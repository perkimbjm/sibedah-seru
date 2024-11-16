import { onMounted, onUnmounted } from "vue";
import L from "leaflet";
import {
    osm,
    cartoLight,
    cartoDark,
    cartoVoyager,
    rbi,
    googleMaps,
    googleSatellite,
    otopomap,
} from "@/utils/basemap";

import "leaflet/dist/leaflet.css";
import "leaflet.locatecontrol/dist/L.Control.Locate.min.css";
import "leaflet-easybutton/src/easy-button.css";
import "leaflet.fullscreen/Control.FullScreen.css";
import "leaflet-iconlayers/dist/iconLayers.css";
import "leaflet.pm/dist/leaflet.pm.css";
import "leaflet.polylinemeasure/Leaflet.PolylineMeasure.css";
import "leaflet-toolbar/dist/leaflet.toolbar.min.css";
import "leaflet.sidepanel/dist/style.css";

// Import plugins
import "leaflet.locatecontrol";
import "leaflet-easybutton";
import "leaflet.fullscreen";
import "leaflet-iconlayers";
import "leaflet.pm";
import "leaflet.polylinemeasure";
import "leaflet-toolbar";
import "leaflet.sidepanel";

export function useLeaflet() {
    let map = null;

    const initializeMap = (containerId) => {
        const mapCenter = [-2.3167, 115.3667]; // Balangan coordinates
        const mapZoom = 11.5;

        // Initialize map
        map = L.map(containerId, {
            zoom: mapZoom,
            center: mapCenter,
            fullscreenControl: {
                pseudoFullscreen: true, // if true, fullscreen to page width and height
            },
            layers: [osm],
        });

        // Ensure map size is correct after loading
        setTimeout(() => {
            map.invalidateSize();
        }, 1000);

        // Add home button
        L.easyButton({
            states: [
                {
                    stateName: "zoom-to-default",
                    icon: "fa-home fa-lg",
                    title: "zoom to Balangan",
                    onClick: function (btn, map) {
                        // Menggunakan fungsi biasa
                        map.setView(mapCenter, mapZoom);
                        btn.state("zoom-to-default");
                    },
                },
            ],
        }).addTo(map);

        // Add locate control
        L.control
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
            .addTo(map);

        // Add scale control
        L.control.scale({ imperial: false }).addTo(map);

        // Add basemap control
        const iconLayers = [
            {
                title: "Positron",
                layer: cartoLight,
                icon: "/img/basemap/greyscale.png",
            },
            { title: "Dark", layer: cartoDark, icon: "/img/basemap/dark.png" },
            {
                title: "Voyager",
                layer: cartoVoyager,
                icon: "/img/basemap/voyager.png",
            },
            { title: "RBI", layer: rbi, icon: "/img/basemap/rbi.png" },
            {
                title: "Google Maps",
                layer: googleMaps,
                icon: "/img/basemap/googleMaps.png",
            },
            {
                title: "Google Imagery",
                layer: googleSatellite,
                icon: "/img/basemap/googleSatellite.png",
            },
            { title: "OSM", layer: osm, icon: "/img/basemap/streetmap.png" },
            {
                title: "Topo Map",
                layer: otopomap,
                icon: "/img/basemap/topomap.png",
            },
        ];

        L.Control.IconLayers(iconLayers, {
            position: "bottomright",
            maxLayersInRow: 4,
        }).addTo(map);

        // Add draw control
        L.easyButton({
            states: [
                {
                    stateName: "draw",
                    icon: "fa fa-wrench fa-lg",
                    title: "draw this map",
                    onClick: function () {
                        // Menggunakan fungsi biasa
                        togglePMToolbar();
                    },
                },
            ],
        }).addTo(map);

        // Initialize PM controls
        map.pm.addControls({
            position: "topleft",
            drawCircle: false,
            drawMarker: true,
            drawPolyline: true,
            drawRectangle: true,
            drawPolygon: true,
            cutPolygon: true,
            editMode: true,
            removalMode: true,
        });

        map.pm.setGlobalOptions({
            // Contoh pengaturan global, sesuaikan dengan kebutuhan Anda
            draggable: true,
            // Tambahkan pengaturan lain jika diperlukan
        });

        // Hide PM toolbar initially
        let pmToolbar = document.querySelector(".leaflet-pm-toolbar");
        if (pmToolbar) {
            pmToolbar.style.display = "none";
        }

        // Add measure control
        L.control.polylineMeasure({ position: "topleft" }).addTo(map);

        // Add sidepanels
        L.control
            .sidepanel("mySidepanelLeft", {
                tabsPosition: "left",
                startTab: "tab-5",
            })
            .addTo(map);

        const sidepanelRight = L.control
            .sidepanel("mySidepanelRight", {
                panelPosition: "right",
                tabsPosition: "top",
                pushControls: true,
                darkMode: true,
                startTab: 2,
            })
            .addTo(map);

        return map;
    };

    // Toggle PM Toolbar function
    const togglePMToolbar = () => {
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
    };

    onUnmounted(() => {
        if (map) {
            map.remove();
            map = null;
        }
    });

    return {
        initializeMap,
        getMap: () => map,
    };
}
