@extends('layouts.menu')

@section('header')
  <x-header></x-header>
@endsection

@section('content')
<!-- Section Informasi Mekanisme Pengusulan -->
<section id="usulan-info" class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
  <div class="container px-4 mx-auto max-w-6xl">
    <div class="mb-12 text-center" data-aos="fade-up">
      <h2 class="mb-4 text-4xl font-bold text-gray-800">Mekanisme Pengusulan RTLH</h2>
      <p class="mx-auto max-w-3xl text-lg text-gray-600">
        Sampaikan usulan Anda untuk rumah tidak layak huni melalui sistem yang transparan dan terstruktur.
      </p>
    </div>

    <div class="grid grid-cols-1 gap-8 mb-12 lg:grid-cols-2">
      <!-- Opsi 1: Lapor Petugas TFL -->
      <div class="p-8 bg-white rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="200">
        <div class="mb-6 text-center">
            <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-blue-500 rounded-full">
                <i class="text-2xl text-white fas fa-bullhorn"></i>
            </div>
            <h4 class="mb-2 text-xl font-semibold">Lapor Petugas TFL</h4>
            <p class="text-gray-600">Hubungi petugas TFL terdekat untuk bantuan pengusulan</p>
        </div>

        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-blue-500 rounded-full">
              <span class="text-sm font-bold text-white">1</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Kontak Petugas TFL</h4>
              <p class="text-sm text-gray-600">Hubungi petugas TFL di kecamatan atau kelurahan terdekat</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-blue-500 rounded-full">
              <span class="text-sm font-bold text-white">2</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Siapkan Dokumen</h4>
              <p class="text-sm text-gray-600">Siapkan KTP, KK, dan dokumen pendukung lainnya</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-blue-500 rounded-full">
              <span class="text-sm font-bold text-white">3</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Verifikasi Lapangan</h4>
              <p class="text-sm text-gray-600">Petugas akan melakukan verifikasi administrasi dan lapangan</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-blue-500 rounded-full">
              <span class="text-sm font-bold text-white">4</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Hasil Verifikasi</h4>
              <p class="text-sm text-gray-600">Anda akan diinformasikan hasil verifikasi dan tindak lanjutnya</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Opsi 2: Daftar Mandiri -->
      <div class="p-8 bg-white rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="400">
        <div class="mb-6 text-center">
            <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-green-500 rounded-full">
                <i class="text-2xl text-white fas fa-clipboard-list"></i>
            </div>
            <h4 class="mb-2 text-xl font-semibold">Daftar Mandiri</h4>
            <p class="text-gray-600">Daftar dan isi usulan secara mandiri melalui sistem online</p>
        </div>

        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-green-500 rounded-full">
              <span class="text-sm font-bold text-white">1</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Daftar Akun</h4>
              <p class="text-sm text-gray-600">Buat akun baru atau login jika sudah memiliki akun</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-green-500 rounded-full">
              <span class="text-sm font-bold text-white">2</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Isi Form Usulan</h4>
              <p class="text-sm text-gray-600">Lengkapi form usulan dengan data yang akurat dan lengkap</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-green-500 rounded-full">
              <span class="text-sm font-bold text-white">3</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Verifikasi oleh TFL</h4>
              <p class="text-sm text-gray-600">Tim TFL akan memverifikasi usulan Anda</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="flex flex-shrink-0 justify-center items-center mt-1 w-6 h-6 bg-green-500 rounded-full">
              <span class="text-sm font-bold text-white">4</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">Informasi Hasil</h4>
              <p class="text-sm text-gray-600">Anda akan mendapat notifikasi hasil verifikasi</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center" data-aos="fade-up" data-aos-delay="600">
      <div class="p-8 mx-auto max-w-2xl bg-white rounded-lg shadow-lg">
        <h3 class="mb-4 text-2xl font-bold text-gray-800">Siap Mengajukan Usulan?</h3>
        <p class="mb-6 text-gray-600">
          Pilih metode yang paling sesuai dengan kebutuhan Anda. Tim kami siap membantu proses pengusulan RTLH.
        </p>
        <div class="flex flex-col gap-4 justify-center sm:flex-row">
          <a href="{{ route('register') }}" class="px-6 py-3 font-semibold text-white bg-green-500 rounded-lg transition-colors duration-300 hover:bg-green-600">
            <i class="mr-2 fas fa-user-plus"></i>Daftar Sekarang
          </a>
          <a href="{{ route('login') }}" class="px-6 py-3 font-semibold text-white bg-blue-500 rounded-lg transition-colors duration-300 hover:bg-blue-600">
            <i class="mr-2 fas fa-sign-in-alt"></i>Login
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section Keuntungan -->
<section id="keuntungan" class="py-12 bg-white">
  <div class="container px-4 mx-auto max-w-6xl">
    <div class="mb-12 text-center" data-aos="fade-up">
      <h2 class="mb-4 text-3xl font-bold text-gray-800">Keuntungan Sistem Online</h2>
      <p class="text-lg text-gray-600">Mengapa memilih sistem pengusulan online?</p>
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
      <div class="text-center" data-aos="fade-up" data-aos-delay="200">
        <div class="flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-blue-500 rounded-full">
          <i class="text-xl text-white fas fa-clock"></i>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-gray-800">24/7 Tersedia</h3>
        <p class="text-gray-600">Akses sistem kapan saja dan di mana saja tanpa batas waktu</p>
      </div>

      <div class="text-center" data-aos="fade-up" data-aos-delay="400">
        <div class="flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-green-500 rounded-full">
          <i class="text-xl text-white fas fa-shield-alt"></i>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-gray-800">Data Aman</h3>
        <p class="text-gray-600">Data pribadi Anda terlindungi dengan sistem keamanan yang terjamin</p>
      </div>

      <div class="text-center" data-aos="fade-up" data-aos-delay="600">
        <div class="flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-purple-500 rounded-full">
          <i class="text-xl text-white fas fa-chart-line"></i>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-gray-800">Transparan</h3>
        <p class="text-gray-600">Pantau status usulan Anda secara real-time dengan mudah</p>
      </div>
    </div>
  </div>
</section>
@endsection

@section('footer')
@include('components.frontend.footer')
@endsection

@push('after-script')
<script>
  AOS.init();
</script>
@endpush
