<template>
    <section id="usulan-info" class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="container px-4 mx-auto" data-aos="fade-up">
            <div class="mb-8 text-center">
                <h3 class="mb-4 text-3xl font-bold text-gray-800">MEKANISME PENGUSULAN RTLH</h3>
                <p class="mx-auto max-w-2xl text-gray-600">
                    Sampaikan usulan Anda untuk rumah tidak layak huni melalui sistem yang transparan dan terstruktur.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 mx-auto max-w-4xl lg:grid-cols-2">
                <!-- Opsi 1: Lapor Petugas TFL -->
                <div class="p-6 bg-white rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="mb-6 text-center">
                        <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-blue-500 rounded-full">
                            <font-awesome-icon :icon="['fas', 'bullhorn']" class="text-2xl text-white icon" />
                        </div>
                        <h4 class="mb-2 text-xl font-semibold">Lapor Petugas TFL</h4>
                        <p class="text-gray-600">Hubungi petugas TFL terdekat untuk bantuan pengusulan</p>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(step, index) in tflSteps" :key="index" class="flex items-start space-x-3">
                            <div
                                class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-blue-500 rounded-full">
                                <span class="text-sm font-bold text-white">{{ index + 1 }}</span>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-800">{{ step.title }}</h5>
                                <p class="text-sm text-gray-600">{{ step.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opsi 2: Daftar Mandiri -->
                <div class="p-6 bg-white rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="400">
                    <div class="mb-6 text-center">
                        <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-green-500 rounded-full">
                            <font-awesome-icon :icon="['fas', 'clipboard-list']" class="text-2xl text-white icon" />
                        </div>
                        <h4 class="mb-2 text-xl font-semibold">Daftar Mandiri</h4>
                        <p class="text-gray-600">Daftar dan isi usulan secara mandiri melalui sistem online</p>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(step, index) in mandiriSteps" :key="index" class="flex items-start space-x-3">
                            <div
                                class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-green-500 rounded-full">
                                <span class="text-sm font-bold text-white">{{ index + 1 }}</span>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-800">{{ step.title }}</h5>
                                <p class="text-sm text-gray-600">{{ step.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="600">
                <div class="p-8 mx-auto max-w-2xl bg-white rounded-lg shadow-lg">
                    <h4 class="mb-4 text-2xl font-bold text-gray-800">Siap Mengajukan Usulan?</h4>
                    <p class="mb-6 text-gray-600">
                        Pilih metode yang paling sesuai dengan kebutuhan Anda. Tim kami siap membantu proses pengusulan
                        RTLH.
                    </p>
                    <div class="flex flex-col gap-4 justify-center sm:flex-row">
                        <!-- Button untuk user yang belum login -->
                        <template v-if="!$page.props.auth.user">
                            <Link :href="route('register')"
                                class="px-6 py-3 font-semibold text-white bg-green-500 rounded-lg transition-colors duration-300 hover:bg-green-600">
                            <i class="mr-2 fas fa-user-plus"></i>Daftar Sekarang
                            </Link>
                            <Link :href="route('login')"
                                class="px-6 py-3 font-semibold text-white bg-blue-500 rounded-lg transition-colors duration-300 hover:bg-blue-600">
                            <i class="mr-2 fas fa-sign-in-alt"></i>Login
                            </Link>
                        </template>

                        <!-- Button untuk user yang sudah login -->
                        <template v-else>
                            <Link :href="route('dashboard')"
                                class="px-6 py-3 font-semibold text-white bg-blue-500 rounded-lg transition-colors duration-300 hover:bg-blue-600">
                            <i class="mr-2 fas fa-tachometer-alt"></i>Dashboard
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

// Mengakses data user dari Inertia
const $page = usePage()

// Data untuk langkah-langkah TFL
const tflSteps = [
    {
        title: 'Kontak Petugas TFL',
        description: 'Hubungi petugas TFL (Tenaga Fasilitator Lapangan) di Dinas atau Kelurahan/Desa terdekat'
    },
    {
        title: 'Siapkan Dokumen',
        description: 'Siapkan KTP, KK, dan dokumen pendukung lainnya'
    },
    {
        title: 'Verifikasi Lapangan',
        description: 'Petugas akan melakukan verifikasi administrasi dan lapangan'
    },
    {
        title: 'Hasil Verifikasi',
        description: 'Anda akan diinformasikan hasil verifikasi dan tindak lanjutnya'
    }
]

// Data untuk langkah-langkah Mandiri
const mandiriSteps = [
    {
        title: 'Daftar Akun',
        description: 'Buat akun baru atau login jika sudah memiliki akun'
    },
    {
        title: 'Isi Form Usulan',
        description: 'Lengkapi form usulan dengan data yang akurat dan lengkap'
    },
    {
        title: 'Verifikasi oleh TFL',
        description: 'Tim TFL akan memverifikasi usulan Anda'
    },
    {
        title: 'Informasi Hasil',
        description: 'Anda akan mendapat notifikasi hasil verifikasi'
    }
]
</script>

<style scoped>
/* Tambahan styling jika diperlukan */
.container {
    max-width: 1200px;
}

/* Animasi hover untuk card */
.bg-white {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Styling untuk icon (mendukung Font Awesome SVG component) */
.icon {
    transition: transform 0.3s ease;
}

.bg-white:hover .icon {
    transform: scale(1.1);
}
</style>
