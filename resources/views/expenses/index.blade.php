<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                💸 {{ __('Admin Expenses') }}
            </h2>
            <button x-data="" x-on:click="$dispatch('open-modal', 'add-expense')" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-bold transition">Record Expense</button>
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
            <div class="bg-white rounded-lg shadow-sm p-4 px-4 sm:px-6 border border-gray-100">
                <form action="{{ route('admin.expenses.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Location</label>
                        <input type="text" name="location" value="{{ request('location') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200" placeholder="Filter by location">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded shadow hover:bg-gray-900 transition">Filter</button>
                        <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition text-center">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table Layout -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @php $totalAmount = 0; @endphp
                            @forelse($expenses as $expense)
                                @php $totalAmount += $expense->amount; @endphp
                                <tr class="hover:bg-slate-50/50 transition" x-data="{ editing: false }">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 uppercase">
                                        {{ $expense->location }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 italic max-w-xs truncate">
                                        {{ $expense->description ?? 'No description' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-indigo-600">
                                        ₹{{ number_format($expense->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button x-on:click="$dispatch('open-modal', 'edit-expense-{{ $expense->id }}')" class="text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded transition font-bold">Edit</button>
                                        
                                        <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Delete this expense record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-3 py-1.5 rounded transition font-bold">Delete</button>
                                        </form>
                                    </td>

                                    {{-- Edit Modal --}}
                                    <x-modal name="edit-expense-{{ $expense->id }}" focusable>
                                        <div class="p-6">
                                            <h2 class="text-lg font-bold text-slate-900 mb-4">Edit Expense Record</h2>
                                            <form action="{{ route('admin.expenses.update', $expense) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-bold text-slate-700 uppercase">Date</label>
                                                        <input type="date" name="date" value="{{ $expense->date }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-bold text-slate-700 uppercase">Amount (₹)</label>
                                                        <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" required>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 uppercase">Location</label>
                                                    <input type="text" name="location" value="{{ $expense->location }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" placeholder="e.g. Madurai Office" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-slate-700 uppercase">Description</label>
                                                    <textarea name="description" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm" rows="3">{{ $expense->description }}</textarea>
                                                </div>

                                                <div class="flex justify-end gap-3 mt-6">
                                                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-slate-900 rounded font-bold hover:bg-gray-300">Cancel</button>
                                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded font-bold hover:bg-indigo-700 shadow-md">Update Expense</button>
                                                </div>
                                            </form>
                                        </div>
                                    </x-modal>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-bold italic">
                                        No expense records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($expenses->count() > 0)
                        <tfoot class="bg-slate-50/50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-black text-slate-900 uppercase">Total</td>
                                <td class="px-6 py-4 text-right font-black text-lg text-indigo-700">₹{{ number_format($totalAmount, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            
            <div class="mt-4 ">
                {{ $expenses->links() }}
            </div>

        </div>
    </div>

    {{-- Add Modal --}}
    <x-modal name="add-expense" focusable>
        <div class="p-6">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                <span class="p-2 bg-indigo-100 rounded-lg">💸</span>
                Record New Expense
            </h2>
            <form action="{{ route('admin.expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide">Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide">Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide">Location</label>
                    <input type="text" name="location" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Garage, Office, Toll Booth" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide">Description</label>
                    <textarea name="description" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Describe the expense details..."></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 bg-slate-50/80 text-slate-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Save Expense</button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
