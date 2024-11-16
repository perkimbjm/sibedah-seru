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
<section id="hero">
  <button class="scrollToTopBtn"><i class="fas fa-chevron-up"></i></button>
    <div class="container">
      <div class="row justify-between">
        <div class="col-lg-5 pt-2 mb-10 pt-lg-0 order-2 order-lg-1 flex justify-center">
            <div data-aos="zoom-out" data-aos-delay="400" class="text-center lg:text-left mt-1 p-3 text-hero">
              <h1 class="text-4xl font-semibold font-lato py-3">SIBEDAH SERU</h1>
              <h3 class="text-2xl font-lato">SISTEM INFORMASI</h3>
              <h3 class="text-2xl font-lato">BEDAH SERIBU RUMAH</h3>
              <h3 class="text-2xl font-lato">KABUPATEN BALANGAN</h3>
              <div class="text-center lg:text-left mb-10">
                <a href="#cta" class="btn btn-explore first scrollto rounded-pill mt-3 px-4 py-3 inline-block">JELAJAHI SEKARANG</a>
              </div>
            </div>
        </div>
        <div class="col-12 col-lg-7 order-1 order-lg-2 flex items-center">
           <img src="{{ asset('img/hero.webp') }}" class="img-fluid animated hero-img" alt="bedah-seribu-rumah" data-aos="zoom-out" data-aos-delay="600">
        </div>
      </div>
    </div>
</section><!-- End Hero -->


  <section id="cta">
    <div id="particles-js"></div>
      <div class="container text-center" data-aos="fade-up">
        <div class="section-header">
          <h3 class="text-2xl font-semibold mb-4">PETA DIGITAL</h3>
        </div> 
        <div class="cta-img flex justify-center mb-4" data-tilt>
            <img src="{{  asset('img/landingpage/macbook.png') }}" class="w-1/4" alt="peta-digital">
        </div>
        <div class="text-center">
          <a href="{{ route('map') }}" class="btn-lg btn-success first rounded-full my-3 d-inline-block bg-green-500 text-white py-2 px-4 hover:bg-green-600 transition duration-200"><i class="fas fa-map"></i>&nbsp;Buka Peta</a>
        </div>         
      </div>
  </section>

<section id="facts">
    <div class="container text-center" data-aos="fade-up">
      <header class="facts-header wow fadeInUp text-black">
        <em><h3>FITUR-FITUR</h3></em>
      </header>

      <div class="row">
        <div class="col-md-4 box" data-aos="fade-up" data-aos-delay="200">
          <img src="{{ asset('img/landingpage/spasial.png') }}" alt="spasial" class="img-fluid zoom-image" />
          <h4 class="title">FITUR 1</h4>
          <p class="description">Lorem ipsum dolor sit amet consectetur</p>
        </div>
        <div class="col-md-4 box" data-aos="fade-up" data-aos-delay="200">
          <img src="{{ asset('img/landingpage/data-inventory.png') }}" alt="inventory" class="img-fluid zoom-image" />
          <h4 class="title">FITUR 2</h4>
          <p class="description">Lorem ipsum dolor sit amet consectetur</p>
        </div>
        <div class="col-md-4 box" data-aos="fade-up" data-aos-delay="200">
          <img src="{{ asset('img/landingpage/dashboard.png') }}" alt="visualize-data" class="img-fluid zoom-image" />
          <h4 class="title">FITUR 3</h4>
          <p class="description">Lorem ipsum dolor sit amet consectetur</p>
        </div>
        <div class="col-md-4 box" data-aos="fade-up" data-aos-delay="300">
          <a href="#">
            <img src="{{ asset('img/landingpage/document.png') }}" alt="download-data" class="img-fluid zoom-image" />
          </a>          
          <h4 class="title">FITUR 4</h4>
          <p class="description">Lorem ipsum dolor sit amet consectetur</p>
        </div>
        <div class="col-md-4 box" data-aos="fade-up" data-aos-delay="200">
          <img src="{{ asset('img/landingpage/route.png') }}" alt="route" class="img-fluid zoom-image" />
          <h4 class="title">FITUR 5</h4>
          <p class="description">Lorem ipsum dolor sit amet consectetur</p>
        </div>
        <div class="col-md-4 box" data-aos="fade-up" data-aos-delay="200">
          <img src="{{ asset('img/landingpage/dynamic.png') }}" alt="route" class="img-fluid zoom-image" />
          <h4 class="title">FITUR 6</h4>
          <p class="description">Lorem ipsum dolor sit amet consectetur</p>
        </div>
      </div>
    </div>
</section>

<section id="gallery">
  <div class="container text-center mb-4" data-aos="fade-up">
      <header class="facts-header wow fadeInUp text-black">
        <em><h3>BEST PRACTICES</h3></em>
      </header>


  <div class="overflow-x-hidden px-4" id="carousel">
    <div class="container mx-auto"></div>
    <div class="d-flex -mx-4 flex-row relative owl-carousel owl-theme owl-loaded">
      <!-- START: GALLERY ROW 1 -->
      {{-- @foreach ($recommendations as $data)
      <div class="px-2 relative card group">
        <div
          class="rounded-xl overflow-hidden card-shadow relative" style="width: 300px; height: 250px">
          <div
            class="absolute opacity-0 group-hover:opacity-100 transition duration-200 d-flex items-center justify-content-center w-100 h-100 bg-black bg-opacity-35">
            <div
              class="bg-white text-black rounded-full d-flex items-center w-16 h-16 justify-content-center">
              <i class="fa fa-eye" aria-hidden="true"></i>
            </div>
          </div>
          <img
            src="{{ Storage::url($data->url) }}"
            alt=""
            class="w-100 h-100 object-cover object-center"
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

@endsection

@section('faq')

@include('v_faq')

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

          function callback(entries, observer) {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                scrollToTopBtn.classList.add("showBtn");
              } else {
                scrollToTopBtn.classList.remove("showBtn");
              }
            });
          }

          function scrollToTop() {
            rootElement.scrollTo({
              top: 0,
              behavior: "smooth"
            });
          }
          scrollToTopBtn.addEventListener("click", scrollToTop);

          let observer = new IntersectionObserver(callback);
          observer.observe(target);
          
        </script>
@endpush

