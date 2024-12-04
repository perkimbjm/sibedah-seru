<template>
    <div>
        <!-- Side Panel Left -->
        <div
            id="mySidepanelLeft"
            class="sidepanel"
            aria-label="side panel"
            aria-hidden="false"
        >
            <div class="sidepanel-inner-wrapper">
                <nav
                    class="sidepanel-tabs-wrapper"
                    aria-label="sidepanel tab navigation"
                >
                    <ul class="sidepanel-tabs">
                        <li
                            class="sidepanel-tab"
                            v-for="tab in leftTabs"
                            :key="tab.id"
                        >
                            <a
                                href="#"
                                class="sidebar-tab-link"
                                role="tab"
                                :data-tab-link="tab.id"
                                @click.prevent="activeLeftTab = tab.id"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    :class="tab.iconClass"
                                    :viewBox="tab.viewBox"
                                >
                                    <path
                                        v-for="path in tab.paths"
                                        :d="path.d"
                                        :fill-rule="path.fillRule"
                                    />
                                </svg>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="sidepanel-content-wrapper">
                    <div class="sidepanel-content">
                        <div
                            v-for="content in leftContents"
                            :key="content.id"
                            class="sidepanel-tab-content"
                            :data-tab-content="content.id"
                            v-show="activeLeftTab === content.id"
                        >
                            <h4>{{ content.title }}</h4>
                            <div v-if="content.id === 'tab-2'">
                                <DesaSearch />
                            </div>
                            <p>{{ content.body }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidepanel-toggle-container">
                <button
                    class="sidepanel-toggle-button"
                    type="button"
                    aria-label="toggle side panel"
                ></button>
            </div>
        </div>

        <!-- Side Panel Right -->
        <div
            id="mySidepanelRight"
            class="sidepanel"
            aria-label="side panel"
            aria-hidden="false"
        >
            <div class="sidepanel-inner-wrapper">
                <nav
                    class="sidepanel-tabs-wrapper"
                    aria-label="sidepanel tab navigation"
                >
                    <ul class="sidepanel-tabs">
                        <li
                            class="sidepanel-tab"
                            v-for="tab in rightTabs"
                            :key="tab.id"
                        >
                            <a
                                href="#"
                                class="sidebar-tab-link"
                                role="tab"
                                :data-tab-link="tab.id"
                                @click.prevent="activeRightTab = tab.id"
                            >
                                {{ tab.label }}
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="sidepanel-content-wrapper">
                    <div class="sidepanel-content">
                        <div
                            v-for="content in rightContents"
                            :key="content.id"
                            class="sidepanel-tab-content"
                            :data-tab-content="content.id"
                            v-show="activeRightTab === content.id"
                        >
                            <h4>{{ content.title }}</h4>
                            <p>{{ content.body }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidepanel-toggle-container">
                <button
                    class="sidepanel-toggle-button"
                    type="button"
                    aria-label="toggle side panel"
                ></button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import DesaSearch from "./DesaSearch.vue";

const activeLeftTab = ref("tab-1");
const activeRightTab = ref("tab-1");
const leftTabs = ref([
    {
        id: "tab-1",
        iconClass: "bi bi-list",
        viewBox: "0 0 16 16",
        paths: [
            {
                d: "M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z",
                fillRule: "evenodd",
            },
        ],
    },
    {
        id: "tab-2",
        iconClass: "bi bi-geo",
        viewBox: "0 0 16 16",
        paths: [
            {
                d: "M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z",
                fillRule: "evenodd",
            },
            {
                d: "M8 14.5c.628 0 1.256-.052 1.882-.154-.09.351-.277.67-.544.944-.378.126-.648.265-.813.395-.751.25-1.82.414-3.024.414s-2.273-.163-3.024-.414c-.436-.145-.826-.33-1.116-.558A.617.617 0 0 1 1 15c0-.619.272-.88.48-1.109C1.78 13.478 2.505 13.25 3 13.144V8z",
                fillRule: null,
            },
        ],
    },
]);
const rightTabs = ref([
    { id: "tab-1", label: "List" },
    { id: "tab-2", label: "Pin" },
    { id: "tab-3", label: "Mark" },
    { id: "tab-4", label: "Bookmarks" },
    { id: "tab-5", label: "Settings" },
]);
const leftContents = ref([
    {
        id: "tab-1",
        title: "Content 1",
        body: "Nam nec lacinia purus, in accumsan arcu...",
    },
    {
        id: "tab-2",
        title: "Cari Desa",
        body: "Aktifkan layer Kel / Desa terlebih dahulu. Lalu Gunakan kotak pencarian di atas untuk mencari desa",
    },
]);
const rightContents = ref([
    {
        id: "tab-1",
        title: "Content 1",
        body: "Nam nec lacinia purus, in accumsan arcu...",
    },
    {
        id: "tab-2",
        title: "Content 2",
        body: "Etiam varius in neque a tristique...",
    },
]);
</script>

<style scoped>
.sidepanel-right {
    display: block !important;
    visibility: visible !important;
}

#mySidepanelRight {
    opacity: 10000 !important;
}

.desa-search-container {
    margin-bottom: 1rem;
}
</style>
