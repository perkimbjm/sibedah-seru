<template>
    <div class="p-12 mx-auto max-w-3xl">
        <div class="mb-8 text-center">
            <h2 class="mb-2 text-3xl font-bold text-gray-800">PENCARIAN DATA RTLH</h2>
            <div class="relative mt-3">
                <div class="absolute bottom-0.5 inset-x-1/2 w-28 h-px bg-gray-300 transform -translate-x-1/2"></div>
                <div class="absolute bottom-0 inset-x-1/2 w-9 h-1 bg-green-500 transform -translate-x-1/2"></div>
            </div>
            <p class="p-6 mt-6 text-gray-600">Masukkan NIK atau Nomor Kartu Keluarga untuk memeriksa status apakah sudah
                terdaftar di basis data RTLH</p>
        </div>

        <form @submit.prevent="searchRTLH" class="mb-8">
            <div class="flex items-center">
                <div class="relative flex-grow">
                    <input v-model="searchInput" type="text" placeholder="Masukkan NIK / Nomor KK"
                        class="px-6 py-4 w-full rounded-l-lg border-2 border-gray-300 transition duration-150 ease-in-out text-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                </div>
                <button type="submit"
                    class="px-8 py-4 text-lg font-semibold text-white bg-blue-600 rounded-r-lg transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Cari
                </button>
            </div>
        </form>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal"
                class="flex overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-start items-center ml-5 sm:justify-center"
                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <div
                    class="inline-block overflow-hidden text-left align-middle bg-white rounded-lg shadow-xl transition-all transform sm:my-8 sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Informasi RTLH
                            </h3>
                            <button @click="closeModal"
                                class="text-xl text-gray-500 hover:text-gray-700 focus:outline-none">
                                &times;
                            </button>
                        </div>
                        <div class="mt-2">
                            <template v-if="rtlhData">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">NIK</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ rtlhData.nik }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Nomor KK</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ rtlhData.kk }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Nama</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ rtlhData.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Alamat</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ rtlhData.address }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Status Renovasi</p>
                                        <p class="mt-1 text-sm font-semibold"
                                            :class="rtlhData.is_renov === 'sudah dibedah rumah' ? 'text-green-600' : 'text-yellow-600'">
                                            {{ rtlhData.is_renov }}
                                        </p>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <p class="text-lg text-red-500">
                                    Data keluarga yang Anda masukkan belum ada di database rumah tidak layak huni.
                                    Silahkan ajukan di Dinas atau periksa kembali input Anda
                                </p>
                            </template>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="closeModal" type="button"
                            class="inline-flex justify-center px-4 py-2 w-full text-base font-medium text-white bg-blue-600 rounded-md border border-transparent shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const searchInput = ref('')
const showModal = ref(false)
const rtlhData = ref(null)

const searchRTLH = async () => {
    try {
        const response = await fetch(`/api/rtlh/${searchInput.value}`)
        if (response.ok) {
            rtlhData.value = await response.json()
        } else {
            rtlhData.value = null
        }
        showModal.value = true
    } catch (error) {
        console.error('Error fetching RTLH data:', error)
        rtlhData.value = null
        showModal.value = true
    }
}

const closeModal = () => {
    showModal.value = false
}
</script>

<style scoped></style>
