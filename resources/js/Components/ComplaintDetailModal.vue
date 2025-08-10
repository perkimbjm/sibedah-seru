<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Detail Pengaduan</h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div v-if="data" class="space-y-6">
                    <!-- Info Pengaduan -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-semibold text-gray-800">Informasi Pengaduan</h4>
                            <span :class="getStatusClass(data.status)"
                                class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ getStatusText(data.status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Kode Tiket:</span>
                                <p class="font-medium">{{ data.kode_tiket }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Tanggal:</span>
                                <p class="font-medium">{{ formatDate(data.created_at) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama:</span>
                                <p class="font-medium">{{ data.name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Lokasi:</span>
                                <p class="font-medium">{{ data.district?.name }}, {{ data.village?.name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Percakapan Timeline -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800">Timeline Percakapan</h4>

                        <!-- Pengaduan Awal -->
                        <div class="flex space-x-3">
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-blue-800">{{ data.name }}</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(data.created_at) }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ data.complain }}</p>

                                    <!-- Foto Pengaduan -->
                                    <div v-if="data.foto_url" class="mt-3">
                                        <img :src="data.foto_url" alt="Foto Pengaduan"
                                            class="max-w-xs rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow"
                                            @click="openPhotoModal(data.foto_url)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Respon 1 -->
                        <div v-if="data.respon" class="flex space-x-3">
                            <div
                                class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headset text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-green-800">Tim Support</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(data.respon_at) }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ data.respon }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaduan Lanjutan 2 -->
                        <div v-if="data.complain2" class="flex space-x-3">
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-blue-800">{{ data.name }}</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(data.complain2_at)
                                            }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ data.complain2 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Respon 2 -->
                        <div v-if="data.respon2" class="flex space-x-3">
                            <div
                                class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headset text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-green-800">Tim Support</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(data.respon2_at) }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ data.respon2 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaduan Lanjutan 3 -->
                        <div v-if="data.complain3" class="flex space-x-3">
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-blue-800">{{ data.name }}</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(data.complain3_at)
                                            }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ data.complain3 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Respon 3 -->
                        <div v-if="data.respon3" class="flex space-x-3">
                            <div
                                class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headset text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-green-800">Tim Support</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(data.respon3_at) }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ data.respon3 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluasi Layanan -->
                    <div v-if="data.expect" class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-3">Evaluasi Layanan</h4>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600">Rating:</span>
                            <div class="flex space-x-1">
                                <i v-for="i in 4" :key="i"
                                    :class="i <= data.expect ? 'text-yellow-400' : 'text-gray-300'"
                                    class="fas fa-star"></i>
                            </div>
                            <span class="text-sm text-gray-600">({{ data.expect }}/4)</span>
                        </div>
                    </div>

                    <!-- Lanjutkan Percakapan -->
                    <div v-if="canContinue" class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-3">Lanjutkan Percakapan</h4>
                        <form @submit.prevent="submitContinuation" class="space-y-3">
                            <textarea v-model="continuationText" rows="3" placeholder="Tulis tanggapan Anda..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <button type="submit" :disabled="!continuationText.trim() || submittingContinuation"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                                <i v-if="submittingContinuation" class="fas fa-spinner fa-spin mr-2"></i>
                                <i v-else class="fas fa-paper-plane mr-2"></i>
                                Kirim Tanggapan
                            </button>
                        </form>
                    </div>

                    <!-- Evaluasi (jika belum ada) -->
                    <div v-if="canEvaluate" class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-3">Evaluasi Layanan</h4>
                        <p class="text-sm text-gray-600 mb-3">Berikan penilaian untuk layanan pengaduan ini:</p>
                        <div class="flex items-center space-x-2 mb-3">
                            <button v-for="i in 4" :key="i" @click="setRating(i)"
                                :class="i <= selectedRating ? 'text-yellow-400' : 'text-gray-300'"
                                class="fas fa-star text-xl hover:text-yellow-400 transition-colors"></button>
                            <span class="text-sm text-gray-600 ml-2">({{ selectedRating }}/4)</span>
                        </div>
                        <button @click="submitEvaluation" :disabled="!selectedRating || submittingEvaluation"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors disabled:opacity-50">
                            <i v-if="submittingEvaluation" class="fas fa-spinner fa-spin mr-2"></i>
                            <i v-else class="fas fa-star mr-2"></i>
                            Kirim Evaluasi
                        </button>
                    </div>

                    <!-- Copy Kode Tiket -->
                    <div class="flex space-x-3 pt-4">
                        <button @click="copyTicketCode"
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Salin Kode Tiket
                        </button>
                        <button @click="$emit('close')"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div v-if="showPhotoModal" class="fixed inset-0 bg-black bg-opacity-75 z-60 flex items-center justify-center p-4"
        @click="closePhotoModal">
        <div class="max-w-4xl max-h-[90vh] relative">
            <img :src="selectedPhotoUrl" alt="Foto Pengaduan" class="max-w-full max-h-full object-contain rounded-lg">
            <button @click="closePhotoModal"
                class="absolute top-2 right-2 bg-black bg-opacity-50 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

// Props & Emits
const props = defineProps({
    show: Boolean,
    data: Object
})

const emit = defineEmits(['close'])

// Reactive data
const continuationText = ref('')
const submittingContinuation = ref(false)
const selectedRating = ref(0)
const submittingEvaluation = ref(false)
const showPhotoModal = ref(false)
const selectedPhotoUrl = ref('')

// Computed
const canContinue = computed(() => {
    if (!props.data) return false
    return props.data.status !== 'closed' && props.data.status !== 'completed'
})

const canEvaluate = computed(() => {
    if (!props.data) return false
    return !props.data.expect && (props.data.status === 'completed' || props.data.respon)
})

// Methods
const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'process': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'closed': 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
    const texts = {
        'pending': 'Menunggu',
        'process': 'Diproses',
        'completed': 'Selesai',
        'closed': 'Ditutup'
    }
    return texts[status] || 'Tidak Diketahui'
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const formatDateTime = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const copyTicketCode = async () => {
    try {
        await navigator.clipboard.writeText(props.data.kode_tiket)
        alert('Kode tiket berhasil disalin!')
    } catch (error) {
        console.error('Error copying text:', error)
        // Fallback untuk browser yang tidak mendukung clipboard API
        const textArea = document.createElement('textarea')
        textArea.value = props.data.kode_tiket
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        alert('Kode tiket berhasil disalin!')
    }
}

const submitContinuation = async () => {
    submittingContinuation.value = true

    try {
        router.post(`/aduan/${props.data.id}/continue`, {
            complain: continuationText.value
        }, {
            onSuccess: () => {
                continuationText.value = ''
                emit('close')
                window.location.reload()
            },
            onError: (errors) => {
                console.error('Error:', errors)
                alert('Terjadi kesalahan saat mengirim tanggapan')
            }
        })
    } catch (error) {
        console.error('Error:', error)
        alert('Terjadi kesalahan saat mengirim tanggapan')
    } finally {
        submittingContinuation.value = false
    }
}

const setRating = (rating) => {
    selectedRating.value = rating
}

const openPhotoModal = (photoUrl) => {
    selectedPhotoUrl.value = photoUrl
    showPhotoModal.value = true
}

const closePhotoModal = () => {
    showPhotoModal.value = false
    selectedPhotoUrl.value = ''
}

const submitEvaluation = async () => {
    submittingEvaluation.value = true

    try {
        router.post(`/aduan/${props.data.id}/evaluate`, {
            expect: selectedRating.value
        }, {
            onSuccess: () => {
                selectedRating.value = 0
                emit('close')
                window.location.reload()
            },
            onError: (errors) => {
                console.error('Error:', errors)
                alert('Terjadi kesalahan saat mengirim evaluasi')
            }
        })
    } catch (error) {
        console.error('Error:', error)
        alert('Terjadi kesalahan saat mengirim evaluasi')
    } finally {
        submittingEvaluation.value = false
    }
}
</script>
