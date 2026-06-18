<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    @include('partials.title-meta')
    @yield('css')
</head>

<body class="bg-light">

    @yield('content')

    @include('partials.vendor-scripts')
    @yield('js')

</body>

</html>
