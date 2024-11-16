<template>
    <div class="p-4 bg-gray-100 min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Dokumen Listing</h1>

            <!-- Search and Filter -->
            <div class="flex flex-wrap gap-4 mb-4">
                <input
                    v-model="search"
                    placeholder="Cari..."
                    class="p-2 border rounded-md flex-grow"
                    @input="filterData"
                />
                <select
                    v-model="filterYear"
                    @change="filterData"
                    class="p-2 border rounded-md"
                >
                    <option value="">Semua Tahun</option>
                    <option
                        v-for="year in uniqueYears"
                        :key="year"
                        :value="year"
                    >
                        {{ year }}
                    </option>
                </select>
            </div>

            <!-- Bulk Actions and Export -->
            <div class="flex justify-between mb-4">
                <div>
                    <button
                        @click="toggleAllSelection"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md mr-2"
                    >
                        {{ allSelected ? "Batal Pilih Semua" : "Pilih Semua" }}
                    </button>
                    <button
                        @click="deleteBulk"
                        class="bg-red-500 text-white px-4 py-2 rounded-md"
                        :disabled="!selectedItems.length"
                    >
                        Hapus Terpilih
                    </button>
                </div>
                <button
                    @click="exportToExcel"
                    class="bg-green-500 text-white px-4 py-2 rounded-md"
                >
                    Export ke Excel
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">
                                <input
                                    type="checkbox"
                                    v-model="allSelected"
                                    @change="toggleAllSelection"
                                />
                            </th>
                            <th
                                v-for="column in visibleColumns"
                                :key="column.key"
                                class="px-4 py-2 text-left"
                            >
                                <div
                                    class="flex items-center cursor-pointer"
                                    @click="sort(column.key)"
                                >
                                    {{ column.label }}
                                    <ArrowUpIcon
                                        v-if="
                                            sortColumn === column.key &&
                                            sortDirection === 'asc'
                                        "
                                        class="w-4 h-4 ml-1"
                                    />
                                    <ArrowDownIcon
                                        v-if="
                                            sortColumn === column.key &&
                                            sortDirection === 'desc'
                                        "
                                        class="w-4 h-4 ml-1"
                                    />
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in paginatedData"
                            :key="item.id"
                            class="hover:bg-gray-100 transition-colors duration-200"
                        >
                            <td class="px-4 py-2">
                                <input
                                    type="checkbox"
                                    v-model="selectedItems"
                                    :value="item.id"
                                />
                            </td>
                            <td
                                v-for="column in visibleColumns"
                                :key="column.key"
                                class="px-4 py-2"
                            >
                                {{ item[column.key] }}
                            </td>
                            <td class="px-4 py-2">
                                <button
                                    @click="downloadDocument(item)"
                                    class="bg-blue-500 text-white px-2 py-1 rounded-md text-sm"
                                >
                                    Unduh
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-between items-center">
                <div>
                    <span
                        >Menampilkan {{ startIndex + 1 }}-{{ endIndex }} dari
                        {{ filteredData.length }} item</span
                    >
                </div>
                <div class="flex space-x-2">
                    <button
                        @click="prevPage"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 bg-gray-200 rounded-md disabled:opacity-50"
                    >
                        Sebelumnya
                    </button>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 bg-gray-200 rounded-md disabled:opacity-50"
                    >
                        Selanjutnya
                    </button>
                </div>
            </div>

            <!-- Manage Columns -->
            <div class="mt-4">
                <h3 class="font-bold mb-2">Atur Kolom:</h3>
                <div class="flex flex-wrap gap-2">
                    <label
                        v-for="column in columns"
                        :key="column.key"
                        class="flex items-center"
                    >
                        <input
                            type="checkbox"
                            v-model="column.visible"
                            class="mr-2"
                        />
                        {{ column.label }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { ArrowUpIcon, ArrowDownIcon } from "lucide-vue-next";

// Sample data
const documents = ref([
    {
        id: 1,
        number: "001/2023",
        year: 2023,
        title: "Dokumen A",
        description: "Deskripsi dokumen A",
    },
    {
        id: 2,
        number: "002/2023",
        year: 2023,
        title: "Dokumen B",
        description: "Deskripsi dokumen B",
    },
    {
        id: 3,
        number: "003/2022",
        year: 2022,
        title: "Dokumen C",
        description: "Deskripsi dokumen C",
    },
    {
        id: 4,
        number: "004/2022",
        year: 2022,
        title: "Dokumen D",
        description: "Deskripsi dokumen D",
    },
    {
        id: 5,
        number: "005/2021",
        year: 2021,
        title: "Dokumen E",
        description: "Deskripsi dokumen E",
    },
]);

// Table columns
const columns = ref([
    { key: "number", label: "Nomor dan Tahun", visible: true },
    { key: "title", label: "Judul", visible: true },
    { key: "description", label: "Deskripsi", visible: true },
]);

// Computed property for visible columns
const visibleColumns = computed(() =>
    columns.value.filter((column) => column.visible)
);

// Search and filter
const search = ref("");
const filterYear = ref("");
const filteredData = ref([]);

// Sorting
const sortColumn = ref("number");
const sortDirection = ref("asc");

// Pagination
const currentPage = ref(1);
const itemsPerPage = 10;

// Bulk actions
const selectedItems = ref([]);
const allSelected = ref(false);

// Filter function
const filterData = () => {
    filteredData.value = documents.value.filter((item) => {
        const matchesSearch = Object.values(item).some((value) =>
            value.toString().toLowerCase().includes(search.value.toLowerCase())
        );
        const matchesYear = filterYear.value
            ? item.year.toString() === filterYear.value
            : true;
        return matchesSearch && matchesYear;
    });
    currentPage.value = 1;
};

// Sort function
const sort = (column) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortColumn.value = column;
        sortDirection.value = "asc";
    }

    filteredData.value.sort((a, b) => {
        if (a[column] < b[column])
            return sortDirection.value === "asc" ? -1 : 1;
        if (a[column] > b[column])
            return sortDirection.value === "asc" ? 1 : -1;
        return 0;
    });
};

// Pagination computed properties
const totalPages = computed(() =>
    Math.ceil(filteredData.value.length / itemsPerPage)
);
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage);
const endIndex = computed(() =>
    Math.min(startIndex.value + itemsPerPage, filteredData.value.length)
);
const paginatedData = computed(() =>
    filteredData.value.slice(startIndex.value, endIndex.value)
);

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

// Bulk actions
const toggleAllSelection = () => {
    if (allSelected.value) {
        selectedItems.value = [];
    } else {
        selectedItems.value = filteredData.value.map((item) => item.id);
    }
    allSelected.value = !allSelected.value;
};

const deleteBulk = () => {
    documents.value = documents.value.filter(
        (item) => !selectedItems.value.includes(item.id)
    );
    selectedItems.value = [];
    allSelected.value = false;
    filterData();
};

// Export to Excel (mock function)
const exportToExcel = () => {
    console.log("Exporting to Excel:", filteredData.value);
    alert("Data telah diekspor ke Excel (simulasi)");
};

// Download document (mock function)
const downloadDocument = (item) => {
    console.log("Downloading document:", item);
    alert(`Mengunduh dokumen: ${item.title}`);
};

// Unique years for filter
const uniqueYears = computed(() => [
    ...new Set(documents.value.map((item) => item.year)),
]);

// Initialize filtered data
onMounted(() => {
    filterData();
});
</script>
