<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $alerts = [];

                // Optimization: Only run for Admin or if user has a vehicle
                if ($user->hasRole('Admin') || $user->hasRole('Vehicle')) {
                    
                    $vehiclesQuery = \App\Models\Vehicle::query();
                    
                    if ($user->hasRole('Vehicle')) {
                        $linkedVehicle = $user->vehicle;
                        if ($linkedVehicle) {
                            $vehiclesQuery->where('id', $linkedVehicle->id);
                        } else {
                            $vehiclesQuery->where('id', 0); // No vehicle linked
                        }
                    }

                    $vehicles = $vehiclesQuery->get();

                    foreach ($vehicles as $v) {
                        // 1. Expiries Check (Only upcoming 7 days)
                        $expiryFields = [
                            'fc_expiry' => 'FC',
                            'insurance_expiry' => 'Insurance',
                            'permit_expiry' => 'Permit',
                            'tax_expiry' => 'Tax',
                            'pollution_expiry' => 'Pollution'
                        ];

                        $today = now()->startOfDay();

                        foreach ($expiryFields as $field => $label) {
                            if ($v->$field) {
                                $expiryDate = $v->$field->startOfDay();
                                $diffDays = $today->diffInDays($expiryDate, false); // Signed difference

                                // Only show alerts for today or future up to 7 days
                                if ($diffDays >= 0 && $diffDays <= 7) {
                                    $alerts[] = [
                                        'type' => 'expiry_soon',
                                        'title' => "$label Expiring",
                                        'message' => "$v->vehicle_number: $label expiring in $diffDays days (" . $expiryDate->format('d/m/Y') . ")",
                                        'url' => route('vehicles.index', ['vehicle_id' => $v->id]),
                                        'urgent' => true // Everything in this window is urgent for the scroll
                                    ];
                                }
                            }
                        }
                    }
                }

                $view->with('globalNotifications', $alerts);
                $view->with('globalNotificationCount', count($alerts));
            }
        });
    }
}
