<template>
    <div
        id="map"
        role="map"
        class="h-[88vh] w-screen mx-auto overflow-hidden"
    ></div>
</template>

<script>
import L from "leaflet";
// import { useLeafletLocate } from "@/Composables/useLeafletLocate";
import "leaflet-easybutton";
import "leaflet.fullscreen";
import "leaflet.pm";
import "leaflet-toolbar";
import "leaflet.sidepanel";

export default {
    name: "MapView",
    mounted() {
        // Inisialisasi peta setelah DOM siap
        this.initMap();
    },
    methods: {
        initMap() {
            const mapCenter = [-2.3167, 115.3667];
            const mapZoom = 11.5;

            // Inisialisasi peta
            const map = new L.Map(this.$el, {
                zoom: mapZoom,
                center: mapCenter,
                fullscreenControl: {
                    pseudoFullscreen: true,
                },
                layers: [
                    L.tileLayer(
                        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                    ),
                ],
            });

            // Memastikan peta diperbarui setelah dimuat sepenuhnya
            setTimeout(() => {
                map.invalidateSize();
            }, 1000);

            // Tambahkan kontrol dan layer tambahan ke peta
            L.control.scale({ imperial: false }).addTo(map);

            // Tambahkan fitur toolbar
            map.pm.addControls({
                position: "topleft",
                drawCircle: false,
            });

            const pmToolbar = document.querySelector(".leaflet-pm-toolbar");
            if (pmToolbar) pmToolbar.style.display = "none";

            // Tambahkan tombol untuk mengaktifkan/menonaktifkan toolbar
            L.easyButton({
                states: [
                    {
                        stateName: "draw",
                        icon: "fa fa-wrench fa-lg",
                        title: "Draw on map",
                        onClick: function () {
                            if (pmToolbar.style.display === "none") {
                                pmToolbar.style.display = "block";
                            } else {
                                pmToolbar.style.display = "none";
                            }
                        },
                    },
                ],
            }).addTo(map);
        },
    },
};
</script>

<style scoped>
@import "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
@import "https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css";
@import "leaflet.locatecontrol/dist/L.Control.Locate.min.css";
@import "https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css";
@import "https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css";

#map {
    height: 100%;
}
</style>
