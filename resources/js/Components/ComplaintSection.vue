<template>
    <section id="pengaduan" class="py-12 bg-gray-50">
        <div class="container mx-auto px-4" data-aos="fade-up">
            <div class="text-center mb-8">
                <h3 class="text-3xl font-bold text-gray-800 mb-4">LAYANAN PENGADUAN</h3>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Sampaikan keluhan, saran, atau aspirasi Anda terkait layanan aplikasi ini.
                    Kami berkomitmen untuk merespons setiap pengaduan dengan baik.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Form Pengaduan -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-center mb-6">
                        <i class="fas fa-comment-alt text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-semibold mb-2">Buat Pengaduan</h4>
                        <p class="text-gray-600">Sampaikan keluhan atau saran Anda</p>
                    </div>
                    <button @click="openComplaintModal"
                        class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Buat Pengaduan Baru
                    </button>
                </div>

                <!-- Tracking Pengaduan -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-center mb-6">
                        <i class="fas fa-search text-4xl text-green-600 mb-4"></i>
                        <h4 class="text-xl font-semibold mb-2">Lacak Pengaduan</h4>
                        <p class="text-gray-600">Cek status pengaduan dengan kode tiket</p>
                    </div>

                    <div class="space-y-4">
                        <input type="text" v-model="trackingCode"
                            placeholder="Masukkan kode tiket (contoh: ADU-ABC123-0702)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button @click="trackComplaint" :disabled="tracking"
                            class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
                            <i v-if="tracking" class="fas fa-spinner fa-spin mr-2"></i>
                            <i v-else class="fas fa-search mr-2"></i>
                            {{ tracking ? 'Melacak...' : 'Lacak Pengaduan' }}
                        </button>

                        <!-- Debug Controls - Remove in production -->
                        <div v-if="false" class="mt-4 space-y-2">
                            <button @click="testErrorModal"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-sm transform hover:scale-105">
                                <i class="fas fa-bug mr-2"></i>Test Error Modal (Ctrl+E)
                            </button>
                            <p class="text-xs text-gray-500 text-center">
                                <i class="fas fa-keyboard mr-1"></i>
                                Tekan Ctrl+E untuk test keyboard shortcut
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form Pengaduan -->
        <ComplaintModal :show="showComplaintModal" @close="closeComplaintModal" @submitted="onComplaintSubmitted" />

        <!-- Modal Tracking Result -->
        <TrackingModal :show="showTrackingModal" :data="trackingData" @close="closeTrackingModal"
            @refresh="refreshTrackingData" />

        <!-- Error Modal -->
        <ErrorModal :show="showErrorModal" :title="errorData.title" :message="errorData.message"
            :additional-info="errorData.additionalInfo" :show-retry-button="errorData.showRetry"
            @close="closeErrorModal" @retry="retryLastAction" />

        <!-- Success Notification -->
        <SuccessNotification :show="showSuccessNotification" :title="successData.title" :message="successData.message"
            :ticket-code="successData.ticketCode" @close="closeSuccessNotification" @track="handleTrackFromSuccess" />
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import ComplaintModal from './ComplaintModal.vue'
import TrackingModal from './TrackingModal.vue'
import ErrorModal from './ErrorModal.vue'
import SuccessNotification from './SuccessNotification.vue'

// Reactive data
const showComplaintModal = ref(false)
const showTrackingModal = ref(false)
const showSuccessNotification = ref(false)
const showErrorModal = ref(false)
const trackingCode = ref('')
const trackingData = ref(null)
const tracking = ref(false)
const successData = ref({
    title: '',
    message: '',
    ticketCode: ''
})
const errorData = ref({
    title: '',
    message: '',
    additionalInfo: '',
    showRetry: false
})
const lastAction = ref('')

// Methods
const openComplaintModal = () => {
    showComplaintModal.value = true
}

const closeComplaintModal = () => {
    showComplaintModal.value = false
}

const onComplaintSubmitted = (data) => {
    closeComplaintModal()

    // Show beautiful success notification
    successData.value = {
        title: 'Pengaduan Berhasil Dikirim!',
        message: 'Pengaduan Anda telah berhasil diterima dan akan segera ditindaklanjuti oleh tim kami.',
        ticketCode: data.kode_tiket
    }
    showSuccessNotification.value = true
}

