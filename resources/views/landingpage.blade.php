@extends('layouts.app')

@push('after-style')
<link rel="stylesheet" href="{{ asset('css/aos.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/glightbox.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"/>
@endpush

@section('header')
  <x-header></x-header>
@endsection

@section('content')
<!-- ======= Hero Section ======= -->
<section id="hero" class="w-screen h-[90vh] sm:h-screen sm:mb-4 mt-8">
  <button class="scrollToTopBtn"><i class="fas fa-chevron-up"></i></button>
    <div class="container px-4 mx-auto max-w-6xl">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex order-2 justify-center pt-2 mb-10 col-lg-5 pt-lg-0 sm:order-1 md:order-1" id="text-hero">
            <div data-aos="zoom-out" data-aos-delay="400" class="p-3 mt-1 text-center lg:text-left text-hero">
              <h1 class="py-3 text-4xl font-semibold font-lato">SIBEDAH SERU</h1>
              <h3 class="text-2xl font-lato">SISTEM INFORMASI</h3>
              <h3 class="text-2xl font-lato">BEDAH SERIBU RUMAH</h3>
              <h3 class="text-2xl font-lato">KABUPATEN BALANGAN</h3>
              <div class="mb-10 text-center lg:text-left">
                <a href="#cta" class="box-border appearance-none bg-[#f8f550] border-2 border-[#f8f550] text-[#292928] cursor-pointer self-center text-base font-semibold leading-none text-center uppercase font-montserrat hover:bg-[#ffc802] hover:text-[#1a1919] focus:outline-nonefirst scrollto rounded-full mt-3 px-4 py-3 inline-block">JELAJAHI SEKARANG</a>
              </div>
            </div>
        </div>
        <div class="flex order-1 items-center col-12 col-lg-7 sm:order-2 md:order-2" id="img-hero">
           <img src="{{ asset('img/hero.webp') }}" class="img-fluid animated hero-img" alt="bedah-seribu-rumah" data-aos="zoom-out" data-aos-delay="600">
        </div>
      </div>
    </div>
</section><!-- End Hero -->


<section id="cta">
  <div id="particles-js"></div>
    <div class="container text-center" data-aos="fade-up">
      <div class="section-header">
        <h3 class="mb-2 text-base font-normal text-white md:mb-4 md:text-2xl md:font-semibold">PETA DIGITAL</h3>
      </div>
      <div class="flex justify-center mb-4 cta-img" data-tilt>
          <img src="{{  asset('img/landingpage/macbook.png') }}" class="w-1/4" alt="peta-digital">
      </div>
      <div class="text-center">
        <a href="{{ route('map') }}" class="hover:font-semibold rounded-full my-3 pt-3 d-inline-block bg-green-500 text-white py-2 px-4 hover:bg-green-600 transition-shadow duration-300 ease-in-out hover:shadow-[0_0_2px_1px_#1a1919]"><i class="fas fa-map"></i>&nbsp;Buka Peta</a>
      </div>
    </div>
</section>

