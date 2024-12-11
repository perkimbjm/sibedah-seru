<template>
    <div class="p-6 bg-gray-100 min-h-screen mx-3">
        <h1 class="text-lg md:text-2xl font-bold mb-4">
            Unduh Dokumen-Dokumen Publik
        </h1>
        <div class="mb-4">
            <!-- Data Summary -->
            <div class="text-sm text-gray-600 mb-4">
                Menampilkan {{ paginatedData.length }} dari
                {{ filteredData.length }} data
                {{ searchQuery ? "(hasil pencarian)" : "" }}
            </div>

            <!-- Search Bar and Actions -->
            <div class="flex flex-wrap justify-between items-center mb-4 space-y-2 md:space-y-0">
                <div class="relative w-full md:w-1/3">
                    <input v-model="searchQuery" type="text" placeholder="Search..."
                        class="w-full p-2 border rounded-lg shadow-sm focus:ring focus:ring-blue-300 mb-2 md:mb-0" />
                </div>
                <div class="flex flex-wrap items-center space-x-2">
                    <PrimaryButton @click="exportToExcel"
                        class="w-full md:w-auto mb-3 bg-emerald-500 hover:bg-emerald-600">
                        Ekspor ke Excel
                    </PrimaryButton>
                    <SecondaryButton @click="downloadSelected" :disabled="selectedRows.length === 0"
                        class="w-full md:w-auto mb-3 disabled:opacity-75 disabled:cursor-not-allowed">
                        Download Selected ({{ selectedRows.length }})
                    </SecondaryButton>
                </div>
            </div>

            <!-- Column Selector -->
            <div class="flex flex-wrap space-x-4 mb-4 items-start md:items-center">
                <label v-for="(column, key) in columnVisibility" :key="key"
                    class="flex items-start md:items-center space-x-2">
                    <input type="checkbox" v-model="columnVisibility[key]"
                        class="form-checkbox h-5 w-5 text-blue-600" />
                    <span>{{ key }}</span>
                </label>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredData.length === 0" class="text-center py-8 bg-white rounded-lg shadow">
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
            <table class="min-w-full table-auto text-xs sm:text-sm md:text-base">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-4 text-left">
                            <input type="checkbox" @change="toggleAllSelection" v-model="allSelected"
                                class="form-checkbox h-5 w-5" />
                        </th>
                        <th v-for="(key, index) in visibleColumns" :key="index" @click="sortColumn(key)"
                            class="p-4 text-left cursor-pointer hover:bg-gray-300">
                            <div class="flex items-center space-x-1">
                                <span>{{ key }}</span>
                                <span class="text-gray-500">
                                    <template v-if="sort.key === key">
                                        {{ sort.order === "asc" ? "↑" : "↓" }}
                                    </template>
                                    <template v-else>↕</template>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in paginatedData" :key="index" class="hover:bg-gray-100">
                        <td class="p-4">
                            <input type="checkbox" v-model="selectedRows" :value="row" class="form-checkbox h-5 w-5" />
                        </td>
                        <td v-if="columnVisibility['Jenis']" class="p-4">
                            {{ row.jenis }}
                        </td>
                        <td v-if="columnVisibility['Nomor dan Tahun']" class="p-4">
                            {{ row.nomorTahun }}
                        </td>
                        <td v-if="columnVisibility['Judul']" class="p-4">
                            {{ row.judul }}
                        </td>
                        <td v-if="columnVisibility['Deskripsi']" class="p-4">
                            {{ row.deskripsi }}
                        </td>
                        <td v-if="columnVisibility['Tombol Unduh']" class="p-4">
                            <a :href="row.url" target="_blank"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">
                                Unduh
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="filteredData.length > 0"
            class="mt-4 flex flex-wrap justify-between items-center text-xs sm:text-sm md:text-base">
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
import PrimaryButton from "./PrimaryButton.vue";
import SecondaryButton from "./SecondaryButton.vue";
import JSZip from "jszip";
import { saveAs } from "file-saver";

const searchQuery = ref("");
const rowsPerPage = ref(5);
const currentPage = ref(1);
const sort = ref({
    key: "",
    order: "",
});
const allSelected = ref(false);
const selectedRows = ref([]);
const columnVisibility = ref({
    Jenis: true,
    "Nomor dan Tahun": true,
    Judul: true,
    Deskripsi: true,
    "Tombol Unduh": true,
});

