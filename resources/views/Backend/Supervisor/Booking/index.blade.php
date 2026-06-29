@extends('partials.layouts.master')

@section('title', 'My Assigned Bookings | ServiceHub')
@section('sub-title', 'Bookings')
@section('pagetitle', 'Bookings')
@section('isDrawer', 'false')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0"><i class="ri-user-star-line me-2 text-primary"></i>Assigned Booking Requests</h5>
                </div>
                <div class="card-body">
                    <table id="supervisor-booking-table" class="table-hover align-middle table table-nowrap w-100">
                        <thead class="bg-light bg-opacity-30">
                            <tr>
                                <th>Booking No</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Shifting Date</th>
                                <th>Amount</th>
                                <th>Vendor</th>
                                <th>My Status</th>
                                <th>Shifting Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    var RESPOND_URL = '{{ url('supervisor/booking') }}';
    var CSRF_TOKEN  = '{{ csrf_token() }}';

    $(document).ready(function () {
        var table = initDataTable('#supervisor-booking-table', '{{ route('supervisor.booking.index') }}', [
            { data: 'booking_number', name: 'booking_number' },
            { data: 'customer_name', name: 'customer_name', orderable: false },
            { data: 'customer_mobile', name: 'customer_mobile', orderable: false },
            { data: 'shifting_date', name: 'shifting_date' },
            { data: 'amount', name: 'amount' },
            { data: 'vendor_name', name: 'vendor_name', orderable: false },
            { data: 'acceptance_status', name: 'acceptance_status', orderable: false, searchable: false },
            { data: 'shifting_status', name: 'shifting_status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]);

        // Accept button click
        $(document).on('click', '.btn-accept', function () {
            var id = $(this).data('id');
            $.ajax({
                url: RESPOND_URL + '/' + id + '/respond',
                method: 'POST',
                data: { status: 'accepted', _token: CSRF_TOKEN },
                success: function (resp) {
                    showToast(resp.message, 'success');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to respond.', 'danger');
                }
            });
        });

        // Reject button click
        $(document).on('click', '.btn-reject', function () {
            if (!confirm('Are you sure you want to reject this assignment?')) return;
            var id = $(this).data('id');
            $.ajax({
                url: RESPOND_URL + '/' + id + '/respond',
                method: 'POST',
                data: { status: 'rejected', _token: CSRF_TOKEN },
                success: function (resp) {
                    showToast(resp.message, 'danger');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to respond.', 'danger');
                }
            });
        });

        @if(session('success'))
            showToast('{{ session('success') }}');
        @endif
    });
</script>
@endsection
