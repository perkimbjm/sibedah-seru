<template>
    <button
        ref="scrollToTopBtn"
        class="fixed bottom-5 right-5 z-50 p-3 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg transition-all duration-200"
        :style="{ display: showButton ? 'block' : 'none' }"
        @click="scrollToTop"
    >
        <svg
            xmlns="https://www.w3.org/2000/svg"
            class="w-6 h-6"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path
                fill-rule="evenodd"
                d="M5.293 12.707a1 1 0 011.414 0L10 9.414l3.293 3.293a1 1 0 001.414-1.414l-4-4a1 1 0 00-1.414 0l-4 4a1 1 0 000 1.414z"
                clip-rule="evenodd"
            />
        </svg>
    </button>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const showButton = ref(false);

const handleScroll = () => {
    showButton.value =
        document.body.scrollTop > 1200 ||
        document.documentElement.scrollTop > 1200;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>
