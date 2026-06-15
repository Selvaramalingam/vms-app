<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Driver Ledger') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6">
                <div class="p-6">
                    <form action="{{ route('ledgers.driver') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Select Driver</label>
                            <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                <option value="">-- Choose Driver --</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" {{ $driver_id == $d->id ? 'selected' : '' }}>{{ $d->driver_name }}</option>
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
                            <h3 class="text-2xl font-black text-slate-900">Driver Ledger: {{ $drivers->find($driver_id)->driver_name }}</h3>
                            <p class="text-sm text-slate-500">Period: {{ Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Total Trips</p>
                            <p class="text-3xl font-black text-blue-600">{{ $data['total_trips'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <p class="text-blue-600 text-sm font-bold uppercase">Total Kilometers</p>
                            <p class="text-2xl font-black text-blue-700">{{ number_format($data['total_km'], 2) }} KM</p>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                            <p class="text-indigo-600 text-sm font-bold uppercase">Total Hours</p>
                            <p class="text-2xl font-black text-indigo-700">{{ number_format($data['total_hour'], 1) }} Hours</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-700 mb-2  pl-2 uppercase tracking-wide text-xs">Trip History</h4>
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-2 py-2 text-left">Date</th>
                                    <th class="px-2 py-2 text-left">Location</th>
                                    <th class="px-2 py-2 text-left">Vehicle</th>
                                    <th class="px-2 py-2 text-right">KM</th>
                                    <th class="px-2 py-2 text-right">Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['trips'] as $trip)
                                <tr class="border-b border-gray-100">
                                    <td class="px-2 py-2">{{ $trip->date->format('d/m/y') }}</td>
                                    <td class="px-2 py-2">{{ $trip->location }}</td>
                                    <td class="px-2 py-2">{{ $trip->vehicle->vehicle_number ?? '-' }}</td>
                                    <td class="px-2 py-2 text-right">{{ $trip->total_km }}</td>
                                    <td class="px-2 py-2 text-right">{{ $trip->total_hour }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
                <div class="bg-white p-12 rounded-lg text-center shadow-sm">
                    <p class="text-gray-400 font-bold uppercase tracking-widest">Select a driver and date range to view ledger</p>
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
    }
</style>
