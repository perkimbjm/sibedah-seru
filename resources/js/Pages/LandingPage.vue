<template>
    <MainLayout>

        <Head />
        <Hero />
        <Cta />
        <StatisticCard :statistics="statistics" />
        <FeaturesList />
        <GalleryCard />
        <Faq :faqs="faqs" />
        <FooterBar :links="links" />
        <ScrollTop />
    </MainLayout>
</template>

<script setup>
import { defineAsyncComponent, shallowRef } from 'vue';
import { Head } from "@inertiajs/vue3";
import { defineProps } from "vue";

// Lazy load components
const MainLayout = defineAsyncComponent(() => import('@/Layouts/MainLayout.vue'));
const Hero = defineAsyncComponent(() => import('@/Components/Hero.vue'));
const ScrollTop = defineAsyncComponent(() => import('@/Components/ScrollTop.vue'));
const Cta = defineAsyncComponent(() => import('@/Components/Cta.vue'));
const FeaturesList = defineAsyncComponent(() => import('@/Components/FeaturesList.vue'));
const GalleryCard = defineAsyncComponent(() => import('@/Components/GalleryCard.vue'));
const Faq = defineAsyncComponent(() => import('@/Pages/Faq.vue'));
const StatisticCard = defineAsyncComponent(() => import('@/Pages/StatisticCard.vue'));
const FooterBar = defineAsyncComponent(() => import('@/Components/FooterBar.vue'));

defineProps({
    statistics: {
        type: Array,
        required: true,
    },
    faqs: {
        type: Array,
        required: true,
    },
    links: {
        type: Array,
        required: true,
    }
});

</script>

<style>
#text-hero {
    min-height: 300px; /* Sesuaikan dengan tinggi teks */
}

.hero-img {
    aspect-ratio: 629 / 512; /* Sesuaikan dengan rasio gambar */
}
</style>
