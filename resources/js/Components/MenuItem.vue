<template>
    <component
        :is="item.route ? 'Link' : 'a'"
        :href="item.href"
        :class="linkClasses"
        :target="item.target ? '_blank' : undefined"
        :rel="item.target ? 'noopener noreferrer' : undefined"
        @click="handleClick"
    >
        {{ item.name }}
        <span
            v-if="item.route"
            class="absolute bottom-0 left-0 w-full h-0.5 bg-[#54e954] transform origin-left scale-x-0 transition-transform duration-300 ease-out group-hover:scale-x-100"
        ></span>
    </component>
</template>

<script setup>
import { defineProps, computed } from "vue";

const props = defineProps({
    item: Object,
    isCurrentRoute: Function, // Menerima isCurrentRoute sebagai prop
});

// Class binding for links
const linkClasses = computed(() => [
    "block py-2 px-3 relative overflow-hidden group",
    {
        "text-green-500": props.isCurrentRoute(props.item.route),
        "text-gray-900 hover:text-[#54e954]": !props.isCurrentRoute(
            props.item.route
        ),
    },
]);

const handleClick = () => {
    if (props.item.target) {
        window.open(props.item.href, "_blank");
    }
};
</script>
