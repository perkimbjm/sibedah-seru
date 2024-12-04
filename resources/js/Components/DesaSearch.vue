<template>
    <div class="search-container p-4">
        <div class="relative">
            <input
                v-model="searchTerm"
                @input="handleSearch"
                type="text"
                placeholder="Cari desa..."
                class="w-full px-4 py-2 border rounded-lg"
            />
            <div
                v-if="searchResults.length"
                class="results-container absolute w-full mt-1 bg-gray-100 border rounded-md shadow-lg"
            >
                <div
                    v-for="result in searchResults"
                    :key="result.id"
                    @click="handleResultClick(result)"
                    class="result-item px-4 py-2 hover:bg-gray-300 cursor-pointer"
                >
                    {{ result.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const searchTerm = ref("");
const searchResults = ref([]);
const selectedPolygon = ref(null);

watch(searchTerm, (newValue) => {
    if (newValue.length > 0) {
        searchResults.value = window.DesaSearch.filter((desa) =>
            desa.name.toLowerCase().includes(newValue.toLowerCase())
        ).slice(0, 5);
    } else {
        searchResults.value = [];
    }
});

const handleResultClick = (result) => {
    // Zoom to the bounds of the selected desa
    const map = window.map; // Pastikan Anda memiliki akses ke instance peta
    const layer = map._layers[result.id];

    if (layer) {
        map.fitBounds(layer.getBounds());

        // Trigger highlight flash
        flashHighlight(layer);
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

    // Alternate styles
    let flashCount = 0;
    const interval = setInterval(() => {
        flashCount++;
        layer.setStyle(flashCount % 2 === 0 ? originalStyle : highlightStyle);
        if (flashCount >= 6) {
            clearInterval(interval);
            layer.setStyle(originalStyle); // Reset to original style
        }
    }, 500); // Change style every 500ms
};
</script>
