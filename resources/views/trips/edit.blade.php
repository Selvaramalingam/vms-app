<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Edit Trip') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="tripEditForm()">
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

                    <form action="{{ route('trips.update', $trip) }}" method="POST" x-ref="form" @submit.prevent="submitForm" class="no-loader">
                        @csrf
                        @method('PUT')
                        @php 
                            $isVehicle = auth()->user()->hasRole('Vehicle'); 
                        @endphp
                        
                        <div class="space-y-4">
                             <div>
                                 <label class="block text-sm font-bold text-slate-700">Trip Type</label>
                                 @if($isVehicle)
                                     <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold uppercase">
                                         {{ $trip->trip_type }}
                                     </div>
                                     <input type="hidden" name="trip_type" value="{{ $trip->trip_type }}" x-model="trip_type">
                                 @else
                                     <select name="trip_type" x-model="trip_type" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                         <option value="rent">Rent</option>
                                         <option value="own">Own</option>
                                         <option value="empty">Empty</option>
                                     </select>
                                 @endif
                             </div>

                             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                 <div>
                                     <label class="block text-sm font-bold text-slate-700">From Date</label>
                                     @if($isVehicle)
                                         <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                             {{ $trip->date->format('d M Y') }}
                                         </div>
                                         <input type="hidden" name="date" value="{{ $trip->date->format('Y-m-d') }}">
                                     @else
                                         <input type="date" name="date" value="{{ old('date', $trip->date->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                     @endif
                                 </div>
                                 <div>
                                     <label class="block text-sm font-bold text-slate-700">To Date</label>
                                     @if($isVehicle)
                                         <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                             {{ $trip->to_date ? $trip->to_date->format('d M Y') : 'N/A' }}
                                         </div>
                                         <input type="hidden" name="to_date" value="{{ $trip->to_date ? $trip->to_date->format('Y-m-d') : '' }}">
                                     @else
                                         <input type="date" name="to_date" value="{{ old('to_date', $trip->to_date ? $trip->to_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                     @endif
                                 </div>
                             </div>

                             <div>
                                 <label class="block text-sm font-bold text-slate-700">Location</label>
                                 <input type="text" name="location" x-model="location" placeholder="e.g. Chennai to Madurai" required class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0 bg-white">
                             </div>

                             <div>
                                 <label class="block text-sm font-bold text-slate-700">User Name</label>
                                 <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold italic">
                                     {{ $trip->user_name ?: auth()->user()->name }}
                                 </div>
                                 <input type="hidden" name="user_name" value="{{ $trip->user_name ?: auth()->user()->name }}">
                             </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Vehicle</label>
                                @if($isVehicle)
                                    <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-medium">
                                        {{ $trip->vehicle->vehicle_number ?? 'N/A' }}
                                    </div>
                                    <input type="hidden" name="vehicle_id" value="{{ $trip->vehicle_id }}">
                                @else
                                    <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $v)
                                            <option value="{{ $v->id }}" {{ $trip->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Driver</label>
                                @if($isVehicle)
                                    <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                        {{ $trip->driver->driver_name ?? 'N/A' }}
                                    </div>
                                    <input type="hidden" name="driver_id" value="{{ $trip->driver_id }}">
                                @else
                                    <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                        <option value="">Select Driver</option>
                                        @foreach($drivers as $d)
                                            <option value="{{ $d->id }}" {{ $trip->driver_id == $d->id ? 'selected' : '' }}>{{ $d->driver_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>



                            <!-- KM Tracking -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Open KM</label>
                                    @if($isVehicle)
                                        <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                            {{ $trip->open_km }}
                                        </div>
                                        <input type="hidden" name="open_km" value="{{ $trip->open_km }}">
                                    @else
                                        <input type="number" min="0" name="open_km" x-model="open_km" value="{{ old('open_km', $trip->open_km) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Close KM</label>
                                    <input type="number" min="0" name="close_km" x-model="close_km" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                </div>
                            </div>

                            <div x-show="open_km || close_km" class="bg-blue-50 p-3 rounded-md border border-blue-100 mt-2">
                                <p class="text-sm font-bold text-blue-800">Total KM: <span x-text="calculateTotalKM()"></span> KM</p>
                            </div>

                            <!-- Hour Tracking -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Open Hour</label>
                                    @if($isVehicle)
                                        <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                            {{ $trip->open_hour }}
                                        </div>
                                        <input type="hidden" name="open_hour" value="{{ $trip->open_hour }}">
                                    @else
                                        <input type="number" min="0" step="0.01" name="open_hour" x-model="open_hour" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Close Hour</label>
                                    <input type="number" min="0" step="0.01" name="close_hour" x-model="close_hour" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                </div>
                            </div>

                            <div x-show="open_hour || close_hour" class="bg-blue-50 p-3 rounded-md border border-blue-100 mt-2">
                                <p class="text-sm font-bold text-blue-800">Running Time: <span x-text="calculateHours()"></span> Hours</p>
                            </div>

                            <!-- Financials -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                 <div>
                                     <label class="block text-sm font-bold text-slate-700">Rent Amount (₹)</label>
                                     @if($isVehicle)
                                         <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-medium text-green-600">
                                             ₹<span x-text="rent_amount"></span>
                                         </div>
                                         <input type="hidden" name="rent_amount" x-model="rent_amount">
                                     @else
                                         <input type="number" min="0" step="0.01" name="rent_amount" x-model="rent_amount" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm font-bold text-green-600">
                                     @endif
                                 </div>
                                 <div>
                                     <label class="block text-sm font-bold text-slate-700">Padi Kaasu (படி காசு)</label>
                                     <input type="number" min="0" step="0.01" name="padi_kaasu" x-model="padi_kaasu" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm font-bold text-indigo-600">
                                 </div>
                            </div>
                            
                            <div x-show="rent_amount || padi_kaasu" class="bg-green-50 p-3 rounded-md border border-green-100 mt-2">
                                <p class="text-sm font-bold text-green-800">Total Amount: ₹<span x-text="calculateTotalAmount()"></span></p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-sm font-bold text-slate-700">Work</label>
                                    <input type="text" name="work" value="{{ old('work', $trip->work) }}" placeholder="Describe work" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Fuel (Litres)</label>
                                    <input type="number" min="0" step="0.01" name="fuel_litre" x-model="fuel_litre" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Diesel Price</label>
                                    <input type="number" min="0" step="0.01" name="diesel_price" x-model="diesel_price" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                                </div>
                            </div>

                        </div>


                        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t sm:relative sm:bg-transparent sm:border-0 sm:p-0 sm:mt-6 sm:pb-0 z-40">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold text-lg py-4 rounded-lg shadow-lg hover:bg-green-700 active:bg-green-800 transition">
                                Update Trip
                            </button>
                        </div>
                        <div class="h-20 sm:hidden"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-50/500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModal" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-8 border-green-500">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-2xl leading-6 font-black text-slate-900 mb-4" id="modal-title">
                                    Trip Closure Summary
                                </h3>
                                <div class="bg-slate-50/50 rounded-xl p-4 space-y-4 border border-gray-100">
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicle</span>
                                        <span class="font-black text-blue-600">{{ $trip->vehicle->vehicle_number ?? 'N/A' }}</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm text-center">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Total KM</p>
                                            <p class="text-xl font-black text-slate-900"><span x-text="close_km - open_km"></span></p>
                                            <p class="text-[10px] text-gray-400">km</p>
                                        </div>
                                        <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm text-center">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Running Time</p>
                                            <p class="text-xl font-black text-indigo-600"><span x-text="calculateHours()"></span></p>
                                            <p class="text-[10px] text-gray-400">hours</p>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-slate-500">Location:</span>
                                            <span class="font-bold text-slate-900" x-text="location || 'Not provided'"></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-slate-500">Fuel Usage:</span>
                                            <span class="font-bold text-slate-900"><span x-text="fuel_litre"></span> L @ ₹<span x-text="diesel_price"></span></span>
                                        </div>
                                        <div class="flex justify-between text-lg pt-2 border-t border-gray-200">
                                            <span class="font-black text-slate-900">Fuel Cost:</span>
                                            <span class="font-black text-red-600">₹<span x-text="(parseFloat(fuel_litre || 0) * parseFloat(diesel_price || 0)).toFixed(2)"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-4 text-xs text-gray-400 text-center italic">Please verify all details before final submission.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50/80 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" @click="confirmSave()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-green-600 text-lg font-black text-white hover:bg-green-700 focus:outline-none transition-all transform hover:scale-105 active:scale-95 sm:w-auto">
                            Confirm & Save
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-200 text-sm shadow-sm px-6 py-3 bg-white text-lg font-bold text-slate-700 hover:bg-slate-50/50 focus:outline-none transition-all sm:mt-0 sm:w-auto">
                            Go Back
                        </button>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        function tripEditForm() {
            return {
                isVehicle: {{ $isVehicle ? 'true' : 'false' }},
                showModal: false,
                trip_type: '{{ old('trip_type', $trip->trip_type) }}',
                location: '{{ old('location', $trip->location) }}',
                open_km: {{ old('open_km', $trip->open_km) ?: 0 }},
                close_km: {{ old('close_km', $trip->close_km) ?: 0 }},
                open_hour: '{{ old('open_hour', $trip->open_hour) }}',
                close_hour: '{{ old('close_hour', $trip->close_hour) }}',
                fuel_litre: '{{ old('fuel_litre', $trip->fuel_litre) }}',
                diesel_price: '{{ old('diesel_price', $trip->diesel_price) }}',
                rent_amount: '{{ old('rent_amount', $trip->rent_amount) }}',
                padi_kaasu: '{{ old('padi_kaasu', $trip->padi_kaasu) }}',

                calculateHours() {
                    let total = (parseFloat(this.close_hour) || 0) - (parseFloat(this.open_hour) || 0);
                    return total > 0 ? total.toFixed(2) : 0;
                },

                calculateTotalKM() {
                    let total = (parseFloat(this.close_km) || 0) - (parseFloat(this.open_km) || 0);
                    return total > 0 ? total.toFixed(2) : 0;
                },

                calculateTotalAmount() {
                    let total = (parseFloat(this.rent_amount) || 0) + (parseFloat(this.padi_kaasu) || 0);
                    return total > 0 ? total.toFixed(2) : 0;
                },

                submitForm() {
                    if (this.isVehicle) {
                        this.showModal = true;
                    } else {
                        window.dispatchEvent(new CustomEvent('loading', { detail: true }));
                        this.$refs.form.submit();
                    }
                },

                confirmSave() {
                    this.showModal = false;
                    window.dispatchEvent(new CustomEvent('loading', { detail: true }));
                    this.$refs.form.submit();
                }
            }
        }
    </script>
</x-app-layout>
