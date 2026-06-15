<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\FuelEntry;
use App\Models\VehicleExpiry;
use App\Models\Maintenance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Specific Dashboard for Vehicles
        if ($user->hasRole('Vehicle') || $user->hasRole('Driver')) {
            $vehicle = $user->vehicle; 
            
            if (!$vehicle) {
                // Fallback if vehicle profile not linked
                $todayTripsCount = 0;
                $todayHoursCount = 0;
                $recentTrips = collect();
                $totalTrips = 0;
            } else {
                $todayTripsCount = Trip::where('vehicle_id', $vehicle->id)->whereDate('date', Carbon::today())->count();
                $todayHoursCount = Trip::where('vehicle_id', $vehicle->id)->whereDate('date', Carbon::today())->sum('total_hour');
                $recentTrips = Trip::with('driver')->where('vehicle_id', $vehicle->id)->latest('date')->take(5)->get();
                $totalTrips = Trip::where('vehicle_id', $vehicle->id)->count();
            }

            return view('dashboard_vehicle', compact('vehicle', 'todayTripsCount', 'todayHoursCount', 'recentTrips', 'totalTrips'));
        }

        $today = Carbon::today();
        
        $todayTrips = Trip::whereDate('date', $today)->count();
        $todayHours = Trip::whereDate('date', $today)->sum('total_hour');
        $todayIncome = Trip::whereDate('date', $today)->sum('rent_amount');
        $fuelExpense = Trip::whereDate('date', $today)->sum('fuel_cost');
        $profit = $todayIncome - $fuelExpense;

        $pendingPayments = \App\Models\TripPayment::where('status', '!=', 'completed')->sum('balance');
        
        // Expiry Alerts (next 30 days)
        $expiryAlerts = VehicleExpiry::with('vehicle')
            ->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->get();
            
        // Recent Trips (last 5)
        $recentTrips = Trip::with('vehicle', 'driver')
            ->latest('date')
            ->take(5)
            ->get();
            
        // Top Vehicles (by Profit)
        $topVehiclesQuery = Trip::selectRaw('vehicle_id, sum(rent_amount) as total_rent, sum(fuel_cost) as total_fuel, sum(rent_amount) - sum(fuel_cost) as total_profit')
            ->with('vehicle')
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('vehicle_id')
            ->orderBy('total_profit', 'desc')
            ->take(5)
            ->get();

        // Monthly profit data for chart (last 30 days, grouped by week)
        $profitData = Trip::selectRaw('strftime("%W", date) as week_num, sum(rent_amount) as rent, sum(fuel_cost) as fuel, sum(rent_amount) - sum(fuel_cost) as profit')
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('week_num')
            ->orderBy('week_num')
            ->get();

        // Total notifications
        $notificationCount = $expiryAlerts->count();
        $pendingCount = \App\Models\TripPayment::where('status', '!=', 'completed')->count();
        $totalNotifications = $notificationCount + $pendingCount;

        return view('dashboard', compact(
            'todayTrips', 'todayHours', 'todayIncome', 'fuelExpense', 'profit', 'pendingPayments', 'expiryAlerts', 'recentTrips', 'topVehiclesQuery', 'totalNotifications', 'profitData'
        ));

    }
}
