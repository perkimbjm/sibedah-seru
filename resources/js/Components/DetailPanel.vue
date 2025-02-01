<template>
    <div class="w-full p-4 bg-white rounded-lg shadow-md">
        <div v-if="isLoading" class="text-center">
            <span class="text-gray-500">Memuat...</span>
        </div>
        <div v-else-if="isError" class="text-center text-red-500">
            <span>Gagal memuat data.</span>
        </div>
        <div v-else-if="displayData" class="space-y-4">
            <!-- Hanya tampilkan gambar jika getPhotoUrl memiliki nilai -->
            <img v-if="getPhotoUrl" :src="getPhotoUrl" :alt="displayData.name"
                class="object-cover w-full h-48 rounded-lg">
            <div class="space-y-2">
                <h2 class="text-lg font-semibold">
                    {{ displayData.name || "Klik pada Salah Satu Data di List atau Titik Bedah Rumah pada Peta" }}</h2>
                <div class="grid grid-cols-1 gap-2">
                    <div v-for="(value, key) in filteredDisplayData" :key="key" class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">{{ formatLabel(key) }}</dt>
                        <dd class="text-base text-gray-900">{{ value || '-' }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    isError: {
        type: Boolean,
        default: false
    }
});

const localData = ref({});

// Add a watch to always prefer the latest click event data
watch(() => props.data, (newData) => {
    // Reset localData if props.data changes
    localData.value = {};
});

const displayData = computed(() => {
    // Prioritize localData from click events over props.data
    return localData.value.id ? localData.value : props.data || {};
});


// Computed untuk mendapatkan URL foto
const getPhotoUrl = computed(() => {
    if (displayData.value.photo) {
        return displayData.value.photo;
    }
    if (displayData.value.renovated_house_photos && displayData.value.renovated_house_photos.length > 0) {
        return displayData.value.renovated_house_photos[0].photo_url;
    }
    return null;
});

// Computed untuk data yang akan ditampilkan (tanpa foto)
const filteredDisplayData = computed(() => {
    const data = { ...displayData.value };
    // Hapus properti foto dari tampilan
    delete data.photo;
    delete data.renovated_house_photos;
    return data;
});

const handleHouseDataChange = (event) => {
    if (event.detail && typeof event.detail === 'object') {
        console.log('Data yang diterima dari peta:', event.detail);
        localData.value = event.detail;
    } else {
        console.warn('Data tidak valid:', event.detail);
        localData.value = {};
    }
};

onMounted(() => {
    window.addEventListener('data:houseChanged', handleHouseDataChange);
});

onUnmounted(() => {
    window.removeEventListener('data:houseChanged', handleHouseDataChange);
});

const formatLabel = (key) => {
    const labels = {
        name: 'Nama',
        photo: 'Foto',
        address: 'Alamat',
        district: 'Kecamatan',
        type: 'Jenis Bantuan',
        year: 'Tahun',
        source: 'Sumber Dana',
    };

    return labels[key] || key
        .replace(/_/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};
</script>
