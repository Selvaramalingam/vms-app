<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Vehicles') }}
            </h2>
            <a href="{{ route('vehicles.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Add Vehicle</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4 px-4 sm:px-6">
                <form action="{{ route('vehicles.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Vehicle</label>
                        <select name="id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Vehicles</option>
                            @foreach($allVehicles as $v)
                                <option value="{{ $v->id }}" {{ request('id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded shadow hover:bg-gray-900 transition">Filter</button>
                        <a href="{{ route('vehicles.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table Layout -->
            <div class="bg-white rounded-lg shadow-sm border-t-4 border-blue-500 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Owner</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Service Info</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Expiries</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($vehicles as $vehicle)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-black text-slate-900 uppercase">{{ $vehicle->vehicle_number }}</div>
                                        <div class="text-xs text-slate-500">{{ $vehicle->vehicle_type }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        <div class="uppercase font-bold">{{ $vehicle->owner_type }}</div>
                                        <div class="text-xs text-slate-500">{{ $vehicle->owner_name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 {{ $vehicle->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-bold rounded uppercase">
                                            {{ $vehicle->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        <div><span class="text-slate-500">Next KM:</span> {{ $vehicle->next_service_km ? $vehicle->next_service_km . ' km' : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-2 gap-y-1">
                                            <div class="{{ $vehicle->fc_expiry && $vehicle->fc_expiry->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                <span class="text-gray-400">FC:</span> {{ $vehicle->fc_expiry ? $vehicle->fc_expiry->format('d/m/y') : 'N/A' }}
                                            </div>
                                            <div class="{{ $vehicle->insurance_expiry && $vehicle->insurance_expiry->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                <span class="text-gray-400">Ins:</span> {{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/y') : 'N/A' }}
                                            </div>
                                            <div class="{{ $vehicle->permit_expiry && $vehicle->permit_expiry->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                <span class="text-gray-400">Permit:</span> {{ $vehicle->permit_expiry ? $vehicle->permit_expiry->format('d/m/y') : 'N/A' }}
                                            </div>
                                            <div class="{{ $vehicle->tax_expiry && $vehicle->tax_expiry->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                <span class="text-gray-400">Tax:</span> {{ $vehicle->tax_expiry ? $vehicle->tax_expiry->format('d/m/y') : 'N/A' }}
                                            </div>
                                            <div class="{{ $vehicle->caliber_certificate_date && $vehicle->caliber_certificate_date->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                <span class="text-gray-400">Caliber:</span> {{ $vehicle->caliber_certificate_date ? $vehicle->caliber_certificate_date->format('d/m/y') : 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded transition">Edit</a>
                                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Delete this vehicle?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-3 py-1.5 rounded transition">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-bold">
                                        No vehicles found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 ">
                {{ $vehicles->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
