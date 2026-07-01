@extends('partials.layouts.master')

@section('title', 'Edit Booking #' . $booking->booking_number . ' | Bhanderi Packers and Partner')
@section('sub-title', 'Edit Booking')
@section('pagetitle', 'Bookings')

@section('content')
<div class="row g-4 booking-modern-shell">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('booking.index') }}">Bookings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('booking.show', $booking->id) }}">{{ $booking->booking_number }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>

    <form id="editBookingForm" action="{{ route('booking.update', $booking->id) }}" method="POST" novalidate class="w-100">
    @csrf
    @method('PUT')

    <div class="col-12">
        <div class="row g-3">

    <div class="col-12">
        <div class="card border-0 shadow-sm tab-nav-card">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill gap-2 booking-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-tab="customer-tab"><i class="ri-user-line me-1"></i>Customer & Status</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-tab="location-tab"><i class="ri-map-pin-line me-1"></i>Locations</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-tab="items-tab"><i class="ri-box-3-line me-1"></i>Items</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-tab="addons-tab"><i class="ri-service-line me-1"></i>Add-ons</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-tab="charges-tab"><i class="ri-money-rupee-circle-line me-1"></i>Charges</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- LEFT COLUMN — Form Inputs --}}
    <div class="col-xl-8">

        {{-- A: Customer & Schedule --}}
        <div class="card mb-3 border-0 shadow-sm tab-panel modern-card" id="customer-tab">
            <div class="card-header d-flex align-items-center gap-2 bg-white border-0 py-3">
                <span class="badge bg-primary rounded-circle p-2"><i class="ri-user-line fs-14"></i></span>
                <h6 class="card-title mb-0">Customer & Schedule</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="customer_search" class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <select class="form-control" id="customer_search" name="customer_id" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $booking->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->mobile ?? 'No Mobile' }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a customer.</div>
                    </div>
                    <div class="col-md-3">
                        <label for="shifting_date" class="form-label fw-semibold">Shifting Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="shifting_date" name="shifting_date" value="{{ $booking->shifting_date }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="shifting_time" class="form-label fw-semibold">Shifting Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="shifting_time" name="shifting_time" value="{{ $booking->shifting_time ? date('H:i', strtotime($booking->shifting_time)) : '' }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ $booking->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tracking_status" class="form-label fw-semibold">Tracking Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="tracking_status" name="tracking_status" required>
                            <option value="pending_confirmation" {{ $booking->tracking_status === 'pending_confirmation' ? 'selected' : '' }}>Booking Confirmation Pending</option>
                            <option value="confirmed" {{ $booking->tracking_status === 'confirmed' ? 'selected' : '' }}>Booking Confirmed</option>
                            <option value="trip_started" {{ $booking->tracking_status === 'trip_started' ? 'selected' : '' }}>Trip Started</option>
                            <option value="shifting_started" {{ $booking->tracking_status === 'shifting_started' ? 'selected' : '' }}>Shifting Started</option>
                            <option value="pickup_completed" {{ $booking->tracking_status === 'pickup_completed' ? 'selected' : '' }}>Pickup Completed</option>
                            <option value="completed" {{ $booking->tracking_status === 'completed' ? 'selected' : '' }}>Shifting Completed</option>
                            <option value="cancelled" {{ $booking->tracking_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- B: Locations --}}
        <div class="card mb-3 border-0 shadow-sm tab-panel modern-card d-none" id="location-tab">
            <div class="card-header d-flex align-items-center gap-2 bg-white border-0 py-3">
                <span class="badge bg-success rounded-circle p-2"><i class="ri-map-pin-line fs-14"></i></span>
                <h6 class="card-title mb-0">Pickup & Drop</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    {{-- Pickup --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold text-success"><i class="ri-map-pin-user-fill me-1"></i>Pickup Details</label>
                    </div>
                    <div class="col-12">
                        <label for="pickup_location" class="form-label">Pickup Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pickup_location" name="pickup_location" value="{{ $booking->pickup_location }}" placeholder="Full pickup address" required>
                    </div>
                    <div class="col-md-4">
                        <label for="pickup_contact_name" class="form-label">Contact Name</label>
                        <input type="text" class="form-control" id="pickup_contact_name" name="pickup_contact_name" value="{{ $booking->pickup_contact_name }}" placeholder="Contact at pickup">
                    </div>
                    <div class="col-md-4">
                        <label for="pickup_contact_mobile" class="form-label">Contact Mobile</label>
                        <input type="text" class="form-control" id="pickup_contact_mobile" name="pickup_contact_mobile" value="{{ $booking->pickup_contact_mobile }}" placeholder="10-digit mobile">
                    </div>
                    <div class="col-md-2">
                        <label for="pickup_latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="pickup_latitude" name="pickup_latitude" value="{{ $booking->pickup_latitude }}" placeholder="23.0225">
                    </div>
                    <div class="col-md-2">
                        <label for="pickup_longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="pickup_longitude" name="pickup_longitude" value="{{ $booking->pickup_longitude }}" placeholder="72.5714">
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    {{-- Drop --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold text-danger"><i class="ri-map-pin-5-fill me-1"></i>Drop Details</label>
                    </div>
                    <div class="col-12">
                        <label for="drop_location" class="form-label">Drop Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="drop_location" name="drop_location" value="{{ $booking->drop_location }}" placeholder="Full drop address" required>
                    </div>
                    <div class="col-md-4">
                        <label for="drop_contact_name" class="form-label">Contact Name</label>
                        <input type="text" class="form-control" id="drop_contact_name" name="drop_contact_name" value="{{ $booking->drop_contact_name }}" placeholder="Contact at destination">
                    </div>
                    <div class="col-md-4">
                        <label for="drop_contact_mobile" class="form-label">Contact Mobile</label>
                        <input type="text" class="form-control" id="drop_contact_mobile" name="drop_contact_mobile" value="{{ $booking->drop_contact_mobile }}" placeholder="10-digit mobile">
                    </div>
                    <div class="col-md-2">
                        <label for="drop_latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="drop_latitude" name="drop_latitude" value="{{ $booking->drop_latitude }}" placeholder="23.0338">
                    </div>
                    <div class="col-md-2">
                        <label for="drop_longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="drop_longitude" name="drop_longitude" value="{{ $booking->drop_longitude }}" placeholder="72.5850">
                    </div>
                </div>
            </div>
        </div>

        {{-- C: Item Selector --}}
        <div class="card mb-3 tab-panel modern-card d-none" id="items-tab">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning rounded-circle p-2"><i class="ri-box-3-line fs-14"></i></span>
                    <h6 class="card-title mb-0">Select Items to Shift</h6>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex gap-1 align-items-center fs-11 text-muted">
                        <span class="badge bg-info-subtle text-info border">S=1</span>
                        <span class="badge bg-warning-subtle text-warning border">M=3</span>
                        <span class="badge bg-danger-subtle text-danger border">L=5</span>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold text-dark fs-12">Score:</span>
                        <span id="totalScoreDisplay" class="badge bg-primary fs-12 px-2">0</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($itemSizes as $size)
                <div class="px-4 {{ $loop->first ? 'pt-3' : 'pt-2' }} pb-2">
                    <p class="text-muted fw-semibold fs-12 mb-2 text-uppercase letter-spacing-1">
                        <i class="ri-checkbox-blank-circle-fill text-primary me-1" style="font-size:8px;"></i>
                        {{ $size->size_name }} Items <span class="badge bg-primary-subtle text-primary ms-1">{{ $size->volume_score }} points each</span>
                    </p>
                    <div class="row g-2">
                        @foreach($size->items as $item)
                        @php
                            $bookedItem = $booking->items->firstWhere('id', $item->id);
                            $qty = $bookedItem ? $bookedItem->pivot->quantity : 0;
                        @endphp
                        <div class="col-md-4 col-6">
                            <div class="item-card border rounded p-2 d-flex justify-content-between align-items-center {{ $qty > 0 ? 'has-qty' : '' }}" data-item-id="{{ $item->id }}" data-volume="{{ $size->volume_score }}">
                                <span class="fs-12 fw-medium text-truncate me-2" title="{{ $item->item_name }}">{{ $item->item_name }}</span>
                                <div class="qty-control d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary p-0 qty-btn" style="width:22px;height:22px;line-height:1;" data-action="minus" data-item="{{ $item->id }}">−</button>
                                    <span class="qty-display fs-12 fw-bold mx-1" style="min-width:18px;text-align:center;">{{ $qty }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary p-0 qty-btn" style="width:22px;height:22px;line-height:1;" data-action="plus" data-item="{{ $item->id }}">+</button>
                                </div>
                                <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}" {{ $qty > 0 ? '' : 'disabled' }} class="qty-input">
                                <input type="hidden" name="items[{{ $item->id }}][quantity]" value="{{ $qty }}" {{ $qty > 0 ? '' : 'disabled' }} class="qty-value">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @if(!$loop->last)
                    <hr class="mx-4 my-2">
                @endif
                @endforeach
            </div>
        </div>

        {{-- D: Add-On Services --}}
        <div class="card mb-3 border-0 shadow-sm modern-card tab-panel d-none" id="addons-tab">
            <div class="card-header d-flex align-items-center justify-content-between bg-white border-0 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info rounded-circle p-2"><i class="ri-service-line fs-14"></i></span>
                    <h6 class="card-title mb-0">Add-On Services</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($addons as $addon)
                    @php
                        $hasAddon = $booking->addOns->contains('id', $addon->id);
                    @endphp
                    <div class="col-md-6">
                        <div class="addon-card border rounded p-3 d-flex align-items-center gap-3 cursor-pointer {{ $hasAddon ? 'selected' : '' }}" id="addon-wrap-{{ $addon->id }}">
                            <div class="form-check mb-0">
                                <input class="form-check-input addon-checkbox" type="checkbox"
                                    id="addon_{{ $addon->id }}"
                                    name="addons[]"
                                    value="{{ $addon->id }}"
                                    data-price="{{ $addon->price }}"
                                    {{ $hasAddon ? 'checked' : '' }}>
                            </div>
                            <label class="form-check-label d-flex justify-content-between align-items-center w-100 cursor-pointer" for="addon_{{ $addon->id }}">
                                <span class="fs-13 fw-medium">{{ $addon->addon_name }}</span>
                                <span class="badge bg-success-subtle text-success fs-12 ms-2 text-nowrap">+₹{{ number_format($addon->price, 0) }}</span>
                            </label>
                        </div>
                    </div>
                    @endforeach

                    {{-- Floor Count --}}
                    <div class="col-12 mt-2">
                        <label for="floors" class="form-label fw-semibold">
                            <i class="ri-building-line me-1"></i>Floors to Carry Without Lift
                            <span class="text-muted fs-12">(total floors at pickup + drop)</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="number" class="form-control" id="floors" name="floors" min="0" max="20" value="{{ $booking->floors }}" style="max-width:120px;">
                            <span class="text-muted fs-12">₹150 per floor will be charged</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- E: Extra Charges --}}
        <div class="card mb-3 border-0 shadow-sm modern-card tab-panel d-none" id="charges-tab">
            <div class="card-header d-flex align-items-center gap-2 bg-white border-0 py-3">
                <span class="badge bg-danger rounded-circle p-2"><i class="ri-money-rupee-circle-line fs-14"></i></span>
                <h6 class="card-title mb-0">Extra Charges</h6>
                <span class="ms-auto badge bg-danger-subtle text-danger fs-11">Optional — Manually entered</span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <div class="alert alert-info border-0 py-2 px-3 fs-12 mb-0 rounded-3">
                            <i class="ri-information-line me-1"></i>
                            This Charges <strong>are not calculated automatically.</strong> Please enter them manually, and they will be included in the total amount.
                        </div>
                    </div>

                    {{-- Loading Charge --}}
                    <div class="col-md-6">
                        <label for="loading_charge" class="form-label fw-semibold">
                            <i class="ri-upload-2-line me-1 text-primary"></i>Loading Charge
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control extra-charge-input" id="loading_charge"
                                name="loading_charge" min="0" step="0.01" value="{{ $booking->loading_charge }}"
                                placeholder="0.00">
                        </div>
                    </div>

                    {{-- Unloading Charge --}}
                    <div class="col-md-6">
                        <label for="unloading_charge" class="form-label fw-semibold">
                            <i class="ri-download-2-line me-1 text-success"></i>Unloading Charge
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control extra-charge-input" id="unloading_charge"
                                name="unloading_charge" min="0" step="0.01" value="{{ $booking->unloading_charge }}"
                                placeholder="0.00">
                        </div>
                    </div>

                    {{-- Packing Charge --}}
                    <div class="col-md-6">
                        <label for="packing_charge" class="form-label fw-semibold">
                            <i class="ri-box-1-line me-1 text-warning"></i>Packing Charge
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control extra-charge-input" id="packing_charge"
                                name="packing_charge" min="0" step="0.01" value="{{ $booking->packing_charge }}"
                                placeholder="0.00">
                        </div>
                    </div>

                    {{-- Labour Charge --}}
                    <div class="col-md-6">
                        <label for="labour_charge" class="form-label fw-semibold">
                            <i class="ri-group-line me-1 text-info"></i>Labour Charge
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control extra-charge-input" id="labour_charge"
                                name="labour_charge" min="0" step="0.01" value="{{ $booking->labour_charge }}"
                                placeholder="0.00">
                        </div>
                    </div>

                    {{-- Live Extra Charges Summary --}}
                    <div class="col-12">
                        <div class="rounded-3 p-3 border" style="background:#f8fafc;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-13 fw-semibold text-dark"><i class="ri-calculator-line me-1"></i>Extra Charges Total</span>
                                <span class="fs-14 fw-bold text-danger" id="extraChargesTotalDisplay">₹0</span>
                            </div>
                            <div class="fs-12 text-muted mt-1">This amount will be added to the system-calculated price</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN — Live Pricing Panel --}}
    <div class="col-xl-4">
        <div class="card sticky-top border-0 shadow-sm modern-sidebar" style="top:80px;">
            <div class="card-header bg-primary text-white d-flex align-items-center gap-2 py-3">
                <i class="ri-price-tag-3-line fs-18"></i>
                <h6 class="card-title mb-0 text-white">Live Price Calculator</h6>
            </div>
            <div class="card-body p-3">

                <div class="pricing-mini-card mb-2 p-2 rounded">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-12 text-muted">Volume Score</span>
                        <span class="fs-12 fw-bold text-primary" id="scoreLabel">0 / 310 pts</span>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-primary" id="scoreBar" role="progressbar" style="width:0%"></div>
                    </div>
                </div>

                <div class="pricing-mini-card mb-2 p-2 rounded">
                    <div class="fs-12 text-muted mb-1">Category</div>
                    <div id="categoryDetected" class="fw-bold text-primary fs-14">—</div>
                    <div class="fs-12 text-muted mt-1"><i class="ri-truck-line me-1"></i><span id="vehicleDetected">No vehicle assigned</span></div>
                </div>

                <div id="surveyAlert" class="alert alert-danger d-none p-2 fs-12 mb-2" role="alert">
                    <i class="ri-error-warning-line me-1"></i>
                    <strong>Survey Required!</strong> Volume is too large for auto-quote.
                </div>

                <div id="pricePanel" class="small pricing-mini-card p-2 rounded">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Base Fare</span>
                        <span class="fs-13 fw-semibold" id="baseFareVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Distance</span>
                        <span class="fs-13 fw-semibold" id="distanceVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Add-ons</span>
                        <span class="fs-13 fw-semibold" id="addonVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Floor</span>
                        <span class="fs-13 fw-semibold" id="floorVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" id="extraChargesRow" style="display:none!important;">
                        <span class="fs-13 text-danger">Extra Charges</span>
                        <span class="fs-13 fw-semibold text-danger" id="extraChargesVal">₹0</span>
                    </div>
                    <div class="text-muted fs-12 mt-2 mb-2" id="pricingHintBox">
                        <div><i class="ri-information-line me-1"></i> Charges update instantly as you fill the form.</div>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" id="weekendRow" style="display:none!important;">
                        <span class="fs-13 text-warning">Weekend</span>
                        <span class="fs-13 fw-semibold text-warning" id="weekendVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" id="monthEndRow" style="display:none!important;">
                        <span class="fs-13 text-danger">Month-End</span>
                        <span class="fs-13 fw-semibold text-danger" id="monthEndVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" id="peakTimeRow" style="display:none!important;">
                        <span class="fs-13 text-info">Peak-Time</span>
                        <span class="fs-13 fw-semibold text-info" id="peakTimeVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 mt-1">
                        <span class="fs-14 fw-bold">Total</span>
                        <span class="fs-15 fw-bold text-primary" id="totalAmountVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted">
                        <span class="fs-12">Advance</span>
                        <span class="fs-12" id="advanceVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted">
                        <span class="fs-12">Distance</span>
                        <span class="fs-12" id="distanceKmVal">0 km</span>
                    </div>
                </div>

                <button type="button" id="calcPriceBtn" class="btn btn-primary w-100 mt-2 btn-sm">
                    <i class="ri-refresh-line me-1"></i> Calculate Price
                </button>
                <div id="calcSpinner" class="text-center mt-2 d-none">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <span class="fs-12 ms-1">Calculating...</span>
                </div>

                <hr class="my-2">

                <button type="submit" id="submitBookingBtn" class="btn btn-success w-100 btn-lg">
                    <i class="ri-save-line me-1"></i> Update Booking
                </button>
                <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>

            </div>
        </div>
    </div>

    </div>
    </div>
    </div>

    </form>
</div>
@endsection

@section('js')
<style>
    .item-card { transition: all 0.2s ease; cursor: default; padding: 0.6rem; }
    .item-card.has-qty { background: #f0f7ff; border-color: #0d6efd !important; }
    .addon-card { transition: all 0.2s ease; cursor: pointer; padding: 0.7rem 0.8rem; }
    .addon-card.selected { background: #f0fff4; border-color: #198754 !important; }
    .card-header { padding-bottom: 0.6rem; }
    .form-label { margin-bottom: 0.35rem; font-size: 0.9rem; }
    .form-control, .form-select { font-size: 0.92rem; }
    .tab-nav-card, .modern-card, .modern-sidebar {
        border-radius: 18px;
    }
    .booking-tabs .nav-link {
        border-radius: 999px;
        color: #475569;
        font-weight: 600;
        padding: 0.65rem 0.95rem;
        transition: all 0.2s ease;
    }
    .booking-tabs .nav-link.active {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
    }
    .modern-card .card-header {
        border-bottom: 1px solid #f1f5f9;
    }
    .modern-sidebar .card-header {
        border-radius: 18px 18px 0 0;
    }
    .pricing-mini-card {
        background: #f8fafc;
        border: 1px solid #eef2f7;
    }
    .booking-tabs .nav-link {
        border-radius: 999px;
        color: #475569;
        font-weight: 600;
        padding: 0.6rem 0.9rem;
    }
    .booking-tabs .nav-link.active {
        background: #2563eb;
        color: #fff;
    }
</style>
<script>
$(document).ready(function () {

    // ── Init ─────────────────────────────────────────────────────────────
    var today = new Date().toISOString().split('T')[0];
    var pricingConfig = {
        perKmRate: {{ $pricingConfig['per_km_rate'] }},
        perFloorCharge: {{ $pricingConfig['per_floor_charge'] }},
        weekendPercent: {{ $pricingConfig['weekend_surge_percentage'] }},
        monthEndPercent: {{ $pricingConfig['month_end_surge_percentage'] }},
        peakPercent: {{ $pricingConfig['peak_time_surge_percentage'] }},
        peakStart: '{{ $pricingConfig['peak_time_start'] }}',
        peakEnd: '{{ $pricingConfig['peak_time_end'] }}',
        advancePercent: {{ $pricingConfig['advance_payment_percentage'] }}
    };

    $('#shifting_date').attr('min', today);

    $('#customer_search').select2({
        width: '100%',
        placeholder: 'Search by name, mobile or email',
        allowClear: true
    });

    $('.booking-tabs .nav-link').on('click', function () {
        $('.booking-tabs .nav-link').removeClass('active');
        $(this).addClass('active');
        $('.tab-panel').addClass('d-none');
        $('#' + $(this).data('tab')).removeClass('d-none');
    });

    // ── Volume Score Tracker ─────────────────────────────────────────────
    var itemQtys = {
        @foreach($booking->items as $itm)
            '{{ $itm->id }}': {{ $itm->pivot->quantity }},
        @endforeach
    };

    function getTotalScore() {
        var total = 0;
        $('.item-card').each(function () {
            var id  = $(this).data('item-id');
            var vol = parseInt($(this).data('volume')) || 0;
            var qty = itemQtys[id] || 0;
            total += vol * qty;
        });
        return total;
    }

    function updateScoreDisplay() {
        var score = getTotalScore();
        var max   = 310;
        var pct   = Math.min(100, Math.round(score / max * 100));

        $('#totalScoreDisplay').text(score);
        $('#scoreLabel').text(score + ' / ' + max + ' pts');
        $('#scoreBar').css('width', pct + '%');

        if (score > max) {
            $('#scoreBar').removeClass('bg-primary bg-warning').addClass('bg-danger');
            $('#surveyAlert').removeClass('d-none');
            $('#categoryDetected').text('Survey Required');
            $('#vehicleDetected').text('Physical survey needed');
        } else {
            $('#scoreBar').removeClass('bg-danger bg-warning').addClass('bg-primary');
            $('#surveyAlert').addClass('d-none');
        }
    }

    // Initialize display with loaded score
    updateScoreDisplay();

    // ── Quantity Buttons ─────────────────────────────────────────────────
    $(document).on('click', '.qty-btn', function (e) {
        e.preventDefault();
        var itemId = $(this).data('item');
        var action = $(this).data('action');
        var card   = $(this).closest('.item-card');
        var curr   = itemQtys[itemId] || 0;

        if (action === 'plus') curr = Math.min(curr + 1, 50);
        if (action === 'minus') curr = Math.max(curr - 1, 0);

        itemQtys[itemId] = curr;
        card.find('.qty-display').text(curr);

        // Enable / disable hidden inputs so they're submitted
        var qtyInput = card.find('.qty-input');
        var qtyValue = card.find('.qty-value');
        if (curr > 0) {
            card.addClass('has-qty');
            qtyInput.prop('disabled', false);
            qtyValue.prop('disabled', false).val(curr);
        } else {
            card.removeClass('has-qty');
            qtyInput.prop('disabled', true);
            qtyValue.prop('disabled', true).val(0);
        }

        updateScoreDisplay();
        $('#calcPriceBtn').trigger('click');
    });

    // ── Add-On Card Toggle ───────────────────────────────────────────────
    $(document).on('change', '.addon-checkbox', function () {
        var wrap = $(this).closest('.addon-card');
        if ($(this).is(':checked')) wrap.addClass('selected');
        else wrap.removeClass('selected');
        $('#calcPriceBtn').trigger('click');
    });

    $(document).on('click', '.addon-card', function (e) {
        if (!$(e.target).is('input')) {
            $(this).find('.addon-checkbox').trigger('click');
        }
    });

    // ── Build AJAX payload ───────────────────────────────────────────────
    function buildPricingPayload() {
        var items = [];
        $.each(itemQtys, function (id, qty) {
            if (qty > 0) items.push({ id: id, quantity: qty });
        });

        var addons = [];
        $('.addon-checkbox:checked').each(function () {
            addons.push(parseInt($(this).val()));
        });

        return {
            items:             items,
            addons:            addons,
            pickup_latitude:   $('#pickup_latitude').val()  || null,
            pickup_longitude:  $('#pickup_longitude').val() || null,
            drop_latitude:     $('#drop_latitude').val()    || null,
            drop_longitude:    $('#drop_longitude').val()   || null,
            shifting_date:     $('#shifting_date').val()    || null,
            shifting_time:     $('#shifting_time').val()    || null,
            floors:            parseInt($('#floors').val()) || 0,
            _token:            '{{ csrf_token() }}'
        };
    }

    // ── Format currency ──────────────────────────────────────────────────
    function fmt(n) {
        return '₹' + parseFloat(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 0 });
    }

    // ── Calculate Price Button ───────────────────────────────────────────
    $('#calcPriceBtn').on('click', function () {
        $('#calcSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url:  '{{ route('admin.booking.ajax-pricing') }}',
            type: 'POST',
            data: JSON.stringify(buildPricingPayload()),
            contentType: 'application/json',
            success: function (data) {
                if (data.survey_required) {
                    $('#categoryDetected').text('Survey Required');
                    $('#vehicleDetected').text('Physical survey needed');
                    $('#surveyAlert').removeClass('d-none');
                    ['baseFareVal','distanceVal','addonVal','floorVal','weekendVal','monthEndVal','totalAmountVal','advanceVal']
                        .forEach(id => $('#'+id).text('—'));
                    return;
                }

                $('#surveyAlert').addClass('d-none');
                $('#categoryDetected').text(data.category_name || '—');
                $('#vehicleDetected').text(data.vehicle_name || 'No vehicle assigned');

                $('#baseFareVal').text(fmt(data.base_fare));
                $('#distanceVal').text(fmt(data.distance_charges));
                $('#addonVal').text(fmt(data.addon_charges));
                $('#floorVal').text(fmt(data.floor_charges));

                $('#baseFareRule').text(fmt(data.base_fare) + ' (category)');
                $('#pointRateRule').text(data.price_per_point > 0 ? '₹' + parseFloat(data.price_per_point).toLocaleString('en-IN') + '/point' : 'Not set');
                $('#distanceRule').text('₹' + parseFloat(pricingConfig.perKmRate).toLocaleString('en-IN') + '/km');
                $('#floorRule').text('₹' + parseFloat(pricingConfig.perFloorCharge).toLocaleString('en-IN') + '/floor');
                $('#weekendRule').text(parseFloat(pricingConfig.weekendPercent).toLocaleString('en-IN') + '%');
                $('#monthEndRule').text(parseFloat(pricingConfig.monthEndPercent).toLocaleString('en-IN') + '%');
                $('#peakRule').text(parseFloat(pricingConfig.peakPercent).toLocaleString('en-IN') + '% (' + pricingConfig.peakStart + '-' + pricingConfig.peakEnd + ')');
                $('#advanceRule').text(parseFloat(pricingConfig.advancePercent).toLocaleString('en-IN') + '%');

                var pricingHint = 'Base fare comes from the selected category, and point-based pricing adds ' + (data.price_per_point > 0 ? 'volume points × ₹' + parseFloat(data.price_per_point).toLocaleString('en-IN') + ' per point' : 'no extra point rate') + '. Floor charge adds ' + (data.floor_charges ? 'based on entered floors' : 'when floors are entered') + '.';
                $('#pricingHintBox').html('<div><i class="ri-information-line me-1"></i>' + pricingHint + '</div>');
                $('#totalAmountVal').text(fmt(data.total_amount));
                $('#advanceVal').text(fmt(data.advance_amount ?? (data.total_amount * 0.20)));
                $('#distanceKmVal').text((data.total_distance_km || 0) + ' km');

                if (data.weekend_charges > 0) {
                    $('#weekendVal').text(fmt(data.weekend_charges));
                    $('#weekendRow').show();
                } else { $('#weekendRow').hide(); }

                if (data.month_end_charges > 0) {
                    $('#monthEndVal').text(fmt(data.month_end_charges));
                    $('#monthEndRow').show();
                } else { $('#monthEndRow').hide(); }

                if (data.peak_time_charges > 0) {
                    $('#peakTimeVal').text(fmt(data.peak_time_charges));
                    $('#peakTimeRow').show();
                } else { $('#peakTimeRow').hide(); }

                // Add extra manual charges to displayed total
                updateExtraChargesDisplay(data.total_amount);
            },
            error: function () {
                showToast('Could not calculate pricing. Please check the form fields.', 'danger');
            },
            complete: function () {
                $('#calcSpinner').addClass('d-none');
                $('#calcPriceBtn').prop('disabled', false);
            }
        });
    });

    // ── Extra Charges Live Calculation ───────────────────────────────────
    var lastSystemTotal = 0;  // stores the last AJAX-fetched system total

    function getExtraChargesTotal() {
        var total = 0;
        $('.extra-charge-input').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        return total;
    }

    function updateExtraChargesDisplay(systemTotal) {
        if (systemTotal !== undefined) lastSystemTotal = systemTotal;
        var extra     = getExtraChargesTotal();
        var grandTotal = lastSystemTotal + extra;

        // Update the summary inside Charges tab
        $('#extraChargesTotalDisplay').text(fmt(extra));

        // Update sidebar extra charges row
        if (extra > 0) {
            $('#extraChargesVal').text(fmt(extra));
            $('#extraChargesRow').show();
        } else {
            $('#extraChargesRow').hide();
        }

        // Update grand total in sidebar (only if system total has been fetched)
        if (lastSystemTotal > 0 || extra > 0) {
            $('#totalAmountVal').text(fmt(grandTotal));
            var advPct = {{ $pricingConfig['advance_payment_percentage'] }};
            $('#advanceVal').text(fmt(grandTotal * advPct / 100));
        }
    }

    $(document).on('input change', '.extra-charge-input', function () {
        updateExtraChargesDisplay();
    });

    // ── Auto-calculate on date / floor / contact change ──────────────────
    $('#shifting_date, #floors, #pickup_latitude, #pickup_longitude, #drop_latitude, #drop_longitude').on('change input', function () {
        $('#calcPriceBtn').trigger('click');
    });

    // Initial calculation trigger
    $('#calcPriceBtn').trigger('click');

    // ── Form Submission ──────────────────────────────────────────────────
    $('#editBookingForm').on('submit', function (e) {
        e.preventDefault();

        var $btn = $('#submitBookingBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

        $.ajax({
            url:  $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.survey_required) {
                    showToast(res.message, 'warning');
                    $btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Update Booking');
                    return;
                }
                showToast(res.message || 'Booking updated!', 'success');
                setTimeout(() => window.location.href = '{{ route('booking.index') }}', 1000);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Update Booking');
                var res = xhr.responseJSON || {};
                if (res.errors) {
                    $.each(res.errors, function (field, msgs) {
                        showToast(msgs[0], 'danger');
                    });
                } else if (res.survey_required) {
                    showToast(res.message, 'warning');
                } else {
                    showToast(res.message || 'Something went wrong!', 'danger');
                }
            }
        });
    });

});
</script>
@endsection
