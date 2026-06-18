@extends('partials.layouts.master')

@section('title', 'User Management | Herozi')

@section('sub-title', 'User Manage')
@section('pagetitle', 'Users')
@section('buttonTitle', '+ New User')
@section('buttonLink', route('user.create'))

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="user-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Name</th>
                            <th>Email</th>
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
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                // {
                //     data: 'image',
                //     name: 'image',
                //     orderable: false,
                //     searchable: false
                // },
                // {
                //     data: 'email_verified_at',
                //     name: 'email_verified_at'
                // },
                // {
                //     data: 'phone',
                //     name: 'phone'
                // },
                // {
                //     data: 'date_of_birth',
                //     name: 'date_of_birth'
                // },
                // {
                //     data: 'gender',
                //     name: 'gender'
                // },
                // {
                //     data: 'address',
                //     name: 'address'
                // },
                // {
                //     data: 'city',
                //     name: 'city'
                // },
                // {
                //     data: 'state',
                //     name: 'state'
                // },
                // {
                //     data: 'country',
                //     name: 'country'
                // },
                // {
                //     data: 'postal_code',
                //     name: 'postal_code'
                // },
                // {
                //     data: 'status',
                //     name: 'status',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'roles',
                    name: 'roles',
                    orderable: false,
                    searchable: false
                },
                // {
                //     data: 'created_at',
                //     name: 'created_at'
                // },
                // {
                //     data: 'updated_at',
                //     name: 'updated_at'
                // },
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
