<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleExpiry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('expiries')->latest();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vehicles = $query->paginate(10)->withQueryString();
        $allVehicles = Vehicle::orderBy('vehicle_number')->get();
        
        return view('vehicles.index', compact('vehicles', 'allVehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles',
            'vehicle_type' => 'required|string',
            'owner_type' => 'required|in:own,rent',
            'owner_name' => 'nullable|string',
            'owner_phone' => 'nullable|string',
            'status' => 'required|string',
            'fc_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'permit_expiry' => 'nullable|date',
            'tax_expiry' => 'nullable|date',
            'pollution_expiry' => 'nullable|date',
            'last_service_km' => 'required|numeric',
            'next_service_km' => 'nullable|numeric',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
        ]);

        $vehicle = Vehicle::create($validated);

        // Map expiries to vehicle_expiries table as well
        $expiryTypes = ['fc', 'insurance', 'permit', 'tax', 'pollution'];
        foreach ($expiryTypes as $type) {
            $dateField = $type . '_expiry';
            if ($request->filled($dateField)) {
                VehicleExpiry::create([
                    'vehicle_id' => $vehicle->id,
                    'expiry_type' => $type,
                    'expiry_date' => $request->$dateField,
                ]);
            }
        }

        \App\Models\ActivityLog::log('created', 'Vehicle', 'New vehicle added: ' . $vehicle->vehicle_number);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added successfully!');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number,' . $vehicle->id,
            'vehicle_type' => 'required|string',
            'owner_type' => 'required|in:own,rent',
            'owner_name' => 'nullable|string',
            'owner_phone' => 'nullable|string',
            'status' => 'required|string',
            'fc_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'permit_expiry' => 'nullable|date',
            'tax_expiry' => 'nullable|date',
            'pollution_expiry' => 'nullable|date',
            'last_service_km' => 'required|numeric',
            'next_service_km' => 'nullable|numeric',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
        ]);

        $vehicle->update($validated);

        // Update expiries mapping
        $expiryTypes = ['fc', 'insurance', 'permit', 'tax', 'pollution'];
        foreach ($expiryTypes as $type) {
            $dateField = $type . '_expiry';
            if ($request->filled($dateField)) {
                VehicleExpiry::updateOrCreate(
                    ['vehicle_id' => $vehicle->id, 'expiry_type' => $type],
                    ['expiry_date' => $request->$dateField]
                );
            }
        }

        \App\Models\ActivityLog::log('updated', 'Vehicle', 'Vehicle updated: ' . $vehicle->vehicle_number);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully!');
    }
}