<section id="facts" class="py-8">
  <div class="container px-4 mx-auto text-center" data-aos="fade-up">
    <header class="mb-8 text-black" data-aos="fade-up">
      <em><h3 class="text-2xl font-bold">FITUR-FITUR</h3></em>
    </header>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
      <!-- Box 1 -->
      <div class="flex flex-col items-center p-4 text-center" data-aos="fade-up" data-aos-delay="200">
        <img src="{{ asset('img/landingpage/spasial.png') }}" alt="spasial"
             class="h-auto transition-transform duration-300 transform hover:scale-110" />
        <h4 class="mt-4 text-xl font-semibold">FITUR 1</h4>
        <p class="mt-2 text-gray-600">Lorem ipsum dolor sit amet consectetur</p>
      </div>

      <!-- Box 2 -->
      <div class="flex flex-col items-center p-4 text-center" data-aos="fade-up" data-aos-delay="200">
        <img src="{{ asset('img/landingpage/data-inventory.png') }}" alt="inventory"
             class="h-auto transition-transform duration-300 transform hover:scale-110" />
        <h4 class="mt-4 text-xl font-semibold">FITUR 2</h4>
        <p class="mt-2 text-gray-600">Lorem ipsum dolor sit amet consectetur</p>
      </div>

      <!-- Box 3 -->
      <div class="flex flex-col items-center p-4 text-center" data-aos="fade-up" data-aos-delay="200">
        <img src="{{ asset('img/landingpage/dashboard.png') }}" alt="visualize-data"
             class="h-auto transition-transform duration-300 transform hover:scale-110" />
        <h4 class="mt-4 text-xl font-semibold">FITUR 3</h4>
        <p class="mt-2 text-gray-600">Lorem ipsum dolor sit amet consectetur</p>
      </div>

      <!-- Box 4 -->
      <div class="flex flex-col items-center p-4 text-center" data-aos="fade-up" data-aos-delay="300">
        <a href="#">
          <img src="{{ asset('img/landingpage/document.png') }}" alt="download-data"
               class="h-auto transition-transform duration-300 transform hover:scale-110" />
        </a>
        <h4 class="mt-4 text-xl font-semibold">FITUR 4</h4>
        <p class="mt-2 text-gray-600">Lorem ipsum dolor sit amet consectetur</p>
      </div>

      <!-- Box 5 -->
      <div class="flex flex-col items-center p-4 text-center" data-aos="fade-up" data-aos-delay="200">
        <img src="{{ asset('img/landingpage/route.png') }}" alt="route"
             class="h-auto transition-transform duration-300 transform hover:scale-110" />
        <h4 class="mt-4 text-xl font-semibold">FITUR 5</h4>
        <p class="mt-2 text-gray-600">Lorem ipsum dolor sit amet consectetur</p>
      </div>

      <!-- Box 6 -->
      <div class="flex flex-col items-center p-4 text-center" data-aos="fade-up" data-aos-delay="200">
        <img src="{{ asset('img/landingpage/dynamic.png') }}" alt="route"
             class="h-auto transition-transform duration-300 transform hover:scale-110" />
        <h4 class="mt-4 text-xl font-semibold">FITUR 6</h4>
        <p class="mt-2 text-gray-600">Lorem ipsum dolor sit amet consectetur</p>
      </div>
    </div>
  </div>
</section>



<!-- Section Pengaduan -->
<section id="pengaduan" class="py-12 bg-gray-50">
  <div class="container px-4 mx-auto" data-aos="fade-up">
    <div class="mb-8 text-center">
      <h3 class="mb-4 text-3xl font-bold text-gray-800">LAYANAN PENGADUAN</h3>
      <p class="mx-auto max-w-2xl text-gray-600">
        Sampaikan keluhan, saran, atau aspirasi Anda terkait layanan aplikasi ini.
        Kami berkomitmen untuk merespons setiap pengaduan dengan baik.
      </p>
    </div>

    <div class="grid grid-cols-1 gap-8 mx-auto max-w-4xl md:grid-cols-2">
      <!-- Form Pengaduan -->
      <div class="p-6 bg-white rounded-lg shadow-lg">
        <div class="mb-6 text-center">
          <i class="mb-4 text-4xl text-blue-600 fas fa-comment-alt"></i>
          <h4 class="mb-2 text-xl font-semibold">Buat Pengaduan</h4>
          <p class="text-gray-600">Sampaikan keluhan atau saran Anda</p>
        </div>
        <button onclick="openComplaintModal()" class="px-6 py-3 w-full text-white bg-blue-600 rounded-lg transition-colors hover:bg-blue-700">
          <i class="mr-2 fas fa-plus"></i>Buat Pengaduan Baru
        </button>
      </div>

      <!-- Tracking Pengaduan -->
      <div class="p-6 bg-white rounded-lg shadow-lg">
        <div class="mb-6 text-center">
          <i class="mb-4 text-4xl text-green-600 fas fa-search"></i>
          <h4 class="mb-2 text-xl font-semibold">Lacak Pengaduan</h4>
          <p class="text-gray-600">Cek status pengaduan dengan kode tiket</p>
        </div>

        <div class="space-y-4">
          <input type="text" id="trackingCode" placeholder="Masukkan kode tiket (contoh: ADU-ABC123-0702)"
                 class="px-4 py-3 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
          <button onclick="trackComplaint()" class="px-6 py-3 w-full text-white bg-green-600 rounded-lg transition-colors hover:bg-green-700">
            <i class="mr-2 fas fa-search"></i>Lacak Pengaduan
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="gallery">
  <div class="container mb-4 text-center" data-aos="fade-up">
      <header class="text-black facts-header wow fadeInUp">
        <em><h3>BEST PRACTICES</h3></em>
      </header>


  <div class="overflow-x-hidden px-4" id="carousel">
    <div class="container mx-auto"></div>
    <div class="relative flex-row -mx-4 d-flex owl-carousel owl-theme owl-loaded">
      <!-- START: GALLERY ROW 1 -->
      {{-- @foreach ($recommendations as $data)
      <div class="relative px-2 card group">
        <div
          class="overflow-hidden relative rounded-xl card-shadow" style="width: 300px; height: 250px">
          <div
            class="absolute items-center bg-black opacity-0 transition duration-200 group-hover:opacity-100 d-flex justify-content-center w-100 h-100 bg-opacity-35">
            <div
              class="items-center w-16 h-16 text-black bg-white rounded-full d-flex justify-content-center">
              <i class="fa fa-eye" aria-hidden="true"></i>
            </div>
          </div>
          <img
            src="{{ Storage::url($data->url) }}"
            alt=""
            class="object-cover object-center w-100 h-100"
          />
        </div>
        <a href="{{ url('/details') }}/{{ $data->build_id }}" class="stretched-link">
          <!-- fake children -->
        </a>
      </div>
      @endforeach --}}
      <!-- END: GALLERY ROW 1 -->

    </div>
    <!-- </div> -->
  </div>
  </div>
