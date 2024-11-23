<template>
    <MainLayout>
        <Head title="Bedah Rumah" />
        <div class="min-h-screen bg-white">
            <!-- Navigation Bar -->
            <nav class="sticky top-0 z-50 bg-white border-b">
                <div class="px-4 py-4">
                    <div
                        class="flex items-center space-x-6 overflow-x-auto scrollbar-hide"
                    >
                        <div
                            v-for="(item, index) in navigationItems"
                            :key="index"
                            class="flex flex-col items-center min-w-[56px] text-gray-600 hover:text-gray-900 cursor-pointer"
                        >
                            <component :is="item.icon" class="h-6 w-6 mb-1" />
                            <span class="text-xs whitespace-nowrap">{{
                                item.label
                            }}</span>
                        </div>

                        <div class="flex-shrink-0 pl-4 border-l">
                            <button
                                class="px-4 py-2 rounded-lg border border-gray-200 flex items-center gap-2"
                            >
                                <SlidersHorizontal class="h-4 w-4" />
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="flex relative">
                <!-- Listings Section -->
                <div
                    :class="[
                        'transition-all duration-300 ease-in-out overflow-y-auto',
                        showList ? 'hidden' : 'flex-1',
                        'p-4',
                    ]"
                >
                    <div class="p-4">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
                        >
                            <div
                                v-for="listing in listings"
                                :key="listing.id"
                                class="relative"
                            >
                                <!-- Image Carousel -->
                                <div
                                    class="relative aspect-[4/3] rounded-xl overflow-hidden group"
                                >
                                    <div class="absolute top-3 right-3 z-10">
                                        <button
                                            class="p-2 rounded-full bg-white/80 hover:bg-white transition-colors"
                                        >
                                            <Heart
                                                class="h-5 w-5 text-gray-600"
                                            />
                                        </button>
                                    </div>

                                    <!-- Image Navigation Dots -->
                                    <div
                                        class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1 z-10"
                                    >
                                        <button
                                            v-for="(
                                                image, idx
                                            ) in listing.images"
                                            :key="idx"
                                            class="w-1.5 h-1.5 rounded-full bg-white/60"
                                            :class="{
                                                'bg-white': idx === 0,
                                            }"
                                        ></button>
                                    </div>

                                    <img
                                        :src="listing.images[0]"
                                        :alt="listing.name"
                                        loading="lazy"
                                        class="w-full h-full object-cover"
                                    />
                                </div>

                                <!-- Listing Details -->
                                <div class="mt-3">
                                    <div
                                        class="flex justify-between items-start"
                                    >
                                        <div class="flex items-center">
                                            <UsersRoundIcon
                                                class="h-4 w-4 text-gray-800"
                                            />
                                            <span
                                                class="ml-1 font-medium text-gray-800"
                                                >{{ listing.name }}</span
                                            >
                                        </div>
                                        <div class="flex items-center">
                                            <Clock
                                                class="h-4 w-4 text-gray-400"
                                            />
                                            <span class="ml-1 text-sm">{{
                                                listing.years
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <MapPinnedIcon
                                            class="h-4 w-4 mr-2 text-pink-500"
                                        />
                                        <p class="text-sm text-gray-500">
                                            Kec. {{ listing.villages }}
                                        </p>
                                    </div>
                                    <div class="flex items-center">
                                        <MapPin
                                            class="h-4 w-4 mr-2 text-green-400"
                                        />
                                        <p class="text-sm text-gray-500">
                                            Kec. {{ listing.location }}
                                        </p>
                                    </div>
                                    <p class="mt-1">
                                        <span>{{ listing.detail }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <div
                    :class="[
                        'transition-all duration-300 ease-in-out',
                        showList ? 'w-full' : 'lg:w-[50%]', // Ubah dari 40% ke 50%
                        'h-[calc(100vh-76px)]',
                        'sticky top-[76px]', // Sesuaikan dengan tinggi navbar
                        { 'fixed inset-0 z-50': showList }, // Gunakan fixed ketika fullscreen
                    ]"
                >
                    <div class="h-full w-full bg-gray-100 relative">
                        <div id="map" class="w-full h-full"></div>

                        <!-- Toggle Button -->
                        <button
                            @click="toggleView"
                            class="absolute top-4 left-4 bg-white text-black font-medium py-2 px-4 rounded-full shadow-lg hover:shadow-xl transition-shadow duration-200 ease-in-out flex items-center space-x-2 z-[9999] border border-gray-200"
                        >
                            <ChevronRight
                                v-if="showList"
                                class="h-4 w-4"
                                :class="{ 'rotate-180 font-bold': showList }"
                            />
                            <span>{{
                                showList ? "Tampilkan daftar" : "&lt;"
                            }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <FooterBar />
    </MainLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import FooterBar from "@/Components/FooterBar.vue";
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import {
    Home,
    SlidersHorizontal,
    ChevronRight,
    MapPin,
    Clock,
    MapPinnedIcon,
    UsersRoundIcon,
} from "lucide-vue-next";

const showList = ref(false);
let map;

const toggleView = () => {
    showList.value = !showList.value;
    // Tunggu transisi CSS selesai (300ms) sebelum memperbarui peta
    setTimeout(() => {
        if (map) {
            map.invalidateSize();
        }
    }, 300); // Sesuaikan dengan durasi transisi CSS
};

const navigationItems = [{ icon: Home, label: "Pencarian Anda" }];

const listings = ref([
    {
        id: 1,
        name: "Duan",
        villages: "Desa Teluk Mesjid",
        location: "Batu Mandi",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah1.jpg"],
    },
    {
        id: 2,
        name: "Sarkowi",
        villages: "Desa Bangkal",
        location: "Halong",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah2.png"],
    },
    {
        id: 3,
        name: "Jannatun",
        villages: "Desa Teluk Bayur",
        location: "Juai",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah3.png"],
    },
    {
        id: 4,
        name: "Syarifuddin",
        villages: "Desa Kusambi Hulu",
        location: "Lampihong",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah4.jpg"],
    },
    {
        id: 5,
        name: "Duan",
        villages: "Desa Teluk Mesjid",
        location: "Batu Mandi",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah1.jpg"],
    },
    {
        id: 6,
        name: "Sarkowi",
        villages: "Desa Bangkal",
        location: "Halong",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah2.png"],
    },
    {
        id: 7,
        name: "Jannatun",
        villages: "Desa Teluk Bayur",
        location: "Juai",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah3.png"],
    },
    {
        id: 8,
        name: "Syarifuddin",
        villages: "Desa Kusambi Hulu",
        location: "Lampihong",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah4.jpg"],
    },
    {
        id: 9,
        name: "Syarifuddin",
        villages: "Desa Kusambi Hulu",
        location: "Lampihong",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah4.jpg"],
    },
    {
        id: 10,
        name: "Duan",
        villages: "Desa Teluk Mesjid",
        location: "Batu Mandi",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah1.jpg"],
    },
    {
        id: 11,
        name: "Sarkowi",
        villages: "Desa Bangkal",
        location: "Halong",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah2.png"],
    },
    {
        id: 12,
        name: "Jannatun",
        villages: "Desa Teluk Bayur",
        location: "Juai",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah3.png"],
    },
    {
        id: 13,
        name: "Syarifuddin",
        villages: "Desa Kusambi Hulu",
        location: "Lampihong",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah4.jpg"],
    },
    {
        id: 14,
        name: "Syarifuddin",
        villages: "Desa Kusambi Hulu",
        location: "Lampihong",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah4.jpg"],
    },
    {
        id: 15,
        name: "Duan",
        villages: "Desa Teluk Mesjid",
        location: "Batu Mandi",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah1.jpg"],
    },
    {
        id: 16,
        name: "Sarkowi",
        villages: "Desa Bangkal",
        location: "Halong",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah2.png"],
    },
    {
        id: 17,
        name: "Jannatun",
        villages: "Desa Teluk Bayur",
        location: "Juai",
        years: "2024",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah3.png"],
    },
    {
        id: 18,
        name: "Syarifuddin",
        villages: "Desa Kusambi Hulu",
        location: "Lampihong",
        years: "2023",
        detail: "BANTUAN RUMAH SWADAYA APBD",
        images: ["/img/rumah/rumah4.jpg"],
    },
]);

// Peta
onMounted(() => {
    map = L.map("map").setView([-2.337971, 115.458156], 15);

    // Tambahkan layer peta dari OpenStreetMap
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap",
    }).addTo(map);

    // Tambahkan marker contoh
    L.marker([-2.337971, 115.458156])
        .addTo(map)
        .bindPopup("Lokasi Contoh")
        .openPopup();

    // Atur posisi tombol zoom in dan zoom out ke kanan atas
    map.zoomControl.setPosition("topright");

    // Tambahkan event listener untuk window resize
    window.addEventListener("resize", () => {
        map.invalidateSize();
    });

    onUnmounted(() => {
        // Bersihkan event listener saat komponen dihapus
        window.removeEventListener("resize", () => {
            map.invalidateSize();
        });
    });
});
</script>

<style scoped>
.leaflet-container {
    width: 100%;
    height: 100%;
}

/* Pastikan container peta memiliki tinggi yang tepat */
#map {
    min-height: calc(100vh - 76px);
    width: 100%;
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
</style>
