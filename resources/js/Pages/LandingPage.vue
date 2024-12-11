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
import { Head } from "@inertiajs/vue3";
import { defineProps } from "vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import Hero from "@/Components/Hero.vue";
import ScrollTop from "@/Components/ScrollTop.vue";
import Cta from "@/Components/Cta.vue";
import FeaturesList from "@/Components/FeaturesList.vue";
import GalleryCard from "@/Components/GalleryCard.vue";
import Faq from "@/Pages/Faq.vue";
import StatisticCard from "@/Pages/StatisticCard.vue";
import FooterBar from "@/Components/FooterBar.vue";

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
/* Anda bisa menambahkan gaya tambahan jika diperlukan */
</style>
