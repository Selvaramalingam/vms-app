<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Driver User Accounts') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100  text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Create New Driver User -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">➕ Create New Driver Account</h3>
                    <form action="{{ route('admin.driver-users.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Password</label>
                                <input type="password" name="password" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700">Link to Driver</label>
                                <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0" required>
                                    <option value="">Select Driver</option>
                                    @foreach($unlinkedDrivers as $d)
                                        <option value="{{ $d->id }}">{{ $d->driver_name }} ({{ $d->phone }})</option>
                                    @endforeach
                                </select>
                                @error('driver_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-700 transition">
                                    Create Account
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Driver Users -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 border-b pb-2">👥 Existing Driver Accounts</h3>
                    
                    @if($driverUsers->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-400">No driver user accounts found.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($driverUsers as $dUser)
                                <div class="border rounded-lg p-4 bg-slate-50/50" x-data="{ editing: false }">
                                    {{-- View Mode --}}
                                    <div x-show="!editing">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $dUser->name }}</p>
                                                <p class="text-sm text-slate-500">{{ $dUser->email }}</p>
                                                @if($dUser->driver)
                                                    <p class="text-xs text-blue-600 mt-1">🔗 Linked to: {{ $dUser->driver->driver_name }} ({{ $dUser->driver->phone }})</p>
                                                @else
                                                    <p class="text-xs text-red-500 mt-1">⚠️ Not linked to any driver record</p>
                                                @endif
                                            </div>
                                            <div class="flex gap-2">
                                                <button @click="editing = true" class="bg-blue-500 text-white text-sm px-3 py-1 rounded hover:bg-indigo-600 transition">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.driver-users.destroy', $dUser) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 text-white text-sm px-3 py-1 rounded hover:bg-red-600 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Edit Mode --}}
                                    <div x-show="editing" x-cloak>
                                        <form action="{{ route('admin.driver-users.update', $dUser) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-600">Name</label>
                                                    <input type="text" name="name" value="{{ $dUser->name }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-600">Email</label>
                                                    <input type="email" name="email" value="{{ $dUser->email }}" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-600">New Password <span class="text-gray-400">(leave blank to keep)</span></label>
                                                    <input type="password" name="password" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-600">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-600">Link to Driver</label>
                                                    <select name="driver_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm">
                                                        @if($dUser->driver)
                                                            <option value="{{ $dUser->driver->id }}" selected>{{ $dUser->driver->driver_name }} (current)</option>
                                                        @endif
                                                        @foreach($unlinkedDrivers as $d)
                                                            <option value="{{ $d->id }}">{{ $d->driver_name }} ({{ $d->phone }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 mt-3">
                                                <button type="submit" class="bg-green-600 text-white text-sm px-4 py-1.5 rounded hover:bg-green-700 transition">Save Changes</button>
                                                <button type="button" @click="editing = false" class="bg-gray-400 text-white text-sm px-4 py-1.5 rounded hover:bg-slate-50/500 transition">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
