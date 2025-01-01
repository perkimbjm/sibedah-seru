<template>
    <div class="p-4 bg-white rounded-lg shadow-md w-full">
        <div v-if="isLoading" class="text-center">
            <span class="text-gray-500">Memuat...</span>
        </div>
        <div v-else-if="isError" class="text-center text-red-500">
            <span>Gagal memuat data.</span>
        </div>
        <div v-else-if="data" class="space-y-4">
            <img v-if="data.photo" 
                 :src="data.photo" 
                 :alt="data.name"
                 class="w-full h-48 object-cover rounded-lg">
            
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
    </div>
</template>

<script setup>
import { computed } from 'vue';

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

const displayData = computed(() => {
    if (!props.data) return {};
    
    const { photo, ...rest } = props.data;
    return rest;
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