<template>
    <header class="w-full h-full bg-white">
        <nav class="bg-white border-gray-200 dark:bg-gray-900">
            <div class="flex flex-wrap justify-between items-center p-4 mx-auto max-w-screen-xl">
                <Link :href="route('landingpage')" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="/img/logobalangan-nav.webp" alt="logo balangan" />
                <img src="/img/logo-sibedah.png" class="w-12" alt="logo sibedah-seru" />
                <div class="flex flex-col justify-center">
                    <p class="font-bold whitespace-nowrap dark:text-white font-roboto">
                        SiBEDAH-SERU
                    </p>
                    <p class="text-sm font-bold whitespace-nowrap dark:text-white font-roboto">
                        PERKIM
                    </p>
                </div>
                </Link>

                <div class="flex items-center space-x-3 md:order-2 rtl:space-x-reverse">
                    <div class="hidden md:block">
                        <AuthButton mobile />
                    </div>

                    <button @click="toggleMenu" type="button"
                        class="inline-flex justify-center items-center p-2 w-10 h-10 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-cta" :aria-expanded="isMenuOpen">
                        <span class="sr-only">Open main menu</span>
                        <svg :class="{ block: !isMenuOpen, hidden: isMenuOpen }" class="w-8 h-8" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                            data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>

                        <svg :class="{ hidden: !isMenuOpen, block: isMenuOpen }" class="w-6 h-6" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                            data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Menu List in mobile or desktop -->
                <div id="navbar-cta" class="w-full md:flex md:w-auto md:order-1" :class="{
                    hidden: !isMenuOpen && !isDesktop,
                    block: isMenuOpen || isDesktop,
                }">
                    <ul
                        class="flex flex-col p-4 mt-4 font-medium bg-gray-50 rounded-lg border border-gray-100 md:p-0 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <template v-for="item in menuItems" :key="item.name">
                            <NavLink :href="item.isExternal
                                ? item.href
                                : route(item.route)
                                " :active="item.isExternal
                                    ? false
                                    : route().current(item.route)
                                    " :target="item.target" :rel="item.rel">
                                {{ item.name }}
                            </NavLink>
                        </template>

                        <li class="md:hidden">
                            <AuthButton mobile />
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavLink from "./NavLink.vue";
import AuthButton from "./AuthButton.vue";

const page = usePage();
const isMenuOpen = ref(false);
const isDesktop = ref(false);

const baseUrl = computed(() => page.props.app?.url || "");

const menuItems = computed(() => [
    {
        name: "Home",
        href: `${baseUrl.value}/`,
        route: "landingpage",
    },
    {
        name: "Peta Digital",
        href: `${baseUrl.value}/map`,
        route: "map",
    },
    {
        name: "Informasi",
        href: `${baseUrl.value}/download`,
        route: "download",
    },
    {
        name: "Bedah Rumah",
        href: `${baseUrl.value}/bedah`,
        route: "bedah",
    },
    {
        name: "Panduan",
        href: `${baseUrl.value}/guide`,
        route: "guide",
    },
]);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

// Deteksi viewport width untuk menentukan apakah desktop atau mobile
const checkViewport = () => {
    isDesktop.value = window.innerWidth >= 768; // 768px adalah breakpoint md di Tailwind
};

onMounted(() => {
    checkViewport();
    window.addEventListener("resize", checkViewport);
});

// Cleanup
onUnmounted(() => {
    window.removeEventListener("resize", checkViewport);
});

// Reset menu state saat route berubah
watch(
    () => page.url,
    () => {
        isMenuOpen.value = false;
    }
);
</script>
