@extends('partials.layouts.master')
@section('title', 'Pricing Settings | ServiceHub')
@section('sub-title', 'Pricing')
@section('pagetitle', 'Pricing Settings')
@section('content')
<div class="row g-4">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm border-0 mb-0">
            <div class="card-header bg-primary-subtle border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="ri-price-tag-3-line me-2"></i>Pricing Configuration</h5>
                        <p class="text-muted small mb-0">Manage global surcharges and dynamic booking price rules.</p>
                    </div>
                    <span class="badge bg-primary">Dynamic Pricing</span>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pricing.store') }}" method="POST">
                    @csrf

                    @php
                        $weekendSetting = $settings->get('weekend_surge_percentage');
                        $monthEndSetting = $settings->get('month_end_surge_percentage');
                        $perKmSetting = $settings->get('per_km_rate');
                        $perFloorSetting = $settings->get('per_floor_charge');
                        $advanceSetting = $settings->get('advance_payment_percentage');
                    @endphp

                    <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                        <h6 class="text-muted fw-semibold mb-3 mt-1"><i class="ri-weekend-line me-2"></i>Weekend Surcharge</h6>
                        <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Enable Weekend Surcharge</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="weekend_enabled" id="weekendEnabled" value="1" {{ optional($weekendSetting)->is_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="weekendEnabled">Enable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weekend Surcharge (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="weekend_percent" value="{{ optional($weekendSetting)->value ?? '' }}" placeholder="e.g. 10" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                        <h6 class="text-muted fw-semibold mb-3"><i class="ri-calendar-event-line me-2"></i>Month-End Surcharge</h6>
                        <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Enable Month-End Surcharge</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="month_end_enabled" id="monthEndEnabled" value="1" {{ optional($monthEndSetting)->is_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="monthEndEnabled">Enable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Month-End Surcharge (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="month_end_percent" value="{{ optional($monthEndSetting)->value ?? '' }}" placeholder="e.g. 15" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted fw-semibold mb-3">⏰ Peak Time Surcharge</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Enable Peak Time Surcharge</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="peak_time_enabled" id="peakTimeEnabled" value="1" {{ optional($settings->get('peak_time_surge_percentage'))->is_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="peakTimeEnabled">Enable</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Peak Time Surcharge (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="peak_time_percent" value="{{ optional($settings->get('peak_time_surge_percentage'))->value ?? '' }}" placeholder="e.g. 10" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" name="peak_time_start" value="{{ optional($settings->get('peak_time_start'))->value ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" name="peak_time_end" value="{{ optional($settings->get('peak_time_end'))->value ?? '' }}">
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted fw-semibold mb-3">🚗 Distance Pricing</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Per KM Charge (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="per_km_charge" value="{{ optional($perKmSetting)->value ?? '' }}" placeholder="e.g. 20">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Per Floor Charge (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="per_floor_charge" value="{{ optional($perFloorSetting)->value ?? '' }}" placeholder="e.g. 150">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Advance Payment (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="advance_percent" value="{{ optional($advanceSetting)->value ?? '' }}" placeholder="e.g. 20" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="ri-save-line me-1"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
