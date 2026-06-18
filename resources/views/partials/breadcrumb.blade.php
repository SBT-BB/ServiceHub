<div class="hstack flex-wrap gap-3 mb-4">
    <div class="flex-grow-1">
        <h4 class="mb-1 fw-semibold">@yield('sub-title')</h4>
        <nav>
            <ol class="breadcrumb breadcrumb-arrow mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">@yield('pagetitle')</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">@yield('sub-title')</li>
            </ol>
        </nav>
    </div>

    @php
        $buttonTitle = trim(View::yieldContent('buttonTitle'));
        $buttonLink = trim(View::yieldContent('buttonLink'));
    @endphp

    @if ($buttonTitle !== '' && $buttonLink !== '')
        <div class="d-flex my-xl-auto align-items-center flex-shrink-0">
            <a href="{{ $buttonLink }}" class="btn btn-sm btn-primary" data-drawer="true">
                {!! $buttonTitle !!}
            </a>
        </div>
    @endif

</div>
