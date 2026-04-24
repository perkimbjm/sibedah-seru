<template>
    <section id="statistic" class="pt-8 pb-12 my-10 bg-white bg-cover">
        <div class="w-full bg-white rounded-lg dark:bg-gray-800">
            <h1 class="mx-auto mt-3 mb-1 text-4xl font-bold text-center text-gray-900 dark:text-gray-400">
                BEDAH SERU DALAM ANGKA
            </h1>
            <div class="relative mt-3">
                <div class="absolute bottom-0.5 inset-x-1/2 w-28 h-px bg-gray-300 transform -translate-x-1/2"></div>
                <div class="absolute bottom-0 inset-x-1/2 w-9 h-1 bg-green-500 transform -translate-x-1/2"></div>
            </div>

            <dl
                class="flex flex-col justify-around items-center mx-auto my-8 text-gray-900 dark:text-white sm:p-8 md:flex-row">
                <div v-for="(statistic, index) in statistics" :key="index"
                    class="flex flex-col items-center my-8 md:mr-8">
                    <dt class="mb-2 text-6xl font-bold transition-all duration-500 ease-in-out hover:text-7xl">
                        <span ref="counterRefs" :data-target="statistic.value" :data-index="index">
                            0
                        </span>
                    </dt>
                    <dd class="flex gap-2 items-center text-lg text-gray-500 dark:text-gray-400">
                        <component :is="statistic.icon" class="w-6 h-6" :stroke-width="2" />
                        {{ statistic.label }}
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { MapPinHouse, HousePlus, House } from "lucide-vue-next";

const statistics = ref([
    {
        value: "150",
        label: "Kelurahan / Desa",
        icon: MapPinHouse,
    },
    {
        value: "1164",
        label: "Rumah yang Diperbaiki",
        icon: HousePlus,
    },
    {
        value: "1870",
        label: "RTLH Tersisa",
        icon: House,
    },
]);

const counterRefs = ref([]);

const animateCounter = (element, target) => {
    const duration = 2000; // Animation duration in milliseconds
    const steps = 50; // Number of steps in the animation
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

    // Get all counter elements and observe them
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
