<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\VehicleExpiry;
use App\Models\Maintenance;
use App\Models\TripPayment;
use Carbon\Carbon;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $expiryCount = VehicleExpiry::whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])->count();
                $serviceCount = Maintenance::whereNotNull('next_service_date')
                    ->whereBetween('next_service_date', [Carbon::now(), Carbon::now()->addDays(15)])
                    ->count();
                $pendingPaymentCount = TripPayment::where('status', '!=', 'completed')->count();
                
                $view->with('globalNotificationCount', $expiryCount + $serviceCount + $pendingPaymentCount);
            }
        });
    }
}
