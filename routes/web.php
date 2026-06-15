<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin, Staff & Vehicle can create/manage maintenance
    Route::middleware(['role:Admin|Vehicle'])->group(function () {
        Route::resource('maintenances', \App\Http\Controllers\MaintenanceController::class);
    });

    // Admin Only: Approval Actions
    Route::middleware(['role:Admin'])->group(function () {
        Route::patch('maintenances/{maintenance}/approve', [\App\Http\Controllers\MaintenanceController::class, 'approve'])->name('maintenances.approve');
        Route::patch('maintenances/{maintenance}/reject', [\App\Http\Controllers\MaintenanceController::class, 'reject'])->name('maintenances.reject');
        
        Route::resource('vehicles', \App\Http\Controllers\VehicleController::class);
        Route::resource('drivers', \App\Http\Controllers\DriverController::class);
        Route::get('admin/login-history', [\App\Http\Controllers\LoginHistoryController::class, 'index'])->name('login-history.index');
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');

        // Settings
        Route::get('settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

        // Activity Logs
        Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Manage Vehicle Logins
        Route::get('admin/vehicle-users', [\App\Http\Controllers\VehicleUserController::class, 'index'])->name('admin.vehicle-users.index');
        Route::post('admin/vehicle-users', [\App\Http\Controllers\VehicleUserController::class, 'store'])->name('admin.vehicle-users.store');
        Route::put('admin/vehicle-users/{user}', [\App\Http\Controllers\VehicleUserController::class, 'update'])->name('admin.vehicle-users.update');
        Route::delete('admin/vehicle-users/{user}', [\App\Http\Controllers\VehicleUserController::class, 'destroy'])->name('admin.vehicle-users.destroy');

        // Admin Expenses
        Route::resource('admin/expenses', \App\Http\Controllers\ExpenseController::class)->names([
            'index' => 'admin.expenses.index',
            'create' => 'admin.expenses.create',
            'store' => 'admin.expenses.store',
            'edit' => 'admin.expenses.edit',
            'update' => 'admin.expenses.update',
            'destroy' => 'admin.expenses.destroy',
        ])->except(['show']);
    });

    Route::get('api/vehicles/{vehicle}/last-trip', [\App\Http\Controllers\TripController::class, 'lastTrip'])->name('api.vehicles.last-trip');

    // Admin, Staff & Vehicle can create trips
    Route::middleware(['role:Admin|Vehicle'])->group(function () {
        Route::get('trips/create', [TripController::class, 'create'])->name('trips.create');
        Route::post('trips', [TripController::class, 'store'])->name('trips.store');
    });

    // Everyone (Admin, Staff, Driver, Vehicle) can edit trips
    Route::middleware(['role:Admin|Vehicle'])->group(function () {
        Route::get('trips/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit');
        Route::put('trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    });

    // Driver/Vehicle can view their own trips
    Route::middleware(['role:Vehicle'])->group(function () {
        Route::get('my-trips', [TripController::class, 'myTrips'])->name('trips.my');
    });

    // Admin & Staff
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('trips', TripController::class)->except(['create', 'store', 'edit', 'update', 'show']);
        Route::resource('fuel', \App\Http\Controllers\FuelEntryController::class)->except(['show']);
        Route::resource('payments', \App\Http\Controllers\TripPaymentController::class)->except(['show']);
        Route::get('trips/{trip}/payments', [\App\Http\Controllers\TripPaymentController::class, 'tripPayment'])->name('trips.payments');
        Route::post('trips/{trip}/payments', [\App\Http\Controllers\TripPaymentController::class, 'storeTripPayment'])->name('trips.payments.store');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
