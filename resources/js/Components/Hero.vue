<template>
    <section id="hero" class="w-screen h-[90vh] sm:h-screen sm:mb-4 mt-8">
        <div class="container mx-auto px-4 max-w-6xl">
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between"
            >
                <div
                    class="col-lg-5 pt-2 mb-10 pt-lg-0 order-2 md:order-1 lg:order-1 xl:order-1 2xl:order-1 flex justify-center"
                    id="text-hero"
                >
                    <div
                        data-aos="zoom-out"
                        data-aos-delay="400"
                        class="text-center lg:text-left mt-1 p-3 text-hero"
                    >
                        <h1 class="text-4xl font-semibold font-lato py-3">
                            SIBEDAH SERU
                        </h1>
                        <h3 class="text-2xl font-lato">SISTEM INFORMASI</h3>
                        <h3 class="text-2xl font-lato">BEDAH SERIBU RUMAH</h3>
                        <h3 class="text-2xl font-lato">KABUPATEN BALANGAN</h3>
                        <div class="text-center lg:text-left mb-10">
                            <a
                                href="#cta"
                                class="box-border appearance-none bg-[#f8f550] border-2 border-[#f8f550] text-[#292928] cursor-pointer self-center text-base font-semibold leading-none text-center uppercase font-montserrat hover:bg-[#ffc802] hover:text-[#1a1919] focus:outline-nonefirst scrollto rounded-full my-3 px-4 py-3 inline-block"
                                >JELAJAHI SEKARANG</a
                            >
                        </div>
                    </div>
                </div>
                <div
                    class="col-12 col-lg-7 order-1 md:order-2 lg:order-2 xl:order-2 2xl:order-2 flex items-center"
                    id="img-hero"
                >
                    <img
                        src="/img/hero.webp"
                        class="img-fluid animated hero-img"
                        alt="bedah-seribu-rumah"
                        data-aos="zoom-out"
                        data-aos-delay="600"
                    />
                </div>
            </div>
        </div>
    </section>
    <!-- End Hero -->
</template>

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
</style>
