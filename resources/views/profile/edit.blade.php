@extends('partials.layouts.master')

@section('title')
    Profile Settings | Herozi
@endsection

@section('sub-title', 'Profile')
@section('pagetitle', 'Dashboard')

@section('css')
    <style>
        .profile-img-wrapper {
            position: relative;
            display: inline-block;
            padding: 5px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-img-wrapper img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #f8f9fa;
            display: block;
        }

        .profile-actions {
            position: absolute;
            bottom: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .btn-camera {
            background-color: #0d6efd;
        }

        .btn-trash {
            background-color: #dc3545;
        }

        /* Animation for smooth appearance */
        #removeImageBtn:not(.d-none) {
            display: flex;
        }
    </style>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Settings</h5>
                </div>
                <div class="card-body">
                    <form id="profileUpdateForm" class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row g-4">
                            <!-- Image Section -->
                            <div class="col-12 text-center mb-4">
                                <div class="profile-img-wrapper">
                                    <img id="imagePreview"
                                        src="{{ $user->image ? asset($user->image) : asset('assets/images/avatar/dummy-avatar.jpg') }}"
                                        alt="Profile image">

                                    <div class="profile-actions">
                                        <label for="image" class="action-btn btn-camera" title="Change Image">
                                            <i class="ri-camera-fill"></i>
                                            <input type="file" id="image" name="image" class="d-none"
                                                accept="image/*">
                                        </label>

                                        <button type="button" id="removeImageBtn"
                                            class="action-btn btn-trash {{ $user->image ? '' : 'd-none' }}"
                                            title="Remove Image">
                                            <i class="ri-delete-bin-fill"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                                </div>
                                <div class="mt-3 text-muted small">Allowed JPG, GIF or PNG. Max size of 2MB</div>
                                <div id="image_error" class="text-danger small mt-1"></div>
                            </div>

                            <div class="col-md-12">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Full Name" value="{{ old('name', $user->name) }}" required>
                                <div class="invalid-feedback">Please enter your full name.</div>
                            </div>

                            <div class="col-md-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                    value="{{ old('email', $user->email) }}" required>
                                <div class="invalid-feedback">Please provide a valid email.</div>
                            </div>

                            <hr class="mt-4 mb-2">
                            <h6 class="mb-0">Change Password</h6>
                            <p class="text-muted small mb-3">Leave blank if you don't want to change password.</p>

                            <div class="col-md-12">
                                <label for="old_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="old_password" name="old_password"
                                    placeholder="Current Password">
                                <div class="invalid-feedback">Please enter your current password to change it.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="New Password">
                                <div class="invalid-feedback">New password must be at least 8 characters.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Confirm New Password">
                                <div class="invalid-feedback">Passwords do not match.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Image Preview
            $('#image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $('#imagePreview').attr('src', event.target.result);
                        $('#removeImageBtn').removeClass('d-none');
                        $('#remove_image').val('0');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Remove Image
            $('#removeImageBtn').on('click', function() {
                $('#imagePreview').attr('src', "{{ asset('assets/images/avatar/dummy-avatar.jpg') }}");
                $('#image').val(''); // Clear file input
                $('#remove_image').val('1');
                $(this).addClass('d-none');
            });

            // Form Submission
            $('#profileUpdateForm').on('submit', function(e) {
                e.preventDefault();

                // Reset errors
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger.small').text('');

                const form = this;
                const formData = new FormData(form);
                const saveBtn = $('#saveBtn');

                saveBtn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('profile.update') }}",
                    method: 'POST', // We use POST with _method PATCH
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showToast(response.message, 'success');
                        saveBtn.prop('disabled', false).text('Save Changes');

                        // Live update header name
                        if (response.user.name) {
                            $('#header-profile-name').text(response.user.name);
                        }

                        // Live update header image
                        var baseUrl = "{{ asset('') }}";
                        if (response.user.image) {
                            $('#header-profile-img').attr('src', baseUrl + response.user.image);
                        } else {
                            $('#header-profile-img').attr('src', baseUrl +
                                "assets/images/avatar/dummy-avatar.jpg");
                        }
                    },
                    error: function(xhr) {
                        saveBtn.prop('disabled', false).text('Save Changes');
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                var field = $('[name="' + key + '"]');
                                if (field.length) {
                                    field.addClass('is-invalid');
                                    // Handle cases where the input is hidden (like image)
                                    if (key === 'image') {
                                        $('#image_error').text(errors[key][0]);
                                    } else {
                                        let feedback = field.siblings(
                                            '.invalid-feedback');
                                        if (feedback.length) {
                                            feedback.text(errors[key][0]);
                                        }
                                    }
                                }
                            });
                        } else {
                            showToast('An error occurred while updating profile.', 'danger');
                        }
                    }
                });
            });
        });

        // Simple Toast Helper (assuming showToast exists, if not I'll define a basic one)
        if (typeof showToast !== 'function') {
            function showToast(message, type = 'success') {
                alert(message); // fallback
            }
        }
    </script>
@endsection
