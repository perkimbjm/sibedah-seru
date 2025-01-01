<template>
    <div class="search-container p-4">
        <div class="relative">
            <input v-model="searchTerm" @input="handleSearch" type="text" placeholder="Cari desa..."
                class="w-full px-4 py-2 border rounded-lg" />
            <div v-if="searchResults.length"
                class="results-container absolute w-full mt-1 bg-gray-100 border rounded-md shadow-lg">
                <div v-for="result in searchResults" :key="result.id" @click="handleResultClick(result)"
                    class="result-item px-4 py-2 hover:bg-gray-300 cursor-pointer">
                    {{ result.name }}
                </div>
            </div>
        </div>
    </div>

    <VillageFilter />
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from "vue";
import VillageFilter from "./VillageFilter.vue";

const searchTerm = ref("");
const searchResults = ref([]);
const selectedPolygon = ref(null);
let flashInterval = null; // Untuk menyimpan interval ID

// Watch untuk searchTerm
const stopWatch = watch(searchTerm, (newValue) => {
    if (newValue.length > 0) {
        // Create unique key combining name and district
        const uniqueResults = [];
        const seen = new Set();
        
        window.DesaSearch.forEach(desa => {
            const uniqueKey = `${desa.name}-${desa.district_id}`;
            if (desa.name.toLowerCase().includes(newValue.toLowerCase()) && !seen.has(uniqueKey)) {
                seen.add(uniqueKey);
                uniqueResults.push(desa);
            }
        });
        
        searchResults.value = uniqueResults.slice(0, 5);
    } else {
        searchResults.value = [];
    }
});
const handleResultClick = (result) => {
    const map = window.map;
    const layer = map._layers[result.id];

    if (layer) {
        map.fitBounds(layer.getBounds());
        flashHighlight(layer);
        
        // Clear search and results
        searchTerm.value = "";
        searchResults.value = [];

        // Tambahkan map pane untuk hasil pencarian
        const pane = L.marker(layer.getBounds().getCenter(), {
            icon: L.divIcon({
                className: 'radar-blue p-4 m-6',
                html: `<div style="width: 10px; height: 10px; border-radius: 50%; background-color: blue; animation: pulse 2s infinite;"><br>${result.name}</div>`,
                iconSize: [10, 10],
            })
        }).addTo(map);
        setTimeout(() => {
            pane.remove();
        }, 3000); // Hapus pane setelah 3 detik
    }
};

// Function to trigger highlight flash
const flashHighlight = (layer) => {
    const originalStyle = {
        color: "skyblue",
        fillColor: "lightgrey",
        fillOpacity: 0.4,
        opacity: 0.85,
    };

    const highlightStyle = {
        color: "red",
        fillColor: "yellow",
        fillOpacity: 0.7,
        opacity: 1,
    };

    // Clear existing interval jika ada
    if (flashInterval) {
        clearInterval(flashInterval);
        flashInterval = null; // Reset interval ID
    }

    // Alternate styles
    let flashCount = 0;
    flashInterval = setInterval(() => {
        if (layer) {
            flashCount++;
            layer.setStyle(flashCount % 2 === 0 ? originalStyle : highlightStyle);
            if (flashCount >= 6) {
                clearInterval(flashInterval);
                layer.setStyle(originalStyle); // Reset to original style
                flashInterval = null; // Reset interval ID
            }
        } else {
            clearInterval(flashInterval); // Hentikan interval jika layer tidak ada
            flashInterval = null;
        }
    }, 500); // Change style every 500ms
};

// Cleanup sebelum komponen di-unmount
onBeforeUnmount(() => {
    try {
        // Hentikan watcher
        stopWatch();

        // Hapus interval jika masih aktif
        if (flashInterval) {
            clearInterval(flashInterval);
            flashInterval = null;
        }

        // Reset semua reactive state
        searchTerm.value = "";
        searchResults.value = [];
        selectedPolygon.value = null;
    } catch (error) {
        console.error("Error during DesaSearch cleanup:", error);
    }
});
</script>