@php
    $isCustomerActive      = request()->routeIs('customer.*');
    $isBookingActive       = request()->routeIs('booking.*');
    $isBookingReqActive    = request()->routeIs('booking-request.*');
    $isBookingsActive      = $isBookingActive || $isBookingReqActive;
    $isUserActive          = request()->routeIs('user.*');
    $isRoleActive          = request()->routeIs('role.*');
    $isUserMgmtActive      = $isUserActive || $isRoleActive;
    $isSettingsActive      = request()->routeIs('settings.*') || request()->routeIs('profile.*');
    $isVehicleActive       = request()->routeIs('admin.vehicles');
    $isCategoryActive      = request()->routeIs('admin.categories');
    $isItemActive          = request()->routeIs('admin.items');
    $isAddonActive         = request()->routeIs('admin.addons');
    $isPricingActive       = request()->routeIs('admin.pricing');
    $isRevenueActive       = request()->routeIs('admin.revenue');
    $isFeedbackActive      = request()->routeIs('admin.feedback');
    $isReportActive        = request()->routeIs('admin.reports');
    $isMastersActive       = $isVehicleActive || $isCategoryActive || $isItemActive || $isAddonActive;
    $isFinanceActive       = $isRevenueActive || $isFeedbackActive || $isReportActive;
    $isSupervisorActive    = request()->routeIs('supervisor.*');
@endphp

