<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Drivers') }}
            </h2>
            <a href="{{ route('drivers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">Add Driver</a>
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
                <form action="{{ route('drivers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Driver</label>
                        <select name="id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Drivers</option>
                            @foreach($allDrivers as $d)
                                <option value="{{ $d->id }}" {{ request('id') == $d->id ? 'selected' : '' }}>{{ $d->driver_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Phone</label>
                        <input type="text" name="phone" value="{{ request('phone') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" placeholder="Phone">
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
                        <a href="{{ route('drivers.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table Layout -->
            <div class="bg-white rounded-lg shadow-sm border-t-4 border-orange-500 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Driver Info</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">License</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($drivers as $driver)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-black text-slate-900">{{ $driver->driver_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">📞 {{ $driver->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        <div class="font-bold">{{ $driver->license_number }}</div>
                                        <div class="text-[10px] {{ $driver->license_expiry->isPast() ? 'text-red-600 font-bold uppercase' : 'text-slate-500' }}">
                                            Exp: {{ $driver->license_expiry->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 bg-slate-50/80 text-xs font-bold rounded text-slate-700 uppercase">
                                            🚗 {{ $driver->vehicle->vehicle_number ?? 'Unassigned' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 {{ $driver->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-[10px] font-bold rounded uppercase">
                                            {{ $driver->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('drivers.edit', $driver) }}" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded transition">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-bold">
                                        No drivers found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 ">
                {{ $drivers->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
