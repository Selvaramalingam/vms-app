<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Vehicle Accounts') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="bg-red-100  text-red-700 p-4 rounded shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Create New Vehicle User -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm border-t-4 border-blue-500">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Create Vehicle Login</h3>
                    <form action="{{ route('admin.vehicle-users.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Link to Vehicle</label>
                                <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                    <option value="">Select an unlinked vehicle</option>
                                    @foreach($unlinkedVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700">User Name</label>
                                <input type="text" name="name" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" placeholder="e.g. Vicky" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Password</label>
                                <input type="password" name="password" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700 font-bold">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Vehicle Users -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Existing Vehicle Accounts</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">User Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Login ID (Vehicle No.)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Linked Vehicle</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($vehicleUsers as $user)
                                    <tr x-data="{ editing: false }">
                                        <!-- View Mode -->
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900" x-show="!editing">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600" x-show="!editing">
                                            {{ $user->vehicle ? $user->vehicle->vehicle_number : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-show="!editing">
                                            @if($user->vehicle)
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">{{ $user->vehicle->vehicle_number }}</span>
                                            @else
                                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Unlinked</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap space-x-2" x-show="!editing">
                                            <button @click="editing = true" class="text-blue-600 hover:text-blue-900 font-bold text-sm bg-blue-50 px-3 py-1 rounded">Edit</button>
                                            
                                            <form action="{{ route('admin.vehicle-users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this vehicle login? This will NOT delete the vehicle record itself.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-sm bg-red-50 px-3 py-1 rounded">Delete</button>
                                            </form>
                                        </td>

                                        <!-- Edit Mode -->
                                        <td colspan="3" class="px-6 py-4" x-show="editing" style="display: none;">
                                            <form action="{{ route('admin.vehicle-users.update', $user) }}" method="POST" class="bg-slate-50/50 p-4 rounded-lg border border-gray-200">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700">User Name</label>
                                                        <input type="text" name="name" value="{{ $user->name }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm text-sm" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700">Change Link (Vehicle)</label>
                                                        <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm text-sm">
                                                            @if($user->vehicle)
                                                                <option value="{{ $user->vehicle->id }}" selected>{{ $user->vehicle->vehicle_number }} (Current)</option>
                                                            @else
                                                                <option value="">-- None --</option>
                                                            @endif
                                                            @foreach($unlinkedVehicles as $v)
                                                                <option value="{{ $v->id }}">{{ $v->vehicle_number }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700">New Password (leave blank to keep)</label>
                                                        <input type="password" name="password" class="mt-1 block w-full rounded-md border-slate-200 text-sm text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700">Confirm New Password</label>
                                                        <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-slate-200 text-sm text-sm">
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="editing = false" class="bg-gray-200 text-slate-900 px-4 py-2 rounded text-sm hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-indigo-700">Save Changes</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-slate-500">No vehicle logins created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
