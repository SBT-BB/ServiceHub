@extends('partials.layouts.master')

@section('title', 'Booking Management | ServiceHub')

@section('sub-title', 'Bookings')
@section('pagetitle', 'Bookings')
@section('buttonTitle', '+ New Booking')
@section('buttonLink', route('booking.create'))
@section('isDrawer', 'false')

@section('content')

    {{-- Statistics widgets --}}
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

    {{-- Booking Data Table --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="booking-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>Booking No</th>
                            <th>Customer Name</th>
                            <th>Customer Mobile</th>
                            <th>Shifting Date</th>
                            <th>Amount</th>
                            <th>Vendor</th>
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
    @php
        $vendorOptions = $vendors->map(fn($v) => ['id' => $v->id, 'name' => $v->name])->values()->toJson();
    @endphp

    <script>
        var VENDORS = {!! $vendorOptions !!};
        var ASSIGN_URL  = '{{ url('booking') }}';
        var CSRF_TOKEN  = '{{ csrf_token() }}';

        function buildVendorSelect(currentId, bookingId, acceptanceStatus) {
            // If the vendor has already accepted, disable the select to prevent accidental reassignment
            var disabled = (acceptanceStatus === 'accepted') ? ' disabled' : '';
            
            var html = '<div class="d-flex flex-column gap-1">';
            html += '<select class="form-select form-select-sm assign-vendor" data-booking-id="' + bookingId + '"' + disabled + ' style="min-width:150px;">';
            html += '<option value="">— None —</option>';
            VENDORS.forEach(function (v) {
                var sel = (parseInt(v.id) === parseInt(currentId)) ? ' selected' : '';
                html += '<option value="' + v.id + '"' + sel + '>' + v.name + '</option>';
            });
            html += '</select>';

            if (currentId) {
                var badgeClass = 'bg-warning-focus text-warning';
                var statusText = 'Pending';
                if (acceptanceStatus === 'accepted') {
                    badgeClass = 'bg-success-focus text-success';
                    statusText = 'Accepted';
                } else if (acceptanceStatus === 'rejected') {
                    badgeClass = 'bg-danger-focus text-danger';
                    statusText = 'Rejected';
                }
                html += '<span class="badge ' + badgeClass + ' d-inline-block text-center fs-10" style="width: fit-content;">' + statusText + '</span>';
            }
            html += '</div>';
            return html;
        }

        $(document).ready(function () {

            var table = initDataTable('#booking-table', '{{ route('booking.index') }}', [
                { data: 'booking_number',  name: 'booking_number' },
                { data: 'customer_name',   name: 'customer_name',   orderable: false },
                { data: 'customer_mobile', name: 'customer_mobile', orderable: false },
                { data: 'shifting_date',   name: 'shifting_date' },
                { data: 'amount',          name: 'amount' },
                {
                    data: 'vendor_id',
                    name: 'vendor_id',
                    orderable: false,
                    searchable: false,
                    @can('assign vendor to booking')
                    render: function (data, type, row) {
                        return buildVendorSelect(data, row.id, row.vendor_acceptance_status);
                    }
                    @else
                    render: function (data, type, row) {
                        if (row.vendor_name) {
                            var statusBadge = '';
                            if (row.vendor_acceptance_status === 'accepted') {
                                statusBadge = ' <span class="badge bg-success-focus text-success">Accepted</span>';
                            } else {
                                statusBadge = ' <span class="badge bg-warning-focus text-warning">Pending</span>';
                            }
                            return '<span class="fw-semibold">' + row.vendor_name + '</span>' + statusBadge;
                        }
                        return '<span class="text-muted fs-12">— None —</span>';
                    }
                    @endcan
                },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]);

            {{-- AJAX Vendor Assign --}}
            @can('assign vendor to booking')
            $(document).on('change', '.assign-vendor', function () {
                var $select      = $(this);
                var bookingId    = $select.data('booking-id');
                var vendorId     = $select.val();
                var prevVal      = $select.data('prev') || '';
                $select.data('prev', vendorId);
                $select.prop('disabled', true);

                $.ajax({
                    url:    ASSIGN_URL + '/' + bookingId + '/assign-vendor',
                    method: 'POST',
                    data: { vendor_id: vendorId, _token: CSRF_TOKEN },
                    success: function (resp) {
                        $select.prop('disabled', false);
                        showToast(resp.message || 'Vendor assigned!', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        $select.prop('disabled', false).val(prevVal);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message : 'Assignment failed.';
                        showToast(msg, 'danger');
                    }
                });
            });
            @endcan

            {{-- Status action forms --}}
            $(document).on('submit', '.status-action-form', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn  = $form.find('button');
                var orig  = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                $.ajax({
                    url:    $form.attr('action'),
                    method: 'POST',
                    data:   $form.serialize(),
                    success: function (response) {
                        showToast(response.message || 'Status updated!');
                        setTimeout(function () { window.location.reload(); }, 800);
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message : 'Action failed.';
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
