<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ isset($driver) ? 'Edit Driver' : 'Add Driver' }}
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

                    <form action="{{ isset($driver) ? route('drivers.update', $driver) : route('drivers.store') }}" method="POST">
                        @csrf
                        @if(isset($driver))
                            @method('PUT')
                        @endif
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Driver Name</label>
                                <input type="text" name="driver_name" value="{{ old('driver_name', $driver->driver_name ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">License Number</label>
                                <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm uppercase" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">License Expiry</label>
                                <input type="date" name="license_expiry" value="{{ old('license_expiry', isset($driver->license_expiry) ? $driver->license_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Assigned Vehicle</label>
                                <select name="assigned_vehicle" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" {{ old('assigned_vehicle', $driver->assigned_vehicle ?? '') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                    <option value="active" {{ old('status', $driver->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $driver->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Sticky Save Button -->
                        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t sm:relative sm:bg-transparent sm:border-0 sm:p-0 sm:mt-6 sm:pb-0 z-50">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-lg shadow-lg hover:bg-indigo-700 active:bg-indigo-800 transition">
                                {{ isset($driver) ? 'Update Driver' : 'Save Driver' }}
                            </button>
                        </div>
                        <div class="h-20 sm:hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
