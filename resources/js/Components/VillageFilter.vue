<template>
    <div class="search-container p-4">
        <!-- District select -->
        <div class="mb-4">
            <select v-model="selectedDistrict" @change="fetchVillages" class="w-full px-4 py-2 border rounded-lg"
                placeholder="Pilih Kecamatan">
                <option value="" disabled selected>Pilih Kecamatan</option>
                <option v-for="district in districts" :key="district.id" :value="district.id">
                    {{ district.name }}
                </option>
            </select>
        </div>

        <!-- Village select (filtered by district) -->
        <div class="mb-4">
            <select v-model="selectedVillage" @change="filterByVillage" class="w-full px-4 py-2 border rounded-lg"
                placeholder="Pilih Desa">
                <option value="" disabled selected>Pilih Desa</option>
                <option v-for="village in villages" :key="village.id" :value="village.id">
                    {{ village.name }}
                </option>
            </select>
        </div>

        <!-- Reset Button -->
        <button @click="resetFilters" class="px-4 py-2 bg-red-500 text-white rounded-lg">Reset</button>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

const districts = ref([]);
const villages = ref([]);
const selectedDistrict = ref("");
const selectedVillage = ref("");
const desaLayer = {};

// Fetch villages based on selected district
const fetchVillages = async () => {
    if (selectedDistrict.value) {
        const response = await fetch(`/api/desa?district_id=${selectedDistrict.value}`);
        const data = await response.json();
        villages.value = data.data;

        // Update window.DesaSearch with filtered data
        window.DesaSearch = data.data;
        
        loadGeoJsonData("/api/desa/geojson", window.desa, window.tooltipDesa, "name", {
            district_id: selectedDistrict.value,
        });
    } else {
        villages.value = [];
    }
};

// Filter by selected village
const filterByVillage = () => {
    if (selectedDistrict.value && selectedVillage.value) {
        loadGeoJsonData("/api/desa/geojson", window.desa, window.tooltipDesa, "name", {
            district_id: selectedDistrict.value,
            village_id: selectedVillage.value,
        });
    }
};

// Watch for changes in selected district
watch(
    () => selectedDistrict.value,
    (newVal) => {
        window.selectedDistrict = newVal; // Save to global scope
        fetchVillages(); // Reload GeoJSON
    }
);

// Watch for changes in selected village
watch(
    () => selectedVillage.value,
    (newVal) => {
        window.selectedVillage = newVal; // Save to global scope
        filterByVillage(); // Filter GeoJSON by village
    }
);

// Reset all filters
const resetFilters = () => {
    selectedDistrict.value = "";
    selectedVillage.value = "";
    villages.value = [];

    // Reload GeoJSON without filters
    window.loadGeoJsonData("/api/desa/geojson", window.desa, window.tooltipDesa, "name");
};

// Fetch districts on component mount
onMounted(async () => {
    const response = await fetch("/api/kecamatan");
    const data = await response.json();
    districts.value = data.data;
});

// Cleanup before component unmount
onBeforeUnmount(() => {
    try {
        // Reset all reactive state
        districts.value = [];
        villages.value = [];
        selectedDistrict.value = "";
        selectedVillage.value = "";

        // Remove global references
        window.selectedDistrict = null;
        window.selectedVillage = null;

        console.log("VillageFilter cleanup completed."); // Log for debugging
    } catch (error) {
        console.error("Error during VillageFilter cleanup:", error);
    }
});
</script>