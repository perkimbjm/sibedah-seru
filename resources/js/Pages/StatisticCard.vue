<template>
    <section id="statistic" class="pt-8 pb-12 bg-white bg-cover my-10">
        <div class="w-full bg-white rounded-lg dark:bg-gray-800">
            <h1 class="text-center text-gray-900 text-4xl dark:text-gray-400 mx-auto mt-3 mb-1 font-bold">
                BEDAH SERU DALAM ANGKA
            </h1>
            <div class="relative mt-3">
                <div class="absolute inset-x-1/2 w-28 h-px bg-gray-300 transform -translate-x-1/2 bottom-0.5"></div>
                <div class="absolute inset-x-1/2 w-9 h-1 bg-green-500 transform -translate-x-1/2 bottom-0"></div>
            </div>

            <dl
                class="my-8 mx-auto text-gray-900 dark:text-white sm:p-8 flex flex-col items-center justify-around md:flex-row">
                <div v-for="(statistic, index) in statistics" :key="index"
                    class="flex flex-col items-center md:mr-8 my-8">
                    <dt class="mb-2 text-6xl hover:text-7xl font-bold transition-all duration-500 ease-in-out">
                        <span ref="counterRefs" :data-target="statistic.value" :data-index="index">
                            0
                        </span>
                    </dt>
                    <dd class="flex items-center text-gray-500 dark:text-gray-400 gap-2 text-lg">
                        <font-awesome-icon :icon="iconMap[statistic.icon]" class="w-10 h-10 text-green-500 2xl" />
                        {{ statistic.label }}
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, defineProps } from "vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
// Mengimpor icon secara individual
import { 
    faMapMarkerAlt as mapPin,
    faHouseCircleCheck as housePlus,
    faHouseCircleExclamation as houseLogo
} from '@fortawesome/free-solid-svg-icons/index';

// Data diterima dari Inertia
defineProps({
    statistics: {
        type: Array,
        required: true
    }
});

const iconMap = {
    "map-pin-house": mapPin,
    "house-plus": housePlus,
    "house-logo": houseLogo,
};

const counterRefs = ref([]);

const animateCounter = (element, target) => {
    const duration = 2000;
    const steps = 50;
    const stepDuration = duration / steps;
    let current = 0;

    const increment = target / steps;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepDuration);
};

const startCounterAnimation = (entries, observer) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            const element = entry.target;
            const target = parseInt(element.dataset.target);
            animateCounter(element, target);
            observer.unobserve(element);
        }
    });
};

onMounted(() => {
    const observer = new IntersectionObserver(startCounterAnimation, {
        threshold: 0.5,
    });

    const counterElements = document.querySelectorAll("[data-target]");
    counterElements.forEach((element) => {
        observer.observe(element);
    });
});
</script>

<style scoped>
.hover\:text-7xl:hover {
    font-size: 4.375rem;
    line-height: 1;
}
</style>