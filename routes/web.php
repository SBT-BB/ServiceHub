<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\BookingRequestController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SystemSettingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Profile ────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Customer Management ────────────────────────────────
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // ── Booking Request Management ─────────────────────────
    Route::prefix('booking-request')->name('booking-request.')->group(function () {
        Route::get('/', [BookingRequestController::class, 'index'])->name('index');
        Route::get('/{bookingRequest}', [BookingRequestController::class, 'show'])->name('show');
        Route::post('/{bookingRequest}/approve', [BookingRequestController::class, 'approve'])->name('approve');
        Route::post('/{bookingRequest}/reject', [BookingRequestController::class, 'reject'])->name('reject');
    });

    // ── Booking Management ─────────────────────────────────
    Route::get('booking/search-customers', [BookingController::class, 'searchCustomers'])->name('booking.search-customers');
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        Route::post('/{booking}/assign-vendor', [BookingController::class, 'assignVendor'])->name('assignVendor');
    });

    // ── Vendor Booking Portal ──────────────────────────────
    Route::middleware(['role:Vendor'])->prefix('vendor')->name('vendor.')->group(function () {
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'show'])->name('show');
            Route::post('/{booking}/respond', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'respond'])->name('respond');
            Route::post('/{booking}/assign-supervisor', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'assignSupervisor'])->name('assignSupervisor');
        });

        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Backend\Vendor\VendorWalletController::class, 'index'])->name('index');
        });
    });

    // ── Supervisor Booking Portal ───────────────────────────
    Route::middleware(['role:Superviser'])->prefix('supervisor')->name('supervisor.')->group(function () {
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'show'])->name('show');
            Route::post('/{booking}/respond', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'respond'])->name('respond');
            Route::post('/{booking}/start-trip', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'startTrip'])->name('startTrip');
            Route::post('/{booking}/start-shifting', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'startShifting'])->name('startShifting');
            Route::post('/{booking}/update-items', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'updateItems'])->name('updateItems');
            Route::post('/{booking}/complete-shifting', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'completeShifting'])->name('completeShifting');
        });
    });

    // ── User Management ────────────────────────────────────
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/quick-create-supervisor', [UserController::class, 'quickCreateSupervisor'])->name('quick-create-supervisor');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])->name('permissions');
        Route::put('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('permissions.update');
    });

    // ── Role Management ────────────────────────────────────
    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
        Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
    });

    // ── System Settings ────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SystemSettingController::class, 'edit'])->name('edit');
        Route::post('/', [SystemSettingController::class, 'update'])->name('update');
    });
});

require __DIR__ . '/auth.php';
require __DIR__ . '/Admin.php';
