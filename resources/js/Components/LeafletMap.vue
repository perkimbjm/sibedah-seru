<template>
    <div>
        <div
            id="map"
            role="map"
            class="h-[88vh] w-screen mx-auto overflow-hidden"
        ></div>
    </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useLeaflet } from "@/Composables/useLeaflet";

const { initializeMap, getMap } = useLeaflet();

onMounted(() => {
    initializeMap("map");
});
</script>

<style>
/* Import required styles */
@import "leaflet/dist/leaflet.css";
@import "leaflet.locatecontrol/dist/L.Control.Locate.min.css";
@import "leaflet-easybutton/src/easy-button.css";
@import "leaflet.fullscreen/Control.FullScreen.css";
@import "leaflet-iconlayers/dist/iconLayers.css";
@import "leaflet.pm/dist/leaflet.pm.css";
@import "leaflet.polylinemeasure/Leaflet.PolylineMeasure.css";
@import "leaflet-toolbar/dist/leaflet.toolbar.min.css";
@import "leaflet.sidepanel/dist/style.css";
</style>