const trackComplaint = async () => {
    if (!trackingCode.value.trim()) {
        showError(
            'Input Tidak Valid',
            'Silakan masukkan kode tiket terlebih dahulu.',
            'Format kode tiket: ADU-XXXXXX-MMDD (contoh: ADU-ABC123-0702)',
            false
        )
        return
    }

    lastAction.value = 'track'
    tracking.value = true

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
        if (!csrfToken) {
            console.error('CSRF token not found')
            showError(
                'Error Sistem',
                'Token keamanan tidak ditemukan.',
                'Silakan refresh halaman dan coba lagi.',
                true
            )
            return
        }

        const formData = new FormData()
        formData.append('kode_tiket', trackingCode.value.trim())

        const response = await fetch('/aduan/track', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })

        // Check if response is actually JSON
        const contentType = response.headers.get('content-type')
        if (!contentType || !contentType.includes('application/json')) {
            const errorText = await response.text()
            console.error('Non-JSON response:', errorText)
            showError(
                'Error Server',
                'Server mengembalikan respons yang tidak valid.',
                `Status: ${response.status}. Silakan coba lagi atau hubungi administrator.`,
                true
            )
            return
        }

        const data = await response.json()
        console.log('Tracking response:', { status: response.status, success: data.success })

        if (response.ok && data.success) {
            trackingData.value = data.data
            showTrackingModal.value = true
        } else {
            // Handle specific error cases
            if (response.status === 404 || (!response.ok && data.message?.includes('tidak ditemukan'))) {
                showError(
                    'Kode Tiket Tidak Ditemukan',
                    'Kode tiket yang Anda masukkan tidak ditemukan dalam sistem.',
                    'Periksa kembali kode tiket Anda. Pastikan format benar: ADU-XXXXXX-MMDD (contoh: ADU-ABC123-0702)',
                    false
                )
            } else {
                showError(
                    'Error Server',
                    data.message || 'Terjadi kesalahan pada server.',
                    `Kode error: ${response.status}. Silakan coba lagi beberapa saat.`,
                    true
                )
            }
        }
    } catch (error) {
        console.error('Network error:', error)
        showError(
            'Error Jaringan',
            'Gagal menghubungi server.',
            'Periksa koneksi internet Anda dan coba lagi.',
            true
        )
    } finally {
        tracking.value = false
    }
}

const showError = (title, message, additionalInfo = '', showRetry = false) => {
    console.log('Showing error modal:', { title, message })

    errorData.value = {
        title,
        message,
        additionalInfo,
        showRetry
    }

    showErrorModal.value = true
}

const closeErrorModal = () => {
    showErrorModal.value = false
    errorData.value = {
        title: '',
        message: '',
        additionalInfo: '',
        showRetry: false
    }
}

const retryLastAction = () => {
    closeErrorModal()
    if (lastAction.value === 'track') {
        trackComplaint()
    }
}

const closeTrackingModal = () => {
    showTrackingModal.value = false
    trackingData.value = null
}

const refreshTrackingData = async () => {
    if (!trackingData.value) return

    // Re-track dengan kode tiket yang sama
    const currentTicket = trackingData.value.kode_tiket
    tracking.value = true

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
        const formData = new FormData()
        formData.append('kode_tiket', currentTicket)

        const response = await fetch('/aduan/track', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        })

        const data = await response.json()

        if (response.ok && data.success) {
            trackingData.value = data.data
        }
    } catch (error) {
        console.error('Error refreshing tracking data:', error)
    } finally {
        tracking.value = false
    }
}

const closeSuccessNotification = () => {
    showSuccessNotification.value = false
    successData.value = { title: '', message: '', ticketCode: '' }
}

const handleTrackFromSuccess = () => {
    closeSuccessNotification()
    trackingCode.value = successData.value.ticketCode
    trackComplaint()
}

const testErrorModal = () => {
    showError(
        'Test Error Modal',
        'Ini adalah test untuk memastikan ErrorModal.vue berfungsi dengan baik.',
        'Jika Anda melihat modal ini, berarti ErrorModal.vue sudah bekerja dengan sempurna!',
        true
    )
}

// Keyboard shortcut for testing (Ctrl+E)
const handleKeyPress = (event) => {
    if (event.ctrlKey && event.key === 'e') {
        event.preventDefault()
        testErrorModal()
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeyPress)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyPress)
})
</script>
