<template>
    <section id="features" class="py-8 bg-white bg-cover mt-10">
        <div class="container mx-auto px-4 text-center" data-aos="fade-up">
            <header class="mb-8 text-black" data-aos="fade-up">
                <h1
                    class="text-center text-gray-900 text-3xl dark:text-gray-400 mx-auto mt-3 mb-1 font-bold"
                >
                    FITUR-FITUR
                </h1>
                <div class="relative mt-3">
                    <div
                        class="absolute inset-x-1/2 w-28 h-px bg-gray-300 transform -translate-x-1/2 bottom-0.5"
                    ></div>
                    <div
                        class="absolute inset-x-1/2 w-9 h-1 bg-green-500 transform -translate-x-1/2 bottom-0"
                    ></div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    v-for="(feature, index) in features"
                    :key="index"
                    class="p-4 flex flex-col items-center text-center"
                    data-aos="fade-up"
                    :data-aos-delay="feature.delay"
                >
                    <a :href="feature.link || '#'">
                        <img
                            :src="feature.imgSrc"
                            :alt="feature.imgAlt"
                            loading="lazy"
                            class="h-auto transform hover:scale-110 transition-transform duration-300"
                        />
                    </a>
                    <h4 class="text-xl font-semibold mt-4">
                        {{ feature.title }}
                    </h4>
                    <p class="text-gray-600 mt-2">{{ feature.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "FeaturesSection",
    data() {
        return {
            features: [
                {
                    imgSrc: "/img/landingpage/spasial.png",
                    imgAlt: "spasial",
                    title: "FITUR 1",
                    description: "Lorem ipsum dolor sit amet consectetur",
                    delay: 200,
                },
                {
                    imgSrc: "/img/landingpage/data-inventory.png",
                    imgAlt: "inventory",
                    title: "FITUR 2",
                    description: "Lorem ipsum dolor sit amet consectetur",
                    delay: 200,
                },
                {
                    imgSrc: "/img/landingpage/dashboard.png",
                    imgAlt: "visualize-data",
                    title: "FITUR 3",
                    description: "Lorem ipsum dolor sit amet consectetur",
                    delay: 200,
                },
                {
                    imgSrc: "/img/landingpage/document.png",
                    imgAlt: "download-data",
                    title: "FITUR 4",
                    description: "Lorem ipsum dolor sit amet consectetur",
                    delay: 300,
                    link: "#", // Optional link
                },
                {
                    imgSrc: "/img/landingpage/route.png",
                    imgAlt: "route",
                    title: "FITUR 5",
                    description: "Lorem ipsum dolor sit amet consectetur",
                    delay: 200,
                },
                {
                    imgSrc: "/img/landingpage/dynamic.png",
                    imgAlt: "dynamic",
                    title: "FITUR 6",
                    description: "Lorem ipsum dolor sit amet consectetur",
                    delay: 200,
                },
            ],
        };
    },
};
</script>

<style scoped>
/* Additional custom styling if needed */
</style>
