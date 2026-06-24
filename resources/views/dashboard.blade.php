@extends('partials.Layouts.master3')

@section('title', 'Dashboard | Herozi - Design & Developed by Bhakti.')
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
        <!-- Customers Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-primary bg-opacity-10 text-primary">
                        <i class="ri-user-star-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Customers</span>
                        <h5 class="fw-medium mb-1">{{ number_format($stats['total_customers']) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Active:</h6>
                        <p class="fs-12 text-muted mb-0">{{ number_format($stats['active_customers']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-success bg-opacity-10 text-success">
                        <i class="ri-calendar-todo-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Bookings</span>
                        <h5 class="fw-medium mb-1">{{ number_format($stats['total_bookings']) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Completed:</h6>
                        <p class="fs-12 text-success mb-0">{{ number_format($stats['completed_bookings']) }}</p>
                    </div>
                    <div class="vr h-30px align-self-center bg-light"></div>
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Pending:</h6>
                        <p class="fs-12 text-warning mb-0">{{ number_format($stats['pending_bookings']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-info bg-opacity-10 text-info">
                        <i class="ri-money-dollar-circle-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Revenue</span>
                        <h5 class="fw-medium mb-1">₹{{ number_format($stats['total_revenue'], 2) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Pending:</h6>
                        <p class="fs-12 text-muted mb-0">₹{{ number_format($stats['pending_revenue'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicles Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-warning bg-opacity-10 text-warning">
                        <i class="ri-truck-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Vehicles</span>
                        <h5 class="fw-medium mb-1">{{ number_format($stats['total_vehicles']) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Active:</h6>
                        <p class="fs-12 text-muted mb-0">{{ number_format($stats['active_vehicles']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== BOOKING ANALYTICS SECTION ===== --}}
    <style>
        .booking-analytics-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.05);
            transition: box-shadow 0.3s ease;
        }
        .booking-analytics-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .booking-stat-pill {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            text-decoration: none;
            margin-bottom: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .booking-stat-pill:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            text-decoration: none;
        }
        .booking-stat-pill:last-child { margin-bottom: 0; }
        .bsp-icon {
            width: 44px; height: 44px; min-width: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .bsp-count {
            font-size: 22px; font-weight: 800;
            line-height: 1;
        }
        .bsp-label { font-size: 12px; color: #8a94a6; font-weight: 500; margin-top: 2px; }
        .bsp-badge {
            margin-left: auto;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .progress-thin {
            height: 5px;
            border-radius: 10px;
            margin-top: 6px;
        }
        .chart-header-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .chart-toggle-btn {
            border: none;
            background: transparent;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .chart-toggle-btn:hover { background: #e9ecef; color: #333; }
        .chart-toggle-btn.active-toggle {
            background: #5b71b9;
            color: #fff;
            box-shadow: 0 2px 8px rgba(91,113,185,0.3);
        }
    </style>

    <div class="row mt-4 g-3">

        {{-- LEFT: Bar Chart Card --}}
        <div class="col-xl-8">
            <div class="booking-analytics-card p-4 h-100">
                {{-- Header --}}
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="font-size:16px; color:#1a1f36;">Booking Status Analytics</h5>
                        <p class="mb-0" style="font-size:12px; color:#8a94a6;">Visual breakdown of all booking statuses</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        {{-- Chart Type Toggle --}}
                        <div class="d-flex align-items-center rounded-pill p-1" style="background:#f3f4f6; gap:2px;">
                            <button id="btn-bar-chart" onclick="switchChartType('bar')" class="chart-toggle-btn active-toggle" title="Bar Chart">
                                <i class="ri-bar-chart-2-line"></i> Bar
                            </button>
                            <button id="btn-line-chart" onclick="switchChartType('line')" class="chart-toggle-btn" title="Line Chart">
                                <i class="ri-line-chart-line"></i> Line
                            </button>
                        </div>
                        <a href="{{ route('booking.index') }}" class="btn btn-sm fw-semibold text-decoration-none" style="background:#5b71b9; color:#fff; border-radius:20px; padding:6px 14px; font-size:12px;">
                            <i class="ri-external-link-line me-1"></i>View All
                        </a>
                    </div>
                </div>

                {{-- Legend dots --}}
                <div class="d-flex flex-wrap gap-3 mb-3">
                    <span class="d-flex align-items-center gap-1 fs-12 fw-medium" style="color:#6c757d;">
                        <span style="width:10px;height:10px;border-radius:3px;background:#f59e0b;display:inline-block;"></span> Pending
                    </span>
                    <span class="d-flex align-items-center gap-1 fs-12 fw-medium" style="color:#6c757d;">
                        <span style="width:10px;height:10px;border-radius:3px;background:#6366f1;display:inline-block;"></span> Confirmed
                    </span>
                    <span class="d-flex align-items-center gap-1 fs-12 fw-medium" style="color:#6c757d;">
                        <span style="width:10px;height:10px;border-radius:3px;background:#10b981;display:inline-block;"></span> Completed
                    </span>
                    <span class="d-flex align-items-center gap-1 fs-12 fw-medium" style="color:#6c757d;">
                        <span style="width:10px;height:10px;border-radius:3px;background:#ef4444;display:inline-block;"></span> Cancelled
                    </span>
                </div>

                <div id="booking-bar-chart"></div>
            </div>
        </div>

        {{-- RIGHT: Status Summary --}}
        <div class="col-xl-4">
            <div class="booking-analytics-card p-4 h-100">
                <h5 class="fw-bold mb-1" style="font-size:16px; color:#1a1f36;">Booking Summary</h5>
                <p class="mb-3" style="font-size:12px; color:#8a94a6;">Click any status to view bookings</p>

                @php
                    $totalB = ($stats['pending_bookings'] + ($stats['confirmed_bookings'] ?? 0) + $stats['completed_bookings'] + ($stats['cancelled_bookings'] ?? 0)) ?: 1;
                @endphp

                {{-- Pending --}}
                <a href="{{ route('booking.index') }}?status=pending" class="booking-stat-pill" style="background: linear-gradient(135deg, #fffbeb, #fef3c7);">
                    <div class="bsp-icon" style="background: linear-gradient(135deg,#f59e0b,#fbbf24); color:#fff;">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="bsp-count" style="color:#d97706;">{{ number_format($stats['pending_bookings']) }}</div>
                                <div class="bsp-label">Pending · Awaiting action</div>
                            </div>
                            <i class="ri-arrow-right-s-line" style="color:#d97706; font-size:18px;"></i>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar" style="width:{{ round(($stats['pending_bookings']/$totalB)*100) }}%; background: linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
                        </div>
                    </div>
                </a>

                {{-- Confirmed --}}
                <a href="{{ route('booking.index') }}?status=confirmed" class="booking-stat-pill" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
                    <div class="bsp-icon" style="background: linear-gradient(135deg,#6366f1,#818cf8); color:#fff;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="bsp-count" style="color:#4f46e5;">{{ number_format($stats['confirmed_bookings'] ?? 0) }}</div>
                                <div class="bsp-label">Confirmed · Ready to go</div>
                            </div>
                            <i class="ri-arrow-right-s-line" style="color:#4f46e5; font-size:18px;"></i>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar" style="width:{{ round((($stats['confirmed_bookings'] ?? 0)/$totalB)*100) }}%; background: linear-gradient(90deg,#6366f1,#818cf8);"></div>
                        </div>
                    </div>
                </a>

                {{-- Completed --}}
                <a href="{{ route('booking.index') }}?status=completed" class="booking-stat-pill" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
                    <div class="bsp-icon" style="background: linear-gradient(135deg,#10b981,#34d399); color:#fff;">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="bsp-count" style="color:#059669;">{{ number_format($stats['completed_bookings']) }}</div>
                                <div class="bsp-label">Completed · Delivered</div>
                            </div>
                            <i class="ri-arrow-right-s-line" style="color:#059669; font-size:18px;"></i>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar" style="width:{{ round(($stats['completed_bookings']/$totalB)*100) }}%; background: linear-gradient(90deg,#10b981,#34d399);"></div>
                        </div>
                    </div>
                </a>

                {{-- Cancelled --}}
                <a href="{{ route('booking.index') }}?status=cancelled" class="booking-stat-pill" style="background: linear-gradient(135deg, #fff1f2, #ffe4e6);">
                    <div class="bsp-icon" style="background: linear-gradient(135deg,#ef4444,#f87171); color:#fff;">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="bsp-count" style="color:#dc2626;">{{ number_format($stats['cancelled_bookings'] ?? 0) }}</div>
                                <div class="bsp-label">Cancelled · Not proceeded</div>
                            </div>
                            <i class="ri-arrow-right-s-line" style="color:#dc2626; font-size:18px;"></i>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar" style="width:{{ round((($stats['cancelled_bookings'] ?? 0)/$totalB)*100) }}%; background: linear-gradient(90deg,#ef4444,#f87171);"></div>
                        </div>
                    </div>
                </a>

                {{-- Total --}}
                <div class="mt-3 pt-3" style="border-top: 1px dashed #e5e7eb;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fs-12 fw-semibold" style="color:#8a94a6;">TOTAL BOOKINGS</span>
                        <span class="fw-bold" style="font-size:20px; color:#1a1f36;">{{ number_format($stats['total_bookings']) }}</span>
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

    {{-- Booking Status Bar Chart — Modern Gradient --}}
    <script>
        (function () {
            function renderBookingBarChart() {
                var el = document.querySelector('#booking-bar-chart');
                if (!el || typeof ApexCharts === 'undefined') return;

                var categories = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
                var data       = [
                    {{ $stats['pending_bookings']   ?? 0 }},
                    {{ $stats['confirmed_bookings'] ?? 0 }},
                    {{ $stats['completed_bookings'] ?? 0 }},
                    {{ $stats['cancelled_bookings'] ?? 0 }}
                ];

                var gradientColors = [
                    { from: '#f59e0b', to: '#fbbf24' },
                    { from: '#6366f1', to: '#818cf8' },
                    { from: '#10b981', to: '#34d399' },
                    { from: '#ef4444', to: '#f87171' }
                ];

                var options = {
                    series: [{ name: 'Bookings', data: data }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: { show: false },
                        fontFamily: 'Inter, system-ui, sans-serif',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 900,
                            animateGradually: { enabled: true, delay: 120 },
                            dynamicAnimation: { enabled: true, speed: 400 }
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 12,
                            borderRadiusApplication: 'end',
                            columnWidth: '42%',
                            distributed: true,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -28,
                        style: {
                            fontSize: '14px',
                            fontWeight: 800,
                            colors: ['#1a1f36']
                        },
                        formatter: function (val) { return val; }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            type: 'vertical',
                            shadeIntensity: 0.4,
                            opacityFrom: 1,
                            opacityTo: 0.75,
                            stops: [0, 100]
                        }
                    },
                    colors: ['#f59e0b', '#6366f1', '#10b981', '#ef4444'],
                    states: {
                        hover: { filter: { type: 'darken', value: 0.85 } },
                        active: { filter: { type: 'darken', value: 0.75 } }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: {
                                fontSize: '13px',
                                fontWeight: 600,
                                colors: ['#d97706','#4f46e5','#059669','#dc2626']
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { fontSize: '12px', colors: ['#8a94a6'] },
                            formatter: function (val) { return Math.floor(val); }
                        },
                        min: 0,
                        tickAmount: 4
                    },
                    grid: {
                        borderColor: '#f3f4f6',
                        strokeDashArray: 5,
                        padding: { top: 10, right: 10, bottom: 0, left: 10 }
                    },
                    legend: { show: false },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function (val) {
                                return '<b>' + val + '</b> booking(s)';
                            },
                            title: { formatter: function (s) { return s + ':'; } }
                        },
                        style: { fontSize: '13px' }
                    }
                };

                var chart = new ApexCharts(el, options);
                chart.render();
            }

            if (document.readyState === 'complete') {
                renderBookingBarChart();
            } else {
                window.addEventListener('load', renderBookingBarChart);
            }
        })();
    </script>
@endsection
