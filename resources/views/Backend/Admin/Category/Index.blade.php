@extends('partials.layouts.master')
@section('title', 'Category Management | ServiceHub')
@section('sub-title', 'Categories')
@section('pagetitle', 'Category Management')
@section('buttonTitle', '+ Add Category')
@section('buttonLink', route('admin.categories.create'))
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body p-0">
                <table id="category-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Vehicle</th>
                            <th>Min Score</th>
                            <th>Max Score</th>
                            <th>Base Fare (₹)</th>
                            <th>Price/Point</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('partials.common-drawer')
@endsection
@section('js')
<script>
    $(document).ready(function() {
        initDataTable('#category-table', '{{ route('admin.categories') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'category_name', name: 'category_name' },
            { data: 'vehicle_name', name: 'vehicle.vehicle_name' },
            { data: 'min_score', name: 'min_score' },
            { data: 'max_score', name: 'max_score' },
            { data: 'base_fare', name: 'base_fare' },
            { data: 'price_per_point', name: 'price_per_point' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
