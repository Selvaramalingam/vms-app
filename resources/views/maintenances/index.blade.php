<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Maintenance') }}
            </h2>
            <a href="{{ route('maintenances.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">Add Record</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100  text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4 px-4 sm:px-6">
                <form action="{{ route('maintenances.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Vehicle</label>
                        <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded shadow hover:bg-gray-900 transition">Filter</button>
                        <a href="{{ route('maintenances.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table Layout -->
            <div class="bg-white rounded-lg shadow-sm border-t-4 border-blue-500 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Service Details</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Cost & Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($maintenances as $m)
                                <tr class="hover:bg-slate-50/50 transition border-l-4 {{ $m->status === 'approved' ? 'border-green-500' : ($m->status === 'rejected' ? 'border-red-500' : 'border-yellow-500') }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">
                                        {{ $m->date ? $m->date->format('d M Y') : 'No Date' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-blue-600 uppercase">
                                        {{ $m->vehicle->vehicle_number ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 max-w-xs truncate">
                                        {{ $m->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-900">
                                        <div><span class="text-slate-500">Service KM:</span> {{ $m->km }}</div>
                                        <div><span class="text-slate-500">Hours:</span> {{ $m->hours ?? 0 }}</div>
                                        <div class="mt-1 text-[10px] text-blue-500 font-bold">Next: {{ $m->next_service_km ?? 'N/A' }} km</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-black text-slate-900">₹{{ number_format($m->cost, 2) }}</div>
                                        <div class="mt-1">
                                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded 
                                                {{ $m->status === 'approved' ? 'bg-green-100 text-green-800' : ($m->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $m->status }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @php
                                            $isAdmin = auth()->user()->hasRole('Admin');
                                            $isVehicle = auth()->user()->hasRole('Vehicle');
                                            $canEdit = $isAdmin || ($isVehicle && $m->status !== 'approved');
                                        @endphp
                                        
                                        <div class="flex flex-wrap gap-2">
                                            @if($isAdmin && $m->status === 'pending')
                                                <form action="{{ route('maintenances.approve', $m) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-xs font-bold transition shadow-sm">Approve</button>
                                                </form>
                                                <form action="{{ route('maintenances.reject', $m) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-xs font-bold transition shadow-sm">Reject</button>
                                                </form>
                                            @endif

                                            @if($canEdit)
                                                <a href="{{ route('maintenances.edit', $m) }}" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-2 py-1 rounded transition text-xs font-bold">Edit</a>
                                                <form action="{{ route('maintenances.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Delete this record?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-2 py-1 rounded transition text-xs font-bold">Del</button>
                                                </form>
                                            @elseif(!$isAdmin)
                                                <span class="text-gray-400 italic text-xs">Locked</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-bold">
                                        No maintenance records found matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 ">
                {{ $maintenances->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
