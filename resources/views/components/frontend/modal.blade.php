<!-- Modal Contact-->
<div id="contact" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="contactLabel" aria-hidden="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div class="bg-blue-200 px-4 py-3 sm:px-6">
        <h4 class="text-lg font-medium text-gray-900" id="contactLabel">{{ trans('frontend.contact') }}</h4>
        <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500" data-dismiss="modal" aria-label="Close">
          <span class="sr-only">Close</span>
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div>
          <h5 class="font-bold flex items-center">
            <img src="{{ asset('img/logobalangan.webp') }}" alt="logo-pemko" class="float-left mr-2 h-8">
            Dinas Pekerjaan Umum Perumahan Rakyat dan Permukiman Kabupaten Balangan
          </h5>
          <p class="text-sm mt-2">Jalan Brigjen H. Hasan Basri No. 82 Kec. Banjarmasin Utara, Kota Banjarmasin Kalimantan Selatan - 70124</p>
          <p class="mt-2">
            <strong>E-mail : </strong><a href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=dinaspupr.banjarmasin@gmail.com" target="_blank" class="text-blue-600 hover:underline">dinaspupr.banjarmasin@gmail.com</a><br>
            <strong>Phone : </strong> 0511 3300197<br>
            <strong>Fax : </strong> 0511 3300097
          </p>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Disclaimer-->
<div id="disclaimer" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="disclaimerLabel" aria-hidden="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div class="bg-yellow-200 px-4 py-3 sm:px-6">
        <h5 class="text-lg font-medium text-gray-900" id="disclaimerLabel">DISCLAIMER</h5>
      </div>
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-96 overflow-y-auto">
        <ol class="list-decimal list-inside space-y-2">
          <li>{{ trans('frontend.disclaimer.discl1') }}</li>
          <li>{{ trans('frontend.disclaimer.discl2') }}</li>
          <li>{{ trans('frontend.disclaimer.discl3') }}</li>
          <li>{{ trans('frontend.disclaimer.discl4') }}</li>
          <li>{{ trans('frontend.disclaimer.discl5') }}</li>
          <li>{{ trans('frontend.disclaimer.discl6') }}</li>
          <li>{{ trans('frontend.disclaimer.discl7') }}</li>
          <li>{{ trans('frontend.disclaimer.discl8') }}</li>
        </ol>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <div class="flex items-center justify-center mb-4 sm:mb-0 sm:mr-4">
          <input type="checkbox" id="terms" value="1" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out" />
          <label for="terms" class="ml-2 block text-sm text-gray-900">
            {{ trans('frontend.disclaimer.concent') }}
          </label>
        </div>
        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" id="closeBtn" data-dismiss="modal" disabled>
          Close
        </button>
      </div>
    </div>
  </div>
</div>