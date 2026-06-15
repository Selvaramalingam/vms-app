<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Edit Payment') }}
            </h2>
            <a href="{{ route('payments.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">← Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="p-6 text-slate-900" x-data="editPaymentForm()">
                    
                    @if($errors->any())
                        <div class="bg-red-100  text-red-700 p-4 mb-4 rounded shadow-sm">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Trip Info (Read Only) --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div>
                                <p class="text-xs text-blue-500 font-bold uppercase tracking-wider mb-1">Trip Details</p>
                                <p class="text-xl font-bold text-blue-900">{{ $payment->trip->location }}</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    {{ $payment->trip->date->format('d M Y') }} • 
                                    <span class="font-semibold">{{ $payment->trip->vehicle->vehicle_number ?? 'N/A' }}</span> • 
                                    {{ $payment->trip->driver->driver_name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-left sm:text-right mt-4 sm:mt-0">
                                <p class="text-xs text-blue-500 font-bold uppercase tracking-wider">Rent Amount</p>
                                <p class="text-2xl font-black text-blue-700">₹{{ number_format($payment->trip->rent_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('payments.update', $payment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            
                            {{-- Form Entry --}}
                            <div>
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">Update Payment Details</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Date</label>
                                        <input type="date" name="date" value="{{ old('date', $payment->date ? $payment->date->format('Y-m-d') : $payment->created_at->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Amount Paid (₹)</label>
                                        <input type="number" step="0.01" name="paid" x-model.number="paid" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0 font-bold text-green-600" min="1" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Payment Method</label>
                                        <select name="method" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                            <option value="Cash" {{ $payment->method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="UPI" {{ $payment->method == 'UPI' ? 'selected' : '' }}>UPI</option>
                                            <option value="Bank Transfer" {{ $payment->method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Live Balance Calculation --}}
                            <div class="rounded-lg p-6 bg-slate-50/50 border border-gray-200">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-center divide-x divide-slate-100">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Rent</p>
                                        <p class="font-bold text-slate-900 text-lg mt-1">₹{{ number_format($payment->trip->rent_amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-green-500 uppercase tracking-wider">Total Paid</p>
                                        <p class="font-bold text-green-600 text-lg mt-1">₹<span x-text="formatCurrency(previousPaid + paid)"></span></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider" :class="newBalance > 0 ? 'text-red-500' : 'text-green-500'">Final Balance</p>
                                        <p class="font-black text-xl mt-1" :class="newBalance > 0 ? 'text-red-600' : 'text-green-600'">₹<span x-text="formatCurrency(Math.abs(newBalance))"></span></p>
                                    </div>
                                </div>
                                <p class="text-sm text-center mt-4 font-medium" :class="newBalance > 0 ? 'text-red-500' : 'text-green-600'">
                                    <span x-show="newBalance > 0">Status will be: Partial</span>
                                    <span x-show="newBalance <= 0">Status will be: Completed</span>
                                </p>
                            </div>

                            {{-- Payment History --}}
                            @if($paymentHistory->count() > 0)
                            <div class="border-t pt-6 mt-6">
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Payment History</h3>
                                <div class="bg-white rounded-lg overflow-x-auto border border-gray-200">
                                    <table class="min-w-full divide-y divide-slate-100">
                                        <thead class="bg-slate-50/50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Method</th>
                                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($paymentHistory as $history)
                                                <tr class="{{ $history->id == $payment->id ? 'bg-blue-50/50' : 'hover:bg-slate-50/50' }}">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">
                                                        {{ $history->date ? $history->date->format('d M Y') : $history->created_at->format('d M Y') }}
                                                        @if($history->id == $payment->id)
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">CURRENT EDIT</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-slate-900">
                                                        ₹{{ number_format($history->paid + $history->advance, 2) }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-50/80 text-slate-900">
                                                            {{ $history->method ?? 'Cash' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                                        <span class="px-2 py-1 text-xs font-bold rounded-full uppercase {{ $history->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                            {{ $history->status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                        </div>

                        {{-- Save --}}
                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white font-bold text-lg px-8 py-3 rounded-lg shadow-lg hover:bg-indigo-700 active:bg-indigo-800 transition">
                                Update Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editPaymentForm() {
            return {
                rentAmount: {{ $payment->trip->rent_amount }},
                paid: {{ old('paid', $payment->paid) }},
                previousPaid: {{ $paymentHistory->where('id', '!=', $payment->id)->sum('paid') + $paymentHistory->where('id', '!=', $payment->id)->sum('advance') }},
                
                get newBalance() { 
                    const bal = this.rentAmount - this.previousPaid - (this.paid || 0);
                    return bal > 0 ? bal : 0; 
                },

                formatCurrency(value) {
                    return Number(value).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
