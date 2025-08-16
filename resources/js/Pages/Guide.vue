<template>
    <MainLayout>
        <div class="w-full">
            <iframe src="https://sibedahseru.web.id/storage/doc/BUKU%20PANDUAN%20Sibedah%20Seru.pdf" frameborder="0"
                allowfullscreen="true" class="w-full h-[88vh]"></iframe>
        </div>
        <FooterBar :links="links" />
    </MainLayout>
</template>

<script setup>
import FooterBar from "@/Components/FooterBar.vue";
import MainLayout from "@/Layouts/MainLayout.vue";


defineProps({
    links: {
        type: Array,
        required: true
    }
})
</script>
