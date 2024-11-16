<header class="h-full w-full bg-white">
  <nav class="bg-white border-gray-200 dark:bg-gray-900">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
        <img src="{{ asset('img/logobalangan-nav.webp') }}" alt="logo sibedah-seru" />
        <span class="self-center font-semibold italic whitespace-nowrap dark:text-white">SIBEDAH SERU</span>
      </a>

      <div class="flex items-center md:order-2 space-x-3 rtl:space-x-reverse">
        <div class="hidden md:block">
          <x-auth-button class="focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800" />
        </div>

        <button data-collapse-toggle="navbar-cta" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-cta" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
          </svg>
        </button>
      </div>

      <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-cta">
        <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
          <x-nav-link href="/" :active="request()->is('/')">Beranda</x-nav-link>
          <x-nav-link href="/map" :active="request()->is('map')">Peta Digital</x-nav-link>
          <x-nav-link href="/download" :active="request()->is('gallery')">Download</x-nav-link>
          <x-nav-link href="https://forms.gle/uEnQ5fygU9p1YUk59" :active="request()->is('complaint')">Smart RTLH</x-nav-link>
          <x-nav-link href="/bedah" :active="request()->is('bedah')">Bedah Rumah</x-nav-link>
          <x-nav-link href="/guide" :active="request()->is('guide')">Panduan</x-nav-link>

          <li class="md:hidden">
            <x-auth-button class="w-full mt-4 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800" />
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

  