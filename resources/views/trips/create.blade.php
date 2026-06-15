<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('New Trip') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="tripForm()">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="p-6 text-slate-900">
                    
                    @if($errors->any())
                        <div class="bg-red-100  text-red-700 p-4 mb-4">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('trips.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Date</label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            </div>

                             <div>
                                 <label class="block text-sm font-bold text-slate-700">Location</label>
                                 <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Chennai to Madurai" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                             </div>

                             <div>
                                 <label class="block text-sm font-bold text-slate-700">User Name</label>
                                 <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold italic">
                                     {{ auth()->user()->name }}
                                 </div>
                                 <input type="hidden" name="user_name" value="{{ auth()->user()->name }}">
                             </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Vehicle</label>
                                @if(isset($linkedVehicle) && $linkedVehicle)
                                    <input type="hidden" name="vehicle_id" value="{{ $linkedVehicle->id }}" x-ref="vehicle_id">
                                    <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                        {{ $linkedVehicle->vehicle_number }}
                                    </div>
                                @else
                                    <select name="vehicle_id" @change="fetchLastTrip($event.target.value)" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $v)
                                            <option value="{{ $v->id }}">{{ $v->vehicle_number }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Driver</label>
                                <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                    <option value="">Select Driver</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}">{{ $d->driver_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Trip Type</label>
                                <select name="trip_type" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                    <option value="rent">Rent</option>
                                    <option value="own">Own</option>
                                    <option value="empty">Empty</option>
                                </select>
                            </div>

                            <!-- KM Tracking -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Open KM</label>
                                    <input type="number" name="open_km" x-model="open_km" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Close KM</label>
                                    <input type="number" name="close_km" value="{{ old('close_km') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                            </div>

                            <!-- Hour Tracking -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Open Hour</label>
                                    <input type="number" step="0.01" name="open_hour" x-model="open_hour" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Close Hour</label>
                                    <input type="number" step="0.01" name="close_hour" x-model="close_hour" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                </div>
                            </div>

                            <div x-show="open_hour || close_hour" class="bg-blue-50 p-3 rounded-md border border-blue-100 mt-2">
                                <p class="text-sm font-bold text-blue-800">Running Time: <span x-text="calculateTotalHours()"></span> Hours</p>
                            </div>

                            <!-- Financials -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Rent Amount (₹)</label>
                                <input type="number" step="0.01" name="rent_amount" value="{{ old('rent_amount') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm font-bold text-green-600">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Fuel (Litres)</label>
                                    <input type="number" step="0.01" name="fuel_litre" value="{{ old('fuel_litre') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Diesel Price</label>
                                    <input type="number" step="0.01" name="diesel_price" value="{{ old('diesel_price') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                            </div>

                            </div>
                        </div>



                        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t sm:relative sm:bg-transparent sm:border-0 sm:p-0 sm:mt-6 sm:pb-0 z-50">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-lg shadow-lg hover:bg-indigo-700 active:bg-indigo-800 transition">
                                Save Trip
                            </button>
                        </div>
                        <div class="h-20 sm:hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tripForm() {
            return {
                open_km: '{{ old('open_km') }}',
                open_hour: '{{ old('open_hour', 0) }}',
                close_hour: '{{ old('close_hour', 0) }}',
                
                init() {
                    // Auto-fetch if vehicle is already linked (Vehicle Login)
                    if (this.$refs.vehicle_id) {
                        this.fetchLastTrip(this.$refs.vehicle_id.value);
                    } else {
                        // For Admin/Staff, check if a vehicle is already selected (e.g. on validation error)
                        let select = document.querySelector('select[name="vehicle_id"]');
                        if (select && select.value && !this.open_km && !this.open_hour) {
                            this.fetchLastTrip(select.value);
                        }
                    }
                },


                calculateTotalHours() {
                    let total = (parseFloat(this.close_hour) || 0) - (parseFloat(this.open_hour) || 0);
                    return total > 0 ? total.toFixed(2) : 0;
                },

                fetchLastTrip(vehicleId) {
                    if (!vehicleId) return;
                    
                    fetch(`/api/vehicles/${vehicleId}/last-trip`)
                        .then(res => res.json())
                        .then(data => {
                            this.open_km = data.close_km;
                            this.open_hour = data.close_hour;
                        })
                        .catch(err => console.error(err));
                }
            }
        }
    </script>
</x-app-layout>
