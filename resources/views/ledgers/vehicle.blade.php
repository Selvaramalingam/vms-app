<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Vehicle Ledger') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6">
                <div class="p-6">
                    <form action="{{ route('ledgers.vehicle') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Select Vehicle</label>
                            <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                <option value="">-- Choose Vehicle --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ $vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ $start_date }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">End Date</label>
                            <input type="date" name="end_date" value="{{ $end_date }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-bold">Filter</button>
                            <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700 font-bold">Print</button>
                        </div>
                    </form>
                </div>
            </div>

            @if($data)
            <div id="printableArea">
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center border-b pb-4 mb-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">Vehicle Ledger: {{ $vehicles->find($vehicle_id)->vehicle_number }}</h3>
                            <p class="text-sm text-slate-500">Period: {{ Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Net Profit</p>
                            <p class="text-3xl font-black {{ $data['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($data['net_profit'], 2) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                            <p class="text-green-600 text-sm font-bold uppercase">Total Income</p>
                            <p class="text-2xl font-black text-green-700">₹{{ number_format($data['total_income'], 2) }}</p>
                            <p class="text-xs text-green-500 mt-1">{{ $data['trips']->count() }} Trips recorded</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                            <p class="text-red-600 text-sm font-bold uppercase">Fuel Expense</p>
                            <p class="text-2xl font-black text-red-700">₹{{ number_format($data['total_fuel'], 2) }}</p>
                            <p class="text-xs text-red-500 mt-1">{{ $data['fuel']->count() }} Fuel entries</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <p class="text-orange-600 text-sm font-bold uppercase">Maintenance</p>
                            <p class="text-2xl font-black text-orange-700">₹{{ number_format($data['total_maintenance'], 2) }}</p>
                            <p class="text-xs text-orange-500 mt-1">{{ $data['maintenances']->count() }} Service records</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Trips Breakdown -->
                        <div>
                            <h4 class="font-bold text-slate-700 mb-2  pl-2 uppercase tracking-wide text-xs">Income Breakdown</h4>
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50/50">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Date</th>
                                        <th class="px-2 py-2 text-left">Location</th>
                                        <th class="px-2 py-2 text-right">Rent Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['trips'] as $trip)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-2 py-2">{{ $trip->date->format('d/m/y') }}</td>
                                        <td class="px-2 py-2">{{ $trip->location }}</td>
                                        <td class="px-2 py-2 text-right">₹{{ number_format($trip->rent_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Expenses Breakdown -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="font-bold text-slate-700 mb-2  pl-2 uppercase tracking-wide text-xs">Fuel History</h4>
                                <table class="min-w-full text-xs">
                                    <thead class="bg-slate-50/50">
                                        <tr>
                                            <th class="px-2 py-2 text-left">Date</th>
                                            <th class="px-2 py-2 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['fuel'] as $f)
                                        <tr class="border-b border-gray-100">
                                            <td class="px-2 py-2">{{ $f->created_at->format('d/m/y') }}</td>
                                            <td class="px-2 py-2 text-right">₹{{ number_format($f->total, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-700 mb-2  pl-2 uppercase tracking-wide text-xs">Maintenance History</h4>
                                <table class="min-w-full text-xs">
                                    <thead class="bg-slate-50/50">
                                        <tr>
                                            <th class="px-2 py-2 text-left">Date</th>
                                            <th class="px-2 py-2 text-right">Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['maintenances'] as $m)
                                        <tr class="border-b border-gray-100">
                                            <td class="px-2 py-2">{{ $m->date->format('d/m/y') }}</td>
                                            <td class="px-2 py-2 text-right">₹{{ number_format($m->cost, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
                <div class="bg-white p-12 rounded-lg text-center shadow-sm">
                    <p class="text-gray-400 font-bold uppercase tracking-widest">Select a vehicle and date range to view ledger</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none; }
    }
</style>
