<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Record Payment') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="p-6 text-slate-900" x-data="paymentForm()">
                    
                    @if($errors->any())
                        <div class="bg-red-100  text-red-700 p-4 mb-4">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            {{-- Trip Selection --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Select Trip</label>
                                <select name="trip_id" x-model="selectedTripId" @change="onTripChange()" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                    <option value="">Select a trip</option>
                                    @foreach($trips as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->date->format('d/m/Y') }} — {{ $t->vehicle->vehicle_number ?? '' }} — {{ $t->location }} — ₹{{ number_format($t->rent_amount) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dynamic Trip Details & Balances --}}
                            <div x-show="selectedTrip" x-transition class="bg-blue-50 border border-blue-200 rounded-lg p-6 space-y-4" style="display: none;">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-center divide-x divide-blue-200">
                                    <div>
                                        <p class="text-xs font-semibold text-blue-500 uppercase tracking-wider">Total Rent</p>
                                        <p class="mt-1 text-xl font-bold text-blue-800">₹<span x-text="formatCurrency(rentAmount)"></span></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-blue-500 uppercase tracking-wider">Total Paid</p>
                                        <p class="mt-1 text-xl font-bold text-green-600">₹<span x-text="formatCurrency(totalPaidHistory)"></span></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-red-500">Remaining Balance</p>
                                        <p class="mt-1 text-xl font-black text-red-600">₹<span x-text="formatCurrency(remainingBalance)"></span></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Entry --}}
                            <div x-show="selectedTrip && remainingBalance > 0" class="border-t pt-4" style="display: none;">
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Record New Payment</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Date</label>
                                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Amount Paid (₹)</label>
                                        <input type="number" step="0.01" name="paid" x-model.number="paid" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0 font-bold text-green-600" min="1" :max="remainingBalance" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700">Payment Method</label>
                                        <select name="method" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                            <option value="Cash" {{ old('method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="UPI" {{ old('method') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                            <option value="Bank Transfer" {{ old('method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                                
                                {{-- Live Calculation for current entry --}}
                                <div class="mt-4 flex items-center justify-between bg-slate-50/50 p-3 rounded text-sm">
                                    <span class="text-gray-600">Balance after this payment:</span>
                                    <span class="font-bold" :class="newBalance <= 0 ? 'text-green-600' : 'text-red-500'">₹<span x-text="formatCurrency(newBalance)"></span></span>
                                </div>
                            </div>
                            
                            <div x-show="selectedTrip && remainingBalance <= 0" class="bg-green-50 border border-green-200 rounded-lg p-6 text-center shadow-sm" style="display: none;">
                                <h3 class="text-lg font-bold text-green-800">Trip Fully Paid</h3>
                                <p class="text-sm text-green-600">No pending balance remains for this trip.</p>
                            </div>

                            {{-- Payment History --}}
                            <div x-show="selectedTrip && paymentHistory.length > 0" class="border-t pt-4" style="display: none;">
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Payment History</h3>
                                <div class="bg-slate-50/50 rounded-lg overflow-x-auto border border-gray-200">
                                    <table class="min-w-full divide-y divide-slate-100">
                                        <thead class="bg-slate-50/80">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Method</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-100">
                                            <template x-for="payment in paymentHistory" :key="payment.id">
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-900" x-text="formatDate(payment.date || payment.created_at)"></td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-slate-900">₹<span x-text="formatCurrency(parseFloat(payment.paid) + parseFloat(payment.advance || 0))"></span></td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="payment.method || 'Cash'"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        {{-- Save --}}
                        <div x-show="selectedTrip && remainingBalance > 0" class="mt-6 flex justify-end" style="display: none;">
                            <button type="submit" class="bg-indigo-600 text-white font-bold text-lg px-8 py-3 rounded-lg shadow-lg hover:bg-indigo-700 active:bg-indigo-800 transition">
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function paymentForm() {
            return {
                tripsData: @json($trips),
                selectedTripId: '{{ old('trip_id', $trip_id ?? '') }}',
                selectedTrip: null,
                rentAmount: 0,
                totalPaidHistory: 0,
                remainingBalance: 0,
                paymentHistory: [],
                paid: {{ old('paid', 0) }},
                
                get newBalance() {
                    const balance = this.remainingBalance - (this.paid || 0);
                    return balance > 0 ? balance : 0;
                },

                init() {
                    if (this.selectedTripId) {
                        this.onTripChange();
                    }
                },

                onTripChange() {
                    if (!this.selectedTripId) {
                        this.selectedTrip = null;
                        return;
                    }
                    
                    // Allow comparison even if types mismatch
                    this.selectedTrip = this.tripsData.find(t => t.id == this.selectedTripId);
                    
                    if (this.selectedTrip) {
                        this.rentAmount = parseFloat(this.selectedTrip.rent_amount) || 0;
                        this.paymentHistory = this.selectedTrip.payments || [];
                        
                        // Sort history latest first
                        this.paymentHistory.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                        
                        this.totalPaidHistory = this.paymentHistory.reduce((sum, p) => {
                            return sum + parseFloat(p.paid || 0) + parseFloat(p.advance || 0);
                        }, 0);
                        
                        this.remainingBalance = this.rentAmount - this.totalPaidHistory;
                        
                        // Reset paid input
                        this.paid = 0;
                    }
                },
                
                formatCurrency(value) {
                    return Number(value).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                
                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                }
            }
        }
    </script>
</x-app-layout>
