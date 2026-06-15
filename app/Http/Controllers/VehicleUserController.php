<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class VehicleUserController extends Controller
{
    /**
     * Show all vehicle users with their linked vehicle records.
     */
    public function index()
    {
        $vehicleUsers = User::role('Vehicle')->with('vehicle')->get();
        $unlinkedVehicles = Vehicle::whereNull('user_id')->where('status', 'active')->get();
        return view('admin.vehicle_users', compact('vehicleUsers', 'unlinkedVehicles'));
    }

    /**
     * Create a new vehicle user account linked to a vehicle record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Generate a random email since email is required by User migration
        $email = strtolower($vehicle->vehicle_number) . '@vms.local';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        // Ensure Vehicle role exists or use it
        $user->assignRole('Vehicle');

        // Link vehicle record to user
        $vehicle->update(['user_id' => $user->id]);

        \App\Models\ActivityLog::log('created', 'VehicleUser', 'Vehicle user account created for ' . $user->name);

        return redirect()->route('admin.vehicle-users.index')->with('success', 'Vehicle user account created successfully!');
    }

    /**
     * Update vehicle user credentials.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $user->update(['name' => $validated['name']]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update vehicle link if changed
        if (isset($validated['vehicle_id'])) {
            // Unlink old vehicle
            Vehicle::where('user_id', $user->id)->update(['user_id' => null]);
            // Link new vehicle
            $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
            $vehicle->update(['user_id' => $user->id]);
            
            // Also update the username to the new vehicle number
            $user->update([
                'name' => $vehicle->vehicle_number,
                'email' => strtolower($vehicle->vehicle_number) . '@vms.local'
            ]);
        }

        \App\Models\ActivityLog::log('updated', 'VehicleUser', 'Vehicle user credentials updated for ' . $user->name);

        return redirect()->route('admin.vehicle-users.index')->with('success', 'Vehicle credentials updated successfully!');
    }

    /**
     * Delete a vehicle user account.
     */
    public function destroy(User $user)
    {
        // Unlink vehicle record
        Vehicle::where('user_id', $user->id)->update(['user_id' => null]);

        $name = $user->name;
        $user->delete();

        \App\Models\ActivityLog::log('deleted', 'VehicleUser', 'Vehicle user account deleted: ' . $name);

        return redirect()->route('admin.vehicle-users.index')->with('success', 'Vehicle user account deleted.');
    }
}
