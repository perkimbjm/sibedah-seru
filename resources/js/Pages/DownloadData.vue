<template>
    <MainLayout>

        <Head title="Download Dokumen" />
        <DocumentDownload :downloads="downloads" />
        <FooterBar :links="links" />
    </MainLayout>
</template>

<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head } from "@inertiajs/vue3";
import { defineProps } from "vue";
import FooterBar from "@/Components/FooterBar.vue";
import DocumentDownload from "./DocumentDownload.vue";

defineProps({
    downloads: {
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
