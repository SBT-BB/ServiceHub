@extends('partials.Layouts.master3')

@section('title', 'Dashboard | Herozi - Design & Developed by ❤️SparkBizTech.')
@section('sub-title', 'Dashboard Details')
@section('pagetitle', 'Dashboard')
@section('buttonTitle', 'Share')
@section('link', '#!')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2">
                        <i class="ri-group-line"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Number of Students</span>
                        <h5 class="fw-medium mb-1">1,200</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-3">
                        <h6 class="mb-0 fw-semibold">Active Students:</h6>
                        <p class="fs-12 text-muted mb-0">1,000</p>
                    </div>
                    <div class="vr h-30px align-self-center bg-light"></div>
                    <div class="hstack gap-3">
                        <h6 class="mb-0 fw-semibold">New Students:</h6>
                        <p class="fs-12 text-muted mb-0">200</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Courses Card -->
        <div class="col-lg-4">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2">
                        <i class="ri-book-line"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Courses</span>
                        <h5 class="fw-medium mb-1">30</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-3">
                        <h6 class="mb-0 fw-semibold">Active Courses:</h6>
                        <p class="fs-12 text-muted mb-0">25</p>
                    </div>
                    <div class="vr h-30px align-self-center bg-light"></div>
                    <div class="hstack gap-3">
                        <h6 class="mb-0 fw-semibold">Archived:</h6>
                        <p class="fs-12 text-muted mb-0">5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructor Performance Card -->
        <div class="col-lg-4">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2">
                        <i class="ri-user-star-line"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Instructor Performance</span>
                        <h5 class="fw-medium mb-1">John Doe - 4.8/5</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-3">
                        <h6 class="mb-0 fw-semibold">Completion Rate:</h6>
                        <p class="fs-12 text-muted mb-0">85%</p>
                    </div>
                    <div class="vr h-30px align-self-center bg-light"></div>
                    <div class="hstack gap-3">
                        <h6 class="mb-0 fw-semibold">New Reviews:</h6>
                        <p class="fs-12 text-muted mb-0">15</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/pages/countup.init.js') }}"></script>
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/charts/apexcharts-config.init.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards/dashboard-online-course.init.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