</section>
<!-- END: JUST ARRIVED -->

<!-- Modal Form Pengaduan -->
<div id="complaintModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50">
  <div class="flex justify-center items-center p-4 min-h-screen">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-semibold">Form Pengaduan</h3>
          <button onclick="closeComplaintModal()" class="text-gray-400 hover:text-gray-600">
            <i class="text-xl fas fa-times"></i>
          </button>
        </div>

        <!-- Form -->
        <form id="complaintForm" class="space-y-4">
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Nama *</label>
            <input type="text" name="name" required class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="complaintEmail" class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" onblur="validateEmail()">
            <div id="emailFeedback" class="hidden mt-1 text-sm"></div>
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Nomor HP</label>
            <input type="tel" name="contact" class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Kecamatan *</label>
            <select name="district_id" id="complaintDistrict" required class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Pilih Kecamatan</option>
            </select>
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Kelurahan/Desa *</label>
            <select name="village_id" id="complaintVillage" required class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Pilih Kelurahan/Desa</option>
            </select>
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Isi Pengaduan *</label>
            <textarea name="complain" rows="4" required placeholder="Jelaskan pengaduan, saran, atau aspirasi Anda..." class="px-3 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>

          <!-- Captcha -->
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Captcha *</label>
            <div class="flex items-center space-x-3">
              <span id="captchaQuestion" class="px-3 py-2 text-lg font-semibold text-blue-600 bg-blue-50 rounded"></span>
              <input type="number" name="captcha" required placeholder="Hasil" class="flex-1 px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <button type="button" onclick="generateCaptcha()" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-refresh"></i>
              </button>
            </div>
          </div>

          <div class="flex pt-4 space-x-3">
            <button type="submit" class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-md transition-colors hover:bg-blue-700">
              <i class="mr-2 fas fa-paper-plane"></i>Kirim Pengaduan
            </button>
            <button type="button" onclick="closeComplaintModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-300 rounded-md transition-colors hover:bg-gray-400">
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tracking Result -->
<div id="trackingModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50">
  <div class="flex justify-center items-center p-4 min-h-screen">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-semibold">Status Pengaduan</h3>
          <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600">
            <i class="text-xl fas fa-times"></i>
          </button>
        </div>
        <div id="trackingResult"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('faq')

<x-faq></x-faq>

@endsection


@section('footer')
@include('components.frontend.footer')
@endsection

