<div id="drawer-form-content">
    <form id="editBookingForm" action="{{ route('booking.update', $booking->id) }}" method="POST"
        class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-12 bg-light p-2 rounded mb-2">
                <span class="text-muted d-block fs-11">Customer Reference</span>
                <span class="fw-semibold text-primary fs-13">{{ $booking->customer ? $booking->customer->name : 'N/A' }}</span>
                <span class="font-monospace text-muted small ms-2">({{ $booking->booking_number }})</span>
            </div>

            <!-- Shifting details -->
            <div class="col-md-6">
                <label for="edit_shifting_date" class="form-label">Shifting Date</label>
                <input type="date" class="form-control" id="edit_shifting_date" name="shifting_date"
                    value="{{ $booking->shifting_date }}" required>
                <div class="invalid-feedback">Please select a valid date.</div>
            </div>

            <div class="col-md-6">
                <label for="edit_shifting_time" class="form-label">Shifting Time</label>
                <input type="time" class="form-control" id="edit_shifting_time" name="shifting_time"
                    value="{{ $booking->shifting_time ? date('H:i', strtotime($booking->shifting_time)) : '' }}" required>
                <div class="invalid-feedback">Please select a shifting time.</div>
            </div>

            <div class="col-md-6">
                <label for="edit_amount" class="form-label">Amount (₹)</label>
                <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount"
                    value="{{ $booking->amount }}" required>
                <div class="invalid-feedback">Please enter a valid amount.</div>
            </div>

            <div class="col-md-6">
                <label for="edit_status" class="form-label">Status</label>
                <select class="form-select" id="edit_status" name="status" required>
                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="in_progress" {{ $booking->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <div class="invalid-feedback">Please select a status.</div>
            </div>

            <div class="col-md-6">
                <label for="edit_tracking_status" class="form-label">Tracking Status</label>
                <select class="form-select" id="edit_tracking_status" name="tracking_status" required>
                    <option value="pending_confirmation" {{ $booking->tracking_status === 'pending_confirmation' ? 'selected' : '' }}>Booking Confirmation Pending</option>
                    <option value="confirmed" {{ $booking->tracking_status === 'confirmed' ? 'selected' : '' }}>Booking Confirmed</option>
                    <option value="trip_started" {{ $booking->tracking_status === 'trip_started' ? 'selected' : '' }}>Trip Started</option>
                    <option value="shifting_started" {{ $booking->tracking_status === 'shifting_started' ? 'selected' : '' }}>Shifting Started</option>
                    <option value="pickup_completed" {{ $booking->tracking_status === 'pickup_completed' ? 'selected' : '' }}>Pickup Completed</option>
                    <option value="completed" {{ $booking->tracking_status === 'completed' ? 'selected' : '' }}>Shifting Completed</option>
                </select>
                <div class="invalid-feedback">Please select a tracking status.</div>
            </div>

            <hr class="my-3">

            <!-- Pickup Location Details -->
            <div class="col-md-12">
                <label for="edit_pickup_location" class="form-label">Pickup Location</label>
                <input type="text" class="form-control" id="edit_pickup_location" name="pickup_location"
                    value="{{ $booking->pickup_location }}" required>
                <div class="invalid-feedback">Please enter pickup location.</div>
            </div>
            
            <div class="col-md-6">
                <label for="edit_pickup_latitude" class="form-label fs-11 text-muted">Pickup Latitude</label>
                <input type="number" step="0.0000000001" class="form-control form-control-sm" id="edit_pickup_latitude" name="pickup_latitude"
                    value="{{ $booking->pickup_latitude }}">
            </div>
            
            <div class="col-md-6">
                <label for="edit_pickup_longitude" class="form-label fs-11 text-muted">Pickup Longitude</label>
                <input type="number" step="0.0000000001" class="form-control form-control-sm" id="edit_pickup_longitude" name="pickup_longitude"
                    value="{{ $booking->pickup_longitude }}">
            </div>

            <!-- Drop Location Details -->
            <div class="col-md-12">
                <label for="edit_drop_location" class="form-label">Drop Location</label>
                <input type="text" class="form-control" id="edit_drop_location" name="drop_location"
                    value="{{ $booking->drop_location }}" required>
                <div class="invalid-feedback">Please enter drop location.</div>
            </div>
            
            <div class="col-md-6">
                <label for="edit_drop_latitude" class="form-label fs-11 text-muted">Drop Latitude</label>
                <input type="number" step="0.0000000001" class="form-control form-control-sm" id="edit_drop_latitude" name="drop_latitude"
                    value="{{ $booking->drop_latitude }}">
            </div>
            
            <div class="col-md-6">
                <label for="edit_drop_longitude" class="form-label fs-11 text-muted">Drop Longitude</label>
                <input type="number" step="0.0000000001" class="form-control form-control-sm" id="edit_drop_longitude" name="drop_longitude"
                    value="{{ $booking->drop_longitude }}">
            </div>
        </div>
    </form>
</div>
