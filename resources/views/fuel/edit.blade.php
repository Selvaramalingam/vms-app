<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Edit Fuel Entry') }}
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

                    <form action="{{ route('fuel.update', $fuel) }}" method="POST" id="fuelForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Vehicle</label>
                                <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" {{ $fuel->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Trip (Optional)</label>
                                <select name="trip_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                    <option value="">Not linked to trip</option>
                                    @foreach($trips as $t)
                                        <option value="{{ $t->id }}" {{ $fuel->trip_id == $t->id ? 'selected' : '' }}>{{ $t->location }} - {{ $t->date->format('d/m') }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Litre</label>
                                    <input type="number" step="0.01" name="litre" id="litre" value="{{ old('litre', $fuel->litre) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Price per Litre (₹)</label>
                                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $fuel->price) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm">
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200 mt-4">
                                <div class="flex justify-between items-center bg-slate-50/50 p-4 rounded-lg">
                                    <span class="font-bold text-slate-700">Total:</span>
                                    <span class="text-2xl font-black text-red-600" id="totalAmount">₹{{ number_format($fuel->total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t sm:relative sm:bg-transparent sm:border-0 sm:p-0 sm:mt-6 sm:pb-0 z-50">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold text-lg py-4 rounded-lg shadow-lg hover:bg-green-700 active:bg-green-800 transition">
                                Update Fuel Entry
                            </button>
                        </div>
                        <div class="h-20 sm:hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const litreInput = document.getElementById('litre');
            const priceInput = document.getElementById('price');
            const totalAmount = document.getElementById('totalAmount');

            function calculateTotal() {
                const litre = parseFloat(litreInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                totalAmount.textContent = '₹' + (litre * price).toFixed(2);
            }

            litreInput.addEventListener('input', calculateTotal);
            priceInput.addEventListener('input', calculateTotal);
        });
    </script>
</x-app-layout>
