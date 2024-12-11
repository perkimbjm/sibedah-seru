<template>
    <div :class="[
        mobile ? 'mt-4 space-y-2 ml-4' : 'flex items-center space-x-4',
        'ml-auto flex md:order-2 md:space-x-0 rtl:space-x-reverse',
    ]">
        <a v-if="authStore.isAuthenticated" :href="route('dashboard')" :class="[
            mobile ? 'block w-full' : '',
            'bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-200',
        ]" :preserve-scroll="false" :preserve-state="false">
            Dashboard
        </a>
        <a v-else :href="route('login')" :class="[
            mobile ? 'block w-full' : '',
            'bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-200',
        ]">
            Login
        </a>
    </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
    mobile: {
        type: Boolean,
        default: false,
    },
});

const authStore = useAuthStore();
</script>
