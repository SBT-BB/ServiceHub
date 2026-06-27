<div id="drawer-form-content">
    <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" class="form-control" name="category_name" value="{{ old('category_name', $category->category_name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vehicle</label>
            <select class="form-select" name="vehicle_id" required>
                <option value="">Select Vehicle</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $category->vehicle_id ?? '') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_name }} (Cap: {{ $vehicle->vehicle_capacity_score }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Min Score</label>
                <input type="number" class="form-control" name="min_score" value="{{ old('min_score', $category->min_score ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Max Score</label>
                <input type="number" class="form-control" name="max_score" value="{{ old('max_score', $category->max_score ?? '') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Base Fare (₹)</label>
                <input type="number" class="form-control" name="base_fare" value="{{ old('base_fare', $category->base_fare ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Price per Point (₹)</label>
                <input type="number" class="form-control" name="price_per_point" value="{{ old('price_per_point', $category->price_per_point ?? 0) }}" min="0" step="0.01">
                <small class="text-muted">Used as: base fare + (total points × price per point)</small>
            </div>
        </div>

        <div class="card border-light bg-light-subtle mb-3">
            <div class="card-body p-3">
                <h6 class="mb-3 text-muted">Custom Pricing Overrides</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Weekend Surcharge (%)</label>
                        <input type="number" class="form-control" name="weekend_surcharge_percent" value="{{ old('weekend_surcharge_percent', $category->weekend_surcharge_percent ?? 0) }}" min="0" max="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Month-End Surcharge (%)</label>
                        <input type="number" class="form-control" name="month_end_surcharge_percent" value="{{ old('month_end_surcharge_percent', $category->month_end_surcharge_percent ?? 0) }}" min="0" max="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Peak-Time Surcharge (%)</label>
                        <input type="number" class="form-control" name="peak_time_surcharge_percent" value="{{ old('peak_time_surcharge_percent', $category->peak_time_surcharge_percent ?? 0) }}" min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Peak Start</label>
                        <input type="time" class="form-control" name="peak_time_start" value="{{ old('peak_time_start', $category->peak_time_start ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Peak End</label>
                        <input type="time" class="form-control" name="peak_time_end" value="{{ old('peak_time_end', $category->peak_time_end ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="1" {{ old('status', $category->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $category->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>
</div>
