<template>
    <div class="p-4 bg-white rounded-lg shadow-md w-full max-w-2xl mx-auto">
        <button v-if="data" @click="clearData" class="mb-4 text-red-500 hover:text-red-700">
            Tutup Detail
        </button>
        <!-- Loading State -->
        <div v-if="isLoading" class="space-y-4">
            <div class="animate-pulse">
                <div class="h-48 bg-gray-200 rounded-lg"></div>
                <div class="h-6 bg-gray-200 rounded mt-4 w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded mt-2 w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded mt-2 w-2/3"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="isError" class="text-center text-red-500">
            <span>Gagal memuat data.</span>
        </div>

        <!-- Data Display -->
        <transition name="fade" mode="out-in">
        <div v-if="data" class="space-y-4">
            <!-- Photo (if available) -->
            <div v-if="data.house_photos && data.house_photos.length > 0" class="space-y-2">
                <h3 class="text-lg font-semibold">Foto Rumah</h3>
                <img :src="data.house_photos" 
                    :alt="'Foto Rumah ' + data.name"
                    class="w-full h-48 object-cover rounded-lg">
            </div>

            <!-- Data Details -->
            <div class="space-y-2">
                <h2 class="text-xl font-semibold">{{ data.name || 'Tidak diketahui' }}</h2>
                <div class="grid grid-cols-1 gap-2">
                    <div v-for="(value, key) in displayData" 
                         :key="key" 
                         class="flex flex-col">
                        <dt class="text-sm font-medium text-gray-500">{{ formatLabel(key) }}</dt>
                        <dd class="text-base text-gray-900">{{ value || '-' }}</dd>
                    </div>
                </div>
            </div>
        </div>
    </transition>
        <!-- No Data State -->
        <div v-if="!data && !isLoading && !isError" class="text-center text-gray-500">
            <span>Tidak ada data yang ditampilkan.</span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Reactive state untuk data, loading, dan error
const data = ref(null);
const isLoading = ref(false);
const isError = ref(false);
const clearData = () => {
    data.value = null;
};

// Fungsi untuk memformat label
const formatLabel = (key) => {
    const labels = {
        name: 'Nama',
        is_renov: "Sudah Dibedah ?",
        house_photos: 'Foto',
        address: 'Alamat',
        district: 'Kecamatan',
        people: 'Jumlah Penghuni',
        area: 'Luas Rumah (m2)',
        pondasi: 'Pondasi',
        rngk_atap: 'Rangka Atap',
        klm_blk: "Kolom / Balok",
        atap: 'Atap',
        dinding: 'Dinding',
        lantai: 'Lantai',
        air: 'Sumber Air Minum',
        jenis_wc: 'Jenis WC',
        status_safety: 'Kelayakan Bangunan',
        status: 'Kelayakan (Keseluruhan)',
        note: 'Catatan',
    };

    return labels[key] || key
        .replace(/_/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

// Fungsi untuk menangani perubahan data RTLH
const handleRtlhDataChange = (newData) => {
    data.value = newData;
};


onMounted(() => {
    window.addEventListener('rtlhDataChanged', (event) => {
        console.log('Data yang diterima:', event.detail);
        handleRtlhDataChange(event.detail);
    });
});

onUnmounted(() => {
    window.removeEventListener('rtlhDataChanged', handleRtlhDataChange);
});

// Computed property untuk menampilkan data tanpa foto
const displayData = computed(() => {
    if (!data.value) return {};

    const { house_photos, is_renov, ...rest } = data.value; // Exclude photo dari data yang ditampilkan
    return {
        ...rest,
        is_renov: is_renov ? "Sudah" : "Belum"
    }
});
</script>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>