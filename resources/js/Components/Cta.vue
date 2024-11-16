<script setup>
import { onMounted, ref } from "vue";
import VanillaTilt from "vanilla-tilt";

// Definisikan refs dengan benar
const element = ref(null);
const isMobile = window.innerWidth <= 768;
const particleCount = isMobile ? 30 : 60;
const particlesContainer = ref(null);

onMounted(async () => {
    try {
        // Load dan inisialisasi particles
        const { tsParticles } = await import("tsparticles-engine");
        const { loadSlim } = await import("tsparticles-slim");

        await loadSlim(tsParticles);

        // Inisialisasi particles
        const container = await tsParticles.load("particles-js", {
            fullScreen: { enable: false },
            background: {
                color: {
                    value: "rgba(17, 17, 17, 0.99)",
                },
            },
            particles: {
                number: {
                    value: particleCount,
                    density: {
                        enable: true,
                        value_area: 800,
                    },
                },
                color: {
                    value: "#ffffff",
                },
                shape: {
                    type: "circle",
                },
                opacity: {
                    value: 0.5,
                    random: false,
                },
                size: {
                    value: 3,
                    random: true,
                },
                links: {
                    enable: true,
                    distance: 150,
                    color: "#ffffff",
                    opacity: 0.4,
                    width: 1,
                },
                move: {
                    enable: true,
                    speed: 6,
                    direction: "none",
                    random: false,
                    straight: false,
                    outModes: "out",
                    bounce: false,
                },
            },
            interactivity: {
                detectsOn: "canvas",
                events: {
                    onHover: {
                        enable: true,
                        mode: "repulse",
                    },
                    onClick: {
                        enable: true,
                        mode: "push",
                    },
                    resize: true,
                },
            },
        });

        // Inisialisasi VanillaTilt jika element ada
        if (element.value) {
            VanillaTilt.init(element.value, {
                max: 20,
                speed: 400,
                glare: false,
                "max-glare": 0.5,
            });
        }
    } catch (error) {
        console.error("Failed to initialize particles:", error);
    }
});
</script>

<template>
    <section id="cta">
        <div id="particles-js" class="absolute inset-0"></div>
        <div
            class="container relative z-10 mx-auto px-4 py-2 text-center"
            data-aos="fade-up"
        >
            <div class="section-header">
                <h3
                    class="mb-2 md:mb-4 md:text-4xl md:font-semibold uppercase text-2xl text-white font-bold tracking-wider"
                >
                    PETA DIGITAL
                </h3>
            </div>
            <div class="cta-img flex justify-center mb-4" ref="element">
                <img
                    src="/img/landingpage/macbook.png"
                    class="w-1/2 md:w-1/4"
                    alt="peta-digital"
                    loading="lazy"
                    width="300"
                    height="200"
                />
            </div>
            <div class="text-center">
                <a
                    href="/map"
                    class="inline-flex items-center px-6 py-3 bg-emerald-500 text-white font-medium rounded-full hover:bg-emerald-600 transition-all duration-300 shadow-lg hover:shadow-emerald-500/30"
                >
                    <i class="fas fa-map" aria-hidden="true"></i>&nbsp;Buka Peta
                </a>
            </div>
        </div>
    </section>
</template>

<style>
.section-header h3 {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
    padding-bottom: 10px;
    margin-top: 10px;
}

.section-header h3::before {
    content: "";
    position: absolute;
    display: block;
    width: 120px;
    height: 1px;
    background: #ddd;
    bottom: 1px;
    left: calc(50% - 60px);
}

.section-header h3::after {
    content: "";
    position: absolute;
    display: block;
    width: 2.3rem;
    height: 3px;
    background: #18d26e;
    bottom: 0;
    left: calc(50% - 20px);
}

.section-header p {
    text-align: center;
    padding-bottom: 20px;
    color: #333;
}

#cta {
    position: relative;
    min-height: 400px;
    z-index: 0;
}

#hero {
    position: relative;
    z-index: 1;
}

@media (max-width: 767px) and (min-width: 440px) {
    #hero {
        min-height: 100vh;
        margin-bottom: 200px;
    }
}
</style>

<style scoped>
#particles-js {
    background-color: rgba(17, 17, 17, 0.99);
    background-repeat: no-repeat;
    position: absolute;
    width: 100%;
}

#cta::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
}

#cta .container {
    position: relative;
    padding: 20px 0 30px 0;
    z-index: 1;
}

#cta .cta-img {
    text-align: center;
    padding: 10px;
    transform-style: preserve-3d;
    perspective: 1000px;
}

#cta .cta-img img {
    transform: translateZ(20px);
    transition: transform 0.3s ease;
}
</style>
