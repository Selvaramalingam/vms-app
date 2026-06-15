<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Vehicle Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(!$vehicle)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow-sm">
                    <p class="font-bold">Vehicle Not Linked</p>
                    <p>Your account has not been linked to a vehicle record yet. Please contact the administrator.</p>
                </div>
            @else
                
                <!-- Welcome & Vehicle Info -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6 border-t-4 border-blue-500">
                    <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900">Vehicle: {{ $vehicle->vehicle_number }}</h3>
                            <p class="text-slate-500">Fleet management summary for this vehicle.</p>
                        </div>
                        <div class="mt-4 md:mt-0 text-left md:text-right">
                            <p class="text-xs text-blue-500 uppercase font-bold tracking-wider mb-1">Status</p>
                            <span class="px-3 py-1 bg-green-100 text-green-800 font-black rounded-full text-sm uppercase">
                                {{ $vehicle->status ?? 'Active' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4  mb-6">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-6 ">
                        <div class="text-sm text-slate-500 uppercase font-bold">Today's Trips</div>
                        <div class="text-3xl font-black text-green-600 mt-2">{{ $todayTripsCount }}</div>
                    </div>
                    
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-6 ">
                        <div class="text-sm text-slate-500 uppercase font-bold">Today's Hours</div>
                        <div class="text-3xl font-black text-indigo-600 mt-2">{{ number_format($todayHoursCount, 2) }}</div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-6 ">
                        <div class="text-sm text-slate-500 uppercase font-bold">Total Trips</div>
                        <div class="text-3xl font-black text-purple-600 mt-2">{{ $totalTrips }}</div>
                    </div>
                </div>


                <!-- Recent Trips -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-slate-50/50 border-b px-6 py-4 flex justify-between items-center">
                        <h3 class="font-bold text-slate-900">Recent Trips</h3>
                        <a href="{{ route('trips.my') }}" class="text-sm text-blue-600 font-bold hover:underline">View All →</a>
                    </div>
                    <div class="p-0">
                        @if($recentTrips->isEmpty())
                            <p class="text-slate-500 text-center py-6">No trips recorded for this vehicle yet.</p>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($recentTrips as $trip)
                                    <li class="px-6 py-4 flex justify-between items-center hover:bg-slate-50/50 transition">
                                        <div>
                                            <p class="font-bold text-slate-900 text-lg">{{ $trip->location }}</p>
                                            <p class="text-sm text-slate-500 mt-0.5">
                                                {{ $trip->date->format('d M Y') }} • <span class="font-semibold">Driver: {{ $trip->driver->driver_name ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 text-xs font-bold">{{ $trip->trip_type }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
