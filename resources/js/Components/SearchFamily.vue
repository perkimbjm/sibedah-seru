<template>  
  <div class="max-w-3xl mx-auto p-12">  
    <div class="text-center mb-8">  
      <h2 class="text-3xl font-bold text-gray-800 mb-2">PENCARIAN DATA RTLH</h2>
      <div class="relative mt-3">
          <div class="absolute inset-x-1/2 w-28 h-px bg-gray-300 transform -translate-x-1/2 bottom-0.5"></div>
          <div class="absolute inset-x-1/2 w-9 h-1 bg-green-500 transform -translate-x-1/2 bottom-0"></div>
      </div>
      <p class="mt-6 p-6 text-gray-600">Masukkan NIK atau Nomor Kartu Keluarga untuk memeriksa status apakah sudah terdaftar di basis data RTLH</p>  
    </div>  
  
    <form @submit.prevent="searchRTLH" class="mb-8">  
      <div class="flex items-center">  
        <div class="relative flex-grow">  
          <input   
            v-model="searchInput"   
            type="text"   
            placeholder="Masukkan NIK / Nomor KK"   
            class="w-full px-6 py-4 text-md border-2 border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out"  
            required  
          >  
        </div>  
        <button   
          type="submit"   
          class="px-8 py-4 bg-blue-600 text-white font-semibold text-lg rounded-r-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out"  
        >  
          Cari  
        </button>  
      </div>  
    </form>  
  
    <!-- Modal -->  
    <Teleport to="body">  
      <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto overflow-x-hidden flex justify-start items-center sm:justify-center ml-5" aria-labelledby="modal-title" role="dialog" aria-modal="true">  
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>  
  
        <div class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 align-middle sm:max-w-lg sm:w-full">  
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">  
            <div class="flex justify-between items-start">  
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">  
                Informasi RTLH  
              </h3>  
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 focus:outline-none text-xl">  
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
                    <p class="mt-1 text-sm font-semibold" :class="rtlhData.is_renov === 'sudah dibedah rumah' ? 'text-green-600' : 'text-yellow-600'">  
                      {{ rtlhData.is_renov }}  
                    </p>  
                  </div>  
                </div>  
              </template>  
              <template v-else>  
                <p class="text-lg text-red-500">  
                  Data keluarga yang Anda masukkan belum ada di database rumah tidak layak huni. Silahkan ajukan di Dinas atau periksa kembali input Anda  
                </p>  
              </template>  
            </div>  
          </div>  
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">  
            <button @click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">  
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
  
  <style scoped>

  </style>