<template>
    <AppLayout title="Pengaduan Saya">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pengaduan Saya
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clipboard-list text-2xl text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Pengaduan</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-2xl text-yellow-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Menunggu</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-cog text-2xl text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Diproses</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.process }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-2xl text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Selesai</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.completed }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengaduan List -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Pengaduan</h3>
                            <Link :href="route('landingpage')"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Buat Pengaduan Baru
                            </Link>
                        </div>

                        <!-- Filter -->
                        <div class="mb-6">
                            <select v-model="selectedStatus" @change="filterComplaints"
                                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="process">Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="closed">Ditutup</option>
                            </select>
                        </div>

                        <!-- Empty State -->
                        <div v-if="!complaints || complaints.length === 0" class="text-center py-12">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pengaduan</h3>
                            <p class="text-gray-500 mb-6">Anda belum pernah membuat pengaduan</p>
                            <Link :href="route('landingpage')"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Buat Pengaduan Pertama
                            </Link>
                        </div>

                        <!-- Complaints List -->
                        <div v-else class="space-y-4">
                            <div v-for="complaint in filteredComplaints" :key="complaint.id"
                                class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ complaint.kode_tiket }}</h4>
                                        <p class="text-sm text-gray-600">{{ formatDate(complaint.created_at) }}</p>
                                    </div>
                                    <span :class="getStatusClass(complaint.status)"
                                        class="px-3 py-1 rounded-full text-sm font-medium">
                                        {{ getStatusText(complaint.status) }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <p class="text-gray-700 line-clamp-3">{{ complaint.complain }}</p>

                                    <!-- Foto Thumbnail -->
                                    <div v-if="complaint.foto_url" class="mt-2">
                                        <img :src="complaint.foto_url" alt="Foto Pengaduan"
                                            class="w-16 h-16 object-cover rounded-md border cursor-pointer hover:shadow-md transition-shadow"
                                            @click="openPhotoModal(complaint.foto_url)">
                                    </div>
                                </div>

                                <div class="flex justify-between items-center text-sm text-gray-600">
                                    <span>{{ complaint.district?.name }}, {{ complaint.village?.name }}</span>
                                    <div class="flex space-x-2">
                                        <button @click="viewDetail(complaint)"
                                            class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </button>
                                        <button @click="copyTicketCode(complaint.kode_tiket)"
                                            class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-copy mr-1"></i>Salin Kode
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <ComplaintDetailModal :show="showDetailModal" :data="selectedComplaint" @close="closeDetailModal" />

        <!-- Photo Modal -->
        <div v-if="showPhotoModal"
            class="fixed inset-0 bg-black bg-opacity-75 z-60 flex items-center justify-center p-4"
            @click="closePhotoModal">
            <div class="max-w-4xl max-h-[90vh] relative">
                <img :src="selectedPhotoUrl" alt="Foto Pengaduan"
                    class="max-w-full max-h-full object-contain rounded-lg">
                <button @click="closePhotoModal"
                    class="absolute top-2 right-2 bg-black bg-opacity-50 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ComplaintDetailModal from '@/Components/ComplaintDetailModal.vue'

// Props
const props = defineProps({
    complaints: Array,
    stats: Object
})

// Reactive data
const selectedStatus = ref('')
const showDetailModal = ref(false)
const selectedComplaint = ref(null)
const showPhotoModal = ref(false)
const selectedPhotoUrl = ref('')

// Computed
const filteredComplaints = computed(() => {
    if (!selectedStatus.value) return props.complaints
    return props.complaints.filter(complaint => complaint.status === selectedStatus.value)
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

const viewDetail = (complaint) => {
    selectedComplaint.value = complaint
    showDetailModal.value = true
}

const closeDetailModal = () => {
    showDetailModal.value = false
    selectedComplaint.value = null
}

const copyTicketCode = async (kodeTicket) => {
    try {
        await navigator.clipboard.writeText(kodeTicket)
        alert('Kode tiket berhasil disalin!')
    } catch (error) {
        console.error('Error copying text:', error)
        // Fallback untuk browser yang tidak mendukung clipboard API
        const textArea = document.createElement('textarea')
        textArea.value = kodeTicket
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        alert('Kode tiket berhasil disalin!')
    }
}

const filterComplaints = () => {
    // Method ini akan otomatis dipanggil ketika selectedStatus berubah
    // karena kita menggunakan computed property filteredComplaints
}

const openPhotoModal = (photoUrl) => {
    selectedPhotoUrl.value = photoUrl
    showPhotoModal.value = true
}

const closePhotoModal = () => {
    showPhotoModal.value = false
    selectedPhotoUrl.value = ''
}
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
