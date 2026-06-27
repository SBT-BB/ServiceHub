@extends('partials.layouts.master')

@section('title', 'Booking Management | Herozi')

@section('sub-title', 'Bookings')
@section('pagetitle', 'Bookings')
@section('buttonTitle', '+ New Booking')
@section('buttonLink', route('booking.create'))
@section('isDrawer', 'false')

@section('content')

    <!-- Statistics widgets -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover overflow-hidden mb-0">
                <div class="card-body hstack gap-3 p-3">
                    <div class="avatar avatar-item rounded-3 bg-primary bg-opacity-10 text-primary fs-20" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-calendar-todo-fill"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Total Bookings</span>
                        <h4 class="fw-semibold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover overflow-hidden mb-0">
                <div class="card-body hstack gap-3 p-3">
                    <div class="avatar avatar-item rounded-3 bg-info bg-opacity-10 text-info fs-20" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-checkbox-circle-fill"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Confirmed</span>
                        <h4 class="fw-semibold mb-0 text-info">{{ $stats['confirmed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover overflow-hidden mb-0">
                <div class="card-body hstack gap-3 p-3">
                    <div class="avatar avatar-item rounded-3 bg-success bg-opacity-10 text-success fs-20" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-checkbox-fill"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Completed</span>
                        <h4 class="fw-semibold mb-0 text-success">{{ $stats['completed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover overflow-hidden mb-0">
                <div class="card-body hstack gap-3 p-3">
                    <div class="avatar avatar-item rounded-3 bg-danger bg-opacity-10 text-danger fs-20" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-close-circle-fill"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Cancelled</span>
                        <h4 class="fw-semibold mb-0 text-danger">{{ $stats['cancelled'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Data Table -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="booking-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>Booking No</th>
                            <th>Customer Name</th>
                            <th>Customer Mobile</th>
                            <!-- <th>Pickup Location</th>
                            <th>Drop Location</th> -->
                            <th>Shifting Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = initDataTable('#booking-table', '{{ route('booking.index') }}', [
                {
                    data: 'booking_number',
                    name: 'booking_number'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name',
                    orderable: false
                },
                {
                    data: 'customer_mobile',
                    name: 'customer_mobile',
                    orderable: false
                },
                // {
                //     data: 'pickup_location',
                //     name: 'pickup_location'
                // },
                // {
                //     data: 'drop_location',
                //     name: 'drop_location'
                // },
                {
                    data: 'shifting_date',
                    name: 'shifting_date'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]);

            // Handle status transitions via Ajax
            $(document).on('submit', '.status-action-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $form.find('button');
                var originalHtml = $btn.html();
                
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serializeb(),
                    success: function(response) {
                        showToast(response.message || 'Status updated successfully!');
                        table.ajax.reload(null, false);
                        // Refresh widgets if needed (reload page after short delay is fine or dynamic update, let's keep it simple)
                        setTimeout(function() {
                            window.location.reload();
                        }, 800);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Action failed.';
                        showToast(msg, 'danger');
                    }
                });
            });

            @if (session('success'))
                showToast('{{ session('success') }}');
            @endif
        });
    </script>
@endsection
