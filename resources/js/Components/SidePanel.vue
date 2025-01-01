<template>
    <div>
        <!-- Side Panel Left -->
        <div id="mySidepanelLeft" class="sidepanel" aria-label="side panel" aria-hidden="false">
            <div class="sidepanel-inner-wrapper">
                <nav class="sidepanel-tabs-wrapper" aria-label="sidepanel tab navigation">
                    <ul class="sidepanel-tabs">
                        <li class="sidepanel-tab" v-for="tab in leftTabs" :key="tab.id">
                            <a href="#" class="sidebar-tab-link" role="tab" :data-tab-link="tab.id"
                                @click.prevent="activeLeftTab = tab.id">
                                <font-awesome-icon v-if="tab.icon" :icon="tab.icon" size="lg" class="p-2 mb-2" />
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="sidepanel-content-wrapper">
                    <div class="sidepanel-content">
                        <div v-for="content in leftContents" :key="content.id" class="sidepanel-tab-content"
                            :data-tab-content="content.id" v-show="activeLeftTab === content.id">
                            <h2>{{ content.title }}</h2>
                            <p>{{ content.body }}</p>
                            <component :is="content.component" v-if="content.component" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidepanel-toggle-container">
                <button class="sidepanel-toggle-button" type="button" aria-label="toggle side panel"></button>
            </div>
        </div>

        <!-- Side Panel Right -->
        <div id="mySidepanelRight" class="sidepanel" aria-label="side panel" aria-hidden="false">
            <div class="sidepanel-inner-wrapper">
                <nav class="sidepanel-tabs-wrapper" aria-label="sidepanel tab navigation">
                    <ul class="sidepanel-tabs">
                        <li class="sidepanel-tab" v-for="tab in rightTabs" :key="tab.id">
                        <a href="#" 
                            class="sidebar-tab-link" 
                            role="tab" 
                            :class="{ 'active': activeRightTab === tab.id }"
                            :data-tab-link="tab.id"
                            @click.prevent="activeRightTab = tab.id">
                            {{ tab.label }}
                        </a>
                        </li>
                    </ul>
                </nav>
                <!-- Tab Content -->
                <div class="sidepanel-content-wrapper-2">
                    <div class="sidepanel-content">
                        <div v-for="content in computedRightContents" 
                            :key="content.id" 
                            class="sidepanel-tab-content"
                            :data-tab-content="content.id" 
                            v-show="activeRightTab === content.id">
                            <h2 class="font-bold text-2xl">{{ content.title }}</h2>
                            <p>{{ content.body }}</p>
                            <component 
                            :is="content.component" 
                            v-if="content.component" 
                            v-bind="content.props"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidepanel-toggle-container">
                <button class="sidepanel-toggle-button" type="button" aria-label="toggle side panel"></button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, markRaw, computed, onBeforeUnmount} from "vue";
import DesaSearch from "./DesaSearch.vue";
import RtlhFilter from "./RtlhFilter.vue";
import ListFeatures from "./ListFeatures.vue";
import DetailPanel from "./DetailPanel.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';

const Components = {
  DesaSearch: markRaw(DesaSearch),
  RtlhFilter: markRaw(RtlhFilter),
  ListFeatures: markRaw(ListFeatures),
  DetailPanel: markRaw(DetailPanel)
};


const activeLeftTab = ref("tab-2");
const activeRightTab = ref("tab-1");

const props = defineProps({
    map: {
        type: Object,
        required: true,
    }
});

const panelState = reactive({
  right: {
    data: null,
    isLoading: false,
    isError: false,
    activeTab: "tab-1"
  },
  left: {
    data: null,
    isLoading: false,
    isError: false,
    activeTab: "tab-2"
  }
});

onBeforeUnmount(() => {
    try {
        // Reset semua reactive state
        activeLeftTab.value = null;
        activeRightTab.value = null;
        rightPanelData.value = {};
        rightPanelLoading.value = false;
        rightPanelError.value = false;

        // Reset panel state
        Object.keys(panelState).forEach(key => {
            panelState[key].data = null;
            panelState[key].isLoading = false;
            panelState[key].isError = false;
            panelState[key].activeTab = null;
        });

        // Hapus event listeners dari sidepanel buttons jika ada
        const leftPanel = document.getElementById('mySidepanelLeft');
        const rightPanel = document.getElementById('mySidepanelRight');

        if (leftPanel) {
            const leftToggleBtn = leftPanel.querySelector('.sidepanel-toggle-button');
            if (leftToggleBtn) {
                leftToggleBtn.replaceWith(leftToggleBtn.cloneNode(true));
            }
            // Bersihkan konten
            const leftContent = leftPanel.querySelector('.sidepanel-content');
            if (leftContent) {
                leftContent.innerHTML = '';
            }
        }

        if (rightPanel) {
            const rightToggleBtn = rightPanel.querySelector('.sidepanel-toggle-button');
            if (rightToggleBtn) {
                rightToggleBtn.replaceWith(rightToggleBtn.cloneNode(true));
            }
            // Bersihkan konten
            const rightContent = rightPanel.querySelector('.sidepanel-content');
            if (rightContent) {
                rightContent.innerHTML = '';
            }
        }

        // Reset tabs
        leftTabs.value = [];
        rightTabs.value = [];

        // Hapus referensi komponen
        Object.keys(Components).forEach(key => {
            Components[key] = null;
        });

        // Bersihkan event listeners yang mungkin terdaftar di map
        if (props.map) {
            // Hapus event listeners spesifik yang mungkin telah didaftarkan
            props.map.off('click');
            props.map.off('moveend');
            props.map.off('zoomend');
        }


    } catch (error) {
        console.error('Error during SidePanel cleanup:', error);
    }
});

