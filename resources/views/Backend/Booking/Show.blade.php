@extends('partials.layouts.master')

@section('title')
    Booking {{ $booking->booking_number }} | Herozi
@endsection

@section('sub-title', 'Booking Details')
@section('pagetitle', 'Bookings')

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('booking.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $booking->booking_number }}</li>
                </ol>
            </nav>
        </div>

        <!-- Left Details Column -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Shifting Details</h5>
                    <span class="font-monospace fw-semibold text-primary">{{ $booking->booking_number }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <span class="text-muted d-block fs-11">Pickup Location</span>
                            <span class="fw-semibold text-success fs-14"><i class="ri-map-pin-user-line me-1"></i>{{ $booking->pickup_location }}</span>
                            @if ($booking->pickup_latitude && $booking->pickup_longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $booking->pickup_latitude }},{{ $booking->pickup_longitude }}" target="_blank" class="d-block small text-primary mt-1">
                                    <i class="ri-external-link-line me-1"></i>View on Google Maps ({{ $booking->pickup_latitude }}, {{ $booking->pickup_longitude }})
                                </a>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block fs-11">Drop Location</span>
                            <span class="fw-semibold text-danger fs-14"><i class="ri-map-pin-5-line me-1"></i>{{ $booking->drop_location }}</span>
                            @if ($booking->drop_latitude && $booking->drop_longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $booking->drop_latitude }},{{ $booking->drop_longitude }}" target="_blank" class="d-block small text-primary mt-1">
                                    <i class="ri-external-link-line me-1"></i>View on Google Maps ({{ $booking->drop_latitude }}, {{ $booking->drop_longitude }})
                                </a>
                            @endif
                        </div>

                        <hr class="my-3">

                        <div class="col-md-4">
                            <span class="text-muted d-block fs-11">Shifting Date</span>
                            <span class="fw-medium text-dark"><i class="ri-calendar-line me-1 text-primary"></i>{{ date('d M Y', strtotime($booking->shifting_date)) }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block fs-11">Shifting Time</span>
                            <span class="fw-medium text-dark"><i class="ri-time-line me-1 text-primary"></i>{{ date('h:i A', strtotime($booking->shifting_time)) }}</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block fs-11">Source Reference</span>
                            <span class="fw-medium text-dark">
                                @if ($booking->booking_request_id)
                                    <span class="badge bg-light text-dark">Converted from Request #{{ $booking->booking_request_id }}</span>
                                @else
                                    <span class="badge bg-light text-dark">Direct Admin Creation</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lifecycle Progress / Tracking -->
            <div class="card mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tracking Progress</h5>
                </div>
                <div class="card-body py-4">
                    @if ($booking->tracking_status === 'cancelled')
                        <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                            <i class="ri-error-warning-line fs-20 me-2"></i>
                            <div>
                                <strong>Booking Cancelled:</strong> This shifting operation was cancelled.
                            </div>
                        </div>
                    @else
                        <!-- Progress Bar & Timeline -->
                        @php
                            $statusOrder = [
                                'pending_confirmation' => 'Booking Confirmation Pending',
                                'confirmed'            => 'Booking Confirmed',
                                'trip_started'         => 'Trip Started',
                                'shifting_started'     => 'Shifting Started',
                                'pickup_completed'     => 'Pickup Completed',
                                'completed'            => 'Shifting Completed'
                            ];
                            $keys = array_keys($statusOrder);
                            $currentIdx = array_search($booking->tracking_status, $keys);
                            if ($currentIdx === false) $currentIdx = 0;
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center position-relative mb-4" style="margin-top: 15px;">
                            <!-- Progress Line behind badges -->
                            <div class="progress position-absolute start-0 end-0" style="height: 4px; z-index: 1; top: 50%; transform: translateY(-50%);">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($currentIdx / (count($keys) - 1)) * 100 }}%"></div>
                            </div>
                            
                            <!-- Badges -->
                            @foreach ($keys as $idx => $stepKey)
                                <div class="text-center position-relative" style="z-index: 2; width: {{ 100 / count($keys) }}%;">
                                    <div class="avatar avatar-md rounded-circle border border-2 mx-auto mb-2 {{ $idx <= $currentIdx ? 'bg-success text-white border-success' : 'bg-white text-muted border-light' }}" style="width: 32px; height: 32px; display:flex; align-items:center; justify-content:center;">
                                        @if ($idx < $currentIdx)
                                            <i class="ri-check-line"></i>
                                        @else
                                            <span>{{ $idx + 1 }}</span>
                                        @endif
                                    </div>
                                    <span class="fs-12 fw-medium {{ $idx == $currentIdx ? 'text-success fw-semibold' : 'text-muted' }}">{{ $statusOrder[$stepKey] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Customer Details & Action Column -->
        <div class="col-lg-4">
            <!-- Customer Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Details</h5>
                </div>
                <div class="card-body">
                    @if ($booking->customer)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $booking->customer->image ? asset($booking->customer->image) : asset('assets/images/avatar/dummy-avatar.jpg') }}"
                                class="rounded-circle border" width="55" height="55" style="object-fit: cover;">
                            <div>
                                <h6 class="mb-1"><a href="{{ route('customer.show', $booking->customer->id) }}">{{ $booking->customer->name }}</a></h6>
                                <span class="text-muted small"><i class="ri-phone-line me-1"></i>{{ $booking->customer->mobile ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="fs-12 d-flex flex-column gap-2 text-muted mt-3">
                            <div><strong class="text-dark">Email:</strong> {{ $booking->customer->email }}</div>
                            <div><strong class="text-dark">City:</strong> {{ $booking->customer->city ?? 'N/A' }}</div>
                            <div><strong class="text-dark">Address:</strong> {{ $booking->customer->address ?? 'N/A' }}</div>
                        </div>
                    @else
                        <div class="text-muted">No customer linked.</div>
                    @endif
                </div>
            </div>

            <!-- Amount Card -->
            <div class="card mb-4 border border-primary bg-primary bg-opacity-10 shadow-none">
                <div class="card-body p-3">
                    <span class="text-primary d-block fs-11">Total Amount</span>
                    <h2 class="mb-0 text-primary fw-bold">₹{{ number_format($booking->amount, 2) }}</h2>
                </div>
            </div>

            <!-- Quick Action controls -->
            <div class="card mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Lifecycle Action</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($booking->status === 'pending')
                            <form action="{{ route('booking.update', $booking->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="pickup_location" value="{{ $booking->pickup_location }}">
                                <input type="hidden" name="drop_location" value="{{ $booking->drop_location }}">
                                <input type="hidden" name="shifting_date" value="{{ $booking->shifting_date }}">
                                <input type="hidden" name="shifting_time" value="{{ $booking->shifting_time }}">
                                <input type="hidden" name="amount" value="{{ $booking->amount }}">
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-primary w-100"><i class="ri-check-double-line me-1"></i>Confirm Booking</button>
                            </form>
                        @endif

                        @if ($booking->status === 'confirmed')
                            <form action="{{ route('booking.update', $booking->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="pickup_location" value="{{ $booking->pickup_location }}">
                                <input type="hidden" name="drop_location" value="{{ $booking->drop_location }}">
                                <input type="hidden" name="shifting_date" value="{{ $booking->shifting_date }}">
                                <input type="hidden" name="shifting_time" value="{{ $booking->shifting_time }}">
                                <input type="hidden" name="amount" value="{{ $booking->amount }}">
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="btn btn-info text-white w-100"><i class="ri-roadster-line me-1"></i>Start Shifting (In Progress)</button>
                            </form>
                        @endif

                        @if ($booking->status === 'in_progress')
                            <form action="{{ route('booking.complete', $booking->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100"><i class="ri-checkbox-circle-line me-1"></i>Mark Shifting Completed</button>
                            </form>
                        @endif

                        @if (in_array($booking->status, ['pending', 'confirmed', 'in_progress']))
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100"><i class="ri-close-circle-line me-1"></i>Cancel Booking</button>
                            </form>
                        @endif

                        @if (in_array($booking->status, ['completed', 'cancelled']))
                            <div class="alert alert-light text-center mb-0" role="alert">
                                No actions available. The booking lifecycle is completed.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
