<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
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

                    <!-- Form Tanggapan Lanjutan -->
                    <div v-if="data.can_add_complaint && !showComplaintForm" class="bg-orange-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Tanggapan Lanjutan</h4>
                            <span class="text-sm text-gray-600">{{ getNextComplaintNumber() }}</span>
                        </div>
                        <p class="text-gray-600 mb-3">Ingin menambahkan tanggapan lanjutan untuk pengaduan ini?</p>
                        <button @click="showComplaintForm = true"
                            class="w-full bg-orange-600 text-white py-2 px-4 rounded-md hover:bg-orange-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Tanggapan Lanjutan
                        </button>
                    </div>

                    <!-- Form Input Tanggapan -->
                    <div v-if="showComplaintForm" class="bg-orange-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Tanggapan Lanjutan {{ getNextComplaintNumber() }}
                            </h4>
                            <button @click="cancelComplaint" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-3">
                            <textarea v-model="complaintText" placeholder="Masukkan tanggapan lanjutan Anda..."
                                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none"
                                rows="4"></textarea>

                            <!-- Captcha -->
                            <div class="flex items-center space-x-3">
                                <div class="flex-1">
                                    <label class="text-sm text-gray-600 mb-1 block">Captcha: {{ captchaQuestion
                                        }}</label>
                                    <input type="number" v-model="captchaAnswer" placeholder="Jawaban captcha"
                                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                <button @click="generateCaptcha"
                                    class="px-3 py-2 bg-gray-200 text-gray-600 rounded-md hover:bg-gray-300 transition-colors">
                                    <i class="fas fa-refresh"></i>
                                </button>
                            </div>

                            <div class="flex space-x-3">
                                <button @click="submitComplaint" :disabled="submittingComplaint"
                                    class="flex-1 bg-orange-600 text-white py-2 px-4 rounded-md hover:bg-orange-700 transition-colors disabled:opacity-50">
                                    <i v-if="submittingComplaint" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-paper-plane mr-2"></i>
                                    {{ submittingComplaint ? 'Mengirim...' : 'Kirim Tanggapan' }}
                                </button>
                                <button @click="cancelComplaint"
                                    class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Selesai -->
                    <div v-if="canCompleteComplaint()" class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Selesaikan Pengaduan</h4>
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <p class="text-gray-600 mb-3">Apakah pengaduan ini sudah selesai dan tidak perlu tindak lanjut?
                        </p>
                        <button @click="completeComplaint" :disabled="completing"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors disabled:opacity-50">
                            <i v-if="completing" class="fas fa-spinner fa-spin mr-2"></i>
                            <i v-else class="fas fa-check mr-2"></i>
                            {{ completing ? 'Menyelesaikan...' : 'Selesaikan Pengaduan' }}
                        </button>
                    </div>

                    <!-- Form Evaluasi -->
                    <div v-if="data.can_evaluate && !data.expect" class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Evaluasi Layanan</h4>
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <p class="text-gray-600 mb-3">Bagaimana penilaian Anda terhadap layanan yang diberikan?</p>

                        <div class="space-y-3">


                            <!-- Rating Stars -->
                            <div class="flex justify-center space-x-1">
                                <button v-for="rating in 4" :key="rating" type="button"
                                    @click.prevent="selectRating(rating)" :title="`Rating ${rating} bintang`" :style="{
                                        color: rating <= selectedRating ? '#fbbf24' : '#d1d5db',
                                        fontSize: '28px',
                                        border: 'none',
                                        background: 'transparent',
                                        cursor: 'pointer',
                                        padding: '6px',
                                        margin: '0 2px',
                                        borderRadius: '4px'
                                    }"
                                    class="hover:scale-110 transition-all duration-200 focus:outline-none hover:bg-yellow-50">
                                    ★
                                </button>
                            </div>

                            <!-- Rating Text -->
                            <div class="text-center text-sm text-gray-600 font-medium">
                                {{ getRatingText(selectedRating) }}
                            </div>

                            <!-- Submit Button -->
                            <button @click="submitEvaluation" :disabled="!selectedRating || submittingEvaluation"
                                class="w-full bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="submittingEvaluation" class="fas fa-spinner fa-spin mr-2"></i>
                                <i v-else class="fas fa-paper-plane mr-2"></i>
                                {{ submittingEvaluation ? 'Mengirim...' : 'Kirim Evaluasi' }}
                            </button>
                        </div>
                    </div>

                    <!-- Evaluasi Layanan (sudah ada) -->
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
                        <p class="text-sm text-gray-600 mt-2">{{ getRatingText(data.expect) }}</p>
                    </div>

                    <!-- Copy Kode Tiket -->
                    <div class="flex space-x-3 pt-4">
                        <button @click="copyTicketCode"
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
                            :class="{ 'bg-green-600 hover:bg-green-700': copied }">
                            <i v-if="copied" class="fas fa-check mr-2"></i>
                            <i v-else class="fas fa-copy mr-2"></i>
                            {{ copied ? 'Tersalin!' : 'Salin Kode Tiket' }}
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
</template>

<script setup>
import { ref, watch } from 'vue'

// Props & Emits
const props = defineProps({
    show: Boolean,
    data: Object
})

const emit = defineEmits(['close', 'refresh'])

