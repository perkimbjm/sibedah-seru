<template>
    <div class="min-h-screen flex flex-col">
        <div class="relative z-50">
            <Navbar />
        </div>
        <div
            v-if="loading"
            class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-75 z-50"
        >
            <span>Loading...</span>
        </div>
        <main class="flex-grow">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";

const page = usePage();
const loading = ref(false);

watch(
    () => page.value,
    (newPage) => {
        // Tambahkan null check
        if (!newPage) return;

        loading.value = true;
        const url = newPage?.url || ""; // Gunakan optional chaining dan default value

        setTimeout(() => {
            loading.value = false;
        }, 500);
    },
    {
        deep: true, // Tambahkan deep watching karena page adalah reactive object
    }
);
</script>
