<template>
    <MainLayout>
        <div class="w-full">
            <iframe
                src="https://docs.google.com/presentation/d/e/2PACX-1vSGwJyxcExpeI-3Sm-XZbRlA2aZ7IwX2A9nfKh3glksPlRJPp_crxEz2wOIJg_Jy7z2i9I2l5Em4XMq/embed?start=true&loop=false&delayms=7000"
                frameborder="0" allowfullscreen="true" class="w-full h-[88vh]"></iframe>
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
