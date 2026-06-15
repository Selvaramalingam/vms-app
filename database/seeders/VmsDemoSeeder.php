<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FuelEntry;
use App\Models\Maintenance;
use App\Models\TripPayment;
use Carbon\Carbon;

class VmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Vehicles
        $vehicles = [
            ['vehicle_number' => 'TN-01-AB-1234', 'vehicle_type' => 'Truck', 'owner_type' => 'own', 'status' => 'active', 'last_service_km' => 12000, 'fc_expiry' => Carbon::now()->addMonths(6)],
            ['vehicle_number' => 'TN-02-CD-5678', 'vehicle_type' => 'Lorry', 'owner_type' => 'own', 'status' => 'active', 'last_service_km' => 8000, 'fc_expiry' => Carbon::now()->addDays(10)],
            ['vehicle_number' => 'TN-03-EF-9012', 'vehicle_type' => 'Van', 'owner_type' => 'rent', 'status' => 'active', 'last_service_km' => 15000, 'fc_expiry' => Carbon::now()->addMonths(1)],
        ];

        foreach ($vehicles as $v) {
            $vehicle = Vehicle::updateOrCreate(['vehicle_number' => $v['vehicle_number']], $v);
            
            // Add some expiries
            \App\Models\VehicleExpiry::updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'expiry_type' => 'fc'],
                ['expiry_date' => $v['fc_expiry']]
            );
        }

        // 2. Drivers
        $drivers = [
            ['driver_name' => 'John Doe', 'phone' => '9876543210', 'license_number' => 'DL-12345', 'license_expiry' => Carbon::now()->addYears(2), 'status' => 'active'],
            ['driver_name' => 'Mike Ross', 'phone' => '9876543211', 'license_number' => 'DL-67890', 'license_expiry' => Carbon::now()->addYears(3), 'status' => 'active'],
        ];

        foreach ($drivers as $d) {
            Driver::updateOrCreate(['driver_name' => $d['driver_name']], $d);
        }

        // 3. Trips & Payments & Fuel
        $allVehicles = Vehicle::all();
        $allDrivers = Driver::all();

        foreach ($allVehicles as $v) {
            // Create 5 trips for each vehicle
            for ($i = 1; $i <= 5; $i++) {
                $date = Carbon::now()->subDays(rand(1, 30));
                $trip = Trip::create([
                    'date' => $date,
                    'location' => 'City ' . rand(1, 10),
                    'vehicle_id' => $v->id,
                    'driver_id' => $allDrivers->random()->id,
                    'trip_type' => 'rent',
                    'open_km' => 1000 * $i,
                    'close_km' => (1000 * $i) + rand(100, 500),
                    'rent_amount' => rand(5000, 15000),
                    'diesel_price' => 95,
                    'fuel_litre' => rand(20, 50),
                    'total_km' => rand(100, 500),
                    'fuel_cost' => rand(2000, 4500),
                ]);

                // Fuel Entry
                FuelEntry::create([
                    'vehicle_id' => $v->id,
                    'trip_id' => $trip->id,
                    'litre' => $trip->fuel_litre,
                    'price' => $trip->diesel_price,
                    'total' => $trip->fuel_cost,
                    'created_at' => $date,
                ]);

                // Payment
                TripPayment::create([
                    'trip_id' => $trip->id,
                    'advance' => rand(1000, 2000),
                    'paid' => rand(2000, 5000),
                    'balance' => rand(0, 3000),
                    'status' => 'partial',
                ]);
            }

            // Maintenance
            Maintenance::create([
                'vehicle_id' => $v->id,
                'date' => Carbon::now()->subDays(rand(1, 60)),
                'description' => 'Oil change and general service',
                'cost' => rand(2000, 5000),
                'km' => $v->last_service_km,
                'next_service_km' => $v->last_service_km + 5000,
                'next_service_date' => Carbon::now()->addMonths(3),
            ]);
        }
    }
}
