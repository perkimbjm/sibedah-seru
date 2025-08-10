<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300">
            <div class="p-6 text-center">
                <!-- Error Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ title }}</h3>

                <!-- Message -->
                <p class="text-gray-600 mb-4">{{ message }}</p>

                <!-- Additional Info (optional) -->
                <div v-if="additionalInfo" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ additionalInfo }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button v-if="showRetryButton" @click="$emit('retry')"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 transform hover:scale-105">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
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
// Props
const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Terjadi Kesalahan'
    },
    message: {
        type: String,
        default: 'Mohon maaf, terjadi kesalahan yang tidak terduga.'
    },
    additionalInfo: {
        type: String,
        default: ''
    },
    showRetryButton: {
        type: Boolean,
        default: false
    }
})

// Emits
const emit = defineEmits(['close', 'retry'])
</script>

<style scoped>
/* Modal enter/leave animations */
.v-enter-active, .v-leave-active {
    transition: all 0.3s ease;
}

.v-enter-from, .v-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
</style>
