<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class DriverUserController extends Controller
{
    /**
     * Show all driver users with their linked driver records.
     */
    public function index()
    {
        $driverUsers = User::role('Driver')->with('driver')->get();
        $unlinkedDrivers = Driver::whereNull('user_id')->where('status', 'active')->get();
        return view('admin.driver_users', compact('driverUsers', 'unlinkedDrivers'));
    }

    /**
     * Create a new driver user account linked to a driver record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Driver');

        // Link driver record to user
        Driver::where('id', $validated['driver_id'])->update(['user_id' => $user->id]);

        \App\Models\ActivityLog::log('created', 'DriverUser', 'Driver user account created for ' . $user->name);

        return redirect()->route('admin.driver-users.index')->with('success', 'Driver user account created successfully!');
    }

    /**
     * Update driver user credentials.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update driver link if changed
        if (isset($validated['driver_id'])) {
            // Unlink old driver
            Driver::where('user_id', $user->id)->update(['user_id' => null]);
            // Link new driver
            Driver::where('id', $validated['driver_id'])->update(['user_id' => $user->id]);
        }

        \App\Models\ActivityLog::log('updated', 'DriverUser', 'Driver user credentials updated for ' . $user->name);

        return redirect()->route('admin.driver-users.index')->with('success', 'Driver credentials updated successfully!');
    }

    /**
     * Delete a driver user account.
     */
    public function destroy(User $user)
    {
        // Unlink driver record
        Driver::where('user_id', $user->id)->update(['user_id' => null]);

        $name = $user->name;
        $user->delete();

        \App\Models\ActivityLog::log('deleted', 'DriverUser', 'Driver user account deleted: ' . $name);

        return redirect()->route('admin.driver-users.index')->with('success', 'Driver user account deleted.');
    }
}
