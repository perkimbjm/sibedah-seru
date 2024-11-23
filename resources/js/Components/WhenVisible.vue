<template>
    <div ref="element" v-show="isVisible">
        <slot />
    </div>
</template>

<script>
export default {
    data() {
        return {
            isVisible: false,
        };
    },
    mounted() {
        const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                this.isVisible = true;
                observer.disconnect(); // Berhenti mengamati setelah terlihat
            }
        });

        observer.observe(this.$refs.element);
    },
};
</script>
