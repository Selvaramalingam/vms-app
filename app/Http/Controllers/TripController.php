<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::with(['vehicle', 'driver'])->latest();

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('trip_type')) {
            $query->where('trip_type', $request->trip_type);
        }

        $trips = $query->paginate(10)->withQueryString();
        $vehicles = \App\Models\Vehicle::orderBy('vehicle_number')->get();
        $drivers = \App\Models\Driver::orderBy('driver_name')->get();
        
        return view('trips.index', compact('trips', 'vehicles', 'drivers'));
    }

    /**
     * Show trips for the logged-in vehicle only.
     */
    public function myTrips()
    {
        $user = auth()->user();
        $vehicle = $user->vehicle;

        if (!$vehicle) {
            return redirect()->route('dashboard')->with('error', 'No vehicle profile linked to your account. Please contact admin.');
        }

        $trips = Trip::with(['vehicle', 'driver'])
            ->where('vehicle_id', $vehicle->id)
            ->latest()
            ->paginate(10);

        return view('trips.my_trips', compact('trips', 'vehicle'));
    }

    public function create()
    {
        $user = auth()->user();
        $linkedVehicle = null;
        $vehicles = collect();
        $drivers = \App\Models\Driver::where('status', 'active')->get();

        $lastTripData = \App\Models\Vehicle::with(['trips' => function($q) {
            $q->latest('date')->take(1);
        }])->get()->mapWithKeys(function($v) {
            $lastTrip = $v->trips->first();
            return [$v->id => [
                'close_km' => $lastTrip ? $lastTrip->close_km : 0,
                'close_hour' => $lastTrip ? $lastTrip->close_hour : 0,
            ]];
        });

        if ($user->hasRole('Vehicle')) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle) {
                return redirect()->route('dashboard')->with('error', 'No vehicle profile linked to your account. Please contact admin.');
            }
        } else {
            $vehicles = \App\Models\Vehicle::where('status', 'active')->get();
        }

        return view('trips.create', compact('vehicles', 'drivers', 'linkedVehicle', 'lastTripData'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('Vehicle')) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle) {
                return redirect()->route('dashboard')->with('error', 'No vehicle profile linked to your account.');
            }
            $request->merge(['vehicle_id' => $linkedVehicle->id]);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'to_date' => 'nullable|date|after_or_equal:date',
            'location' => 'required|string|max:255',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'trip_type' => 'nullable|in:rent,own,empty',
            'open_km' => 'nullable|numeric',
            'close_km' => 'nullable|numeric',
            'open_hour' => 'nullable',
            'close_hour' => 'nullable',
            'rent_amount' => 'nullable|numeric',
            'padi_kaasu' => 'nullable|numeric',
            'work' => 'nullable|string',
            'diesel_price' => 'nullable|numeric',
            'fuel_litre' => 'nullable|numeric',
            'user_name' => 'nullable|string',
        ]);


        if (empty($validated['open_km']) && empty($validated['open_hour'])) {
            return back()->withInput()->withErrors(['open_km' => 'At least open_km OR open_hour must be filled.']);
        }

        // Set defaults for nullable fields
        $validated['date'] = $validated['date'] ?? now()->toDateString();
        $validated['open_km'] = $validated['open_km'] ?? 0;
        $validated['close_km'] = $validated['close_km'] ?? 0;
        $validated['rent_amount'] = $validated['rent_amount'] ?? 0;
        $validated['padi_kaasu'] = $validated['padi_kaasu'] ?? 0;
        $validated['diesel_price'] = $validated['diesel_price'] ?? 0;
        $validated['fuel_litre'] = $validated['fuel_litre'] ?? 0;

        $validated['total_km'] = $validated['close_km'] - $validated['open_km'];
        if ($validated['total_km'] < 0) $validated['total_km'] = 0;
        $validated['fuel_cost'] = $validated['diesel_price'] * $validated['fuel_litre'];
        
        // Hour diff logic
        $openHour = (float)($validated['open_hour'] ?? 0);
        $closeHour = (float)($validated['close_hour'] ?? 0);
        $validated['total_hour'] = max(0, $closeHour - $openHour);


        $trip = Trip::create($validated);

        \App\Models\ActivityLog::log('created', 'Trip', 'New trip created to ' . $trip->location);

        if ($user->hasRole('Vehicle')) {
            return redirect()->route('trips.my')->with('success', 'Trip added successfully!');
        }

        return redirect()->route('trips.index')->with('success', 'Trip added successfully!');
    }

    public function edit(Trip $trip)
    {
        $user = auth()->user();

        // Vehicle user can only edit their own vehicle's trips
        if ($user->hasRole('Vehicle')) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle || $trip->vehicle_id !== $linkedVehicle->id) {
                return redirect()->route('trips.my')->with('error', 'You can only edit your own vehicle trips.');
            }
        }

        $linkedVehicle = null;
        $vehicles = collect();
        $drivers = \App\Models\Driver::where('status', 'active')->get();

        if ($user->hasRole('Vehicle')) {
            $linkedVehicle = $user->vehicle;
        } else {
            $vehicles = \App\Models\Vehicle::where('status', 'active')->get();
        }

        return view('trips.edit', compact('trip', 'vehicles', 'drivers', 'linkedVehicle'));
    }

    public function update(Request $request, Trip $trip)
    {
        $user = auth()->user();
        $isVehicleUser = $user->hasRole('Vehicle');

        // Vehicle user can only update their own vehicle's trips
        if ($isVehicleUser) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle || $trip->vehicle_id !== $linkedVehicle->id) {
                return redirect()->route('trips.my')->with('error', 'You can only edit your own vehicle trips.');
            }
            $request->merge(['vehicle_id' => $linkedVehicle->id]);
        }

        $rules = [
            'diesel_price' => 'nullable|numeric',
            'fuel_litre' => 'nullable|numeric',
            'close_hour' => 'nullable',
            'location' => 'required|string',
            'close_km' => 'nullable|numeric',
            'maintenance_note' => 'nullable|string',
            'loan_note' => 'nullable|string',
            'padi_kaasu' => 'nullable|numeric',
            'work' => 'nullable|string',
        ];


        // Only validate all fields if not a vehicle user
        if (!$isVehicleUser) {
            $rules = array_merge($rules, [
                'date' => 'required|date',
                'to_date' => 'nullable|date|after_or_equal:date',
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_id' => 'required|exists:drivers,id',
                'trip_type' => 'nullable|in:rent,own,empty',
                'open_km' => 'nullable|numeric',
                'open_hour' => 'nullable',
                'rent_amount' => 'nullable|numeric',
            ]);
        }

        $validated = $request->validate($rules);

        // Keep original values for restricted fields if Vehicle user
        if ($isVehicleUser) {
            $validated['date'] = $trip->date;
            $validated['to_date'] = $trip->to_date;
            $validated['vehicle_id'] = $trip->vehicle_id;
            $validated['driver_id'] = $trip->driver_id;
            $validated['trip_type'] = $trip->trip_type;
            $validated['open_km'] = $trip->open_km;
            $validated['open_hour'] = $trip->open_hour;
            $validated['rent_amount'] = $trip->rent_amount;
            $validated['work'] = $trip->work;
        }

        if (empty($validated['open_km']) && empty($validated['open_hour'])) {
            return back()->withInput()->withErrors(['open_km' => 'At least open_km OR open_hour must be filled.']);
        }

        $validated['open_km'] = $validated['open_km'] ?? 0;
        $validated['close_km'] = $validated['close_km'] ?? 0;
        $validated['rent_amount'] = $validated['rent_amount'] ?? 0;
        $validated['padi_kaasu'] = $validated['padi_kaasu'] ?? 0;
        $validated['diesel_price'] = $validated['diesel_price'] ?? 0;
        $validated['fuel_litre'] = $validated['fuel_litre'] ?? 0;

        $validated['total_km'] = $validated['close_km'] - $validated['open_km'];
        if ($validated['total_km'] < 0) $validated['total_km'] = 0;
        $validated['fuel_cost'] = $validated['diesel_price'] * $validated['fuel_litre'];

        $openHour = (float)($validated['open_hour'] ?? $trip->open_hour);
        $closeHour = (float)($validated['close_hour'] ?? $trip->close_hour);
        $validated['total_hour'] = max(0, $closeHour - $openHour);


        $trip->update($validated);

        \App\Models\ActivityLog::log('updated', 'Trip', 'Trip updated: ' . $trip->location);

        if ($isVehicleUser) {
            return redirect()->route('trips.my')->with('success', 'Trip updated successfully!');
        }

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully!');
    }

    public function destroy(Trip $trip)
    {
        $location = $trip->location;
        $trip->delete();
        \App\Models\ActivityLog::log('deleted', 'Trip', 'Trip deleted: ' . $location);
        return redirect()->route('trips.index')->with('success', 'Trip deleted.');
    }

    public function lastTrip(\App\Models\Vehicle $vehicle)
    {
        $lastTrip = Trip::where('vehicle_id', $vehicle->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();
        
        if ($lastTrip) {
            return response()->json([
                'close_km' => $lastTrip->close_km ?? 0,
                'close_hour' => $lastTrip->close_hour ?? 0,
            ]);
        }
        
        return response()->json([
            'close_km' => 0,
            'close_hour' => 0,
        ]);
    }

}
