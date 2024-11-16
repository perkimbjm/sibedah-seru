<template>
    <div class="max-w-7xl mx-auto p-4 font-sans">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">
                Program Bedah Rumah - Kota Sejahtera
            </h1>
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <ClockIcon class="w-4 h-4" />
                    <span>3-4 minggu pengerjaan</span>
                </div>
                <div class="flex items-center gap-2">
                    <MapPinIcon class="w-4 h-4" />
                    <span>Kecamatan Harapan, Indonesia</span>
                </div>
                <div class="flex items-center gap-2">
                    <HomeIcon class="w-4 h-4" />
                    <span>Renovasi, Pembangunan, Sosial</span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Images Section -->
            <div class="md:col-span-2">
                <div class="grid grid-cols-5 gap-2">
                    <!-- Large left image -->
                    <div class="col-span-3">
                        <img
                            src="/img/rumah/rumah2.png?height=400&width=600"
                            alt="Rumah setelah renovasi"
                            class="w-full h-[400px] object-cover rounded-lg"
                        />
                    </div>

                    <!-- Right side images stack -->
                    <div class="col-span-2 space-y-2">
                        <img
                            src="/img/rumah/rumah4.jpg?height=200&width=300"
                            alt="Proses renovasi"
                            class="w-full h-[196px] object-cover rounded-lg"
                        />
                        <div class="grid grid-cols-2 gap-2">
                            <img
                                src="/img/rumah/rumah4.jpg?height=150&width=150"
                                alt="Interior rumah"
                                class="w-full h-[196px] object-cover rounded-lg"
                            />
                            <div class="relative">
                                <img
                                    src="/img/rumah/rumah4.jpg?height=150&width=150"
                                    alt="Serah terima"
                                    class="w-full h-[196px] object-cover rounded-lg"
                                />
                                <button
                                    class="absolute inset-0 bg-black/50 text-white flex items-center justify-center rounded-lg"
                                >
                                    <CameraIcon class="w-5 h-5 mr-2" />
                                    Lihat semua foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="bg-white p-6 rounded-lg shadow-lg h-fit">
                <div class="mb-4">
                    <h2 class="text-xl font-bold">Status Program</h2>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-green-600 font-semibold">Aktif</span>
                        <span class="text-sm text-gray-500"
                            >• 15 rumah selesai</span
                        >
                    </div>
                </div>

                <div class="border-t border-b py-4 my-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Target Selesai</span>
                        <span class="font-semibold">Desember 2024</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Anggaran per Unit</span>
                        <span class="font-semibold">Rp 50.000.000</span>
                    </div>
                </div>

                <button
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold mb-4 hover:bg-blue-700 transition-colors flex items-center justify-center"
                >
                    <Share2 class="w-5 h-5 mr-2" />
                    Bagikan
                </button>

                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-2">
                        Informasi Penting
                    </h3>
                    <p class="text-sm text-blue-800">
                        Pendaftaran dibuka sampai 30 November 2024. Prioritas
                        diberikan kepada keluarga prasejahtera.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="mt-8">
            <div class="border-b">
                <nav class="flex gap-6">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'py-4 relative',
                            activeTab === tab.id
                                ? 'text-blue-600 font-semibold'
                                : 'text-gray-600',
                        ]"
                    >
                        {{ tab.name }}
                        <div
                            v-if="activeTab === tab.id"
                            class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600"
                        ></div>
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="py-6">
                <!-- Overview Content -->
                <div v-if="activeTab === 'overview'" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex gap-4">
                            <HomeIcon
                                class="w-6 h-6 text-blue-600 flex-shrink-0"
                            />
                            <div>
                                <h3 class="font-semibold mb-1">
                                    Tipe Program: Bedah Rumah Total
                                </h3>
                                <p class="text-gray-600 text-sm">
                                    Renovasi menyeluruh meliputi perbaikan
                                    struktur, atap, lantai, dan fasilitas dasar
                                    rumah
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <UsersIcon
                                class="w-6 h-6 text-blue-600 flex-shrink-0"
                            />
                            <div>
                                <h3 class="font-semibold mb-1">
                                    Sasaran: Keluarga Prasejahtera
                                </h3>
                                <p class="text-gray-600 text-sm">
                                    Diprioritaskan untuk keluarga dengan kondisi
                                    rumah tidak layak huni
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-700 leading-relaxed">
                        Program Bedah Rumah merupakan inisiatif pemerintah untuk
                        meningkatkan kualitas hunian masyarakat prasejahtera.
                        Program ini mencakup perbaikan struktur rumah,
                        peningkatan sanitasi, dan penyediaan fasilitas dasar
                        yang layak. Setiap unit rumah akan direnovasi sesuai
                        standar kelayakan hunian dengan memperhatikan kebutuhan
                        penghuninya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import {
    ClockIcon,
    MapPinIcon,
    HomeIcon,
    CameraIcon,
    Share2,
    UsersIcon,
} from "lucide-vue-next";

const activeTab = ref("overview");
const tabs = [
    { id: "overview", name: "Ringkasan" },
    { id: "requirements", name: "Persyaratan" },
    { id: "process", name: "Proses" },
];
</script>
