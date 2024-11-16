<header class="h-full w-full bg-white box-border">
    <!-- Navbar -->
    <div class="header-7-1 font-sans">
        <nav class="navbar py-3 md:px-16 md:py-4 lg:px-24 lg:py-4 navbar-expand-lg navbar-light pt-6">
            <div class="container mx-auto px-4 xl:px-24 flex gap-1 max-w-7xl py-6 sm:px-6 lg:px-8">
                <a class="navbar-brand flex-shrink-0" href="/">
                    <img src="{{ asset('img/logobalangan-nav.webp') }}"
                        alt="logo sibedah-seru" />
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-3">
                        <li class="nav-item hover-underline-animation">
                            <a class="nav-link px-md-3 {{ request()->is('/') ? 'active' : '' }}" aria-current="page" href="/">Home</a>
                        </li>
                        <li class="nav-item hover-underline-animation">
                          <a class="nav-link px-md-3 {{ request()->is('map') ? 'active' : '' }}" href={{ url('/map') }}>Peta Digital</a>
                        </li>
                        <li class="nav-item hover-underline-animation">
                            <a class="nav-link px-md-3 {{ request()->is('download') ? 'active' : '' }}" href={{ url('/download') }}>Download</a>
                          </li>
                        <li class="nav-item hover-underline-animation">
                            <a class="nav-link px-md-3" href={{ url("https://forms.gle/uEnQ5fygU9p1YUk59") }}>Smart RTLH</a>
                          </li>
                        <li class="nav-item hover-underline-animation">
                            <a class="nav-link px-md-3 {{ request()->is('bedah') ? 'active' : '' }}" href={{ url("/bedah") }}>Bedah Rumah</a>
                        </li>
                        <li class="nav-item hover-underline-animation">
                            <a class="nav-link px-md-3 {{ request()->is('guide') ? 'active' : '' }}" href={{ url("/guide") }}>Panduan</a>
                        </li>
                    </ul>


                    @auth
                    <ul class="navbar-nav mb-2 mb-lg-0" >
                        <li class="nav-item">
                            <a class="nav-link px-md-3" href="{{ route('home') }}">
                                Dashboard
                            </a>
                        </li>
                    </ul>
                    @endauth
                    @guest
                    <div class="flex">
                        <a href="{{ route('login') }}" class="nav-link px-2">
                            <button class="btn-login-green font-semibold py-2 px-4 rounded-lg hover:bg-green-700 shadow-md" >
                                Login
                            </button>
                        </a>
                    </div>
                    @endguest


                   
                </div>
            </div>
        </nav>
    </div>
</header>