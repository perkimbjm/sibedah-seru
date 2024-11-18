<template>
    <header class="h-full w-full bg-white">
        <nav class="bg-white border-gray-200">
            <div
                class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4"
            >
                <Link
                    href="/"
                    class="flex items-center space-x-3 rtl:space-x-reverse"
                >
                    <img
                        src="/img/logobalangan-nav.webp"
                        alt="logo sibedah-seru"
                    />
                    <span
                        class="self-center font-semibold italic whitespace-nowrap"
                        >SIBEDAH SERU</span
                    >
                </Link>

                <div
                    class="flex items-center md:order-2 space-x-3 rtl:space-x-reverse"
                >
                    <template v-if="!isMobile">
                        <div>
                            <Login />
                        </div>
                    </template>

                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    >
                        <span class="sr-only">Open main menu</span>
                        <svg
                            class="w-5 h-5"
                            aria-hidden="true"
                            xmlns="https://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 17 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M1 1h15M1 7h15M1 13h15"
                            />
                        </svg>
                    </button>
                </div>

                <div
                    :class="[
                        'items-center justify-between w-full md:flex md:w-auto md:order-1',
                        { hidden: !mobileMenuOpen },
                    ]"
                >
                    <ul
                        class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white"
                    >
                        <li v-for="(item, index) in menuItems" :key="index">
                            <!-- Gunakan anchor tag untuk external link -->
                            <a
                                v-if="item.isExternal"
                                :href="item.href"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block py-2 px-3 relative overflow-hidden group"
                                :class="[
                                    isCurrentRoute(item.route)
                                        ? 'text-green-500'
                                        : 'text-gray-900 hover:text-[#54e954]',
                                ]"
                            >
                                {{ item.name }}
                                <span
                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-[#54e954] transform origin-left scale-x-0 transition-transform duration-300 ease-out group-hover:scale-x-100"
                                ></span>
                            </a>
                            <!-- Gunakan Link untuk internal route -->
                            <Link
                                v-else
                                :href="item.href"
                                :class="[
                                    'block py-2 px-3 relative overflow-hidden group',
                                    isCurrentRoute(item.route)
                                        ? 'text-green-500'
                                        : 'text-gray-900 hover:text-[#54e954]',
                                ]"
                            >
                                {{ item.name }}
                                <span
                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-[#54e954] transform origin-left scale-x-0 transition-transform duration-300 ease-out group-hover:scale-x-100"
                                ></span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Login from "@/Components/Login.vue";

const mobileMenuOpen = ref(false);
const isMobile = ref(false);

const menuItems = ref([
    { name: "Beranda", href: "/", route: "landingpage" },
    { name: "Peta Digital", href: "/map", route: "map" },
    { name: "Download", href: "/download", route: "download" },
    {
        name: "Smart RTLH",
        href: "https://forms.gle/uEnQ5fygU9p1YUk59",
        isExternal: true,
        target: "_blank",
        rel: "noopener noreferrer",
    },
    { name: "Bedah Rumah", href: "/bedah", route: "bedah" },
    { name: "Panduan", href: "/guide", route: "guide" },
]);

const isCurrentRoute = (routeName) => {
    const currentPath = window.location.pathname;
    if (routeName === "home") return currentPath === "/";
    return currentPath.includes(routeName);
};

const checkMobile = () => {
    isMobile.value = window.innerWidth < 425;
    if (
        isMobile.value &&
        !menuItems.value.find((item) => item.name === "Login")
    ) {
        menuItems.value.push({ name: "Login", href: "/login", route: "login" });
    }
};

onMounted(() => {
    checkMobile();
    window.addEventListener("resize", checkMobile);
});
</script>

<style scoped>
.text-gray-900 {
    transition: color 0.3s ease;
}
</style>
