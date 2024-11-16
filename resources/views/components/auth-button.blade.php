@props(['mobile' => false])

<div class="{{ $mobile ? 'mt-4 space-y-2 ml-4' : 'flex items-center space-x-4' }} ml-auto flex md:order-2 md:space-x-0 rtl:space-x-reverse">
    @auth
        <button onclick="window.location='{{ route('home') }}'" class="{{ $mobile ? 'block w-full' : '' }} bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-200">
            Dashboard
        </button>
    @else
        <button onclick="window.location='{{ route('login') }}'" class="{{ $mobile ? 'block w-full' : '' }} bg-green-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors duration-200">
            Login
        </button>
    @endauth
</div>