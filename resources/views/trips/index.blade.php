<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Trips') }}
            </h2>
            <a href="{{ route('trips.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Add Trip</a>
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
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 px-4 sm:px-6">
                <form action="{{ route('trips.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Vehicle</label>
                        <select name="vehicle_id" class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Driver</label>
                        <select name="driver_id" class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->driver_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Type</label>
                        <select name="trip_type" class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="rent" {{ request('trip_type') == 'rent' ? 'selected' : '' }}>Rent</option>
                            <option value="own" {{ request('trip_type') == 'own' ? 'selected' : '' }}>Own</option>
                            <option value="empty" {{ request('trip_type') == 'empty' ? 'selected' : '' }}>Empty</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white font-medium text-sm py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition">Filter</button>
                        <a href="{{ route('trips.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg shadow-sm hover:bg-slate-50 transition flex items-center justify-center">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table Layout -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle & Driver</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Location & Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Financials</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($trips as $trip)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">
                                        {{ $trip->date ? $trip->date->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        <div class="font-bold text-blue-600">{{ $trip->vehicle->vehicle_number ?? 'N/A' }}</div>
                                        <div class="text-xs text-slate-500">{{ $trip->driver->driver_name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        <div class="font-bold">{{ $trip->location }}</div>
                                        <div class="flex gap-2 items-center mt-1">
                                            <span class="px-2 py-0.5 bg-slate-50/80 text-[10px] font-bold rounded uppercase">{{ $trip->trip_type }}</span>
                                            <span class="text-xs text-slate-500">{{ $trip->total_km }} km</span>
                                            <span class="text-xs text-indigo-500 font-bold ml-1">{{ $trip->total_hour }} hrs</span>
                                        </div>
                                        @if($trip->maintenance_note || $trip->loan_note)
                                            <div class="mt-2 space-y-1">
                                                @if($trip->maintenance_note)
                                                    <p class="text-[10px] text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded inline-block">🛠️ {{ $trip->maintenance_note }}</p>
                                                @endif
                                                @if($trip->loan_note)
                                                    <p class="text-[10px] text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded inline-block">💰 {{ $trip->loan_note }}</p>
                                                @endif
                                            </div>
                                        @endif


                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-xs">
                                            <span class="text-slate-500">Rent:</span> <span class="font-bold text-green-600 text-right">₹{{ number_format($trip->rent_amount, 2) }}</span>
                                            <span class="text-slate-500">Fuel:</span> <span class="font-bold text-red-500 text-right">₹{{ number_format($trip->fuel_cost, 2) }}</span>
                                            <span class="text-slate-500 font-bold">Profit:</span> <span class="font-black text-blue-600 text-right">₹{{ number_format($trip->profit, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('trips.edit', $trip) }}" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-2 py-1 rounded transition">Edit</a>
                                            <a href="{{ route('trips.payments', $trip) }}" class="text-green-600 hover:text-green-900 bg-green-50 px-2 py-1 rounded transition">Pay</a>
                                            <form action="{{ route('trips.destroy', $trip) }}" method="POST" class="inline" onsubmit="return confirm('Delete this trip?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-2 py-1 rounded transition">Del</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-bold">
                                        No trips found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 ">
                {{ $trips->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
