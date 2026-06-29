<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\Admin\VehicleController;
use App\Http\Controllers\Backend\Admin\CategoryController;
use App\Http\Controllers\Backend\Admin\ItemController;
use App\Http\Controllers\Backend\Admin\AddOnController;
use App\Http\Controllers\Backend\Admin\PricingController;
use App\Http\Controllers\Backend\Admin\RevenueController;
use App\Http\Controllers\Backend\Admin\FeedbackController;
use App\Http\Controllers\Backend\Admin\ReportController;
use App\Http\Controllers\Backend\Admin\ItemSizeController;
use App\Http\Controllers\Backend\VendorSupervisorController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ── MASTER MANAGEMENT ─────────────────────────────────

    // Vendor‑Supervisor Linking Module
    Route::prefix('vendor-supervisor')->name('vendor-supervisor.')->group(function () {
        Route::get('/', [VendorSupervisorController::class, 'index'])->name('index');
        Route::get('/create', [VendorSupervisorController::class, 'create'])->name('create');
        Route::post('/', [VendorSupervisorController::class, 'store'])->name('store');
        Route::get('/{vendor}/edit', [VendorSupervisorController::class, 'edit'])->name('edit');
        Route::put('/{vendor}', [VendorSupervisorController::class, 'update'])->name('update');
        Route::delete('/{vendor}/{supervisor}', [VendorSupervisorController::class, 'destroy'])->name('destroy');
    });

    // ── Dashboard (main) ───────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Booking Overview (used in dashboard stats only — not sidebar)
        Route::get('/bookings/total',     [BookingController::class, 'total'])->name('bookings.total');
        Route::get('/bookings/pending',   [BookingController::class, 'pending'])->name('bookings.pending');
        Route::get('/bookings/completed', [BookingController::class, 'completed'])->name('bookings.completed');

        // Revenue
        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue');

        // Vehicles
        Route::get('/vehicles',              [VehicleController::class, 'index'])->name('vehicles');
        Route::get('/vehicles/create',       [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles',             [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{id}',         [VehicleController::class, 'show'])->name('vehicles.show');
        Route::get('/vehicles/{id}/edit',    [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{id}',         [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{id}',      [VehicleController::class, 'destroy'])->name('vehicles.destroy');

        // Categories
        Route::get('/categories',              [CategoryController::class, 'index'])->name('categories');
        Route::get('/categories/create',       [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories',             [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}',         [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{id}/edit',    [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}',         [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}',      [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Item Master
        Route::get('/items',              [ItemController::class, 'index'])->name('items');
        Route::get('/items/create',       [ItemController::class, 'create'])->name('items.create');
        Route::post('/items',             [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{id}',         [ItemController::class, 'show'])->name('items.show');
        Route::get('/items/{id}/edit',    [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{id}',         [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{id}',      [ItemController::class, 'destroy'])->name('items.destroy');

        // Item Sizes Master
        Route::get('/item-sizes',              [ItemSizeController::class, 'index'])->name('item-sizes');
        Route::get('/item-sizes/create',       [ItemSizeController::class, 'create'])->name('item-sizes.create');
        Route::post('/item-sizes',             [ItemSizeController::class, 'store'])->name('item-sizes.store');
        Route::get('/item-sizes/{item_size}/edit', [ItemSizeController::class, 'edit'])->name('item-sizes.edit');
        Route::put('/item-sizes/{item_size}',      [ItemSizeController::class, 'update'])->name('item-sizes.update');
        Route::delete('/item-sizes/{item_size}',   [ItemSizeController::class, 'destroy'])->name('item-sizes.destroy');

        // Add-On Services
        Route::get('/addons',              [AddOnController::class, 'index'])->name('addons');
        Route::get('/addons/create',       [AddOnController::class, 'create'])->name('addons.create');
        Route::post('/addons',             [AddOnController::class, 'store'])->name('addons.store');
        Route::get('/addons/{id}',         [AddOnController::class, 'show'])->name('addons.show');
        Route::get('/addons/{id}/edit',    [AddOnController::class, 'edit'])->name('addons.edit');
        Route::put('/addons/{id}',         [AddOnController::class, 'update'])->name('addons.update');
        Route::delete('/addons/{id}',      [AddOnController::class, 'destroy'])->name('addons.destroy');

        // Ajax Pricing
        Route::post('booking/ajax-pricing', [BookingController::class, 'ajaxPricing'])->name('booking.ajax-pricing');

        // Pricing Settings
        Route::get('/pricing',  [PricingController::class, 'index'])->name('pricing');
        Route::post('/pricing', [PricingController::class, 'store'])->name('pricing.store');

        // Feedback & Ratings
        Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    });
});
