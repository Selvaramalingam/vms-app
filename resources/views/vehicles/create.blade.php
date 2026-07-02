<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle' }}
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

                    <form action="{{ isset($vehicle) ? route('vehicles.update', $vehicle) : route('vehicles.store') }}" method="POST">
                        @csrf
                        @if(isset($vehicle))
                            @method('PUT')
                        @endif
                        
                        <div class="space-y-6">
                            
                            <!-- Basic Details -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">Basic Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Vehicle Number</label>
                                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $vehicle->vehicle_number ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 uppercase" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Vehicle Type</label>
                                        <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $vehicle->vehicle_type ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Owner Type</label>
                                        <select name="owner_type" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                            <option value="own" {{ old('owner_type', $vehicle->owner_type ?? '') == 'own' ? 'selected' : '' }}>Own</option>
                                            <option value="rent" {{ old('owner_type', $vehicle->owner_type ?? '') == 'rent' ? 'selected' : '' }}>Rent</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Status</label>
                                        <select name="status" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                            <option value="active" {{ old('status', $vehicle->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $vehicle->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="maintenance" {{ old('status', $vehicle->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Owner Name</label>
                                        <input type="text" name="owner_name" value="{{ old('owner_name', $vehicle->owner_name ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Owner Phone</label>
                                        <input type="text" name="owner_phone" value="{{ old('owner_phone', $vehicle->owner_phone ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Expiry Tracking -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">Expiry Tracking</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">FC Expiry</label>
                                        <input type="date" name="fc_expiry" value="{{ old('fc_expiry', isset($vehicle->fc_expiry) ? $vehicle->fc_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Insurance Expiry</label>
                                        <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry', isset($vehicle->insurance_expiry) ? $vehicle->insurance_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Permit Expiry</label>
                                        <input type="date" name="permit_expiry" value="{{ old('permit_expiry', isset($vehicle->permit_expiry) ? $vehicle->permit_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Tax Expiry</label>
                                        <input type="date" name="tax_expiry" value="{{ old('tax_expiry', isset($vehicle->tax_expiry) ? $vehicle->tax_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Pollution Expiry</label>
                                        <input type="date" name="pollution_expiry" value="{{ old('pollution_expiry', isset($vehicle->pollution_expiry) ? $vehicle->pollution_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Caliber Certificate</label>
                                        <input type="date" name="caliber_certificate_date" value="{{ old('caliber_certificate_date', isset($vehicle->caliber_certificate_date) ? $vehicle->caliber_certificate_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Service Tracking -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">Service Tracking</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Last Service KM</label>
                                        <input type="number" name="last_service_km" value="{{ old('last_service_km', $vehicle->last_service_km ?? 0) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Next Service KM</label>
                                        <input type="number" name="next_service_km" value="{{ old('next_service_km', $vehicle->next_service_km ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Last Service Date</label>
                                        <input type="date" name="last_service_date" value="{{ old('last_service_date', isset($vehicle->last_service_date) ? $vehicle->last_service_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Next Service Date</label>
                                        <input type="date" name="next_service_date" value="{{ old('next_service_date', isset($vehicle->next_service_date) ? $vehicle->next_service_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Save Button -->
                        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t sm:relative sm:bg-transparent sm:border-0 sm:p-0 sm:mt-6 sm:pb-0 z-50">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-lg shadow-lg hover:bg-indigo-700 active:bg-indigo-800 transition">
                                {{ isset($vehicle) ? 'Update Vehicle' : 'Save Vehicle' }}
                            </button>
                        </div>
                        <div class="h-20 sm:hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
