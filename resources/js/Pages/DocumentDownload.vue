<template>
    <div class="p-6 mx-3 min-h-screen bg-gray-100">
        <h1 class="mb-4 text-lg font-bold md:text-2xl">
            Unduh Dokumen-Dokumen Publik
        </h1>
        <div class="mb-4">
            <!-- Data Summary -->
            <div class="mb-4 text-sm text-gray-600">
                Menampilkan {{ paginatedData.length }} dari
                {{ filteredData.length }} data
                {{ searchQuery ? "(hasil pencarian)" : "" }}
            </div>

            <!-- Search Bar and Actions -->
            <div class="flex flex-wrap justify-between items-center mb-4 space-y-2 md:space-y-0">
                <div class="relative w-full md:w-1/3">
                    <input v-model="searchQuery" type="text" placeholder="Search..."
                        class="p-2 mb-2 w-full rounded-lg border shadow-sm focus:ring focus:ring-blue-300 md:mb-0" />
                </div>
                <div class="flex flex-wrap items-center space-x-2">
                    <PrimaryButton @click="exportToExcel"
                        class="mb-3 w-full bg-emerald-500 md:w-auto hover:bg-emerald-600">
                        Ekspor ke Excel
                    </PrimaryButton>
                    <SecondaryButton @click="downloadSelected" :disabled="selectedRows.length === 0"
                        class="mb-3 w-full md:w-auto disabled:opacity-75 disabled:cursor-not-allowed">
                        Download Selected ({{ selectedRows.length }})
                    </SecondaryButton>
                </div>
            </div>

            <!-- Column Selector -->
            <div class="flex flex-wrap items-start mb-4 space-x-4 md:items-center">
                <label v-for="(download, key) in columnVisibility" :key="key"
                    class="flex items-start space-x-2 md:items-center">
                    <input type="checkbox" v-model="columnVisibility[key]"
                        class="w-5 h-5 text-blue-600 form-checkbox" />
                    <span>{{ key }}</span>
                </label>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredData.length === 0" class="py-8 text-center bg-white rounded-lg shadow">
            <div class="text-gray-500">
                {{
                    searchQuery
                        ? "Tidak ada data yang sesuai dengan pencarian Anda"
                        : "Tidak ada data yang tersedia"
                }}
            </div>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-xs table-auto sm:text-sm md:text-base">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-4 text-left">
                            <input type="checkbox" @change="toggleAllSelection" v-model="allSelected"
                                class="w-5 h-5 form-checkbox" />
                        </th>
                        <th v-for="(key, index) in visibleColumns" :key="index" @click="sortColumn(key)"
                            class="p-4 text-left transition-colors duration-200 cursor-pointer hover:bg-gray-300"
                            :title="`Klik untuk mengurutkan berdasarkan ${key} (${getSortType(key)})`">
                            <div class="flex items-center space-x-1">
                                <span>{{ key }}</span>
                                <span class="ml-1 text-gray-500">
                                    <template v-if="sort.key === getSortKey(key)">
                                        <span v-if="sort.order === 'desc'" class="font-bold text-blue-600">↓</span>
                                        <span v-else class="font-bold text-blue-600">↑</span>
                                    </template>
                                    <template v-else class="text-gray-400">↕</template>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(download, index) in paginatedData" :key="index" class="hover:bg-gray-100">
                        <td class="p-4">
                            <input type="checkbox" v-model="selectedRows" :value="download"
                                class="w-5 h-5 form-checkbox" />
                        </td>
                        <td v-if="columnVisibility['Jenis']" class="p-4">
                            {{ download.title }}
                        </td>
                        <td v-if="columnVisibility['Nomor dan Tahun']" class="p-4">
                            {{ download.year }}
                        </td>
                        <td v-if="columnVisibility['Deskripsi']" class="p-4">
                            {{ download.description }}
                        </td>
                        <td v-if="columnVisibility['Tombol Unduh']" class="p-4">
                            <a :href="download.file_url" target="_blank"
                                class="px-4 py-2 text-white bg-blue-500 rounded-lg shadow hover:bg-blue-600"
                                loading="lazy">
                                Unduh
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="filteredData.length > 0"
            class="flex flex-wrap justify-between items-center mt-4 text-xs sm:text-sm md:text-base">
            <div class="text-gray-600">
                Halaman {{ currentPage }} dari {{ totalPages }} ({{
                    (currentPage - 1) * rowsPerPage + 1
                }}-{{
                    Math.min(currentPage * rowsPerPage, filteredData.length)
                }})
            </div>
            <div class="flex items-center space-x-2">
                <button @click="prevPage" :disabled="currentPage === 1"
                    class="px-4 py-2 bg-gray-200 rounded-lg shadow hover:bg-gray-300 disabled:opacity-50">
                    Prev
                </button>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    class="px-4 py-2 bg-gray-200 rounded-lg shadow hover:bg-gray-300 disabled:opacity-50">
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import * as XLSX from "xlsx";
import { ref, computed, watch } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import JSZip from "jszip";
import { saveAs } from "file-saver";

