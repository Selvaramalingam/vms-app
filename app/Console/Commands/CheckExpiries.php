<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VehicleExpiry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiries extends Command
{
    protected $signature = 'vms:check-expiries';
    protected $description = 'Check vehicle expiries and log/notify for 30, 15, 7, and 1 days intervals';

    public function handle()
    {
        $intervals = [30, 15, 7, 1];
        $today = Carbon::today();

        foreach ($intervals as $days) {
            $targetDate = $today->copy()->addDays($days);
            
            $expiringSoon = VehicleExpiry::with('vehicle')
                ->whereDate('expiry_date', $targetDate)
                ->get();

            foreach ($expiringSoon as $expiry) {
                // Here you would normally dispatch an email/SMS notification.
                // For now, we will log it.
                Log::info("Vehicle {$expiry->vehicle->vehicle_number} has {$expiry->expiry_type} expiring in {$days} days on {$expiry->expiry_date->toDateString()}.");
                
                $this->info("Alert: Vehicle {$expiry->vehicle->vehicle_number} has {$expiry->expiry_type} expiring in {$days} days.");
            }
        }
        
        $this->info('Expiry check completed successfully.');
    }
}
