<template>
    <MainLayout>

        <Head title="Bedah Rumah" />
        <div class="min-h-screen bg-white">
            <!-- Navigation Bar -->
            <nav class="sticky top-0 z-50 bg-white border-b">
                <div class="px-4 py-4">
                    <div class="flex items-center space-x-6 overflow-x-auto scrollbar-hide">
                        <div v-for="(item, index) in navigationItems" :key="index"
                            class="flex flex-col items-center min-w-[56px] text-gray-600 hover:text-gray-900 cursor-pointer">
                            <component :is="item.icon" class="h-6 w-6 mb-1" />
                            <span class="text-xs whitespace-nowrap">{{ item.label }}</span>
                        </div>
                        <div class="flex-shrink-0 pl-4 border-l">
                            <button @click="showFilterModal = true"
                                class="px-4 py-2 rounded-lg border border-gray-200 flex items-center gap-2">
                                <SlidersHorizontal class="h-4 w-4" /> Filter
                            </button>
                        </div>
                        <span class="text-gray-600 mr-2">Menampilkan {{ listingsCount }} Data</span>
                        <!-- Tag Filter -->
                        <div class="flex space-x-2">
                            <span v-for="(selectedType, index) in selectedFilters.types" :key="index"
                                class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full flex items-center">
                                {{ selectedType }}
                                <button @click="removeFilter('types', selectedType)" class="ml-2 text-yellow-600">
                                    &times;
                                </button>
                            </span>
                            <span v-for="(selectedSource, index) in selectedFilters.sources" :key="index"
                                class="bg-orange-200 text-orange-800 px-2 py-1 rounded-full flex items-center">
                                {{ selectedSource }}
                                <button @click="removeFilter('sources', selectedSource)" class="ml-2 text-orange-600">
                                    &times;
                                </button>
                            </span>
                            <span v-for="(selectedYear, index) in selectedFilters.years" :key="index"
                                class="bg-slate-200 text-slate-800 px-2 py-1 rounded-full flex items-center">
                                {{ selectedYear }}
                                <button @click="removeFilter('years', selectedYear)" class="ml-2 text-slate-600">
                                    &times;
                                </button>
                            </span>
                            <span v-for="(selectedDistrictId, index) in selectedFilters.districts" :key="index"
                                class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full flex items-center">
                                {{ districts.find(d => d.id === selectedDistrictId)?.name || 'Unknown' }}
                                <button @click="removeFilter('districts', selectedDistrictId)"
                                    class="ml-2 text-blue-600">
                                    &times;
                                </button>
                            </span>
                            <span v-for="(selectedVillageId, index) in selectedFilters.villages" :key="index"
                                class="bg-green-200 text-green-800 px-2 py-1 rounded-full flex items-center">
                                {{ villages.find(d => d.id === selectedVillageId)?.name || 'Unknown' }}
                                <button @click="removeFilter('villages', selectedVillageId)"
                                    class="ml-2 text-green-600">
                                    &times;
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </nav>

            <FilterModals :show-filter-modal="showFilterModal" :districts="formattedDistricts" :villages="allVillages"
                :years="uniqueYears" :types="uniqueTypes" :sources="uniqueSources" :selected-filters="selectedFilters"
                @close="showFilterModal = false" @apply-filters="applyFilters" class="z-[9999]" />

            <!-- Main Content -->
            <div class="flex relative">
                <!-- Listings Section -->
                <div
                    :class="['transition-all duration-300 ease-in-out overflow-y-auto', showList ? 'hidden' : 'flex-1', 'p-4']">
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                            <div v-for="listing in listings" :key="listing.id" class="relative">
                                <div class="relative aspect-[4/3] rounded-xl overflow-hidden group">
                                    <div
                                        class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1 z-10">
                                        <button v-for="(image, idx) in listing.renovated_house_photos" :key="idx"
                                            class="w-1.5 h-1.5 rounded-full bg-white/60"
                                            :class="{ 'bg-white': idx === 0 }"></button>
                                    </div>
                                    <img :src="listing.renovated_house_photos?.[0]?.photo_url ? `/${listing.renovated_house_photos[0].photo_url}` : '/img/placeholder.png'"
                                        :alt="listing.name || 'No Name'" loading="lazy"
                                        class="w-full h-full object-cover" />
                                </div>
                                <div class="mt-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center">
                                            <UsersRoundIcon class="h-4 w-4 text-gray-800" />
                                            <span class="ml-1 font-medium text-gray-800">{{ listing.name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <Clock class="h-4 w-4 text-gray-400" />
                                            <span class="ml-1 text-sm">{{ listing.year }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <MapPinnedIcon class="h-4 w-4 mr-2 text-pink-500" />
                                        <p class="text-sm text-gray-500">{{ listing.address }}</p>
                                    </div>
                                    <div class="flex items-center">
                                        <MapPin class="h-4 w-4 mr-2 text-green-400" />
                                        <p class="text-sm text-gray-500">Kec. {{ listing.district.name }}</p>
                                    </div>
                                    <p class="mt-1"><span>{{ listing.type }} dari {{ listing.source }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <div
                    :class="['transition-all duration-300 ease-in-out', showList ? 'w-full' : 'lg:w-[50%]', 'h-[calc(100vh-76px)]', 'sticky top-[76px]', { 'fixed inset-0 z-50': showList }]">
                    <div class="h-full w-full bg-gray-100 relative">
                        <div id="map" class="w-full h-full"></div>

                        <!-- Toggle Button -->
                        <button @click="toggleView"
                            class="absolute top-4 left-4 bg-white text-black font-medium py-2 px-4 rounded-full shadow-lg hover:shadow-xl transition-shadow duration-200 ease-in-out flex items-center space-x-2 z-[9999] border border-gray-200">
                            <ChevronRight v-if="showList" class="h-4 w-4"
                                :class="{ 'rotate-180 font-bold': showList }" />
                            <span>{{ showList ? "Tampilkan daftar" : '<' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <FooterBar :links="links" />
    </MainLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, defineProps, computed, reactive } from "vue";
import { Head } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import FooterBar from "@/Components/FooterBar.vue";
import FilterModals from "@/Components/FilterModals.vue";
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import debounce from "lodash.debounce";
import {
    Home,
    SlidersHorizontal,
    ChevronRight,
    MapPin,
    Clock,
    MapPinnedIcon,
    UsersRoundIcon,
} from "lucide-vue-next";


defineProps({
    links: {
        type: Array,
        required: true,
    },
});

const showList = ref(false);

const resizeMap = () => {
    const navbarHeight = 76; // Tinggi navbar
    const height = window.innerHeight - navbarHeight;
    document.getElementById('map').style.height = `${height}px`;
};

let map;

const toggleView = () => {
    showList.value = !showList.value;
    setTimeout(() => {
        if (map) {
            map.invalidateSize();
        }
    }, 300); // Sesuaikan dengan durasi transisi CSS
};

const navigationItems = [{ icon: Home, label: "Pencarian Anda" }];

const listings = ref([]);
const listingsCount = computed(() => listings.value?.length ?? 0);
const showFilterModal = ref(false);
const selectedFilters = reactive({
    districts: [],
    villages: [],
    years: [],
    types: [],
    sources: [],
});

const districts = ref([]); // Untuk menyimpan data kecamatan
const villages = ref([]); // Untuk menyimpan data desa

const allVillages = computed(() => {
    if (!listings.value?.length) return [];
    const villagesSet = new Set();
    const villagesArray = [];

    listings.value.forEach(house => {
        if (house?.village && house?.district?.id && !villagesSet.has(house.village.id)) {
            villagesSet.add(house.village.id);
            villagesArray.push({
                id: house.village.id,
                name: house.village.name,
                district_id: house.district.id
            });
        }
    });

    return villagesArray.sort((a, b) => a.name.localeCompare(b.name));
});

const uniqueDistricts = computed(() =>
    [...new Set(listings.value.map(house => house.district.name))]
);

const villagesByDistrict = computed(() => {
    const villages = {};
    listings.value?.forEach(house => {
        if (house?.district?.id && house?.village) {
            if (!villages[house.district.id]) {
                villages[house.district.id] = [];
            }

            if (!villages[house.district.id].some(v => v.id === house.village.id)) {
                villages[house.district.id].push({
                    id: house.village.id,
                    name: house.village.name,
                    district_id: house.district.id
                });
            }
        }
    });
    return villages;
});

const uniqueYears = computed(() =>
    [...new Set(listings.value.map(house => house.year))]
        .filter(year => year) // Pastikan tahun valid
        .sort((a, b) => b - a) // Sort numerik descending
);

const uniqueTypes = computed(() =>
    [...new Set(listings.value.map(house => house.type))].filter(Boolean)
);

const uniqueSources = computed(() =>
    [...new Set(listings.value.map(house => house.source))].filter(Boolean)
)

const resetFilters = () => {
    selectedFilters.districts = [];
    selectedFilters.villages = [];
    selectedFilters.years = [];
    selectedFilters.types = [];
    selectedFilters.sources = [];
};


const fetchMarkers = debounce(async () => {
    try {
        let url;
        const queryParams = new URLSearchParams();

        // Tentukan URL berdasarkan lebar jendela
        if (window.innerWidth < 768) {
            // Jika lebar kurang dari 768px, gunakan fungsi getHouses
            url = `/api/bedah/houses`;
        } else {
            // Jika lebar lebih besar atau sama dengan 768px, gunakan getHousesInBounds
            url = `/api/bedah/houses/in-bounds`;

            // Tambahkan parameter bounding box
            if (!map) return;
            const bounds = map.getBounds();
            queryParams.append('south', bounds.getSouthWest().lat);
            queryParams.append('west', bounds.getSouthWest().lng);
            queryParams.append('north', bounds.getNorthEast().lat);
            queryParams.append('east', bounds.getNorthEast().lng);
        }

        // Perbaikan akses selectedFilters
        if (selectedFilters.districts?.length) {
            selectedFilters.districts.forEach(district => {
                queryParams.append('district_id[]', district);
            });
        }
        if (selectedFilters.villages?.length) {
            selectedFilters.villages.forEach(village => {
                queryParams.append('village_id[]', village);
            });
        }
        if (selectedFilters.years?.length) {
            selectedFilters.years.forEach(year => {
                queryParams.append('year[]', year);
            });
        }
        if (selectedFilters.types?.length) {
            selectedFilters.types.forEach(type => {
                queryParams.append('type[]', type);
            });
        }
        if (selectedFilters.sources?.length) {
            selectedFilters.sources.forEach(source => {
                queryParams.append('source[]', source);
            });
        }

        window.markersLayer?.clearLayers();

        const response = await fetch(`${url}?${queryParams}`);
        const data = await response.json();

        if (data.success && Array.isArray(data.data)) {
            listings.value = data.data;
            districts.value = [...new Set(data.data.map(house => house.district))]; // Simpan data kecamatan
            villages.value = [...new Set(data.data.map(house => house.village))]; // Simpan data desa
            // Tambahkan marker baru
            window.markersLayer = L.layerGroup().addTo(map);

            data.data.forEach((house) => {
                if (!house?.lat || !house?.lng || isNaN(house.lat) || isNaN(house.lng)) {
                    return;
                }

                const latlng = L.latLng(house.lat, house.lng);
                const marker = L.marker(latlng, {
                    icon: L.divIcon({
                        html: `
                            <div class="flex justify-center">
                                <span class="relative flex h-4 w-4">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-30"></span>
                                    <span class="relative inline-flex h-4 w-4 rounded-full bg-blue-500 opacity-75"></span>
                                </span>
                            </div>
                        `,
                        className: "marker-icon",
                    }),
                });

                const popupContent = `
                    <b>${house.name || 'Tidak ada nama'}</b><br>
                    ${house.address || ''}<br>
                    Kec. ${house.district?.name || ''}
                    ${house.map_popup_content || ''}
                `;

                marker.bindPopup(popupContent);
                window.markersLayer.addLayer(marker);
            });
        }
    } catch (error) {
        console.error("Error fetching markers:", error);
    }
}, 300);

const applyFilters = (filters) => {
    // Pastikan kita menyimpan ID untuk district dan village
    selectedFilters.districts = filters.districts;
    selectedFilters.villages = filters.villages;
    selectedFilters.years = filters.years;
    selectedFilters.types = filters.types;
    selectedFilters.sources = filters.sources;

    fetchMarkers();
    showFilterModal.value = false;
};

const formattedDistricts = computed(() => {
    const districtsSet = new Set();

    return listings.value
        ?.reduce((result, house) => {
            const district = house?.district;
            if (district?.id && !districtsSet.has(district.id)) {
                districtsSet.add(district.id);
                result.push({ id: district.id, name: district.name });
            }
            return result;
        }, [])
        .sort((a, b) => a.name.localeCompare(b.name)) || [];
});

const removeFilter = (filterType, value) => {
    const index = selectedFilters[filterType].indexOf(value);
    if (index > -1) {
        selectedFilters[filterType].splice(index, 1);
        applyFilters(); // Memanggil applyFilters untuk memperbarui tampilan
    }
};


// Peta
onMounted(() => {
    resizeMap();
    window.addEventListener("resize", resizeMap);

    map = L.map("map").setView([-2.3155972, 115.4979602], 16);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap",
    }).addTo(map);

    window.markersLayer = L.layerGroup().addTo(map);

    // Event listeners
    map.on("moveend", fetchMarkers);
    map.on('zoomend', fetchMarkers);
    map.zoomControl.setPosition("topright");

    // Panggil fetchMarkers dengan setTimeout untuk memastikan map sudah siap
    setTimeout(() => {
        fetchMarkers();
    }, 100);
});

onUnmounted(() => {
    window.removeEventListener("resize", resizeMap);
    map?.remove();
});
</script>

<style scoped>
.leaflet-container {
    width: 100%;
    height: 100%;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.transition-all {
    transition: all 0.3s ease-in-out;
}

:deep(.modal-overlay) {
    z-index: 9999 !important;
}
</style>