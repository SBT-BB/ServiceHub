@extends('partials.layouts.master')

@section('title', 'Vendor Booking Requests | ServiceHub')

@section('sub-title', 'Bookings')
@section('pagetitle', 'Bookings')
@section('isDrawer', 'false')

@section('content')
    {{-- Vendor Booking Data Table --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0">Assigned Booking Requests</h5>
                </div>
                <div class="card-body">
                    <table id="vendor-booking-table" class="table-hover align-middle table table-nowrap w-100">
                        <thead class="bg-light bg-opacity-30">
                            <tr>
                                <th>Booking No</th>
                                <th>Customer Name</th>
                                <th>Customer Mobile</th>
                                <th>Shifting Date</th>
                                <th>Amount</th>
                                <th>Acceptance Status</th>
                                <th>Assign Supervisor</th>
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
    @php
        $supervisorOptions = $supervisors->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->toJson();
    @endphp

    <script>
        var SUPERVISORS = {!! $supervisorOptions !!};
        var RESPOND_URL = '{{ url('vendor/booking') }}';
        var CSRF_TOKEN  = '{{ csrf_token() }}';

        function buildSupervisorSelect(currentId, bookingId, acceptanceStatus) {
            if (acceptanceStatus === 'reassigned') {
                return '<span class="text-muted fs-11">—</span>';
            }
            if (acceptanceStatus !== 'accepted') {
                return '<span class="text-muted fs-11">Accept request first</span>';
            }
            
            var html = '<select class="form-select form-select-sm assign-supervisor" data-booking-id="' + bookingId + '" style="min-width:140px;">';
            html += '<option value="">— None —</option>';
            SUPERVISORS.forEach(function (s) {
                var sel = (parseInt(s.id) === parseInt(currentId)) ? ' selected' : '';
                html += '<option value="' + s.id + '"' + sel + '>' + s.name + '</option>';
            });
            html += '</select>';
            return html;
        }

        $(document).ready(function () {
            var table = initDataTable('#vendor-booking-table', '{{ route('vendor.booking.index') }}', [
                { data: 'booking_number',  name: 'booking_number' },
                { data: 'customer_name',   name: 'customer_name',   orderable: false },
                { data: 'customer_mobile', name: 'customer_mobile', orderable: false },
                { data: 'shifting_date',   name: 'shifting_date' },
                { data: 'amount',          name: 'amount' },
                {
                    data: 'vendor_acceptance_status',
                    name: 'vendor_acceptance_status',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (data === 'pending') {
                            return '<div class="hstack gap-1">' +
                                '<button class="btn btn-sm btn-success btn-respond" data-status="accepted" data-id="' + row.id + '"><i class="ri-check-line me-1"></i>Accept</button>' +
                                '<button class="btn btn-sm btn-danger btn-respond" data-status="rejected" data-id="' + row.id + '"><i class="ri-close-line me-1"></i>Reject</button>' +
                                '</div>';
                        } else if (data === 'accepted') {
                            return '<span class="badge bg-success-focus text-success"><i class="ri-checkbox-circle-line me-1"></i>Accepted</span>';
                        } else if (data === 'rejected') {
                            return '<span class="badge bg-danger-focus text-danger"><i class="ri-close-circle-line me-1"></i>Rejected</span>';
                        } else if (data === 'reassigned') {
                            return '<span class="badge bg-secondary-focus text-secondary"><i class="ri-history-line me-1"></i>Assigned to Another</span>';
                        }
                        return '<span class="badge bg-light text-dark">' + data + '</span>';
                    }
                },
                {
                    data: 'supervisor_id',
                    name: 'supervisor_id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return buildSupervisorSelect(data, row.id, row.vendor_acceptance_status);
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var badge = 'bg-light text-dark';
                        if (data === 'pending') badge = 'bg-warning-focus text-warning';
                        else if (data === 'confirmed') badge = 'bg-primary-focus text-primary';
                        else if (data === 'in_progress') badge = 'bg-info-focus text-info';
                        else if (data === 'completed') badge = 'bg-success-focus text-success';
                        else if (data === 'cancelled') badge = 'bg-danger-focus text-danger';
                        
                        return '<span class="badge ' + badge + '">' + data.charAt(0).toUpperCase() + data.slice(1).replace('_', ' ') + '</span>';
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]);

            {{-- Respond to Booking Request (Accept/Reject) --}}
            $(document).on('click', '.btn-respond', function () {
                var $btn = $(this);
                var bookingId = $btn.data('id');
                var status = $btn.data('status');
                
                if (status === 'rejected' && !confirm('Are you sure you want to reject this request?')) {
                    return;
                }

                $btn.prop('disabled', true);

                $.ajax({
                    url: RESPOND_URL + '/' + bookingId + '/respond',
                    method: 'POST',
                    data: {
                        status: status,
                        _token: CSRF_TOKEN
                    },
                    success: function (resp) {
                        showToast(resp.message || 'Response submitted!', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Action failed.';
                        showToast(msg, 'danger');
                    }
                });
            });

            {{-- Assign Supervisor --}}
            $(document).on('change', '.assign-supervisor', function () {
                var $select = $(this);
                var bookingId = $select.data('booking-id');
                var supervisorId = $select.val();
                var prevVal = $select.data('prev') || '';
                $select.data('prev', supervisorId);
                $select.prop('disabled', true);

                $.ajax({
                    url: RESPOND_URL + '/' + bookingId + '/assign-supervisor',
                    method: 'POST',
                    data: {
                        supervisor_id: supervisorId,
                        _token: CSRF_TOKEN
                    },
                    success: function (resp) {
                        $select.prop('disabled', false);
                        showToast(resp.message || 'Supervisor assigned!', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        $select.prop('disabled', false).val(prevVal);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Assignment failed.';
                        showToast(msg, 'danger');
                    }
                });
            });
        });
    </script>
@endsection
