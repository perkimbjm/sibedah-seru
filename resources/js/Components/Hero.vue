<template>
    <section id="hero" class="w-screen h-[90vh] sm:h-screen sm:mb-4 mt-8">
        <div class="container max-w-6xl px-4 mx-auto">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex justify-center order-2 pt-2 mb-10 col-lg-5 pt-lg-0 md:order-1 lg:order-1 xl:order-1 2xl:order-1"
                    id="text-hero">
                    <div data-aos="zoom-out" data-aos-delay="400" class="p-3 mt-1 text-center lg:text-left text-hero">
                        <h1 class="py-3 text-4xl font-semibold font-lato">
                            SIBEDAH SERU
                        </h1>
                        <h3 class="text-3xl font-lato">SISTEM INFORMASI</h3>
                        <h3 class="text-3xl font-lato">BEDAH SERIBU RUMAH</h3>
                        <h4 class="mt-2 text-xl font-lato">
                            BID. PERUMAHAN RAKYAT & KAWASAN PERMUKIMAN
                        </h4>
                        <h4 class="text-xl font-lato">KABUPATEN BALANGAN</h4>
                        <div class="mb-10 text-center lg:text-left">
                            <a href="#cta"
                                class="box-border appearance-none bg-[#f8f550] border-2 border-[#f8f550] text-[#292928] cursor-pointer self-center text-base font-semibold leading-none text-center uppercase font-montserrat hover:bg-[#ffc802] hover:text-[#1a1919] focus:outline-nonefirst scrollto rounded-full my-3 px-4 py-3 inline-block">JELAJAHI
                                SEKARANG →</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center order-1 col-12 col-lg-7 md:order-2 lg:order-2 xl:order-2 2xl:order-2"
                    id="img-hero">
                    <img :src="isHovered ? '/img/hero_h.webp' : '/img/hero_c.webp'"
                        class="transition-opacity duration-300 ease-in-out img-fluid animated hero-img"
                        alt="bedah-seribu-rumah" data-aos="zoom-out" data-aos-delay="600" width="629" height="512"
                        loading="eager" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
                </div>
            </div>
        </div>
    </section>
    <!-- End Hero -->
</template>

<script setup>
import { ref } from 'vue'

const isHovered = ref(false)
</script>

<style scoped>
.text-hero h1 {
    font-family: "Poppins", "Roboto", sans-serif;
    font-size: 2.75rem;
}

.text-hero h2 {
    font-size: 2.25rem;
}

.text-hero h2,
h3 {
    font-family: "Roboto", sans-serif;
}

.hero-img {
    transition: all 0.3s ease-in-out;
}
</style>
