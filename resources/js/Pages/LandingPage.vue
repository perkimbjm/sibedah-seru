<template>
    <MainLayout>
        <Head title="SIBEDAH SERU" />
        <Hero />
        <Cta />
        <StatisticCard />
        <FeaturesList />
        <GalleryCard />
        <Faq />

        <ScrollTop />
    </MainLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import Hero from "@/Components/Hero.vue";
import ScrollTop from "@/Components/ScrollTop.vue";
import Cta from "@/Components/Cta.vue";
import FeaturesList from "@/Components/FeaturesList.vue";
import GalleryCard from "@/Components/GalleryCard.vue";
import Faq from "@/Components/Faq.vue";
import StatisticCard from "@/Components/StatisticCard.vue";
</script>

<style>
/* Anda bisa menambahkan gaya tambahan jika diperlukan */
</style>
