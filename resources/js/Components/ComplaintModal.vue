<template>
    <div v-if="show" class="flex fixed inset-0 z-50 justify-center items-center p-4 bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Form Pengaduan</h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                        <i class="text-xl fas fa-times"></i>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitComplaint" class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Nama *</label>
                        <input type="text" v-model="form.name" required
                            class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                        <input type="email" v-model="form.email" @blur="validateEmail" :disabled="authStore.isAuthenticated"
                            class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                        <div v-if="emailFeedback.show" :class="emailFeedback.class" class="mt-1 text-sm">
                            {{ emailFeedback.message }}
                        </div>
                        <div v-if="authStore.isAuthenticated" class="mt-1 text-sm text-gray-600">
                            Email akan diisi otomatis dari akun yang login
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Nomor HP</label>
                        <input type="tel" v-model="form.contact"
                            class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Kecamatan *</label>
                        <select v-model="form.district_id" @change="loadVillages" required
                            class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kecamatan</option>
                            <option v-for="district in districts" :key="district.id" :value="district.id">
                                {{ district.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Kelurahan/Desa *</label>
                        <select v-model="form.village_id" required
                            class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="!form.district_id">
                            <option value="">Pilih Kelurahan/Desa</option>
                            <option v-for="village in villages" :key="village.id" :value="village.id">
                                {{ village.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Isi Pengaduan *</label>
                        <textarea v-model="form.complain" rows="4" required
                            placeholder="Jelaskan pengaduan, saran, atau aspirasi Anda..."
                            class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- Foto Upload -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Foto Pendukung (Opsional)
                            <span class="text-xs text-gray-500">- Max 3MB, format: JPG, PNG, GIF, WebP</span>
                        </label>
                        <div class="space-y-3">
                            <input type="file" ref="fotoInput" @change="handleFotoUpload" accept="image/*"
                                class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <!-- Preview Foto -->
                            <div v-if="fotoPreview" class="relative">
                                <img :src="fotoPreview" alt="Preview" class="object-cover w-32 h-32 rounded-md border">
                                <button type="button" @click="removeFoto"
                                    class="flex absolute -top-2 -right-2 justify-center items-center w-6 h-6 text-white bg-red-500 rounded-full hover:bg-red-600">
                                    <i class="text-xs fas fa-times"></i>
                                </button>
                            </div>

                            <!-- Upload Progress -->
                            <div v-if="uploadProgress > 0 && uploadProgress < 100"
                                class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-600 rounded-full transition-all duration-300"
                                    :style="{ width: uploadProgress + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Captcha -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Captcha *</label>
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-2 text-lg font-semibold text-blue-600 bg-blue-50 rounded">
                                {{ captcha.num1 }} + {{ captcha.num2 }} = ?
                            </span>
                            <input type="number" v-model="form.captcha" required placeholder="Hasil"
                                class="flex-1 px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" @click="generateCaptcha" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-refresh"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Loading state -->
                    <div v-if="loading" class="py-4 text-center">
                        <i class="text-blue-600 fas fa-spinner fa-spin"></i>
                        <p class="mt-2 text-sm text-gray-600">Mengirim pengaduan...</p>
                    </div>

                    <!-- Error message -->
                    <div v-if="errorMessage" class="p-3 bg-red-50 rounded-md border border-red-200">
                        <p class="text-sm text-red-600">{{ errorMessage }}</p>
                    </div>

                    <div class="flex pt-4 space-x-3">
                        <button type="submit" :disabled="loading"
                            class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-md transition-colors hover:bg-blue-700 disabled:opacity-50">
                            <i class="mr-2 fas fa-paper-plane"></i>Kirim Pengaduan
                        </button>
                        <button type="button" @click="$emit('close')"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-300 rounded-md transition-colors hover:bg-gray-400">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.js'

// Props & Emits
const props = defineProps({
    show: Boolean
})

const emit = defineEmits(['close', 'submitted'])

// Auth store
const authStore = useAuthStore()

// Reactive data
const form = reactive({
    name: '',
    email: '',
    contact: '',
    district_id: '',
    village_id: '',
    complain: '',
    captcha: ''
})

const districts = ref([])
const villages = ref([])
const captcha = reactive({
    num1: 0,
    num2: 0
})
const loading = ref(false)
const errorMessage = ref('')
const fotoFile = ref(null)
const fotoPreview = ref(null)
const uploadProgress = ref(0)
const fotoInput = ref(null)

// Email validation feedback
const emailFeedback = reactive({
    show: false,
    message: '',
    class: ''
})

// Methods
const loadDistricts = async () => {
    try {
        const response = await fetch('/api/kecamatan')
        const data = await response.json()
        districts.value = data.data
    } catch (error) {
        console.error('Error loading districts:', error)
    }
}

const loadVillages = async () => {
    if (!form.district_id) {
        villages.value = []
        return
    }

    try {
        const response = await fetch(`/aduan/villages/${form.district_id}`)
        const data = await response.json()
        villages.value = data
        form.village_id = '' // Reset village selection
    } catch (error) {
        console.error('Error loading villages:', error)
    }
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
            const text = await response.text()
            console.error('Response is not JSON:', text)
            throw new Error('Response is not JSON')
        }

        const data = await response.json()
        captcha.num1 = data.num1
        captcha.num2 = data.num2
        form.captcha = '' // Reset captcha input
        console.log('Captcha generated:', data.question) // Debug log
    } catch (error) {
        console.error('Error generating captcha:', error)
        // Fallback jika API gagal - generate local
        captcha.num1 = Math.floor(Math.random() * 10) + 1
        captcha.num2 = Math.floor(Math.random() * 10) + 1
        form.captcha = ''
        console.log('Using fallback captcha:', captcha.num1, '+', captcha.num2)
    }
}

const validateEmail = async () => {
    const email = form.email.trim()

    // Clear previous feedback
    emailFeedback.show = false
    emailFeedback.message = ''
    emailFeedback.class = ''

    // If email is empty, don't validate
    if (!email) {
        return
    }

    // Basic email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(email)) {
        emailFeedback.show = true
        emailFeedback.message = 'Format email tidak valid.'
        emailFeedback.class = 'text-red-600'
        return
    }

    // Show loading state
    emailFeedback.show = true
    emailFeedback.message = 'Memvalidasi email...'
    emailFeedback.class = 'text-blue-600'

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        const formData = new FormData()
        formData.append('email', email)

        const response = await fetch('/aduan/validate-email', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })

        const data = await response.json()

        if (data.valid) {
            emailFeedback.show = true
            emailFeedback.message = data.message
            emailFeedback.class = 'text-green-600'
        } else {
            emailFeedback.show = true
            emailFeedback.message = data.message
            emailFeedback.class = 'text-red-600'
        }
    } catch (error) {
        console.error('Error validating email:', error)
        emailFeedback.show = true
        emailFeedback.message = 'Terjadi kesalahan saat memvalidasi email.'
        emailFeedback.class = 'text-red-600'
    }
}

const handleFotoUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return

    // Validate file size (3MB = 3 * 1024 * 1024 bytes)
    if (file.size > 3 * 1024 * 1024) {
        errorMessage.value = 'Ukuran foto maksimal 3MB'
        event.target.value = ''
        return
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
    if (!allowedTypes.includes(file.type)) {
        errorMessage.value = 'Format foto harus JPG, PNG, GIF, atau WebP'
        event.target.value = ''
        return
    }

    fotoFile.value = file

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
        fotoPreview.value = e.target.result
    }
    reader.readAsDataURL(file)

    // Clear any previous error
    errorMessage.value = ''
}

const removeFoto = () => {
    fotoFile.value = null
    fotoPreview.value = null
    uploadProgress.value = 0
    if (fotoInput.value) {
        fotoInput.value.value = ''
    }
}

const resetForm = () => {
    Object.assign(form, {
        name: '',
        email: '',
        contact: '',
        district_id: '',
        village_id: '',
        complain: '',
        captcha: ''
    })
    villages.value = []
    errorMessage.value = ''
    removeFoto()
}

const submitComplaint = async () => {
    loading.value = true
    errorMessage.value = ''

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
        if (!csrfToken) {
            errorMessage.value = 'CSRF token tidak ditemukan. Harap refresh halaman.'
            return
        }

        const formData = new FormData()
        Object.keys(form).forEach(key => {
            if (form[key]) {
                formData.append(key, form[key])
            }
        })

        // Add foto file if exists
        if (fotoFile.value) {
            formData.append('foto', fotoFile.value)
        }

        console.log('Submitting complaint with data:', Object.fromEntries(formData.entries()))

        const response = await fetch('/aduan', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Pastikan cookies session dikirim
        })

        console.log('Submit response status:', response.status)
        console.log('Submit response headers:', Object.fromEntries(response.headers.entries()))

        // Check if response is actually JSON
        const contentType = response.headers.get('content-type')
        if (!contentType || !contentType.includes('application/json')) {
            const errorText = await response.text()
            console.error('Non-JSON response from submit:', errorText)
            errorMessage.value = `Server mengembalikan HTML error (${response.status}). Periksa console untuk detail lengkap.`
            return
        }

        const data = await response.json()
        console.log('Submit response data:', data)

        if (data.success) {
            emit('submitted', data.data)
            resetForm()
        } else {
            errorMessage.value = data.message || 'Gagal mengirim pengaduan'
            // Regenerate captcha on error
            generateCaptcha()
        }
    } catch (error) {
        console.error('Submit network error:', error)
        errorMessage.value = 'Terjadi kesalahan jaringan: ' + error.message
        generateCaptcha()
    } finally {
        loading.value = false
    }
}

// Watchers
watch(() => props.show, (newVal) => {
    if (newVal) {
        generateCaptcha()
    } else {
        resetForm()
    }
})

// Lifecycle
onMounted(() => {
    loadDistricts()
})
</script>
