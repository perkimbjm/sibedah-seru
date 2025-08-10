<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300">
            <div class="p-6 text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ title }}</h3>

                <!-- Message -->
                <p class="text-gray-600 mb-4">{{ message }}</p>

                <!-- Ticket Code Display -->
                <div v-if="ticketCode" class="bg-gray-50 p-4 rounded-lg mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Tiket Anda:</label>
                    <div class="flex items-center space-x-2">
                        <input :value="ticketCode" readonly
                            class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-center font-mono text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button @click="copyTicketCode"
                            class="relative px-4 py-2 rounded-md transition-all duration-200 transform"
                            :class="copied
                                ? 'bg-green-600 hover:bg-green-700 text-white scale-95'
                                : 'bg-blue-600 hover:bg-blue-700 text-white hover:scale-105'">
                            <span v-if="copying" class="flex items-center">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span v-else-if="copied" class="flex items-center">
                                <i class="fas fa-check mr-1"></i>
                                <span class="text-sm">Tersalin!</span>
                            </span>
                            <span v-else class="flex items-center">
                                <i class="fas fa-copy mr-1"></i>
                                <span class="text-sm">Salin</span>
                            </span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Simpan kode ini untuk melacak status pengaduan Anda
                    </p>

                    <!-- Copy Success Message -->
                    <div v-if="copied" class="mt-2 p-2 bg-green-50 border border-green-200 rounded-md animate-fade-in">
                        <p class="text-xs text-green-700 flex items-center justify-center">
                            <i class="fas fa-check-circle mr-1"></i>
                            Kode tiket berhasil disalin ke clipboard!
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button v-if="showTrackButton" @click="$emit('track')"
                        class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-search mr-2"></i>Lacak Pengaduan
                    </button>
                    <button @click="$emit('close')"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

// Props
const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Berhasil!'
    },
    message: {
        type: String,
        default: 'Operasi berhasil dilakukan.'
    },
    ticketCode: String,
    showTrackButton: {
        type: Boolean,
        default: true
    }
})

// Emits
const emit = defineEmits(['close', 'track'])

// Reactive data
const copied = ref(false)
const copying = ref(false)

// Methods
const copyTicketCode = async () => {
    copying.value = true

    try {
        await navigator.clipboard.writeText(props.ticketCode)
        copying.value = false
        copied.value = true

        // Reset copied state after 3 seconds
        setTimeout(() => {
            copied.value = false
        }, 3000)
    } catch (error) {
        console.error('Error copying text:', error)
        // Fallback untuk browser yang tidak mendukung clipboard API
        try {
            const textArea = document.createElement('textarea')
            textArea.value = props.ticketCode
            textArea.style.position = 'fixed'
            textArea.style.opacity = '0'
            document.body.appendChild(textArea)
            textArea.focus()
            textArea.select()
            document.execCommand('copy')
            document.body.removeChild(textArea)

            copying.value = false
            copied.value = true

            setTimeout(() => {
                copied.value = false
            }, 3000)
        } catch (fallbackError) {
            copying.value = false
            console.error('Fallback copy failed:', fallbackError)
            alert('Gagal menyalin. Silakan salin kode secara manual: ' + props.ticketCode)
        }
    }
}
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
