<template>
    <div v-if="isActive" class="sidebar-wrapper">
        <div class="panel panel-default" id="features">
            <div class="panel-body mb-3">
                <div class="flex">
                    <div class="flex-3 pr-2">
                        <input type="text" v-model="searchInput"
                            class="form-control w-full border border-gray-300 rounded px-2 py-1 search"
                            placeholder="Filter" />
                    </div>
                    <div class="flex-1 pl-2">
                        <button type="button"
                            class="btn bg-blue-600 text-white hover:bg-blue-700 flex items-center justify-center w-full rounded px-2 py-1 sort"
                            data-sort="feature-name" id="sort-btn" @click="handleSort">
                            <i class="fas fa-sort"></i>&nbsp Sort
                        </button>
                    </div>
                </div>
            </div>
            <div class="sidebar-table border border-gray-300 rounded">
                <table class="table-auto w-full" id="feature-list">
                    <thead class="hidden">
                        <tr>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <!-- Template untuk list.js -->
                        <tr class="feature-item" style="display: none;">
                            <td class="feature-name"></td>
                            <td class="feature-district"></td>
                            <td class="align-middle">
                                <i class="fa-solid fa-chevron-right"></i>
                            </td>
                        </tr>
                        <tr v-for="feature in filteredFeatures" :key="feature.id"
                            class="feature-row hover:bg-gray-100 cursor-pointer" :id="feature.id"
                            @click="handleRowClick(feature)">
                            <td class="feature-name py-2">{{ feature.id }} {{ feature.name }}</td>
                            <td class="feature-district py-2">Kec. {{ feature.district }} </td>
                            <td class="align-middle px-1 py-2">
                                <i class="fa-solid fa-chevron-right"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>


<script setup>
import { ref, watch, onMounted, computed, onBeforeUnmount, onActivated, onDeactivated } from "vue";
import { } from '@fortawesome/fontawesome-free/js/all.js';

const map = ref(null);
const visibleFeatures = ref([]);
const searchInput = ref('');
const sortOrder = ref('asc');
const isActive = ref(true);
const isZooming = ref(false);


const props = defineProps({
    map: {
        type: Object,
        required: true
    }
})


const filteredFeatures = computed(() => {
    let features = [...visibleFeatures.value];

    // Filter berdasarkan search input
    if (searchInput.value) {
        const searchTerm = searchInput.value.toLowerCase();
        features = features.filter(feature =>
            feature.id.toString().toLowerCase().includes(searchTerm) ||
            feature.name.toLowerCase().includes(searchTerm) ||
            feature.district.toLowerCase().toString().includes(searchTerm)
        );
    }

    // Sort berdasarkan id + name
    features.sort((a, b) => {
        const aStr = `${a.id} ${a.name} ${a.district}`.toLowerCase();
        const bStr = `${b.id} ${b.name} ${a.district}`.toLowerCase();
        return sortOrder.value === 'asc'
            ? aStr.localeCompare(bStr)
            : bStr.localeCompare(aStr);
    });

    return features;
});

// Method untuk sync sidebar dengan bound peta
const syncSidebar = () => {
    if (!props.map || !isActive.value) return;

    const newFeatures = [];

    if (window.house && props.map.hasLayer(window.houseCluster)) {
        window.house.eachLayer((layer) => {
            if (props.map.getBounds().contains(layer.getLatLng())) {
                newFeatures.push({
                    id: layer.feature.properties.id,
                    name: layer.feature.properties.name,
                    district: layer.feature.properties.district,
                    lat: layer.getLatLng().lat,
                    lng: layer.getLatLng().lng
                });
            }
        });
    }

    visibleFeatures.value = newFeatures;
};

// Cleanup function
const cleanup = () => {
    if (props.map) {
        props.map.off('moveend', syncSidebar);
        props.map.off('layeradd layerremove', syncSidebar);
        props.map.off('zoomstart', handleZoomStart);
        props.map.off('zoomend', handleZoomEnd);
    }

    visibleFeatures.value = [];
    searchInput.value = '';

    if (window.houseCluster && props.map.hasLayer(window.houseCluster)) {
        try {
            window.houseCluster.unspiderfy();
        } catch (e) {
            console.warn('Error during unspiderfy:', e);
        }
        props.map.removeLayer(window.houseCluster);
    }
};

const handleZoomStart = () => {
    isZooming.value = true;
    if (window.houseCluster) {
        try {
            window.houseCluster.unspiderfy();
        } catch (e) {
            console.warn('Error during unspiderfy:', e);
        }
    }
};

const handleZoomEnd = () => {
    isZooming.value = false;
};

// Setup
const setup = () => {
    if (props.map) {
        props.map.on('moveend', syncSidebar);
        props.map.on('layeradd layerremove', syncSidebar);
        props.map.on('zoomstart', handleZoomStart);
        props.map.on('zoomend', handleZoomEnd);

        setTimeout(() => {
            syncSidebar();
        }, 1000);
    }
};

onMounted(() => {
    setup();
});

// Cleanup when component is unmounted
onBeforeUnmount(() => {
    cleanup();
});

// Handle keep-alive activation/deactivation
onActivated(() => {
    isActive.value = true;
    setup();
});

onDeactivated(() => {
    isActive.value = false;
    cleanup();
});


// Handle click pada row
const handleRowClick = async (feature) => {
    if (isZooming.value) return;

    const layer = window.house.getLayers().find(l =>
        l.feature.properties.id === feature.id
    );

    if (layer) {
        // Unspiderfy sebelum zoom
        if (window.houseCluster) {
            try {
                window.houseCluster.unspiderfy();
            } catch (e) {
                console.warn('Error during unspiderfy:', e);
            }
        }

        // Tunggu sedikit sebelum zoom
        await new Promise(resolve => setTimeout(resolve, 100));

        try {
            props.map.setView([feature.lat, feature.lng], 18, {
                animate: true,
                duration: 1
            });

            // Tunggu animation selesai sebelum open popup
            setTimeout(() => {
                if (layer && layer.openPopup) {
                    layer.openPopup();
                }
            }, 1000);
        } catch (e) {
            console.warn('Error during zoom:', e);
        }
    }
};


// Method untuk menangani pengurutan
const handleSort = () => {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
};
</script>


<style scoped>
.sidebar-wrapper {
    width: 100%;
    height: 100%;
    position: relative;
}


.panel-default {
    background: white;
    border-radius: 0.375rem;
}

.sidebar-table {
    max-height: calc(100vh - 200px);
    width: 100%;
    overflow-y: auto;
    padding: 16px;
}

.table {
    margin-bottom: 0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, .075);
    cursor: pointer;
}

/* Styling untuk row yang ditampilkan */
.feature-row {
    cursor: pointer;
    font-size: 1rem;
}

.feature-row:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Hide the template row */
.feature-item[style*="display: none"] {
    display: none !important;
}
</style>