// Reactive data
const copied = ref(false)
const showComplaintForm = ref(false)
const complaintText = ref('')
const captchaQuestion = ref('')
const captchaAnswer = ref('')
const submittingComplaint = ref(false)
const completing = ref(false)
const selectedRating = ref(0)
const submittingEvaluation = ref(false)

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
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (error) {
        console.error('Error copying text:', error)
        // Fallback untuk browser yang tidak mendukung clipboard API
        const textArea = document.createElement('textarea')
        textArea.value = props.data.kode_tiket
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    }
}

const getNextComplaintNumber = () => {
    if (!props.data.complain2) return '2'
    if (!props.data.complain3) return '3'
    return ''
}

const canCompleteComplaint = () => {
    // Bisa selesaikan jika ada respon dari admin dan status bukan completed
    return props.data.status !== 'completed' && (props.data.respon || props.data.respon2 || props.data.respon3)
}

const generateCaptcha = async () => {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        const response = await fetch('/aduan/captcha', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Pastikan cookies session dikirim
        })

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        // Check if response is JSON
        const contentType = response.headers.get('content-type')
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON')
        }

        const data = await response.json()
        captchaQuestion.value = data.question
    } catch (error) {
        console.error('Error generating captcha:', error)
        // Fallback captcha generation
        const num1 = Math.floor(Math.random() * 10) + 1
        const num2 = Math.floor(Math.random() * 10) + 1
        captchaQuestion.value = `${num1} + ${num2} = ?`
    }
}

const cancelComplaint = () => {
    showComplaintForm.value = false
    complaintText.value = ''
    captchaAnswer.value = ''
}

const submitComplaint = async () => {
    if (!complaintText.value.trim()) {
        alert('Tanggapan tidak boleh kosong!')
        return
    }

    if (!captchaAnswer.value) {
        alert('Silakan jawab captcha!')
        return
    }

    submittingComplaint.value = true

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        const formData = new FormData()
        formData.append('kode_tiket', props.data.kode_tiket)
        formData.append('complaint_text', complaintText.value.trim())
        formData.append('captcha', captchaAnswer.value)

        const response = await fetch('/aduan/add-complaint', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Pastikan cookies session dikirim
        })

        const data = await response.json()

        if (response.ok && data.success) {
            alert('Tanggapan lanjutan berhasil ditambahkan!')
            cancelComplaint()
            // Refresh data dengan emit ke parent
            emit('refresh')
        } else {
            alert(data.message || 'Terjadi kesalahan saat mengirim tanggapan')
        }
    } catch (error) {
        console.error('Error submitting complaint:', error)
        alert('Terjadi kesalahan saat mengirim tanggapan')
    } finally {
        submittingComplaint.value = false
    }
}

const completeComplaint = async () => {
    if (!confirm('Apakah Anda yakin ingin menyelesaikan pengaduan ini?')) {
        return
    }

    completing.value = true

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        const formData = new FormData()
        formData.append('kode_tiket', props.data.kode_tiket)

        const response = await fetch('/aduan/complete', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })

        const data = await response.json()

        if (response.ok && data.success) {
            alert('Pengaduan berhasil diselesaikan!')
            // Refresh data dengan emit ke parent
            emit('refresh')
        } else {
            alert(data.message || 'Terjadi kesalahan saat menyelesaikan pengaduan')
        }
    } catch (error) {
        console.error('Error completing complaint:', error)
        alert('Terjadi kesalahan saat menyelesaikan pengaduan')
    } finally {
        completing.value = false
    }
}

const selectRating = (rating) => {
    selectedRating.value = rating
}

const getRatingText = (rating) => {
    const texts = {
        1: 'Sangat Tidak Puas',
        2: 'Tidak Puas',
        3: 'Puas',
        4: 'Sangat Puas'
    }
    return texts[rating] || 'Pilih rating'
}

const submitEvaluation = async () => {
    if (!selectedRating.value) {
        alert('Silakan pilih rating!')
        return
    }

    submittingEvaluation.value = true

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        const formData = new FormData()
        formData.append('kode_tiket', props.data.kode_tiket)
        formData.append('rating', selectedRating.value)

        const response = await fetch('/aduan/evaluate', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })

        const data = await response.json()

        if (response.ok && data.success) {
            alert('Evaluasi berhasil dikirim! Terima kasih atas penilaian Anda.')
            selectedRating.value = 0
            // Refresh data dengan emit ke parent
            emit('refresh')
        } else {
            alert(data.message || 'Terjadi kesalahan saat mengirim evaluasi')
        }
    } catch (error) {
        console.error('Error submitting evaluation:', error)
        alert('Terjadi kesalahan saat mengirim evaluasi')
    } finally {
        submittingEvaluation.value = false
    }
}

// Generate captcha when showing complaint form
watch(showComplaintForm, (newValue) => {
    if (newValue) {
        generateCaptcha()
    }
})

// Reset form states when modal is opened or data changes
watch([() => props.show, () => props.data], ([newShow, newData], [oldShow, oldData]) => {
    if (newShow && (!oldShow || newData !== oldData)) {
        // Reset form states kecuali selectedRating untuk evaluasi
        showComplaintForm.value = false
        complaintText.value = ''
        captchaAnswer.value = ''
        captchaQuestion.value = ''
        copied.value = false
        submittingComplaint.value = false
        completing.value = false
        submittingEvaluation.value = false

        // Hanya reset selectedRating jika user sudah pernah evaluasi (ada data.expect)
        if (newData && newData.expect) {
            selectedRating.value = 0
        } else {
            // Jika belum ada evaluasi, biarkan user memilih rating
            selectedRating.value = 0
        }


    }
})


</script>
