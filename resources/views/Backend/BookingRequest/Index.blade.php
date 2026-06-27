@extends('partials.layouts.master')

@section('title', 'Booking Requests | Herozi')

@section('sub-title', 'Booking Requests')
@section('pagetitle', 'Requests')

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="request-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Customer Mobile</th>
                            <!-- <th>Pickup Location</th>
                            <th>Drop Location</th> -->
                            <th>Shifting Date</th>
                            <th>Est. Amount</th>
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
            var table = initDataTable('#request-table', '{{ route('booking-request.index') }}', [
                {
                    data: 'id',
                    name: 'id'
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
                    data: 'estimated_amount',
                    name: 'estimated_amount'
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

            @if (session('success'))
                showToast('{{ session('success') }}');
            @endif
        });
    </script>
@endsection
