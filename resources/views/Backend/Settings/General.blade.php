@extends('partials.layouts.master')

@section('title', 'System Settings | Herozi')

@section('sub-title', 'System Settings')
@section('pagetitle', 'Settings')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo">
                                @error('logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Recommended size similar to current logo.</div>
                                <div class="mt-2">
                                    <img id="logo-preview"
                                        src="{{ !empty($settings['logo']) ? asset($settings['logo']) : asset('assets/images/light-logo.png') }}"
                                        alt="Current Logo" height="40">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                    id="favicon" name="favicon">
                                @error('favicon')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">PNG/ICO, square icon.</div>
                                <div class="mt-2">
                                    <img id="favicon-preview"
                                        src="{{ !empty($settings['favicon']) ? asset($settings['favicon']) : asset('assets/images/Favicon.png') }}"
                                        alt="Current Favicon" height="32">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Footer Text</label>
                                <textarea name="footer_text" class="form-control @error('footer_text') is-invalid @enderror" rows="2"
                                    placeholder="Footer text">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea>
                                @error('footer_text')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            @if (session('success'))
                if (typeof showToast === 'function') {
                    showToast('{{ session('success') }}');
                }
            @endif

            $('#logo').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                $('#logo-preview').attr('src', url);
            });

            $('#favicon').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                $('#favicon-preview').attr('src', url);
            });
        });
    </script>
@endsection
