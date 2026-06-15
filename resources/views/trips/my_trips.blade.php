<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('My Trips') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100  text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Vehicle Summary Card -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6  mx-4 sm:mx-0">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900">Vehicle: {{ $vehicle->vehicle_number }}</h3>
                        <p class="text-sm text-slate-500">Model: {{ $vehicle->model }} • RC: {{ $vehicle->rc_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Total Trips</p>
                        <p class="text-2xl font-black text-indigo-600">{{ $trips->total() }}</p>
                    </div>
                </div>
            </div>

            @if($trips->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center mx-4 sm:mx-0">
                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-600">No trips yet</h3>
                    <p class="text-gray-400 mt-1">When a trip is assigned to you, it will appear here.</p>
                </div>
            @else
                <!-- Mobile First Card Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ">
                    @foreach($trips as $trip)
                        <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-blue-500">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $trip->date->format('d M Y') }}</span>
                                    <h3 class="font-bold text-lg text-slate-900">{{ $trip->location }}</h3>
                                </div>
                                <span class="px-2 py-1 bg-slate-50/80 text-xs font-bold rounded uppercase">{{ $trip->trip_type }}</span>
                            </div>
                            
                            <div class="text-sm text-gray-600 mb-4 border-b pb-2">
                                <p><strong>Vehicle:</strong> {{ $trip->vehicle->vehicle_number ?? 'N/A' }}</p>
                                <div class="flex justify-between">
                                    <p><strong>Total KM:</strong> {{ $trip->total_km }} km</p>
                                    <p class="text-indigo-600 font-bold"><strong>Hours:</strong> {{ $trip->total_hour }} hrs</p>
                                </div>
                                @if($trip->maintenance_note || $trip->loan_note)
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @if($trip->maintenance_note)
                                            <span class="text-[10px] text-orange-600 bg-orange-50 px-2 py-0.5 rounded border border-orange-100">🛠️ {{ $trip->maintenance_note }}</span>
                                        @endif
                                        @if($trip->loan_note)
                                            <span class="text-[10px] text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-100">💰 {{ $trip->loan_note }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>


                            
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-slate-500">Rent</p>
                                    <p class="font-bold text-green-600">₹{{ number_format($trip->rent_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">Fuel</p>
                                    <p class="font-bold text-red-500">₹{{ number_format($trip->fuel_cost, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Profit</p>
                                    <p class="font-black text-blue-600">₹{{ number_format($trip->profit, 2) }}</p>
                                </div>
                            </div>
                            
                            <!-- Edit Button -->
                            <div class="mt-3 pt-3 border-t">
                                <a href="{{ route('trips.edit', $trip) }}" class="block w-full text-center text-sm font-medium px-3 py-1.5 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                    ✏️ Edit Trip
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 ">
                    {{ $trips->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
