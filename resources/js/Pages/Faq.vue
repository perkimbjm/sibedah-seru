<template>
    <section id="faq" class="container mx-auto my-8">
        <h1 class="text-center text-gray-900 text-3xl dark:text-gray-400 mx-auto mt-3 mb-1 font-bold"
            style="letter-spacing: 5px">
            F.A.Q.
        </h1>
        <h4 class="text-center mb-2 italic text-base">
            Pertanyaan yang Paling Sering Ditanyakan
        </h4>
        <div class="px-6 sm:px-8 md:px-16 lg:px-24 xl:px-32 mx-auto my-8">
            <div id="accordion-color" data-accordion="collapse"
                data-active-classes="bg-blue-500 dark:bg-gray-800 text-gray-800 dark:text-white rounded-xl">
                <template v-for="(faq, index) in faqs" :key="index">
                    <h2 :id="`accordion-color-heading-${index}`">
                        <button type="button"
                            class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-400 hover:text-gray-900 dark:hover:bg-gray-800 gap-3"
                            :data-accordion-target="`#accordion-color-body-${index}`" :aria-expanded="faq.expanded"
                            :aria-controls="`accordion-color-body-${index}`">
                            <span>{{ faq.question }}</span>
                            <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                                xmlns="https://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 5 5 1 1 5" />
                            </svg>
                        </button>
                    </h2>
                    <div :id="`accordion-color-body-${index}`" class="hidden"
                        :aria-labelledby="`accordion-color-heading-${index}`">
                        <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                            <p class="mb-2 text-gray-500 dark:text-gray-400">
                                {{ faq.answer }}
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, defineProps } from "vue";
import { initAccordions } from "flowbite";

defineProps({
    faqs: {
        type: Array,
        required: true
    }
});

onMounted(() => {
    initAccordions();
});

</script>
