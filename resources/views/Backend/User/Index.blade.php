@extends('partials.layouts.master')

@section('title', 'User Management | Herozi')

@section('sub-title', 'User Manage')
@section('pagetitle', 'Users')
@section('buttonTitle', '+ New User')
@section('buttonLink', route('user.create'))

@section('content')

    <!-- Filters -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-header border-bottom-dashed">
                    <h5 class="card-title mb-0"><i class="ri-filter-2-line align-middle me-1"></i> Filter Users</h5>
                </div>
                <div class="card-body">
                    <form id="filter-form" class="row align-items-end g-3">
                        <div class="col-xxl-4 col-sm-6">
                            <div>
                                <label for="role-filter" class="form-label text-muted fw-semibold">Role</label>
                                <select class="form-select" id="role-filter" name="role">
                                    <option value="">All Roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xxl-2 col-sm-6">
                            <div class="hstack gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i> Filter
                                </button>
                                <button type="button" id="reset-filter" class="btn btn-outline-secondary flex-grow-1">
                                    <i class="ri-refresh-line me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="user-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Roles</th>
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
            var table = initDataTable('#user-table', '{{ route('user.index') }}', [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'mobile',
                    name: 'mobile'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'roles',
                    name: 'roles',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ], {
                ajaxData: function(d) {
                    d.role = $('#role-filter').val();
                }
            });

            // Filter Form Submit
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Reset Filter Click
            $('#reset-filter').on('click', function() {
                $('#role-filter').val('');
                table.draw();
            });

            @if (session('success'))
                showToast('{{ session('success') }}');
            @endif
        });
    </script>
@endsection
