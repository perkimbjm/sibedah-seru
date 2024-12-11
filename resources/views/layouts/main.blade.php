<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileImage" content="" />
    <meta name="msapplication-TileColor" content="#000000" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title ?? 'Sibedah Seru Dashboard' }}</title>
    
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png')}}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png')}}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png')}}">
  <link href="{{ asset('img/favicon/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">

  @stack('before-style')

  @include('app.style')

  @stack('after-style')

    @yield('styles')
</head>

<body class="sidebar-mini layout-fixed hold-transition" style="height: auto;">
    <div class="wrapper">
                
        <div class="preloader">
            <div class="loading">
                <img src="{{ asset('img/dualball.svg') }}">
            </div>
        </div>

        <nav class="main-header navbar navbar-expand bg-green-gradation navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt text-gray-500"></i>
                        </a>
                    </li>
                </ul>

        </nav>

        @include('app.menu')
        <div class="content-wrapper" style="min-height: 917px;">
            <!-- Main content -->
            <section class="content" style="padding-top: 20px">
                @if(session('message'))
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                @endif
                @if($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-page-header :title="$menuName" :currentRoute="$currentRoute" />                                     

                    @yield('content')
            </section>
            <!-- /.content -->
        </div>

        <footer class="main-footer bg-green-gradation">
            <div class="float-right d-none d-sm-block">
                Dinas PUPR Perkim Kab. Balangan
            </div>
            <strong> &copy;</strong>  Hak cipta dilindungi undang-undang
        </footer>

        </aside>
        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>

    @stack('before-script')

    @include('app.script')
    
    @stack('after-script')
    
    @yield('scripts')
</body>

</html>
