@extends('partials.layouts.master')

@section('title', 'Vendor‑Supervisor Linking | ServiceHub')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="vendor-supervisor-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Supervisors</th>
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
            var table = initDataTable('#vendor-supervisor-table', '{{ route('vendor-supervisor.index') }}', [
                { data: 'id', name: 'id' },
                { data: 'vendor_name', name: 'vendor_name' },
                { data: 'supervisors', name: 'supervisors', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]);
        });
    </script>
@endsection