const searchQuery = ref("");
const rowsPerPage = ref(20);
const currentPage = ref(1);
const sort = ref({
    key: "created_at", // Default sorting berdasarkan waktu penambahan
    order: "desc", // Descending untuk yang terakhir ditambahkan muncul pertama
});
const allSelected = ref(false);
const selectedRows = ref([]);
const columnVisibility = ref({
    Jenis: true,
    "Nomor dan Tahun": true,
    Deskripsi: true,
    "Tombol Unduh": true,
});

const props = defineProps({
    downloads: {
        type: Array,
        required: true
    }
});

// Computed and methods for sorting and filtering
const filteredData = computed(() => {
    let result = props.downloads.filter(
        (download) =>
            download.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            download.year
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            download.description
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase())
    );

    // Always apply sorting, even if no specific column is selected
    if (sort.value.key) {
        // Handle different data types for sorting based on column type
        if (sort.value.key === "title") {
            // String sorting for title column (alphabetical)
            result = [...result].sort((a, b) => {
                const aVal = a.title ? a.title.toString().toLowerCase() : "";
                const bVal = b.title ? b.title.toString().toLowerCase() : "";
                const order = sort.value.order === "asc" ? 1 : -1;
                return aVal.localeCompare(bVal) * order;
            });
        } else if (sort.value.key === "year") {
            // String sorting for year column (alphabetical)
            result = [...result].sort((a, b) => {
                const aVal = a.year ? a.year.toString().toLowerCase() : "";
                const bVal = b.year ? b.year.toString().toLowerCase() : "";
                const order = sort.value.order === "asc" ? 1 : -1;
                return aVal.localeCompare(bVal) * order;
            });
        } else if (sort.value.key === "description") {
            // String sorting for description column (alphabetical)
            result = [...result].sort((a, b) => {
                const aVal = a.description ? a.description.toString().toLowerCase() : "";
                const bVal = b.description ? b.description.toString().toLowerCase() : "";
                const order = sort.value.order === "asc" ? 1 : -1;
                return aVal.localeCompare(bVal) * order;
            });
        } else if (sort.value.key === "created_at") {
            // Date sorting for created_at column
            result = [...result].sort((a, b) => {
                const aVal = a.created_at ? new Date(a.created_at).getTime() : (a.id || 0);
                const bVal = b.created_at ? new Date(b.created_at).getTime() : (b.id || 0);
                const order = sort.value.order === "asc" ? 1 : -1;
                return (aVal - bVal) * order;
            });
        } else if (sort.value.key === "id") {
            // Numeric sorting for ID column
            result = [...result].sort((a, b) => {
                const aVal = a.id || 0;
                const bVal = b.id || 0;
                const order = sort.value.order === "asc" ? 1 : -1;
                return (aVal - bVal) * order;
            });
        }
    } else {
        // Default sorting by created_at or id if no specific sorting is set
        result = [...result].sort((a, b) => {
            const aVal = a.created_at ? new Date(a.created_at).getTime() : (a.id || 0);
            const bVal = b.created_at ? new Date(b.created_at).getTime() : (b.id || 0);
            return bVal - aVal; // Descending order (newest first)
        });
    }

    return result;
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage.value;
    return filteredData.value.slice(start, start + rowsPerPage.value);
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const visibleColumns = computed(() => {
    return Object.keys(columnVisibility.value).filter(
        (key) => columnVisibility.value[key]
    );
});

// Watch for changes that should reset pagination
watch([searchQuery, rowsPerPage, sort], () => {
    currentPage.value = 1;
    selectedRows.value = [];
    allSelected.value = false;
});

// Watch for changes in downloads data to maintain sorting
watch(() => props.downloads, () => {
    // Reset to first page when data changes
    currentPage.value = 1;
    // Maintain current sorting
}, { deep: true });

// Enhanced sorting function
const sortColumn = (key) => {
    // Map display column names to actual data keys with specific sorting logic
    const columnMapping = {
        "Jenis": "title",           // String sorting (alphabetical)
        "Nomor dan Tahun": "year",  // String sorting (alphabetical)
        "Deskripsi": "description", // String sorting (alphabetical)
        "Tombol Unduh": "created_at" // Date sorting (chronological)
    };

    const actualKey = columnMapping[key];

    if (sort.value.key === actualKey) {
        // Toggle order if same column is clicked
        sort.value.order = sort.value.order === "asc" ? "desc" : "asc";
    } else {
        // Set new column and default to ascending order
        sort.value.key = actualKey;
        sort.value.order = "asc";
    }

    // Reset to first page when sorting changes
    currentPage.value = 1;
};

// Helper function to get the actual sort key for a display column
const getSortKey = (displayKey) => {
    const columnMapping = {
        "Jenis": "title",           // String sorting (alphabetical)
        "Nomor dan Tahun": "year",  // String sorting (alphabetical)
        "Deskripsi": "description", // String sorting (alphabetical)
        "Tombol Unduh": "created_at" // Date sorting (chronological)
    };
    return columnMapping[displayKey];
};

const getSortType = (key) => {
    const columnMapping = {
        "Jenis": "Alphabetical",
        "Nomor dan Tahun": "Alphabetical",
        "Deskripsi": "Alphabetical",
        "Tombol Unduh": "Chronological"
    };
    return columnMapping[key] || "Unknown";
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

// Enhanced selection handling
const toggleAllSelection = () => {
    if (allSelected.value) {
        selectedRows.value = [...paginatedData.value];
    } else {
        selectedRows.value = [];
    }
};

// Watch selectedRows to update allSelected state
watch(selectedRows, (newVal) => {
    allSelected.value =
        paginatedData.value.length > 0 &&
        paginatedData.value.every((row) =>
            newVal.some((selected) => selected === row)
        );
});

// Export functions
const exportToExcel = () => {
    // Use filtered and sorted data for export
    const dataToExport = filteredData.value;
    const worksheet = XLSX.utils.json_to_sheet(dataToExport);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "DokumenPublik");
    XLSX.writeFile(workbook, "dokumen_publik.xlsx");
};

const downloadSelected = async () => {
    if (selectedRows.value.length === 0) {
        alert("Pilih minimal satu dokumen untuk diunduh");
        return;
    }

    const zip = new JSZip();
    const folder = zip.folder("dokumen");

    for (const row of selectedRows.value) {
        const response = await fetch(row.file_url);
        const blob = await response.blob();
        folder.file(row.title + ".pdf", blob);
    }

    zip.generateAsync({ type: "blob" }).then((content) => {
        saveAs(content, "dokumen_terpilih.zip");
    });
};
</script>

<style>
/* Additional styling if needed */
</style>
