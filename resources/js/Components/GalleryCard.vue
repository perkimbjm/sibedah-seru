<template>
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div
            class="flex flex-col md:flex-row justify-between items-start mb-16 gap-8"
        >
            <h2 class="text-5xl font-semibold">Best Practices</h2>
            <div class="max-w-lg">
                <p class="text-gray-700 text-lg mb-4">
                    Dokumentasi foto-foto terbaik pada kegiatan bedah seru
                    (bedah seribu rumah) di Kabupaten Balangan.
                </p>
                <Link
                    href="/bedah"
                    class="inline-flex items-center px-4 py-2 rounded-full border bg-lime-300 hover:bg-lime-500 hover:text-white transition-colors"
                >
                    Lihat Semua
                    <ChevronDownIcon class="w-4 h-4 ml-2" />
                </Link>
            </div>
        </div>

        <Carousel
            :items-to-show="itemsToShow"
            :wrap-around="true"
            :transition="1000"
            :autoplay="2000"
            class="gallery-carousel"
        >
            <Slide v-for="property in properties" :key="property.id">
                <div class="px-2">
                    <div class="relative group cursor-pointer">
                        <img
                            :src="property.image"
                            :alt="property.title"
                            loading="lazy"
                            class="w-full h-[300px] object-cover rounded-lg transition-transform duration-300 group-hover:scale-105"
                        />
                        <div
                            class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/50 to-transparent rounded-b-lg"
                        >
                            <h3 class="text-white font-medium">
                                {{ property.title }}
                            </h3>
                            <p
                                v-if="property.location"
                                class="text-white/80 text-sm flex items-center mt-1"
                            >
                                <MapPinIcon class="w-4 h-4 mr-1" />
                                {{ property.location }}
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
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { Carousel, Navigation, Pagination, Slide } from "vue3-carousel";
import { Link } from "@inertiajs/vue3";
import { ChevronDownIcon, MapPinIcon } from "lucide-vue-next";
import "vue3-carousel/dist/carousel.css";

const properties = [
    {
        id: 1,
        title: "BSPS 2023",
        image: "/img/rumah/rumah1.jpg",
        location: "Paringin",
    },
    {
        id: 2,
        title: "BSPS 2023",
        image: "/img/rumah/IMG_20221015_091740.jpg",
        location: "Juai",
    },
    {
        id: 3,
        title: "BSPS 2023",
        image: "/img/rumah/rumah2.png",
        location: "Awayan",
    },
    {
        id: 4,
        title: "BSPS 2023",
        image: "/img/rumah/rumah3.png",
        location: "Paringin Selatan",
    },
    {
        id: 5,
        title: "BSPS 2023",
        image: "/img/rumah/rumah4.jpg",
        location: "Awayan",
    },
];

const itemsToShow = ref(3); // Default untuk desktop

const responsiveItemsToShow = computed(() => {
    if (window.innerWidth <= 426) return 1; // Mobile: 1 item
    if (window.innerWidth <= 768) return 2; // Tablet: 2 items
    return 3; // Desktop: 3 items
});

const updateItemsToShow = () => {
    itemsToShow.value = responsiveItemsToShow.value;
};

onMounted(() => {
    updateItemsToShow();
    window.addEventListener("resize", updateItemsToShow);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", updateItemsToShow);
});
</script>

<style scoped>
.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 9999px;
    width: 40px;
    height: 40px;
    color: #374151;
}

.gallery-carousel :deep(.carousel__prev:hover),
.gallery-carousel :deep(.carousel__next:hover) {
    background-color: #f3f4f6;
}

.gallery-carousel :deep(.carousel__pagination) {
    margin-top: 1rem;
}

.gallery-carousel :deep(.carousel__pagination-button) {
    background-color: #e5e7eb;
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    margin: 0 4px;
}

.gallery-carousel :deep(.carousel__pagination-button--active) {
    background-color: #374151;
}
</style>
