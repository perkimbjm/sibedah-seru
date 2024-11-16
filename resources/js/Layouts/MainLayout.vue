<template>
    <div>
        <Navbar />
        <div
            v-if="loading"
            class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-75 z-50"
        >
            <span>Loading...</span>
        </div>
        <main>
            <slot />
        </main>
        <FooterBar />
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import FooterBar from "@/Components/FooterBar.vue";

const page = usePage();
const loading = ref(false);

watch(
    () => page.value,
    () => {
        loading.value = true;
        setTimeout(() => {
            loading.value = false;
        }, 500); // Sesuaikan timeout jika diperlukan
    }
);
</script>
