<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Trip Payment') }}
            </h2>
            <a href="{{ route('trips.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">← Back to Trips</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 mb-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100  text-red-700 p-4 mb-4 rounded shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. Trip Summary -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 border-t-4 border-gray-800">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Trip Summary</h3>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <p class="text-xl font-bold text-slate-900">{{ $trip->location }}</p>
                        <p class="text-sm text-slate-500 mt-1">
                            <span class="font-medium text-slate-700">{{ $trip->vehicle->vehicle_number ?? 'N/A' }}</span> • 
                            {{ $trip->date->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Payment Summary -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-center divide-x divide-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Rent</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">₹{{ number_format($trip->rent_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Paid</p>
                        <p class="mt-1 text-xl font-bold text-green-600">₹{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="{{ $balance > 0 ? 'bg-red-50 rounded-lg p-2 -my-2' : 'bg-green-50 rounded-lg p-2 -my-2' }}">
                        <p class="text-xs font-semibold uppercase tracking-wider {{ $balance > 0 ? 'text-red-500' : 'text-green-500' }}">Balance</p>
                        <p class="mt-1 text-2xl font-black {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">₹{{ number_format(abs($balance), 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- 3. Payment Entry -->
            @if($balance > 0)
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 border border-blue-100">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Record New Payment</h3>
                <form action="{{ route('trips.payments.store', $trip) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0 font-bold text-lg" min="1" max="{{ $balance }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Method</label>
                            <select name="method" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                <option value="Cash" {{ old('method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="UPI" {{ old('method') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                <option value="Card" {{ old('method') == 'Card' ? 'selected' : '' }}>Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="bg-indigo-600 text-white font-bold px-6 py-3 rounded-lg shadow hover:bg-indigo-700 transition w-full sm:w-auto">
                            Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center shadow-sm">
                <svg class="w-12 h-12 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-lg font-bold text-green-800">Trip Fully Paid</h3>
                <p class="text-sm text-green-600">No pending balance remains for this trip.</p>
            </div>
            @endif

            <!-- 4. Payment History -->
            @if($payments->count() > 0)
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Payment History</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($payments as $payment)
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-slate-50/50 transition">
                        <div>
                            <p class="text-lg font-bold text-slate-900">₹{{ number_format($payment->paid + $payment->advance, 2) }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $payment->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 bg-slate-50/80 text-gray-600 text-xs font-bold rounded-full border">
                                {{ $payment->method ?? 'Cash' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
