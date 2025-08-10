<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Usulan RTLH</h3>
        </div>

        <div class="card-body">
            <form @submit.prevent="submitForm" enctype="multipart/form-data" id="usulan-form">
                <!-- Identitas Diri -->
                <div class="mb-3 card">
                    <div class="text-white card-header bg-primary">
                        <h5 class="mb-0"><i class="mr-2 fas fa-user"></i>Identitas Diri</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama" class="required">Nama Lengkap</label>
                                    <input type="text" class="form-control" :class="{ 'is-invalid': errors.nama }"
                                        id="nama" v-model="form.nama" required>
                                    <div v-if="errors.nama" class="invalid-feedback">{{ errors.nama }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nik" class="required">NIK (16 digit)</label>
                                    <input type="text" class="form-control"
                                        :class="{ 'is-invalid': errors.nik, 'is-valid': nikValid }" id="nik"
                                        v-model="form.nik" maxlength="16" pattern="[0-9]{16}" required
                                        @blur="validateNIK">
                                    <div id="nik-feedback" class="invalid-feedback" v-if="nikFeedback">{{ nikFeedback }}
                                    </div>
                                    <div v-if="errors.nik" class="invalid-feedback">{{ errors.nik }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_kk" class="required">Nomor KK (16 digit)</label>
                                    <input type="text" class="form-control"
                                        :class="{ 'is-invalid': errors.nomor_kk, 'is-valid': kkValid }" id="nomor_kk"
                                        v-model="form.nomor_kk" maxlength="16" pattern="[0-9]{16}" required
                                        @blur="validateKK">
                                    <div id="kk-feedback" class="invalid-feedback" v-if="kkFeedback">{{ kkFeedback }}
                                    </div>
                                    <div v-if="errors.nomor_kk" class="invalid-feedback">{{ errors.nomor_kk }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_hp">Nomor HP</label>
                                    <input type="tel" class="form-control" :class="{ 'is-invalid': errors.nomor_hp }"
                                        id="nomor_hp" v-model="form.nomor_hp" placeholder="628xxxxxxxxxx">
                                    <div v-if="errors.nomor_hp" class="invalid-feedback">{{ errors.nomor_hp }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lokasi -->
                <div class="mb-3 card">
                    <div class="text-white card-header bg-success">
                        <h5 class="mb-0"><i class="mr-2 fas fa-map-marker-alt"></i>Lokasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="district_id" class="required">Kecamatan</label>
                                    <select class="form-control" :class="{ 'is-invalid': errors.district_id }"
                                        id="district_id" v-model="form.district_id" required @change="loadVillages">
                                        <option value="">Pilih Kecamatan</option>
                                        <option v-for="district in districts" :key="district.id" :value="district.id">
                                            {{ district.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.district_id" class="invalid-feedback">{{ errors.district_id }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="village_id" class="required">Kelurahan/Desa</label>
                                    <select class="form-control" :class="{ 'is-invalid': errors.village_id }"
                                        id="village_id" v-model="form.village_id" required>
                                        <option value="">Pilih Kelurahan/Desa</option>
                                        <option v-for="village in villages" :key="village.id" :value="village.id">
                                            {{ village.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.village_id" class="invalid-feedback">{{ errors.village_id }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat_lengkap" class="required">Alamat Lengkap</label>
                            <textarea class="form-control" :class="{ 'is-invalid': errors.alamat_lengkap }"
                                id="alamat_lengkap" v-model="form.alamat_lengkap" rows="3" required
                                placeholder="RT/RW, Nama Jalan, Nomor Rumah"></textarea>
                            <div v-if="errors.alamat_lengkap" class="invalid-feedback">{{ errors.alamat_lengkap }}</div>
                        </div>
                    </div>
                </div>

                <!-- Jenis Usulan -->
                <div class="mb-3 card">
                    <div class="text-white card-header bg-warning">
                        <h5 class="mb-0"><i class="mr-2 fas fa-home"></i>Jenis Usulan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="required">Pilih Jenis Usulan</label>
                            <div class="form-check">
                                <input class="form-check-input" :class="{ 'is-invalid': errors.jenis_usulan }"
                                    type="radio" name="jenis_usulan" id="rtlh" value="RTLH" v-model="form.jenis_usulan"
                                    required>
                                <label class="form-check-label" for="rtlh">
                                    <strong>RTLH (Rumah Tidak Layak Huni)</strong><br>
                                    <small class="text-muted">Untuk rumah yang tidak memenuhi standar kelayakan
                                        huni</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" :class="{ 'is-invalid': errors.jenis_usulan }"
                                    type="radio" name="jenis_usulan" id="bencana" value="Rumah Korban Bencana"
                                    v-model="form.jenis_usulan" required>
                                <label class="form-check-label" for="bencana">
                                    <strong>Rumah Korban Bencana</strong><br>
                                    <small class="text-muted">Untuk rumah yang rusak akibat bencana alam</small>
                                </label>
                            </div>
                            <div v-if="errors.jenis_usulan" class="invalid-feedback d-block">{{ errors.jenis_usulan }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foto Rumah -->
                <div class="mb-3 card">
                    <div class="text-white card-header bg-info">
                        <h5 class="mb-0"><i class="mr-2 fas fa-camera"></i>Foto Rumah (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="foto_rumah">Upload Foto Rumah</label>
                            <input type="file" class="form-control" :class="{ 'is-invalid': errors.foto_rumah }"
                                id="foto_rumah" @change="handleFileUpload" accept="image/*">
                            <small class="form-text text-muted">
                                Format: JPG, PNG, WEBP. Maksimal 2MB. Foto akan membantu proses verifikasi.
                            </small>
                            <div v-if="errors.foto_rumah" class="invalid-feedback">{{ errors.foto_rumah }}</div>
                        </div>
                        <div id="foto-preview" class="mt-3" v-if="fotoPreview" style="display: block;">
                            <img :src="fotoPreview" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    </div>
                </div>

                <!-- Koordinat -->
                <div class="mb-3 card">
                    <div class="text-white card-header bg-secondary">
                        <h5 class="mb-0"><i class="mr-2 fas fa-map"></i>Koordinat Lokasi (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="mr-2 fas fa-info-circle"></i>
                            <strong>Petunjuk:</strong> Geser marker pada peta untuk menentukan koordinat lokasi rumah
                            Anda.
                            Koordinat akan membantu tim verifikasi menemukan lokasi dengan tepat.
                        </div>

                        <div id="map" style="height: 400px; width: 100%;" class="mb-3"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" class="form-control" :class="{ 'is-invalid': errors.latitude }"
                                        id="latitude" v-model="form.latitude" readonly>
                                    <div v-if="errors.latitude" class="invalid-feedback">{{ errors.latitude }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="text" class="form-control" :class="{ 'is-invalid': errors.longitude }"
                                        id="longitude" v-model="form.longitude" readonly>
                                    <div v-if="errors.longitude" class="invalid-feedback">{{ errors.longitude }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Konfirmasi -->
                <div class="mb-3 card">
                    <div class="text-white card-header bg-dark">
                        <h5 class="mb-0"><i class="mr-2 fas fa-check-circle"></i>Konfirmasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="konfirmasi" v-model="form.konfirmasi"
                                required>
                            <label class="form-check-label" for="konfirmasi">
                                Saya menyatakan bahwa data yang saya berikan adalah benar dan dapat
                                dipertanggungjawabkan.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <i class="mr-2 fas fa-spinner fa-spin" v-if="isSubmitting"></i>
                        <i class="mr-2 fas fa-paper-plane" v-else></i>
                        {{ isSubmitting ? 'Mengirim...' : 'Kirim Usulan' }}
                    </button>
                    <a :href="route('usulan.proposals.index')" class="btn btn-secondary">
                        <i class="mr-2 fas fa-arrow-left"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps({
    districts: {
        type: Array,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

// Reactive data
const form = reactive({
    nama: '',
    nik: '',
    nomor_kk: '',
    nomor_hp: '',
    district_id: '',
    village_id: '',
    alamat_lengkap: '',
    jenis_usulan: '',
    foto_rumah: null,
    latitude: '',
    longitude: '',
    konfirmasi: false
})

const villages = ref([])
const isSubmitting = ref(false)
const fotoPreview = ref(null)
const nikValid = ref(false)
const kkValid = ref(false)
const nikFeedback = ref('')
const kkFeedback = ref('')

// Methods
const loadVillages = async () => {
    if (!form.district_id) {
        villages.value = []
        return
    }

    try {
        const response = await fetch(`/usulan/villages-by-district?district_id=${form.district_id}`)
        const data = await response.json()
        villages.value = data
    } catch (error) {
        console.error('Error loading villages:', error)
    }
}

const validateNIK = async () => {
    if (form.nik.length !== 16) return

    try {
        const response = await fetch('/usulan/validate-nik', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ nik: form.nik })
        })
        const data = await response.json()

        nikValid.value = data.valid
        nikFeedback.value = data.message
    } catch (error) {
        console.error('Error validating NIK:', error)
    }
}

const validateKK = async () => {
    if (form.nomor_kk.length !== 16) return

    try {
        const response = await fetch('/usulan/validate-kk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ nomor_kk: form.nomor_kk })
        })
        const data = await response.json()

        kkValid.value = data.valid
        kkFeedback.value = data.message
    } catch (error) {
        console.error('Error validating KK:', error)
    }
}

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.foto_rumah = file
        const reader = new FileReader()
        reader.onload = (e) => {
            fotoPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    } else {
        fotoPreview.value = null
    }
}

const submitForm = async () => {
    if (!form.konfirmasi) {
        alert('Anda harus menyetujui pernyataan konfirmasi terlebih dahulu.')
        return
    }

    isSubmitting.value = true

    const formData = new FormData()
    Object.keys(form).forEach(key => {
        if (form[key] !== null && form[key] !== '') {
            formData.append(key, form[key])
        }
    })

    try {
        await router.post('/usulan/usulan', formData, {
            onSuccess: () => {
                // Success handled by Inertia
            },
            onError: (errors) => {
                console.error('Form errors:', errors)
            }
        })
    } catch (error) {
        console.error('Error submitting form:', error)
    } finally {
        isSubmitting.value = false
    }
}

// Initialize map
onMounted(() => {
    // Initialize Leaflet map here
    // This would require Leaflet to be available globally
    if (typeof L !== 'undefined') {
        const map = L.map('map').setView([-2.123456, 115.123456], 10)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map)

        const marker = L.marker([-2.123456, 115.123456], { draggable: true }).addTo(map)

        // Update coordinates when marker is dragged
        marker.on('dragend', function (event) {
            const position = event.target.getLatLng()
            form.latitude = position.lat.toFixed(8)
            form.longitude = position.lng.toFixed(8)
        })
    }
})
</script>

<style scoped>
/* Additional styling if needed */
.card {
    margin-bottom: 1rem;
}

.form-check {
    margin-bottom: 1rem;
}

.btn {
    margin-right: 0.5rem;
}

/* Map container styling */
#map {
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
}
</style>
