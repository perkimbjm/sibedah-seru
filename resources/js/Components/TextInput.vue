<script setup>
import { onMounted, ref } from "vue";

defineProps({
    modelValue: String,
});

defineEmits(["update:modelValue"]);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 shadow-sm outline-none transition-all"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
