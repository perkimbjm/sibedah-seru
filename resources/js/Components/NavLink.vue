<template>
    <li>
        <Link
            :href="href"
            :class="[
                'block py-2 px-3 md:p-2 relative overflow-hidden group',
                active
                    ? 'text-white font-bold bg-[#3e8e41] rounded md:bg-transparent md:text-[#3e8e41] md:dark:text-[#3e8e41]'
                    : 'text-gray-900 rounded hover:text-[#54e954] hover:bg-gray-100 md:hover:bg-transparent md:dark:hover:text-[#3e8e41] dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700',
            ]"
        >
            <slot></slot>
            <span
                class="absolute bottom-0 left-0 w-full h-0.5 bg-[#54e954] transform origin-left scale-x-0 transition-transform duration-300 ease-out group-hover:scale-x-100"
            ></span>
        </Link>
    </li>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>
