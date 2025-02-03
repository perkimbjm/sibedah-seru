<aside class="overflow-y-hidden main-sidebar sidebar-green-info elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="/" class="brand-link ">
        <img src="{{ asset('img/logo-sibedah.png') }}" alt="logo Sibedah-seru" class="brand-image" style="background: white; border-radius: 50%; padding:5px;">
        <span class="brand-text">SIBEDAH SERU</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel d-flex">
            <!-- Sidebar user (optional) -->
            <div class="info">
                <a href="#" class="ml-3 text-white d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route("dashboard") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route("webgis") }}">
                        <i class="fas fa-globe-asia nav-icon">
                        </i>
                        <p>
                            WebGIS Balangan
                        </p>
                    </a>
                </li>

                @can('data_access')
                    <li class="nav-item has-treeview {{ request()->is("app/houses*") ? "menu-open" : "" }} {{ request()->is("app/nsups*") ? "menu-open" : "" }} {{ request()->is("app/ipals*") ? "menu-open" : "" }} {{ request()->is("app/build-galleries*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-database">

                            </i>
                            <p>
                                Manajemen Data
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route("app.houses.index") }}" class="nav-link {{ request()->is("app/houses") || request()->is("app/houses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-home">

                                        </i>
                                        <p>
                                            Bedah Rumah
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("app.rtlh.index") }}" class="nav-link {{ request()->is("app/rtlh") || request()->is("app/rtlh/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-house-damage">

                                        </i>
                                        <p>
                                            Rumah Tidak Layak Huni
                                        </p>
                                    </a>
                                </li>



                        </ul>
                    </li>
                @endcan

                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("app/permissions*") ? "menu-open" : "" }} {{ request()->is("app/roles*") ? "menu-open" : "" }} {{ request()->is("app/users*") ? "menu-open" : "" }} {{ request()->is("app/audit-logs*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                Manajemen User
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route("app.permissions.index") }}" class="nav-link {{ request()->is("app/permissions") || request()->is("app/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-key">

                                        </i>
                                        <p>
                                            Hak Akses
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("app.roles.index") }}" class="nav-link {{ request()->is("app/roles") || request()->is("app/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user-tag">

                                        </i>
                                        <p>
                                            Role
                                        </p>
                                    </a>

                                <li class="nav-item">
                                    <a href="{{ route("app.users.index") }}" class="nav-link {{ request()->is("app/users") || request()->is("app/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            User
                                        </p>
                                    </a>
                                </li>

                                {{-- <li class="nav-item">
                                    <a href="{{ route("app.audit-logs.index") }}" class="nav-link {{ request()->is("app/audit-logs") || request()->is("app/audit-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-clipboard-list">

                                        </i>
                                        <p>
                                            Audit Log
                                        </p>
                                    </a>
                                </li> --}}

                        </ul>
                    </li>
                    @endcan


                    @can('content_management_access')
                    <li class="nav-item has-treeview {{ request()->is("app/faqs*") ? "menu-open" : "" }} {{ request()->is("app/links*") ? "menu-open" : "" }} {{ request()->is("app/downloads*") ? "menu-open" : "" }} ">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-laptop">

                            </i>
                            <p>
                                Manajemen Konten
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route("app.faqs.index") }}" class="nav-link {{ request()->is("app/faqs") || request()->is("app/faqs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-question-circle">

                                        </i>
                                        <p>
                                            FAQ
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("app.links.index") }}" class="nav-link {{ request()->is("app/links") || request()->is("app/links/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-link">

                                        </i>
                                        <p>
                                            Link Website
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("app.downloads.index") }}" class="nav-link {{ request()->is("app/downloads") || request()->is("app/downloads*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-download">

                                        </i>
                                        <p>
                                            Unduh Dokumen
                                        </p>
                                    </a>
                                </li>



                        </ul>
                    </li>
                @endcan

                @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('profile') || request()->is('profile/*') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                                <i class="fa-fw fas fa-user nav-icon">
                                </i>
                                <p>
                                    Edit Profil
                                </p>
                            </a>
                        </li>
                @endif
                @can('file-manager_access')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('file-manager') || request()->is('file-manager/*') ? 'active' : '' }}" href="{{ route('app.file-manager.index') }}">
                        <i class="fa-fw fas fa-folder nav-icon">
                        </i>
                        <p>
                            File Manager
                        </p>
                    </a>
                </li>
                @endcan
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt nav-icon">

                            </i>
                            <p>Logout</p>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
