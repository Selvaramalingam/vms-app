<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            🕐 {{ __('Login History') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Tabs --}}
            <div class="flex gap-2">
                <a href="{{ route('login-history.index', array_merge(request()->query(), ['tab' => 'Admin'])) }}"
                   class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2
                   {{ $tab === 'Admin' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-gray-600 hover:bg-slate-50/50 border border-gray-200' }}">
                    🧑‍💼 Admin Logins
                </a>
                <a href="{{ route('login-history.index', array_merge(request()->query(), ['tab' => 'Vehicle'])) }}"
                   class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2
                   {{ $tab === 'Vehicle' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-gray-600 hover:bg-slate-50/50 border border-gray-200' }}">
                    🚗 Vehicle Logins
                </a>
            </div>

            {{-- Last Device Login Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Admin Last Login Card --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-lg">🧑‍💼</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Admin Last Login</h3>
                            <p class="text-xs text-gray-400">Latest admin device session</p>
                        </div>
                    </div>
                    @if($lastAdminLogin)
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">User</span>
                                <span class="font-semibold text-slate-900">{{ $lastAdminLogin->user->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Device</span>
                                <span class="font-medium text-slate-700 flex items-center gap-1">
                                    {{ $lastAdminLogin->platform_type === 'mobile' ? '📱' : '💻' }}
                                    {{ $lastAdminLogin->device_name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Time</span>
                                <span class="font-medium text-slate-700">{{ $lastAdminLogin->login_datetime->format('d/m/Y h:i A') }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No admin login records found.</p>
                    @endif
                </div>

                {{-- Vehicle Last Login Card --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-lg">🚗</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Vehicle Last Login</h3>
                            <p class="text-xs text-gray-400">Latest vehicle device session</p>
                        </div>
                    </div>
                    @if($lastVehicleLogin)
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">User</span>
                                <span class="font-semibold text-slate-900">{{ $lastVehicleLogin->user->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Device</span>
                                <span class="font-medium text-slate-700 flex items-center gap-1">
                                    {{ $lastVehicleLogin->platform_type === 'mobile' ? '📱' : '💻' }}
                                    {{ $lastVehicleLogin->device_name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Time</span>
                                <span class="font-medium text-slate-700">{{ $lastVehicleLogin->login_datetime->format('d/m/Y h:i A') }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No vehicle login records found.</p>
                    @endif
                </div>
            </div>

            {{-- Filters & Search --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <form method="GET" action="{{ route('login-history.index') }}" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    {{-- User Dropdown --}}
                    <div class="relative flex-1 w-full sm:w-auto">
                        <select name="user_id" class="w-full pl-4 pr-10 py-2 text-sm border border-slate-200 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Period Filter --}}
                    <div class="flex gap-1.5 flex-wrap">
                        <a href="{{ route('login-history.index', ['tab' => $tab, 'user_id' => $userId]) }}"
                           class="px-3 py-2 text-xs font-medium rounded-lg transition
                           {{ $period === '' ? 'bg-indigo-600 text-white' : 'bg-slate-50/80 text-gray-600 hover:bg-gray-200' }}">
                            All
                        </a>
                        <a href="{{ route('login-history.index', ['tab' => $tab, 'user_id' => $userId, 'period' => 'today']) }}"
                           class="px-3 py-2 text-xs font-medium rounded-lg transition
                           {{ $period === 'today' ? 'bg-indigo-600 text-white' : 'bg-slate-50/80 text-gray-600 hover:bg-gray-200' }}">
                            Today
                        </a>
                        <a href="{{ route('login-history.index', ['tab' => $tab, 'user_id' => $userId, 'period' => '7days']) }}"
                           class="px-3 py-2 text-xs font-medium rounded-lg transition
                           {{ $period === '7days' ? 'bg-indigo-600 text-white' : 'bg-slate-50/80 text-gray-600 hover:bg-gray-200' }}">
                            Last 7 Days
                        </a>
                        <a href="{{ route('login-history.index', ['tab' => $tab, 'user_id' => $userId, 'period' => '30days']) }}"
                           class="px-3 py-2 text-xs font-medium rounded-lg transition
                           {{ $period === '30days' ? 'bg-indigo-600 text-white' : 'bg-slate-50/80 text-gray-600 hover:bg-gray-200' }}">
                            Last 30 Days
                        </a>
                    </div>

                    {{-- Search Button --}}
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                        Search
                    </button>
                </form>
            </div>

            {{-- Login History Table --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">User</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Role</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Login Date & Time</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Logout Date & Time</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Device</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Platform</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Browser</th>
                                <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-xs">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($histories as $history)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                                        {{ $history->user->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($history->role === 'Admin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">🧑‍💼 Admin</span>
                                        @elseif($history->role === 'Vehicle')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">🚗 Vehicle</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50/80 text-slate-700 rounded-full text-xs font-semibold">{{ $history->role }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                            {{ $history->login_datetime ? $history->login_datetime->format('d/m/Y h:i A') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                        @if($history->logout_datetime)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                {{ $history->logout_datetime->format('d/m/Y h:i A') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="flex items-center gap-1.5 text-slate-700">
                                            {{ $history->platform_type === 'mobile' ? '📱' : '💻' }}
                                            {{ $history->device_name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $history->platform }}</td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $history->browser_name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($history->is_active)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50/80 text-slate-500 rounded-full text-xs font-medium">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                Logged out
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-sm">No login records found for {{ $tab }} role.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($histories->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $histories->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
