<?php

namespace App\Http\Controllers;

use App\Models\FuelEntry;
use App\Models\Vehicle;
use App\Models\Trip;
use Illuminate\Http\Request;

class FuelEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = FuelEntry::with(['vehicle', 'trip'])->latest();

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $fuelEntries = $query->paginate(10)->withQueryString();
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        
        return view('fuel.index', compact('fuelEntries', 'vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'active')->get();
        $trips = Trip::latest()->take(50)->get();
        return view('fuel.create', compact('vehicles', 'trips'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'trip_id' => 'nullable|exists:trips,id',
            'litre' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        $validated['litre'] = $validated['litre'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['total'] = $validated['litre'] * $validated['price'];

        FuelEntry::create($validated);
        return redirect()->route('fuel.index')->with('success', 'Fuel entry added successfully!');
    }

    public function edit(FuelEntry $fuel)
    {
        $vehicles = Vehicle::where('status', 'active')->get();
        $trips = Trip::latest()->take(50)->get();
        return view('fuel.edit', compact('fuel', 'vehicles', 'trips'));
    }

    public function update(Request $request, FuelEntry $fuel)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'trip_id' => 'nullable|exists:trips,id',
            'litre' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        $validated['litre'] = $validated['litre'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['total'] = $validated['litre'] * $validated['price'];

        $fuel->update($validated);
        return redirect()->route('fuel.index')->with('success', 'Fuel entry updated!');
    }

    public function destroy(FuelEntry $fuel)
    {
        $fuel->delete();
        return redirect()->route('fuel.index')->with('success', 'Fuel entry deleted.');
    }
}
