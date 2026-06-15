<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Fuel Entries') }}
            </h2>
            <a href="{{ route('fuel.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Add Fuel</a>
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
                <form action="{{ route('fuel.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Vehicle</label>
                        <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded shadow hover:bg-gray-900 transition">Filter</button>
                        <a href="{{ route('fuel.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table Layout -->
            <div class="bg-white rounded-lg shadow-sm border-t-4 border-red-500 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Trip Details</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fuel Details</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($fuelEntries as $entry)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                        {{ $entry->created_at->format('d M Y') }}
                                        <div class="text-xs text-slate-500">{{ $entry->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-blue-600 uppercase">
                                        {{ $entry->vehicle->vehicle_number ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        @if($entry->trip)
                                            <div class="font-bold">{{ $entry->trip->location }}</div>
                                            <div class="text-xs text-slate-500">{{ $entry->trip->date->format('d/m/y') }}</div>
                                        @else
                                            <span class="text-gray-400 italic">No trip linked</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        <div><span class="text-slate-500">Litre:</span> {{ $entry->litre }} L</div>
                                        <div><span class="text-slate-500">Price:</span> ₹{{ number_format($entry->price, 2) }}/L</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-red-600">
                                        ₹{{ number_format($entry->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('fuel.edit', $entry) }}" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-2 py-1 rounded transition">Edit</a>
                                            <form action="{{ route('fuel.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('Delete this entry?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-2 py-1 rounded transition">Del</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-bold">
                                        No fuel entries found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 ">
                {{ $fuelEntries->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
