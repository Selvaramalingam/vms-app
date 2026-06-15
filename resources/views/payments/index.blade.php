<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Payments') }}
            </h2>
            <a href="{{ route('payments.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">+ Add Payment</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4 px-4 sm:px-6">
                <form action="{{ route('payments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Vehicle</label>
                        <select name="vehicle_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Driver</label>
                        <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->driver_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
                            <option value="">All</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded shadow hover:bg-gray-900 transition">Filter</button>
                        <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition">Clear</a>
                    </div>
                </form>
            </div>

            @if($payments->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center mx-4 sm:mx-0">
                    <h3 class="text-lg font-semibold text-gray-600">No payments recorded yet</h3>
                    <p class="text-gray-400 mt-1">Start by adding a payment for a trip.</p>
                    <a href="{{ route('payments.create') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">Record First Payment</a>
                </div>
            @else
                <!-- Table Layout -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Trip & Vehicle</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Financials</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-slate-50/50 transition border-l-4 {{ $payment->status == 'completed' ? 'border-green-500' : ($payment->status == 'partial' ? 'border-yellow-500' : 'border-red-500') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">
                                            {{ $payment->date ? $payment->date->format('d M Y') : $payment->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-900">
                                            <div class="font-bold text-slate-900">{{ $payment->trip->location ?? 'Trip #'.$payment->trip_id }}</div>
                                            <div class="text-xs text-slate-500">
                                                <span class="font-bold text-blue-600">{{ $payment->trip->vehicle->vehicle_number ?? '' }}</span> • 
                                                {{ $payment->trip->driver->driver_name ?? '' }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 mt-1">Trip Date: {{ $payment->trip->date?->format('d M Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-[10px] font-bold rounded-full uppercase
                                                {{ $payment->status == 'completed' ? 'bg-green-100 text-green-700' : ($payment->status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $payment->status }}
                                            </span>
                                            @if($payment->method)
                                                <div class="text-[10px] text-slate-500 mt-1">Via: {{ $payment->method }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                                                <span class="text-xs text-slate-500">Trip Rent:</span> <span class="font-bold text-slate-900 text-right">₹{{ number_format($payment->trip->rent_amount, 2) }}</span>
                                                <span class="text-xs text-blue-600">Last Payment:</span> <span class="font-bold text-blue-700 text-right">₹{{ number_format($payment->paid, 0) }}</span>
                                                <span class="text-xs text-green-600">Total Paid:</span> <span class="font-bold text-green-700 text-right">₹{{ number_format($payment->trip->rent_amount - $payment->balance, 0) }}</span>
                                                <span class="text-xs text-{{ $payment->balance > 0 ? 'red' : 'gray' }}-600 font-bold">Balance:</span> <span class="font-bold text-{{ $payment->balance > 0 ? 'red' : 'gray' }}-700 text-right">₹{{ number_format($payment->balance, 0) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('payments.edit', $payment) }}" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-2 py-1 rounded transition">Edit</a>
                                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Delete this payment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-2 py-1 rounded transition">Del</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4 ">
                    {{ $payments->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
