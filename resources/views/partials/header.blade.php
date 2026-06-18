<!-- START HEADER -->
<header class="app-header">
    <div class="container-fluid">
        <div class="nav-header">

            <div class="header-left hstack gap-3">
                <!-- HORIZONTAL BRAND LOGO -->
                @php
                    $logo = \App\Models\Setting::get('logo', 'assets/images/light-logo.png');
                    $faviconSmall = \App\Models\Setting::get('favicon', 'assets/images/Favicon.png');
                @endphp
                <div class="app-sidebar-logo app-horizontal-logo justify-content-center align-items-center">
                    <a href="{{ route('dashboard') }}">
                        <img height="35" class="app-sidebar-logo-default" alt="Logo" loading="lazy"
                            src="{{ asset($logo) }}">
                        <img height="40" class="app-sidebar-logo-minimize" alt="Logo" loading="lazy"
                            src="{{ asset($faviconSmall) }}">
                    </a>
                </div>

                <!-- Sidebar Toggle Btn -->
                <button type="button" class="btn btn-light-light icon-btn sidebar-toggle d-none d-md-block"
                    aria-expanded="false" aria-controls="main-menu">
                    <span class="visually-hidden">Toggle sidebar</span>
                    <i class="ri-menu-2-fill"></i>
                </button>

                <!-- Sidebar Toggle for Mobile -->
                <button class="btn btn-light-light icon-btn d-md-none small-screen-toggle" id="smallScreenSidebarLabel"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#smallScreenSidebar"
                    aria-controls="smallScreenSidebar">
                    <span class="visually-hidden">Sidebar toggle for mobile</span>
                    <i class="ri-arrow-right-fill"></i>
                </button>

                <!-- Sidebar Toggle for Horizontal Menu -->
                <button class="btn btn-light-light icon-btn d-lg-none small-screen-horizontal-toggle" type="button"
                    ria-expanded="false" aria-controls="main-menu">
                    <span class="visually-hidden">Sidebar toggle for horizontal</span>
                    <i class="ri-arrow-right-fill"></i>
                </button>


            </div>

            <div class="header-right hstack gap-3">

                <!-- Profile Section -->
                <div class="dropdown profile-dropdown features-dropdown">
                    <button type="button" id="accountNavbarDropdown"
                        class="btn profile-btn shadow-none px-0 hstack gap-0 gap-sm-3" data-bs-toggle="dropdown"
                        aria-expanded="false" data-bs-auto-close="outside" data-bs-dropdown-animation>
                        <span class="position-relative">
                            <span class="avatar-item avatar overflow-hidden">
                                <img id="header-profile-img" class="img-fluid"
                                    src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('assets/images/avatar/dummy-avatar-2.jpg') }}"
                                    alt="avatar image">
                            </span>
                            {{-- <span
                                class="position-absolute border-2 border border-white h-12px w-12px rounded-circle bg-success end-0 bottom-0"></span>
                            --}}
                        </span>
                        <span>
                            <span id="header-profile-name"
                                class="h6 d-none d-xl-inline-block text-start fw-semibold mb-0">{{ Auth::user()->name }}</span>
                            <span id="header-profile-role"
                                class="d-none d-xl-block fs-12 text-start text-muted">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end header-language-scrollable"
                        aria-labelledby="accountNavbarDropdown">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile Settings</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>
<!-- END HEADER -->