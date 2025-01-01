<template>
    <div class="filter-modal bg-white rounded-lg shadow-lg">
        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800">Filter Data RTLH</h2>
            </div>

            <!-- Filter Form -->
            <div class="space-y-4">
                <!-- Kecamatan Filter -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kecamatan
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="district in districts" :key="district.id" @click="toggleDistrict(district.id)"
                            :class="[
                                'px-3 py-1 rounded-full text-sm border',
                                selectedDistrict.includes(district.id)
                                    ? 'bg-red-500 text-white border-red-500'
                                    : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400'
                            ]">
                            {{ district.name }}
                        </button>
                    </div>
                </div>

                <!-- Desa Filter -->
                <!-- Desa Filter -->
                <div class="form-group" v-if="selectedDistrict.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="village in limitedVillages" :key="village.id" @click="toggleVillage(village.id)"
                            :class="[
                                'px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500',
                                selectedVillage.includes(village.id) ? 'border-red-500 text-red-500 bg-red-100' : ''
                            ]">
                            {{ village.name }}
                        </button>

                        <button v-if="showViewMoreButton" @click="showAllVillagesModal = true"
                            class="px-4 py-2 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-50">
                            Lihat Lainnya ({{ villages.length - 8 }})
                        </button>
                    </div>
                </div>

                <!-- Modal for All Villages -->

                <div v-if="showAllVillagesModal"
                    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-96 relative max-h-[80vh] overflow-y-auto">
                        <button @click="showAllVillagesModal = false"
                            class="absolute top-2 right-2 p-2 hover:bg-gray-100 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold mb-4">Daftar Desa</h2>
                        <div class="space-y-2 max-h-[60vh] overflow-y-auto">
                            <button v-for="village in villages" :key="village.id"
                                class="w-full px-4 py-2 rounded-md border border-gray-300 text-left text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500 text-red-500 bg-red-100': selectedVillage.includes(village.id) }"
                                @click="toggleVillage(village.id)">
                                {{ village.name }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Area Filter -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Luas Rumah (m²)
                    </label>
                    <div class="flex space-x-4">
                        <input v-model="minArea" type="number" placeholder="Minimum"
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <input v-model="maxArea" type="number" placeholder="Maximum"
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Status Keselamatan Bangunan Filter -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kelayakan Rumah (Aspek Keselamatan Bangunan)
                    </label>
                    <select v-model="selectedStatusSafety"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option v-for="status_safety in statusOptions" :key="status_safety" :value="status_safety">
                            {{ status_safety }}
                        </option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kelayakan Rumah (Keseluruhan Aspek)
                    </label>
                    <select v-model="selectedStatus"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option v-for="status in statusOptions" :key="status" :value="status">
                            {{ status }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button @click="resetFilters"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Reset
                </button>
                <button @click="applyFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Terapkan
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, onBeforeUnmount } from 'vue';
import axios from 'axios';

const districts = ref([]);
const villages = ref([]);
const selectedDistrict = ref([]);
const selectedVillage = ref([]);
const selectedStatusSafety = ref('');
const selectedStatus = ref('');
const statusOptions = ['LAYAK', 'TIDAK LAYAK', 'MENUJU LAYAK', 'KURANG LAYAK'];
const minArea = ref('');
const maxArea = ref('');
const showAllVillagesModal = ref(false);

let fetchTimeout = null; // Untuk menyimpan timeout ID

// Methods
const loadDistricts = async () => {
    try {
        const response = await axios.get('/api/rtlh/districts');
        districts.value = response.data.map(district => ({ ...district }));
    } catch (error) {
        console.error('Error loading districts:', error);
    }
};

const toggleDistrict = (districtId) => {
    const index = selectedDistrict.value.indexOf(districtId);
    if (index === -1) {
        selectedDistrict.value.push(districtId);
    } else {
        selectedDistrict.value.splice(index, 1);
    }
    loadVillages();
};

const loadVillages = async () => {
    if (selectedDistrict.value.length === 0) {
        villages.value = [];
        return;
    }

    try {
        villages.value = [];
        for (const districtId of selectedDistrict.value) {
            const response = await axios.get(`/api/rtlh/villages/${districtId}`);
            villages.value.push(...response.data);
        }
    } catch (error) {
        console.error('Error loading villages:', error);
    }
};

const limitedVillages = computed(() => {
    return villages.value.slice(0, 8);
});

const showViewMoreButton = computed(() => {
    return villages.value.length > 8;
});

const toggleVillage = (villageId) => {
    const index = selectedVillage.value.indexOf(villageId);
    if (index === -1) {
        selectedVillage.value.push(villageId);
    } else {
        selectedVillage.value.splice(index, 1);
    }
};

const resetFilters = () => {
    selectedDistrict.value = [];
    selectedVillage.value = [];
    minArea.value = '';
    maxArea.value = '';
    selectedStatusSafety.value = '';
    selectedStatus.value = '';
    villages.value = [];
    emitFilter({});
};

const applyFilters = () => {
    const filters = {};
    if (selectedDistrict.value.length > 0) filters.district_id = selectedDistrict.value;
    if (selectedVillage.value.length > 0) filters.village_id = selectedVillage.value;
    if (minArea.value.length > 0) filters.area = minArea.value;
    if (maxArea.value) filters.area = maxArea.value;
    if (selectedStatusSafety.value) filters.status_safety = [selectedStatusSafety.value];
    if (selectedStatus.value) filters.status = [selectedStatus.value];
    emitFilter(filters);
};

const emitFilter = (filters) => {
    window.dispatchEvent(new CustomEvent('filterRtlh', {
        detail: filters
    }));
};

// Watch effect
watch(selectedDistrict, (newValue) => {
    selectedVillage.value = [];
    if (newValue.length > 0) {
        loadVillages();
    }
});

// Lifecycle
onMounted(() => {
    loadDistricts();
});

// Cleanup sebelum komponen di-unmount
onBeforeUnmount(() => {
    try {
        // Hapus timeout jika ada
        if (fetchTimeout) {
            clearTimeout(fetchTimeout);
            fetchTimeout = null;
        }

        // Reset semua reactive state
        districts.value = [];
        villages.value = [];
        selectedDistrict.value = [];
        selectedVillage.value = [];
        selectedStatusSafety.value = '';
        selectedStatus.value = '';
        minArea.value = '';
        maxArea.value = '';
        showAllVillagesModal.value = false;

        // Hapus event listeners jika ada
        window.removeEventListener('filterRtlh', emitFilter);
    } catch (error) {
        console.error('Error during RtlhFilter cleanup:', error);
    }
});
</script>


<style scoped>
.filter-modal {
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}

.form-group {
    position: relative;
}

.form-group:hover select {
    border-color: #a0aec0;
}
</style>