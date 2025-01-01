<template>
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex flex-col md:flex-row justify-between items-start mb-16 gap-8">
            <h2 class="text-5xl font-semibold">Best Practices</h2>
            <div class="max-w-lg">
                <p class="text-gray-700 text-lg mb-4">
                    Dokumentasi foto-foto terbaik pada kegiatan bedah seru
                    (bedah seribu rumah) di Kabupaten Balangan.
                </p>
                <Link href="/bedah"
                    class="inline-flex items-center px-4 py-2 rounded-full border bg-lime-300 hover:bg-lime-500 hover:text-white transition-colors">
                Lihat Semua
                <font-awesome-icon :icon="['fas', 'chevron-down']" class="w-4 h-4 ml-2" />
                </Link>
            </div>
        </div>

        <Carousel :settings="settings" :breakpoints="breakpoints" :wrap-around="true" :transition="1000"
            :autoplay="5000" class="gallery-carousel">
            <Slide v-for="property in properties" :key="property.id">
                <div class="carousel__item px-2">
                    <div class="relative group cursor-pointer">
                        <img :src="property.image" :alt="property.title" loading="lazy"
                            class="w-full h-[400px] object-cover rounded-lg transition-transform duration-300 group-hover:scale-200" />
                        <div class="ribbon">
                            {{ property.year }}
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/50 to-transparent rounded-b-lg">
                            <h2 class="text-white font-bold">
                                {{ property.type }}
                            </h2>
                            <h3 class="text-white font-medium">
                                a.n {{ property.name }}
                            </h3>
                            <p v-if="property.address" class="text-white/80 text-sm flex items-center mt-1">
                                <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="w-4 h-4 mr-1" />
                                {{ property.address }} Kec. {{ property.district }}
                            </p>
                        </div>
                    </div>
                </div>
            </Slide>

            <template #addons>
                <Navigation />
                <Pagination />
            </template>
        </Carousel>
    </section>
</template>

<script setup>
import { Carousel, Navigation, Pagination, Slide } from "vue3-carousel";
import { Link } from "@inertiajs/vue3";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faMapMarkerAlt, faChevronDown } from '@fortawesome/free-solid-svg-icons';
import "vue3-carousel/dist/carousel.css";

library.add(faMapMarkerAlt, faChevronDown);

const properties = [
    {
        id: 1,
        name: "Saukani",
        type: "Bantuan Korban Bencana",
        year: "2022",
        image: "/img/rumah/1131_SAUKANI.jpg",
        address: "Muara Jaya RT. 04",
        district: "Awayan"
    },
    {
        id: 2,
        name: "Sabirin",
        type: "Bantuan Rumah Swadaya",
        year: "2023",
        image: "/img/rumah/142_SABIRIN.jpg",
        address: "Bakung RT. 03",
        district: "Batumandi"
    },
    {
        id: 3,
        name: "Rusmilawati",
        type: "Bantuan Korban Bencana",
        year: "2022",
        image: "/img/rumah/1125_RUSMILAWATI.jpg",
        address: "Muara Jaya RT. 04",
        district: "Awayan"
    },
    {
        id: 4,
        name: "Misnawati",
        type: "Bantuan Korban Bencana",
        year: "2022",
        image: "/img/rumah/1133_MISNAWATI.jpg",
        address: "Muara Jaya RT. 04",
        district: "Awayan"
    },
    {
        id: 5,
        name: "M. Sidik",
        type: "Bantuan Korban Bencana",
        year: "2022",
        image: "/img/rumah/1127_M-SIDIK.jpg",
        address: "Muara Jaya RT. 02",
        district: "Awayan"
    },
    {
        id: 6,
        name: "Hapsah",
        type: "Bantuan Korban Bencana",
        year: "2022",
        image: "/img/rumah/1134_HAPSAH.jpg",
        address: "Muara Jaya RT. 03",
        district: "Awayan"
    },
    {
        id: 7,
        name: "Haniyati",
        type: "Bantuan Korban Bencana",
        year: "2023",
        image: "/img/rumah/1115_HANIYATI.jpg",
        address: "Murung Abuin RT. 02",
        district: "Paringin Selatan"
    },
    {
        id: 8,
        name: "Ardiansyah",
        type: "Bantuan Korban Bencana",
        year: "2022",
        image: "/img/rumah/1124_ARDIANSYAH.jpg",
        address: "Muara Jaya RT. 01",
        district: "Awayan"
    },
    {
        id: 9,
        name: "Arbani",
        type: "Bantuan Rumah Swadaya",
        year: "2023",
        image: "/img/rumah/356_ARBANI.jpg",
        address: "Gunung Batu RT. 03",
        district: "Tebing Tinggi"
    },
];

const settings = {
    itemsToShow: 1,
    snapAlign: "center",
};

const breakpoints = {
    768: {
        itemsToShow: 2,
        snapAlign: "center",
    },
    1024: {
        itemsToShow: 3,
        snapAlign: "center",
    },
};
</script>

<style scoped>
.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    background-color: rgba(255, 255, 255, 0.5);
    border: 1px solid #e5e7eb;
    border-radius: 9999px;
    width: 25px;
    height: 25px;
    color: #374151;
}

.gallery-carousel :deep(.carousel__prev:hover),
.gallery-carousel :deep(.carousel__next:hover) {
    background-color: #f3f4f6;
}

.gallery-carousel :deep(.carousel__pagination) {
    margin-top: 1rem;
}

.gallery-carousel :deep(.carousel__pagination-button--active) {
    background-color: #dedede;
    border-radius: 50%;
    margin: 0 6px;
}

.gallery-carousel :deep(.carousel__slide) {
    padding: 1px;
}

.ribbon {
    position: absolute;
    top: 10px;
    right: -30px;
    width: 120px;
    background-color: #f64444;
    color: #fff;
    text-align: center;
    line-height: 30px;
    transform: rotate(45deg);
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.4);
    font-weight: bold;
    z-index: 10;
}
</style>
