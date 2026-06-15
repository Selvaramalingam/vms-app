<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 text-slate-900">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Logo -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Company Logo</label>
                                @if(isset($settings['logo']))
                                    <div class="mb-3">
                                        <img src="{{ $settings['logo'] }}" alt="Logo" class="h-20 w-auto rounded border">
                                    </div>
                                @endif
                                <input type="file" name="logo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-slate-500">Allowed formats: PNG, JPG, JPEG. Max size: 2MB.</p>
                            </div>

                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Company Name</label>
                                <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Address</label>
                                <textarea name="address" rows="3" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500">{{ $settings['address'] ?? '' }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700">Phone</label>
                                <input type="text" name="phone" value="{{ $settings['phone'] ?? '' }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Currency Symbol</label>
                                    <input type="text" name="currency" value="{{ $settings['currency'] ?? '₹' }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700">Date Format</label>
                                    <select name="date_format" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500">
                                        <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg shadow hover:bg-indigo-700 transition">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
