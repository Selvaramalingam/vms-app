<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();

        $query = Trip::with(['vehicle', 'driver'])->orderBy('date', 'asc');

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by location (partial match)
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by from date
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        // Filter by to date
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $trips = $query->get();

        // Selected vehicle info
        $selectedVehicle = null;
        if ($request->filled('vehicle_id')) {
            $selectedVehicle = Vehicle::find($request->vehicle_id);
        }

        // Totals
        $totalRentAmount     = $trips->sum('total_amount');
        $totalDieselLtr      = $trips->sum('fuel_litre');
        $totalOverallDiesel  = $trips->sum('fuel_cost');

        return view('invoices.index', compact(
            'vehicles',
            'trips',
            'selectedVehicle',
            'totalRentAmount',
            'totalDieselLtr',
            'totalOverallDiesel'
        ));
    }
}
