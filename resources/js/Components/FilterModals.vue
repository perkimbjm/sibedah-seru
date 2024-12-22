<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" v-if="showFilterModal">
        <div v-if="!showAllVillages" class="bg-white rounded-lg shadow-lg p-6 w-96 relative max-h-80vh overflow-y-auto">
            <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-bold mb-4">Filter Data</h2>
            <div>
                <div class="mb-4">
                    <p class="font-medium mb-2">Kecamatan:</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="district in districts" :key="district.id"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500 text-red-500 bg-red-100': selectedFilters.districts.includes(district.id) }"
                            @click="toggleFilter('districts', district.id)">
                            {{ district.name }}
                        </button>
                    </div>
                </div>
                <div class="mb-4" v-if="selectedFilters.districts.length > 0">
                    <p class="font-medium mb-2">Desa:</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="village in limitedVillages" :key="village.id"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500 text-red-500 bg-red-100': selectedFilters.villages.includes(village.id) }"
                            @click="toggleFilter('villages', village.id)">
                            {{ village.name }}
                        </button>
                        <button v-if="showViewMoreButton" @click="showAllVillages = true"
                            class="px-4 py-2 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-50">
                            Lihat Lainnya ({{ filteredVillages.length - 10 }})
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="font-medium mb-2">Tahun:</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="year in years" :key="year"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500 text-red-500 bg-red-100': selectedFilters.years.includes(year) }"
                            @click="toggleFilter('years', year)">
                            {{ year }}
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="font-medium mb-2">Jenis Bantuan:</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="type in types" :key="type"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500 text-red-500 bg-red-100': selectedFilters.types.includes(type) }"
                            @click="toggleFilter('types', type)">
                            {{ type }}
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="font-medium mb-2">Sumber Dana:</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="source in sources" :key="source"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500 text-red-500 bg-red-100': selectedFilters.sources.includes(source) }"
                            @click="toggleFilter('sources', source)">
                            {{ source }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button @click="resetFilters" class="px-4 py-2 bg-gray-300 rounded-md mr-2">Atur Ulang</button>
                <button @click="$emit('applyFilters', selectedFilters)"
                    class="px-4 py-2 bg-blue-500 text-white rounded-md">Pakai</button>
            </div>
        </div>

        <div v-else class="bg-white rounded-lg shadow-lg p-6 w-96 relative max-h-[80vh] overflow-y-auto">
            <div class="flex items-center mb-4">
                <button @click="showAllVillages = false" class="p-2 hover:bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h2 class="text-xl font-bold ml-2">Daftar Desa</h2>
            </div>

            <div class="space-y-2">
                <button v-for="village in filteredVillages" :key="village.id"
                    class="w-full px-4 py-2 rounded-md border border-gray-300 text-left text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :class="{ 'border-red-500 text-red-500 bg-red-100': selectedFilters.villages.includes(village.id) }"
                    @click="toggleFilter('villages', village.id)">
                    {{ village.name }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';

const props = defineProps({
    showFilterModal: {
        type: Boolean,
        required: true,
    },
    districts: {
        type: Array,
        required: true,
    },
    villages: {
        type: Array,
        required: true,
    },
    years: {
        type: Array,
        required: true,
    },
    types: {
        type: Array,
        required: true,
    },
    sources: {
        type: Array,
        required: true,
    },
    selectedFilters: {
        type: Object,
        required: true,
    },
});

const filteredVillages = computed(() => {
    if (props.selectedFilters.districts.length === 0) return [];

    const filtered = props.villages.filter(village =>
        props.selectedFilters.districts.includes(village.district_id)
    );

    console.log('Filtered Villages:', filtered);
    return filtered;
});

const showAllVillages = ref(false);

const limitedVillages = computed(() => {
    return filteredVillages.value.slice(0, 10);
});

const showViewMoreButton = computed(() => {
    return filteredVillages.value.length > 10;
});

const emit = defineEmits(['applyFilters', 'close', 'update:selectedFilters']);

const toggleFilter = (filterType, value) => {
    const filters = props.selectedFilters[filterType];
    const index = filters.indexOf(value);
    if (index > -1) {
        filters.splice(index, 1);
    } else {
        filters.push(value);
    }
    emit('update:selectedFilters', props.selectedFilters);
};

const resetFilters = () => {
    Object.keys(props.selectedFilters).forEach(key => {
        props.selectedFilters[key] = [];
    });
    emit('update:selectedFilters', props.selectedFilters);
    emit('applyFilters', props.selectedFilters);
};

</script>

<style scoped>
.max-h-80vh {
    max-height: 80vh;
}

.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #F7FAFC;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #F7FAFC;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #CBD5E0;
    border-radius: 3px;
}
</style>