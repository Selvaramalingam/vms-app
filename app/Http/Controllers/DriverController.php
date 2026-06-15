<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with('vehicle')->latest();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $drivers = $query->paginate(10)->withQueryString();
        $allDrivers = Driver::orderBy('driver_name')->get();
        return view('drivers.index', compact('drivers', 'allDrivers'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'active')->get();
        return view('drivers.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_name' => 'required|string',
            'phone' => 'required|string',
            'license_number' => 'required|string|unique:drivers',
            'license_expiry' => 'required|date',
            'assigned_vehicle' => 'nullable|exists:vehicles,id',
            'status' => 'required|string',
        ]);

        Driver::create($validated);
        return redirect()->route('drivers.index')->with('success', 'Driver added successfully!');
    }

    public function edit(Driver $driver)
    {
        $vehicles = Vehicle::where('status', 'active')->get();
        return view('drivers.edit', compact('driver', 'vehicles'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'driver_name' => 'required|string',
            'phone' => 'required|string',
            'license_number' => 'required|string|unique:drivers,license_number,' . $driver->id,
            'license_expiry' => 'required|date',
            'assigned_vehicle' => 'nullable|exists:vehicles,id',
            'status' => 'required|string',
        ]);

        $driver->update($validated);
        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully!');
    }
}
