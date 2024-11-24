<template>
    <header class="h-full w-full bg-white">
        <nav class="bg-white border-gray-200 dark:bg-gray-900">
            <div
                class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4"
            >
                <Link
                    :href="route('landingpage')"
                    class="flex items-center space-x-3 rtl:space-x-reverse"
                >
                    <img
                        src="/img/logobalangan-nav.webp"
                        alt="logo sibedah-seru"
                    />
                    <div class="flex flex-col justify-center">
                        <p
                            class="font-semibold italic whitespace-nowrap dark:text-white"
                        >
                            SIBEDAH SERU
                        </p>
                        <p
                            class="font-semibold italic whitespace-nowrap dark:text-white"
                        >
                            PERKIM
                        </p>
                    </div>
                </Link>

                <div
                    class="flex items-center md:order-2 space-x-3 rtl:space-x-reverse"
                >
                    <div class="hidden md:block">
                        <AuthButton mobile />
                    </div>

                    <button
                        @click="toggleMenu"
                        type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-cta"
                        :aria-expanded="isMenuOpen"
                    >
                        <span class="sr-only">Open main menu</span>
                        <svg
                            :class="{ block: !isMenuOpen, hidden: isMenuOpen }"
                            class="h-8 w-8"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                            data-slot="icon"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                            />
                        </svg>

                        <svg
                            :class="{ hidden: !isMenuOpen, block: isMenuOpen }"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                            data-slot="icon"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18 18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Menu List in mobile or desktop -->
                <div
                    id="navbar-cta"
                    class="w-full md:flex md:w-auto md:order-1"
                    :class="{
                        hidden: !isMenuOpen && !isDesktop,
                        block: isMenuOpen || isDesktop,
                    }"
                >
                    <ul
                        class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700"
                    >
                        <template v-for="item in menuItems" :key="item.name">
                            <NavLink
                                :href="
                                    item.isExternal
                                        ? item.href
                                        : route(item.route)
                                "
                                :active="
                                    item.isExternal
                                        ? false
                                        : route().current(item.route)
                                "
                                :target="item.target"
                                :rel="item.rel"
                            >
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
import { ref, onMounted, onUnmounted, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavLink from "./NavLink.vue";
import AuthButton from "./AuthButton.vue";

const page = usePage();
const isMenuOpen = ref(false);
const isDesktop = ref(false);

const menuItems = ref([
    {
        name: "Beranda",
        href: usePage().props.app.url + "/",
        route: "landingpage",
    },
    {
        name: "Peta Digital",
        href: usePage().props.app.url + "/map",
        route: "map",
    },
    {
        name: "Download",
        href: usePage().props.app.url + "/download",
        route: "download",
    },
    {
        name: "Smart RTLH",
        href: "https://forms.gle/uEnQ5fygU9p1YUk59",
        isExternal: true,
        target: "_blank",
        rel: "noopener noreferrer",
    },
    {
        name: "Bedah Rumah",
        href: usePage().props.app.url + "/bedah",
        route: "bedah",
    },
    {
        name: "Panduan",
        href: usePage().props.app.url + "/guide",
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
