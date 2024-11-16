<template>
    <div
        :class="[
            'transition-all duration-200',
            mobile ? 'mt-4 space-y-2 ml-4' : 'flex items-center space-x-4',
        ]"
    >
        <div class="ml-auto flex md:order-2 md:space-x-0 rtl:space-x-reverse">
            <template v-if="isLoaded">
                <!-- Jika sudah login, tampilkan tombol dashboard yang mengarah ke halaman dashboard -->
                <Link
                    v-if="isAuthenticated"
                    href="/dashboard"
                    class="bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-200"
                    :class="{ 'block w-full': mobile }"
                >
                    Dashboard
                </Link>
                <!-- Jika belum login, tampilkan link ke halaman login -->
                <Link
                    v-else
                    href="/login"
                    class="bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-200"
                    :class="{ 'block w-full': mobile }"
                    preserve-scroll
                >
                    Login
                </Link>
            </template>
            <div
                v-else
                class="animate-pulse bg-gray-300 h-10 w-24 rounded-lg"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
    mobile: {
        type: Boolean,
        default: false,
    },
});

const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const isLoaded = computed(() => authStore.isLoaded);

// Initialize auth state
onMounted(() => {
    authStore.initAuth();
});
</script>
