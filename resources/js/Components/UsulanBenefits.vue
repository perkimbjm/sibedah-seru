<template>
    <section id="keuntungan" class="py-12 bg-white">
        <div class="container px-4 mx-auto max-w-6xl">
            <div class="mb-12 text-center" data-aos="fade-up">
                <h2 class="mb-4 text-3xl font-bold text-gray-800">Keuntungan Sistem Online</h2>
                <p class="text-lg text-gray-600">Mengapa memilih sistem pengusulan online?</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div v-for="(benefit, index) in benefits" :key="index" class="text-center" data-aos="fade-up"
                    :data-aos-delay="200 * (index + 1)">
                    <div class="flex justify-center items-center mx-auto mb-4 w-16 h-16 rounded-full"
                        :class="benefit.bgColor">
                        <font-awesome-icon :icon="benefit.icon" class="text-xl text-white icon" />
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-800">{{ benefit.title }}</h3>
                    <p class="text-gray-600">{{ benefit.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
// Data untuk keuntungan sistem online
const benefits = [
    {
        title: '24/7 Tersedia',
        description: 'Akses sistem kapan saja dan di mana saja tanpa batas waktu',
        icon: ['fas', 'clock'],
        bgColor: 'bg-blue-500'
    },
    {
        title: 'Proses Lebih Mudah',
        description: 'Pengajuan usulan diproses lebih mudah tanpa harus cetak proposal',
        icon: ['fas', 'bolt'],
        bgColor: 'bg-yellow-500'
    },
    {
        title: 'Transparan',
        description: 'Pantau status usulan Anda secara real-time dengan mudah',
        icon: ['fas', 'chart-line'],
        bgColor: 'bg-purple-500'
    }
]
</script>

<style scoped>
/* Animasi hover untuk benefit cards */
.text-center {
    transition: transform 0.3s ease;
}

.text-center:hover {
    transform: translateY(-5px);
}

/* Animasi untuk icon (SVG component) */
.icon {
    transition: transform 0.3s ease;
}

.text-center:hover .icon {
    transform: scale(1.2);
}
</style>