<ul class="main-menu" id="all-menu-items" role="menu">

    {{-- ── MAIN ────────────────────────────────────────────── --}}
    @canany(['view dashboard'])
    <li class="menu-title" role="presentation">Main</li>
    @endcanany

    @can('view dashboard')
    <li class="slide">
        <a href="{{ route('dashboard') }}"
           class="side-menu__item {{ request()->routeIs('dashboard') ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-home-2-line"></i></span>
            <span class="side-menu__label">Dashboard</span>
        </a>
    </li>
    @endcan

    {{-- ── VENDOR SERVICE LINKS ────────────────────────────── --}}
    @if(auth()->user() && auth()->user()->hasRole('Vendor'))
    <li class="menu-title" role="presentation">Vendor Panel</li>
    <li class="slide">
        <a href="{{ route('vendor.booking.index') }}"
           class="side-menu__item {{ request()->routeIs('vendor.booking.*') ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-calendar-todo-line"></i></span>
            <span class="side-menu__label">My Bookings</span>
        </a>
    </li>
    @endif

    {{-- ── SUPERVISOR SERVICE LINKS ───────────────────────── --}}
    @if(auth()->user() && auth()->user()->hasRole('Superviser'))
    <li class="menu-title" role="presentation">Supervisor Panel</li>
    <li class="slide">
        <a href="{{ route('supervisor.booking.index') }}"
           class="side-menu__item {{ $isSupervisorActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-user-star-line"></i></span>
            <span class="side-menu__label">My Bookings</span>
        </a>
    </li>
    @endif

    {{-- ── SERVICE MANAGEMENT ──────────────────────────────── --}}
    @if(!auth()->user() || !auth()->user()->hasRole('Vendor'))
    @canany(['view customer', 'view booking', 'view booking request'])
    <li class="menu-title" role="presentation">Service Management</li>
    @endcanany
    @endif

    {{-- Customers --}}
    @if(!auth()->user() || !auth()->user()->hasRole('Vendor'))
    @can('view customer')
    <li class="slide">
        <a href="{{ route('customer.index') }}"
           class="side-menu__item {{ $isCustomerActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-user-star-line"></i></span>
            <span class="side-menu__label">Customers</span>
        </a>
    </li>
    @endcan
    @endif

    {{-- Bookings (dropdown) --}}
    @if(!auth()->user() || !auth()->user()->hasRole('Vendor'))
    @canany(['view booking request', 'view booking'])
    <li class="slide {{ $isBookingsActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isBookingsActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-calendar-todo-line"></i></span>
            <span class="side-menu__label">Bookings</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            @can('view booking request')
            <li class="slide">
                <a href="{{ route('booking-request.index') }}"
                   class="side-menu__item {{ $isBookingReqActive ? 'active' : '' }}" role="menuitem">
                    Booking Requests
                </a>
            </li>
            @endcan
            @can('view booking')
            <li class="slide">
                <a href="{{ route('booking.index') }}"
                   class="side-menu__item {{ $isBookingActive ? 'active' : '' }}" role="menuitem">
                    Booking Manage
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany
    @endif

    {{-- Revenue --}}
    @can('view revenue')
    <li class="slide">
        <a href="{{ route('admin.revenue') }}"
           class="side-menu__item {{ $isRevenueActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-money-dollar-circle-line"></i></span>
            <span class="side-menu__label">Revenue</span>
        </a>
    </li>
    @endcan

    {{-- ── MASTER MANAGEMENT ───────────────────────────────── --}}
    @canany(['view vehicle', 'view category', 'view item', 'view addon'])
    <li class="menu-title" role="presentation">Master Management</li>

    <li class="slide {{ $isMastersActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isMastersActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-database-2-line"></i></span>
            <span class="side-menu__label">Master Settings</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            @can('view vehicle')
            <li class="slide">
                <a href="{{ route('admin.vehicles') }}"
                   class="side-menu__item {{ $isVehicleActive ? 'active' : '' }}" role="menuitem">
                    Vehicles
                </a>
            </li>
            @endcan

            @can('view category')
            <li class="slide">
                <a href="{{ route('admin.categories') }}"
                   class="side-menu__item {{ $isCategoryActive ? 'active' : '' }}" role="menuitem">
                    Categories
                </a>
            </li>
            @endcan

            <li class="slide">
                <a href="{{ route('admin.item-sizes') }}"
                   class="side-menu__item {{ request()->routeIs('admin.item-sizes*') ? 'active' : '' }}" role="menuitem">
                    Item Sizes Master
                </a>
            </li>

            @can('view item')
            <li class="slide">
                <a href="{{ route('admin.items') }}"
                   class="side-menu__item {{ $isItemActive ? 'active' : '' }}" role="menuitem">
                    Item Master
                </a>
            </li>
            @endcan

            @can('view addon')
            <li class="slide">
                <a href="{{ route('admin.addons') }}"
                   class="side-menu__item {{ $isAddonActive ? 'active' : '' }}" role="menuitem">
                    Add-On Services
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany

    {{-- ── REPORTS & FEEDBACK ──────────────────────────────── --}}
    @canany(['view feedback', 'view report'])
    <li class="menu-title" role="presentation">Reports & Feedback</li>
    @endcanany

    @can('view feedback')
    <li class="slide">
        <a href="{{ route('admin.feedback') }}"
           class="side-menu__item {{ $isFeedbackActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-star-line"></i></span>
            <span class="side-menu__label">Feedback & Ratings</span>
        </a>
    </li>
    @endcan

    @can('view report')
    <li class="slide">
        <a href="{{ route('admin.reports') }}"
           class="side-menu__item {{ $isReportActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-bar-chart-line"></i></span>
            <span class="side-menu__label">Reports</span>
        </a>
    </li>
    @endcan

    {{-- ── APPLICATIONS ────────────────────────────────────── --}}
    @canany(['view user', 'view role'])
    <li class="menu-title" role="presentation">Applications</li>

    {{-- User Management (dropdown) --}}
    <li class="slide {{ $isUserMgmtActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isUserMgmtActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-group-line"></i></span>
            <span class="side-menu__label">User Management</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            @can('view user')
            <li class="slide">
                <a href="{{ route('user.index') }}"
                   class="side-menu__item {{ $isUserActive ? 'active' : '' }}" role="menuitem">
                    Users
                </a>
            </li>
            @endcan
            @can('view role')
            <li class="slide">
                <a href="{{ route('role.index') }}"
                   class="side-menu__item {{ $isRoleActive ? 'active' : '' }}" role="menuitem">
                    Roles & Permissions
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany

    {{-- ── SETTINGS ─────────────────────────────────────────── --}}
    @canany(['view settings', 'view pricing settings', 'view profile settings'])
    <li class="menu-title" role="presentation">Settings</li>

    <li class="slide {{ $isSettingsActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isSettingsActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-settings-3-line"></i></span>
            <span class="side-menu__label">System Settings</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            @can('view settings')
            <li class="slide">
                <a href="{{ route('settings.edit') }}"
                   class="side-menu__item {{ request()->routeIs('settings.edit') ? 'active' : '' }}" role="menuitem">
                    General Settings
                </a>
            </li>
            @endcan
            @can('view pricing settings')
            <li class="slide">
                <a href="{{ route('admin.pricing') }}"
                   class="side-menu__item {{ $isPricingActive ? 'active' : '' }}" role="menuitem">
                    Pricing Settings
                </a>
            </li>
            @endcan
            @can('view profile settings')
            <li class="slide">
                <a href="{{ route('profile.edit') }}"
                   class="side-menu__item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" role="menuitem">
                    Profile Settings
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany

</ul>
