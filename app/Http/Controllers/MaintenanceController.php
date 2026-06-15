<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Maintenance::with('vehicle')->latest();

        if ($user->hasRole('Vehicle') || $user->hasRole('Driver')) {
            $vehicle = $user->vehicle;
            if (!$vehicle) {
                return redirect()->route('dashboard')->with('error', 'No vehicle profile linked to your account.');
            }
            $query->where('vehicle_id', $vehicle->id);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $maintenances = $query->paginate(10)->withQueryString();

        $vehicles = collect();
        if ($user->hasRole('Admin') || $user->hasRole('Staff')) {
            $vehicles = Vehicle::where('status', 'active')->orderBy('vehicle_number')->get();
        } else {
            $vehicles = collect([$user->vehicle])->filter();
        }

        return view('maintenances.index', compact('maintenances', 'vehicles'));
    }

    public function approve(Maintenance $maintenance)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $maintenance->update(['status' => 'approved']);
        
        $this->updateVehicleServiceData([
            'vehicle_id' => $maintenance->vehicle_id,
            'km' => $maintenance->km,
            'date' => $maintenance->date,
            'next_service_km' => $maintenance->next_service_km,
            'next_service_date' => $maintenance->next_service_date,
        ]);

        \App\Models\ActivityLog::log('updated', 'Maintenance', 'Maintenance record approved #' . $maintenance->id . ' for ' . $maintenance->vehicle->vehicle_number);

        return redirect()->route('maintenances.index')->with('success', 'Maintenance record approved and vehicle data updated!');
    }

    public function reject(Maintenance $maintenance)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $maintenance->update(['status' => 'rejected']);

        \App\Models\ActivityLog::log('updated', 'Maintenance', 'Maintenance record rejected #' . $maintenance->id);

        return redirect()->route('maintenances.index')->with('success', 'Maintenance record rejected.');
    }


    public function create()
    {
        $user = auth()->user();
        $vehicles = collect();
        $linkedVehicle = null;

        if ($user->hasRole('Vehicle') || $user->hasRole('Driver')) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle) {
                return redirect()->route('dashboard')->with('error', 'No vehicle profile linked to your account.');
            }
        } else {
            $vehicles = Vehicle::where('status', 'active')->get();
        }

        return view('maintenances.create', compact('vehicles', 'linkedVehicle'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isVehicleUser = $user->hasRole('Vehicle') || $user->hasRole('Driver');

        if ($isVehicleUser) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle) {
                return redirect()->route('dashboard')->with('error', 'No vehicle profile linked to your account.');
            }
            $request->merge(['vehicle_id' => $linkedVehicle->id]);
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'km' => 'nullable|numeric',
            'hours' => 'nullable|numeric',
            'next_service_km' => 'nullable|numeric',
            'next_service_date' => 'nullable|date',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $validated['date'] = $validated['date'] ?? now()->toDateString();
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['km'] = $validated['km'] ?? 0;
        $validated['hours'] = $validated['hours'] ?? 0;
        
        if ($isVehicleUser) {
            $validated['status'] = 'pending';
        } else {
            $validated['status'] = $validated['status'] ?? 'pending';
        }

        $maintenance = Maintenance::create($validated);

        // Update vehicle's service tracking only if approved
        if ($maintenance->status == 'approved' && !empty($validated['vehicle_id'])) {
            $this->updateVehicleServiceData($validated);
        }

        \App\Models\ActivityLog::log('created', 'Maintenance', 'Maintenance record added for ' . Maintenance::find($maintenance->id)->vehicle->vehicle_number);

        return redirect()->route('maintenances.index')->with('success', 'Maintenance record added successfully!');
    }

    public function edit(Maintenance $maintenance)
    {
        $user = auth()->user();

        if ($user->hasRole('Vehicle') || $user->hasRole('Driver')) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle || $maintenance->vehicle_id !== $linkedVehicle->id) {
                return redirect()->route('maintenances.index')->with('error', 'You can only edit your own vehicle maintenance.');
            }
            if ($maintenance->status === 'approved') {
                return redirect()->route('maintenances.index')->with('error', 'Approved maintenance records cannot be edited.');
            }
        }

        $vehicles = Vehicle::where('status', 'active')->get();
        return view('maintenances.edit', compact('maintenance', 'vehicles'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $user = auth()->user();
        $isVehicleUser = $user->hasRole('Vehicle') || $user->hasRole('Driver');

        if ($isVehicleUser) {
            $linkedVehicle = $user->vehicle;
            if (!$linkedVehicle || $maintenance->vehicle_id !== $linkedVehicle->id) {
                return redirect()->route('maintenances.index')->with('error', 'You can only edit your own vehicle maintenance.');
            }
            if ($maintenance->status === 'approved') {
                return redirect()->route('maintenances.index')->with('error', 'Approved maintenance records cannot be edited.');
            }
            $request->merge(['vehicle_id' => $linkedVehicle->id, 'status' => 'pending']);
        }

        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'km' => 'nullable|numeric',
            'hours' => 'nullable|numeric',
            'next_service_km' => 'nullable|numeric',
            'next_service_date' => 'nullable|date',
        ];

        if (!$isVehicleUser) {
            $rules['status'] = 'nullable|in:pending,approved,rejected';
        }

        $validated = $request->validate($rules);

        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['km'] = $validated['km'] ?? 0;
        $validated['hours'] = $validated['hours'] ?? 0;

        $wasNotApproved = $maintenance->status !== 'approved';
        $isNowApproved = isset($validated['status']) && $validated['status'] === 'approved';

        $maintenance->update($validated);

        if ($wasNotApproved && $isNowApproved && !empty($validated['vehicle_id'])) {
            $this->updateVehicleServiceData($validated);
        }

        \App\Models\ActivityLog::log('updated', 'Maintenance', 'Maintenance record updated #' . $maintenance->id);

        return redirect()->route('maintenances.index')->with('success', 'Maintenance record updated!');
    }

    public function destroy(Maintenance $maintenance)
    {
        $user = auth()->user();
        if ($user->hasRole('Vehicle') || $user->hasRole('Driver')) {
            if ($maintenance->status === 'approved') {
                return redirect()->route('maintenances.index')->with('error', 'Approved maintenance records cannot be deleted.');
            }
            if ($maintenance->vehicle_id !== $user->vehicle->id) {
                return redirect()->route('maintenances.index')->with('error', 'Unauthorized.');
            }
        }

        $maintenance->delete();
        \App\Models\ActivityLog::log('deleted', 'Maintenance', 'Maintenance record deleted #' . $maintenance->id);
        return redirect()->route('maintenances.index')->with('success', 'Maintenance record deleted.');
    }

    private function updateVehicleServiceData($validated)
    {
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($vehicle) {
            $vehicle->update([
                'last_service_km' => $validated['km'],
                'last_service_date' => $validated['date'],
                'next_service_km' => $validated['next_service_km'] ?? $vehicle->next_service_km,
                'next_service_date' => $validated['next_service_date'] ?? $vehicle->next_service_date,
            ]);
        }
    }
}
