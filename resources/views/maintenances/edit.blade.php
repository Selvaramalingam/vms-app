<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Edit Maintenance Record') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="p-6 text-slate-900">
                    
                    @if($errors->any())
                        <div class="bg-red-100  text-red-700 p-4 mb-4">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $isVehicle = auth()->user()->hasRole('Vehicle');
                    @endphp

                    <form action="{{ route('maintenances.update', $maintenance) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Vehicle</label>
                                @if($isVehicle)
                                    <div class="mt-1 block w-full rounded-md border border-slate-200 text-sm bg-slate-50/50 px-3 py-2 text-slate-700 font-semibold">
                                        {{ $maintenance->vehicle->vehicle_number ?? 'Unknown' }}
                                    </div>
                                @else
                                    <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $v)
                                            <option value="{{ $v->id }}" {{ $maintenance->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Date</label>
                                <input type="date" name="date" value="{{ old('date', $maintenance->date ? $maintenance->date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Description</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" placeholder="Details of work done...">{{ old('description', $maintenance->description) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Cost (₹)</label>
                                    <input type="number" step="0.01" name="cost" value="{{ old('cost', $maintenance->cost) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm font-bold text-red-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Service KM</label>
                                    <input type="number" name="km" value="{{ old('km', $maintenance->km) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Service Hours</label>
                                    <input type="number" name="hours" value="{{ old('hours', $maintenance->hours) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                            </div>
                            
                            <h4 class="font-bold text-slate-900 pt-4 border-t">Forecast</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Next Service KM</label>
                                    <input type="number" name="next_service_km" value="{{ old('next_service_km', $maintenance->next_service_km) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Next Service Date</label>
                                    <input type="date" name="next_service_date" value="{{ old('next_service_date', $maintenance->next_service_date ? $maintenance->next_service_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                            </div>

                            @hasanyrole('Admin|Manager')
                            <div class="pt-4 border-t">
                                <label class="block text-sm font-bold text-slate-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm font-bold 
                                    {{ $maintenance->status == 'approved' ? 'text-green-600' : ($maintenance->status == 'rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                                    <option value="pending" {{ old('status', $maintenance->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $maintenance->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $maintenance->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            @endhasanyrole
                        </div>

                        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t sm:relative sm:bg-transparent sm:border-0 sm:p-0 sm:mt-6 sm:pb-0 z-50">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold text-lg py-4 rounded-lg shadow-lg hover:bg-green-700 active:bg-green-800 transition">
                                Update Record
                            </button>
                        </div>
                        <div class="h-20 sm:hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