// Fungsi untuk mengupdate data panel
const updatePanelData = async (position, id) => {
    const panel = panelState[position];
    panel.isLoading = true;
    panel.isError = false;
    
    try {
        const abortController = new AbortController();
        const timeoutId = setTimeout(() => abortController.abort(), 10000); // 10 detik timeout

        const response = await fetch(`/api/bedah/houses/${id}`, {
            signal: abortController.signal
        });
        clearTimeout(timeoutId);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.features && data.features.length > 0) {
            const properties = data.features[0].properties;
            panelState[position].data = {
                id: properties.id,
                name: properties.name,
                address: properties.address,
                district: properties.district,
                type: properties.type,
                year: properties.year,
                source: properties.source,
                photo: properties.renovated_house_photos,
                lat: properties.lat,
                lng: properties.lng
            };
        }
        
        if (position === 'right') {
            activeRightTab.value = "tab-2";
        } else {
            activeLeftTab.value = "tab-2";
        }
    } catch (error) {
        panel.isError = true;
        console.error('Error fetching house details:', error);
    } finally {
        panel.isLoading = false;
    }
};

// Update rightContents dengan reactive state
const computedRightContents = computed(() => [
  {
    id: "tab-1",
    title: "Bedah Rumah",
    body: "Aktifkan layer Bedah Rumah terlebih dahulu",
    component: markRaw(ListFeatures),
    props: {
      map: props.map,
      onSelectFeature: (id) => updatePanelData('right', id)
    }
  },
  {
    id: "tab-2",
    title: "Detail Data Bedah Rumah",
    component: markRaw(DetailPanel),
    props: {
      map: props.map,
      data: panelState.right.data,
      isLoading: panelState.right.isLoading,
      isError: panelState.right.isError
    }
  }
]);

const rightPanelData = ref({});
const rightPanelLoading = ref(false);
const rightPanelError = ref(false);

const leftTabs = ref([
    { id: "tab-1", label: "Lihat Detail Data", icon: null},
    { id: "tab-2", label: "Cari Desa", icon: null},
    { id: "tab-3", label: "Filter RTLH", icon: null},
]);

const rightTabs = ref([
  { id: "tab-1", label: "List" },
  { id: "tab-2", label: "Detail" }
]);

const leftContents = ([
    {
        id: "tab-1",
        title: "Content 1",
        body: "Nam nec lacinia purus, in accumsan arcu...",
    },
    {
        id: "tab-2",
        title: "Cari Desa",
        component: Components.DesaSearch,
        body: "Aktifkan layer Kel / Desa terlebih dahulu. Lalu Gunakan kotak pencarian di atas untuk mencari desa",
    },
    {
        id: "tab-3",
        title: "Filter RTLH",
        component: Components.RtlhFilter,
        body: "Aktifkan layer RTLH terlebih dahulu.",
    },
]);

// Lazy load icons
const loadIcons = async () => {
    const { faEye, faMagnifyingGlass, faFilter } = await import('@fortawesome/free-solid-svg-icons');
    library.add(faEye, faMagnifyingGlass, faFilter);

    leftTabs.value = [
        { id: "tab-1", label: "Lihat Detail Data", icon: ['fas', 'eye']},
        { id: "tab-2", label: "Cari Desa", icon: ['fas', 'magnifying-glass']},
        { id: "tab-3", label: "Filter RTLH", icon: ['fas', 'filter']},
    ];
};

// Call loadIcons to lazy load the icons
loadIcons();

</script>

<style scoped>
.sidepanel-right {
    display: block;
    visibility: visible !important;
}

.desa-search-container {
    margin-bottom: 1rem;
}

.sidepanel-tabs {
  display: flex;
  gap: 1rem;
  padding: 0.75rem;
  background-color: #f3f4f6;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-tab-link {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  transition: all 0.2s;
}

.sidebar-tab-link:hover {
  background-color: #e5e7eb;
}

.sidebar-tab-link.active {
  background-color: #fff;
  color: #1f2937;
  font-weight: 500;
}

.sidepanel-content-wrapper-2 {
  position: absolute;
  top: 50px;
  height: calc(100% - 60px);
  width: 100%;
  color: #191a1d;
  overflow-y: auto;
  overflow-x: hidden;
  background-color: #fff;
}
.sidepanel-content-wrapper-2 .sidepanel-content {
    padding: 1rem;
    margin-top: 2rem;
}

.sidepanel-content-wrapper-2 .sidepanel-content h2 {
    font-size: large;
}

.sidepanel-content-wrapper-2 .sidepanel-content p {
    margin-bottom: 1rem;
}
</style>
