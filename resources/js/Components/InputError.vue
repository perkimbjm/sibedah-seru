<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600">
            {{ message }}
        </p>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-in-out;
}
</style>
