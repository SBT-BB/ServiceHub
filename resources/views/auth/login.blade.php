@extends('layouts.admin-auth')

@php
    // $admin_settings = getSettings('admin', 1); // Commented out to avoid errors if not defined
@endphp

@section('title', __('Sign In'))

@section('css')
    @include('partials.head-css', ['auth' => 'layout-auth'])
@endsection

@section('content')

    <!-- START -->
    <div class="account-pages">
        {{-- <img src="{{ asset('img/auth_bg.jpg') }}" alt="auth_bg" class="auth-bg light">
        <img src="{{ asset('img/auth_bg_dark.jpg') }}" alt="auth_bg_dark" class="auth-bg dark"> --}}
        <div class="container">
            <div class="justify-content-center row gy-0">
                <div class="col-lg-6">
                    <div class="auth-box card card-body m-0 h-100 border-0 justify-content-center"
                        style="border-radius: 15px; border: 2px solid #18bcc7;">
                        <div class="mb-5 text-center">
                            <div class="mb-4">
                                <img src="{{ asset('assets/images/light-logo.png') }}" alt="Om Homeopathy Logo"
                                    class="img-fluid" style="max-height: 80px;">
                            </div>
                            <h4 class="fw-normal"><span class="fw-bold text-primary">Welcome To Herozi
                                </span>
                            </h4>
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="form-custom mt-10">
                            @csrf
                            <div class="mb-5">
                                <label class="form-label" for="login-email">{{ __('Email') }}<span
                                        class="text-danger ms-1">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="login-email" name="email" value="{{ old('email') }}"
                                    placeholder="{{ __('Enter your email') }}" required autofocus>
                                @error('email')
                                    <span class="text-danger fs-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label class="form-label" for="LoginPassword">{{ __('Password') }}<span
                                        class="text-danger ms-1">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="LoginPassword"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="{{ __('Enter your password') }}" data-visible="false" required
                                        autocomplete="current-password">
                                    <a class="input-group-text bg-transparent toggle-password" href="javascript:;"
                                        data-target="password">
                                        <i class="ri-eye-off-line text-muted toggle-icon"></i>
                                    </a>
                                </div>
                                @error('password')
                                    <span class="text-danger fs-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row mb-5">
                                <div class="col-sm-6">
                                    <div class="form-check form-check-sm d-flex align-items-center gap-2 mb-0">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember-me">
                                        <label class="form-check-label" for="remember-me">{{ __('Remember me') }}</label>
                                    </div>
                                </div>
                                {{-- <div class="col-sm-6 text-end">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                            class="fs-14 text-muted">{{ __('Forgot your password?') }}</a>
                                    @endif
                                </div> --}}
                            </div>

                            <button type="submit" class="btn btn-primary rounded-2 w-100 btn-loader">
                                <span class="indicator-label">{{ __('Sign In') }}</span>
                                <span class="indicator-progress flex gap-2 justify-content-center w-100">
                                    <span>{{ __('Please Wait') }} ...</span>
                                    <i class="ri-loader-2-fill"></i>
                                </span>
                            </button>
                            {{-- 
                            <div class="center-hr my-4 text-nowrap text-muted">{{ __('Or') }}</div>

                            <a href="{{ route('google.login') }}"
                                class="btn btn-outline-secondary rounded-2 w-100 d-flex align-items-center justify-content-center gap-2"
                                style="border-color: #dadce0; background-color: #fff; color: #3c4043; padding: 10px 16px;">
                                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                                    <path fill="#EA4335"
                                        d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                    <path fill="#4285F4"
                                        d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                    <path fill="#FBBC05"
                                        d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                    <path fill="#34A853"
                                        d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                </svg>
                                <span>{{ __('Login with Google') }}</span>
                            </a>

                            @if (Route::has('register'))
                                <p class="mb-0 mt-5 text-muted text-center">
                                    {{ __("Don't have an account?") }}
                                    <a href="{{ route('register') }}"
                                        class="text-primary fw-medium text-decoraton-underline ms-1">{{ __('Sign up') }}</a>
                                </p>
                            @endif --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