@push('after-script')
    <script src="{{ asset('js/particles.js') }}"></script>
    <script src="{{ asset('js/particle-app.js') }}"></script>
    <script src="{{ asset('js/aos.js') }}"></script>
    <script defer src="{{ asset('js/glightbox.min.js') }}"></script>
    <script defer src="{{ asset('js/tilt.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.4.1/flowbite.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        <script>
          AOS.init();
        $('.owl-carousel').owlCarousel({
            nav:true,
            loop: false,
            autoWidth: true,
            lazyLoad: true,
            autoplay: true,
            autoplayHoverPause: true,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:3
                },
                1000:{
                    items:4
                }
            }
        })

        let target = document.querySelector("footer");
        let scrollToTopBtn = document.querySelector(".scrollToTopBtn");
        let rootElement = document.documentElement;

        // Modifikasi callback untuk menampilkan tombol berdasarkan scroll position
        function callback(entries, observer) {
          if (window.scrollY > 300) { // Tampilkan tombol setelah scroll 300px
            scrollToTopBtn.classList.add("showBtn");
          } else {
            scrollToTopBtn.classList.remove("showBtn");
          }
        }

        function scrollToTop() {
          rootElement.scrollTo({
            top: 0,
            behavior: "smooth"
          });
        }

                // Gunakan window scroll event sebagai gantinya
        window.addEventListener("scroll", callback);
        scrollToTopBtn.addEventListener("click", scrollToTop);

        // Complaint System Functions
        let captchaAnswer = 0;

        // Load districts on page load
        loadDistricts();

        function loadDistricts() {
            fetch('/api/kecamatan')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('complaintDistrict');
                    data.data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        select.appendChild(option);
                    });
                });
        }

        function openComplaintModal() {
            document.getElementById('complaintModal').classList.remove('hidden');
            generateCaptcha();
        }

        function closeComplaintModal() {
            document.getElementById('complaintModal').classList.add('hidden');
            document.getElementById('complaintForm').reset();
        }

        // Generate captcha
        function generateCaptcha() {
            fetch('/aduan/captcha')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('captchaQuestion').textContent = data.question;
                })
                .catch(error => {
                    console.error('Error generating captcha:', error);
                });
        }

        // Validate email function
        function validateEmail() {
            const emailInput = document.getElementById('complaintEmail');
            const emailFeedback = document.getElementById('emailFeedback');
            const email = emailInput.value.trim();

            // Clear previous feedback
            emailFeedback.className = 'mt-1 text-sm hidden';
            emailFeedback.textContent = '';

            // If email is empty, don't validate
            if (!email) {
                return;
            }

            // Basic email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailFeedback.className = 'mt-1 text-sm text-red-600';
                emailFeedback.textContent = 'Format email tidak valid.';
                return;
            }

            // Show loading state
            emailFeedback.className = 'mt-1 text-sm text-blue-600';
            emailFeedback.textContent = 'Memvalidasi email...';

            // Send validation request
            const formData = new FormData();
            formData.append('email', email);

            fetch('/aduan/validate-email', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    emailFeedback.className = 'mt-1 text-sm text-green-600';
                    emailFeedback.textContent = data.message;
                } else {
                    emailFeedback.className = 'mt-1 text-sm text-red-600';
                    emailFeedback.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error validating email:', error);
                emailFeedback.className = 'mt-1 text-sm text-red-600';
                emailFeedback.textContent = 'Terjadi kesalahan saat memvalidasi email.';
            });
        }

        // District change handler
        document.getElementById('complaintDistrict').addEventListener('change', function() {
            const districtId = this.value;
            const villageSelect = document.getElementById('complaintVillage');

            // Clear villages
            villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';

            if (districtId) {
                fetch(`/aduan/villages/${districtId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(village => {
                            const option = document.createElement('option');
                            option.value = village.id;
                            option.textContent = village.name;
                            villageSelect.appendChild(option);
                        });
                    });
            }
        });

        // Submit complaint form
        document.getElementById('complaintForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/aduan', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Pengaduan berhasil dikirim!\n\nKode Tiket: ${data.data.kode_tiket}\n\n${data.data.pesan}`);
                    closeComplaintModal();
                } else {
                    alert('Gagal mengirim pengaduan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim pengaduan.');
            });
        });

        // Track complaint function
        function trackComplaint() {
            const code = document.getElementById('trackingCode').value.trim();

            if (!code) {
                alert('Masukkan kode tiket terlebih dahulu!');
                return;
            }

            const formData = new FormData();
            formData.append('kode_tiket', code);

            fetch('/aduan/track', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showTrackingResult(data.data);
                } else {
                    alert('Kode tiket tidak ditemukan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat melacak pengaduan.');
            });
        }

        function showTrackingResult(data) {
            let html = `
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-blue-800">Informasi Pengaduan</h4>
                        <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                            <div><strong>Kode Tiket:</strong> ${data.kode_tiket}</div>
                            <div><strong>Nama:</strong> ${data.nama}</div>
                            <div><strong>Tanggal:</strong> ${data.tanggal}</div>
                            <div><strong>Status:</strong> <span class="font-semibold">${data.status}</span></div>
                            <div><strong>Lokasi:</strong> ${data.kelurahan}, ${data.kecamatan}</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-semibold">Riwayat Percakapan:</h4>

                        <div class="p-3 bg-gray-50 rounded border-l-4 border-blue-500">
                            <div class="font-medium text-blue-600">Pengaduan Awal:</div>
                            <p class="mt-1 text-sm">${data.pengaduan}</p>
                        </div>
            `;

            if (data.respon) {
                html += `
                    <div class="p-3 bg-green-50 rounded border-l-4 border-green-500">
                        <div class="font-medium text-green-600">Tanggapan Admin:</div>
                        <p class="mt-1 text-sm">${data.respon}</p>
                    </div>
                `;
            }

            if (data.pengaduan2) {
                html += `
                    <div class="p-3 bg-gray-50 rounded border-l-4 border-blue-500">
                        <div class="font-medium text-blue-600">Pengaduan Lanjutan:</div>
                        <p class="mt-1 text-sm">${data.pengaduan2}</p>
                    </div>
                `;
            }

            if (data.respon2) {
                html += `
                    <div class="p-3 bg-green-50 rounded border-l-4 border-green-500">
                        <div class="font-medium text-green-600">Tanggapan Admin:</div>
                        <p class="mt-1 text-sm">${data.respon2}</p>
                    </div>
                `;
            }

            if (data.pengaduan3) {
                html += `
                    <div class="p-3 bg-gray-50 rounded border-l-4 border-blue-500">
                        <div class="font-medium text-blue-600">Pengaduan Lanjutan:</div>
                        <p class="mt-1 text-sm">${data.pengaduan3}</p>
                    </div>
                `;
            }

            if (data.respon3) {
                html += `
                    <div class="p-3 bg-green-50 rounded border-l-4 border-green-500">
                        <div class="font-medium text-green-600">Tanggapan Admin (Final):</div>
                        <p class="mt-1 text-sm">${data.respon3}</p>
                    </div>
                `;
            }

            html += '</div>';

            if (data.evaluasi) {
                html += `
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h4 class="font-semibold text-yellow-800">Evaluasi Layanan</h4>
                        <div class="flex items-center mt-2">
                `;
                for (let i = 1; i <= 4; i++) {
                    html += `<i class="fas fa-star ${i <= data.evaluasi ? 'text-yellow-500' : 'text-gray-300'}"></i>`;
                }
                html += `
                        </div>
                        <p class="mt-1 text-sm text-yellow-700">
                            ${data.evaluasi == 1 ? 'Tidak ada tanggapan' :
                              data.evaluasi == 2 ? 'Ada, tapi tidak berfungsi' :
                              data.evaluasi == 3 ? 'Berfungsi tapi kurang maksimal' :
                              'Dikelola dengan baik'}
                        </p>
                    </div>
                `;
            }

            html += '</div>';

            document.getElementById('trackingResult').innerHTML = html;
            document.getElementById('trackingModal').classList.remove('hidden');
        }

        function closeTrackingModal() {
            document.getElementById('trackingModal').classList.add('hidden');
        }

        </script>
@endpush

