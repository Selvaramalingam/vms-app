<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FuelEntry;
use App\Models\Maintenance;
use Carbon\Carbon;

class LedgerController extends Controller
{
    public function vehicle(Request $request)
    {
        $vehicles = Vehicle::all();
        $vehicle_id = $request->get('vehicle_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = null;
        if ($vehicle_id) {
            $trips = Trip::where('vehicle_id', $vehicle_id)
                ->whereBetween('date', [$start_date, $end_date])
                ->get();
            
            $fuel = FuelEntry::where('vehicle_id', $vehicle_id)
                ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                ->get();

            $maintenances = Maintenance::where('vehicle_id', $vehicle_id)
                ->whereBetween('date', [$start_date, $end_date])
                ->get();

            $data = [
                'trips' => $trips,
                'fuel' => $fuel,
                'maintenances' => $maintenances,
                'total_income' => $trips->sum('rent_amount'),
                'total_fuel' => $fuel->sum('total'),
                'total_maintenance' => $maintenances->sum('cost'),
            ];
            $data['net_profit'] = $data['total_income'] - $data['total_fuel'] - $data['total_maintenance'];
        }

        return view('ledgers.vehicle', compact('vehicles', 'vehicle_id', 'start_date', 'end_date', 'data'));
    }

    public function driver(Request $request)
    {
        $drivers = Driver::all();
        $driver_id = $request->get('driver_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = null;
        if ($driver_id) {
            $trips = Trip::where('driver_id', $driver_id)
                ->whereBetween('date', [$start_date, $end_date])
                ->get();

            $data = [
                'trips' => $trips,
                'total_km' => $trips->sum('total_km'),
                'total_trips' => $trips->count(),
                'total_hour' => $trips->sum('total_hour'),
            ];
        }

        return view('ledgers.driver', compact('drivers', 'driver_id', 'start_date', 'end_date', 'data'));
    }
}
