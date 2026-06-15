<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4  mb-6">

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-4 ">
                    <div class="text-sm text-slate-500 uppercase font-bold">Today Trips</div>
                    <div class="text-2xl font-black text-slate-900">{{ $todayTrips }}</div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-4 ">
                    <div class="text-sm text-slate-500 uppercase font-bold">Today Hours</div>
                    <div class="text-2xl font-black text-indigo-600">{{ number_format($todayHours, 2) }}</div>
                </div>

                
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-4 ">
                    <div class="text-sm text-slate-500 uppercase font-bold">Today Income</div>
                    <div class="text-2xl font-black text-green-600">₹{{ number_format($todayIncome, 2) }}</div>
                </div>
                
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-4 ">
                    <div class="text-sm text-slate-500 uppercase font-bold">Fuel Expense</div>
                    <div class="text-2xl font-black text-red-600">₹{{ number_format($fuelExpense, 2) }}</div>
                </div>
                
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-4 ">
                    <div class="text-sm text-slate-500 uppercase font-bold">Today Profit</div>
                    <div class="text-2xl font-black text-purple-600">₹{{ number_format($profit, 2) }}</div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class=" mb-6">
                <div class="bg-amber-50 border border-amber-200  text-orange-700 p-4 rounded shadow-sm flex justify-between items-center">
                    <div>
                        <p class="font-bold text-lg">Pending Payments</p>
                        <p>Total outstanding balance from trips.</p>
                    </div>
                    <div class="text-2xl font-black">₹{{ number_format($pendingPayments, 2) }}</div>
                </div>
            </div>

            <!-- Row 1: Expiry Alerts + Recent Trips (separate grids) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6  mb-6">
                
                <!-- Expiry Alerts -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-red-500 text-white px-4 py-3 font-bold flex justify-between">
                        <span>⚠️ Expiry Alerts (Next 30 Days)</span>
                        <span class="bg-white text-red-500 rounded-full px-2 py-0.5 text-xs">{{ $expiryAlerts->count() }}</span>
                    </div>
                    <div class="p-4">
                        @if($expiryAlerts->isEmpty())
                            <p class="text-slate-500 text-center py-4">No upcoming expiries. ✅</p>
                        @else
                            <ul class="divide-y divide-slate-100">
                                @foreach($expiryAlerts as $alert)
                                    <li class="py-3 flex justify-between items-center">
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $alert->vehicle->vehicle_number ?? 'Unknown' }}</p>
                                            <p class="text-sm text-slate-500 uppercase">{{ $alert->expiry_type }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-red-500 font-bold">{{ \Carbon\Carbon::parse($alert->expiry_date)->format('d M Y') }}</span>
                                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($alert->expiry_date)->diffForHumans() }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Recent Trips -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-blue-500 text-white px-4 py-3 font-bold flex justify-between">
                        <span>🚛 Recent Trips</span>
                        <span class="bg-white text-blue-500 rounded-full px-2 py-0.5 text-xs">Last 5</span>
                    </div>
                    <div class="p-4">
                        @if($recentTrips->isEmpty())
                            <p class="text-slate-500 text-center py-4">No trips recorded yet.</p>
                        @else
                            <ul class="divide-y divide-slate-100">
                                @foreach($recentTrips as $trip)
                                    <li class="py-3 flex justify-between items-center">
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $trip->location }}</p>
                                            <p class="text-xs text-slate-500">{{ $trip->vehicle->vehicle_number ?? '' }} • {{ $trip->driver->driver_name ?? '' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-green-600">₹{{ number_format($trip->rent_amount, 0) }}</span>
                                            <p class="text-xs text-gray-400">{{ $trip->date->format('d M Y') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Row 2: Top Vehicles + Business Overview (separate grids) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6  mb-6">
                <!-- Top Vehicles Chart -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2 flex justify-between items-center">
                        <span>🏆 Top Vehicles (Last 30 Days)</span>
                        <span class="text-xs font-normal text-slate-500">By Profit</span>
                    </h3>
                    
                    @if($topVehiclesQuery->isEmpty())
                        <p class="text-slate-500 text-center py-4">No data available yet.</p>
                    @else
                        @php
                            $maxProfit = $topVehiclesQuery->max('total_profit') > 0 ? $topVehiclesQuery->max('total_profit') : 1;
                        @endphp
                        <div class="space-y-4">
                            @foreach($topVehiclesQuery as $tv)
                                @php
                                    $percentage = min(100, max(0, ($tv->total_profit / $maxProfit) * 100));
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-bold text-slate-700">{{ $tv->vehicle->vehicle_number ?? 'Unknown' }}</span>
                                        <span class="font-bold {{ $tv->total_profit >= 0 ? 'text-green-600' : 'text-red-500' }}">₹{{ number_format($tv->total_profit, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="{{ $tv->total_profit >= 0 ? 'bg-indigo-600' : 'bg-red-500' }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-400 flex justify-between mt-1">
                                        <span>Rent: ₹{{ number_format($tv->total_rent, 0) }}</span>
                                        <span>Fuel: ₹{{ number_format($tv->total_fuel, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Business Overview -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">
                        <span>📈 Business Overview</span>
                    </h3>
                    @if($profitData->isEmpty())
                        <p class="text-slate-500 text-center py-4">No data for last 30 days.</p>
                    @else
                        @php
                            $maxVal = max($profitData->max('rent'), 1);
                        @endphp
                        <div class="h-48 flex items-end justify-around gap-3 px-2 border-b border-gray-100 pb-2">
                            @foreach($profitData as $week)
                                @php
                                    $rentH = max(5, ($week->rent / $maxVal) * 100);
                                    $fuelH = max(3, ($week->fuel / $maxVal) * 100);
                                @endphp
                                <div class="flex flex-col items-center gap-1 flex-1">
                                    <div class="w-full flex items-end justify-center gap-0.5" style="height:180px;">
                                        <div class="w-1/2 bg-blue-400 rounded-t" style="height:{{ $rentH }}%"></div>
                                        <div class="w-1/2 bg-red-300 rounded-t" style="height:{{ $fuelH }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-center gap-6 mt-3 text-xs text-slate-500">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-400 rounded"></span> Income</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-300 rounded"></span> Fuel</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('trips.create') }}" class="bg-white border border-slate-200 text-slate-900 text-center py-4 rounded-xl shadow-sm font-semibold text-sm hover:bg-slate-50 hover:border-slate-300 transition flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                        New Trip
                    </a>
                    <a href="{{ route('payments.create') }}" class="bg-white border border-slate-200 text-slate-900 text-center py-4 rounded-xl shadow-sm font-semibold text-sm hover:bg-slate-50 hover:border-slate-300 transition flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/></svg>
                        Add Payment
                    </a>
                    <a href="{{ route('reports.index') }}" class="bg-white border border-slate-200 text-slate-900 text-center py-4 rounded-xl shadow-sm font-semibold text-sm hover:bg-slate-50 hover:border-slate-300 transition flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Reports
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="bg-white border border-slate-200 text-slate-900 text-center py-4 rounded-xl shadow-sm font-semibold text-sm hover:bg-slate-50 hover:border-slate-300 transition flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18M7 17a1 1 0 100 2 1 1 0 000-2zm10 0a1 1 0 100 2 1 1 0 000-2z"/></svg>
                        Vehicles
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
