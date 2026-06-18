@extends('partials.layouts.master')

@section('title')
    Edit {{ $role->name }} | Herozi
@endsection

@section('sub-title', 'Edit Role')
@section('pagetitle', 'Dashboard')

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Role</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Role</h5>
                </div>
                <div class="card-body">
                    <div id="drawer-form-content">
                        <form id="editRoleForm" action="{{ route('role.update', $role->id) }}" method="POST"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label for="name" class="form-label">Role Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Role Name" value="{{ $role->name }}" required>
                                    <div class="invalid-feedback">Please enter a role name.</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Form Submission
            $('#editRoleForm').on('submit', function(e) {
                e.preventDefault();
                if (this.checkValidity()) {
                    $.ajax({
                        url: '{{ route('role.update', $role->id) }}',
                        method: 'PUT',
                        data: $(this).serialize(),
                        success: function(response) {
                            showToast(response.message);
                            setTimeout(function() {
                                window.location.href = '{{ route('role.index') }}';
                            }, 1000);
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function(key) {
                                    showToast(errors[key][0], 'danger');
                                });
                            } else {
                                showToast('An error occurred.', 'danger');
                            }
                        }
                    });
                }
                $(this).addClass('was-validated');
            });
        });
    </script>
@endsection