// Sample data
const tableData = ref([
    {
        jenis: "Peraturan Bupati",
        nomorTahun: "2/2024",
        judul: "Perubahan atas Peraturan Bupati Balangan Nomor 46 Tahun 2022 tentang Pedoman Pelaksanaan Pemberian Bantuan Rumah Swadaya",
        deskripsi: "Disisipkannya pada Perda Bupati no.46 tahun 2022 yaitu ayat yang menjelaskan tentang Syarat-syarat Penerima BRS, Besaran nilai BRS serta rincian penggunaan bantuan",
        url: "/doc/perbup2-2024.pdf",
    },
    {
        jenis: "Peraturan Daerah",
        nomorTahun: "25/2013",
        judul: "Dokumen B",
        deskripsi: "Deskripsi dokumen B",
        url: "/doc/perda-25-2013.pdf",
    },
    {
        jenis: "Peraturan Menteri",
        nomorTahun: "29/2018",
        judul: "Dokumen C",
        deskripsi: "Deskripsi dokumen C",
        url: "/doc/PermenPUPR29-2018.pdf",
    },
    {
        jenis: "Peraturan Menteri",
        nomorTahun: "29/2018",
        judul: "Dokumen D",
        deskripsi: "Deskripsi dokumen D",
        url: "/doc/PermenPUPR29-2018-Lamp1.pdf",
    },
    {
        jenis: "Peraturan Menteri",
        nomorTahun: "29/2018",
        judul: "Dokumen E",
        deskripsi: "Deskripsi dokumen E",
        url: "/doc/PermenPUPR29-2018-Lamp2.pdf",
    },
]);

// Computed and methods for sorting and filtering
const filteredData = computed(() => {
    let result = tableData.value.filter(
        (row) =>
            row.jenis.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            row.nomorTahun
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            row.judul.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            row.deskripsi
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase())
    );

    if (sort.value.key) {
        const key = sort.value.key.toLowerCase().replace(/\s+/g, "");
        const mapped = {
            jenis: "jenis",
            nomordantahun: "nomorTahun",
            judul: "judul",
            deskripsi: "deskripsi",
        };
        const sortKey = mapped[key];

        if (sortKey) {
            result = [...result].sort((a, b) => {
                const aVal = a[sortKey]
                    ? a[sortKey].toString().toLowerCase()
                    : "";
                const bVal = b[sortKey]
                    ? b[sortKey].toString().toLowerCase()
                    : "";
                const order = sort.value.order === "asc" ? 1 : -1;
                return aVal.localeCompare(bVal) * order;
            });
        }
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
watch([searchQuery, rowsPerPage], () => {
    currentPage.value = 1;
    selectedRows.value = [];
    allSelected.value = false;
});

// Enhanced sorting function
const sortColumn = (key) => {
    if (sort.value.key === key) {
        sort.value.order = sort.value.order === "asc" ? "desc" : "asc";
    } else {
        sort.value.key = key;
        sort.value.order = "asc";
    }
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
    const worksheet = XLSX.utils.json_to_sheet(tableData.value);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "TableData");
    XLSX.writeFile(workbook, "table_unduh.xlsx");
};

const downloadSelected = async () => {
    if (selectedRows.value.length === 0) {
        alert("Pilih minimal satu dokumen untuk diunduh");
        return;
    }

    const zip = new JSZip(); // Membuat instance JSZip
    const folder = zip.folder("dokumen"); // Membuat folder dalam ZIP

    // Menambahkan setiap dokumen ke dalam ZIP
    for (const row of selectedRows.value) {
        const response = await fetch(row.url); // Mengambil file dari URL
        const blob = await response.blob(); // Mengubah response menjadi blob
        folder.file(row.judul + ".pdf", blob); // Menambahkan file ke dalam folder ZIP
    }

    // Menghasilkan file ZIP dan menyimpannya
    zip.generateAsync({ type: "blob" }).then((content) => {
        saveAs(content, "dokumen_terpilih.zip"); // Mengunduh file ZIP
    });
};

const download = (row) => {
    const link = document.createElement("a");
    link.href = row.url;
    link.target = "_blank";
    link.download = "";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<style>
/* Additional styling if needed */
</style>
