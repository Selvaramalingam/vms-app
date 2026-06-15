<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6">
                <div class="p-6">
                    <form action="{{ route('reports.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:gap-4 md:items-end">
                        
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-700">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-700">Vehicle</label>
                            <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-700">Driver</label>
                            <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                <option value="">All Drivers</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->driver_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto mt-4 md:mt-0">
                            <button type="submit" class="flex-1 md:flex-none bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Filter</button>
                            <a href="{{ route('reports.index') }}" class="flex-1 md:flex-none bg-gray-200 text-slate-900 text-center px-4 py-2 rounded shadow hover:bg-gray-300">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results & Export Actions -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-700">Report Results ({{ $trips->total() }})</h3>
                    
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button onclick="window.print()" class="flex-1 sm:flex-none bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700 font-bold">🖨️ Print</button>
                        
                        <form action="{{ route('reports.index') }}" method="GET" class="flex-1 sm:flex-none no-loader">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}">
                            <input type="hidden" name="driver_id" value="{{ request('driver_id') }}">
                            <input type="hidden" name="export" value="pdf">
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 font-bold">📄 PDF</button>
                        </form>
                        
                        <form action="{{ route('reports.index') }}" method="GET" class="flex-1 sm:flex-none no-loader">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}">
                            <input type="hidden" name="driver_id" value="{{ request('driver_id') }}">
                            <input type="hidden" name="export" value="excel">
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 font-bold">📊 Excel</button>
                        </form>
                    </div>
                </div>
                
                <!-- Print Area View -->
                <div class="p-6 overflow-x-auto" id="printableArea">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-4 py-2 text-left font-bold text-slate-700 uppercase">Date</th>
                                <th class="px-4 py-2 text-left font-bold text-slate-700 uppercase">Location</th>
                                <th class="px-4 py-2 text-left font-bold text-slate-700 uppercase">Vehicle</th>
                                <th class="px-4 py-2 text-left font-bold text-slate-700 uppercase">Driver</th>
                                <th class="px-4 py-2 text-right font-bold text-slate-700 uppercase">Rent</th>
                                <th class="px-4 py-2 text-right font-bold text-slate-700 uppercase">Fuel</th>
                                <th class="px-4 py-2 text-right font-bold text-slate-700 uppercase">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $totalRent = 0; $totalFuel = 0; $totalProfit = 0;
                            @endphp
                            @forelse($trips as $trip)
                                @php
                                    $totalRent += $trip->rent_amount;
                                    $totalFuel += $trip->fuel_cost;
                                    $totalProfit += $trip->profit;
                                @endphp
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $trip->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">{{ $trip->location }}</td>
                                    <td class="px-4 py-2">{{ $trip->vehicle->vehicle_number ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $trip->driver->driver_name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-right">₹{{ number_format($trip->rent_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-right">₹{{ number_format($trip->fuel_cost, 2) }}</td>
                                    <td class="px-4 py-2 text-right font-bold">₹{{ number_format($trip->profit, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">No records found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($trips->count() > 0)
                        <tfoot class="bg-slate-50/50 font-bold">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right uppercase text-gray-600">Page Totals:</td>
                                <td class="px-4 py-3 text-right text-green-600">₹{{ number_format($totalRent, 2) }}</td>
                                <td class="px-4 py-3 text-right text-red-600">₹{{ number_format($totalFuel, 2) }}</td>
                                <td class="px-4 py-3 text-right text-blue-600">₹{{ number_format($totalProfit, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                
                <div class="p-4 border-t border-gray-200">
                    {{ $trips->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>
