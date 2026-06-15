{{-- Sleek SaaS Sidebar + Top Header Navigation --}}
<div x-data="{ notificationsOpen: false }">

    {{-- Mobile Overlay --}}
    <div x-show="$store.sidebar.open" 
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden" 
         @click="$store.sidebar.close()"></div>

    {{-- Sidebar --}}
    <aside @click.away="$store.sidebar.close()"
           :class="$store.sidebar.open ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-[260px] transform transition-transform duration-300 ease-in-out flex flex-col bg-white border-r border-slate-200 shadow-[4px_0_24px_rgba(0,0,0,0.05)] lg:shadow-none">

        {{-- Logo & Close Button --}}
        <div class="flex items-center justify-between px-5 h-16 shrink-0 border-b border-transparent">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded bg-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18M7 17a1 1 0 100 2 1 1 0 000-2zm10 0a1 1 0 100 2 1 1 0 000-2z"/></svg>
                </div>
                <span class="text-slate-900 font-bold text-lg tracking-tight">VMS</span>
            </div>
            <button @click="$store.sidebar.close()" class="p-2 rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Close Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Nav Items --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 sidebar-scroll">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zm10-3a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/></svg>
                <span>Overview</span>
            </a>

            @hasrole('Admin')
            <div class="sidebar-section">Operations</div>
            <a href="{{ route('trips.index') }}" class="sidebar-link {{ request()->routeIs('trips.*') && !request()->routeIs('trips.my') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.382V5.618a2 2 0 011.106-1.789L9 1m0 0l6 3m-6-3v18m6-15l5.447 2.724A2 2 0 0121 8.618v9.764a2 2 0 01-1.106 1.789L15 22m0-18v18"/></svg>
                <span>Trips</span>
            </a>
            @endhasanyrole

            @hasrole('Admin')
            <a href="{{ route('vehicles.index') }}" class="sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18M7 17a1 1 0 100 2 1 1 0 000-2zm10 0a1 1 0 100 2 1 1 0 000-2z"/></svg>
                <span>Vehicles</span>
            </a>
            <a href="{{ route('drivers.index') }}" class="sidebar-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Drivers</span>
            </a>
            <a href="{{ route('admin.vehicle-users.index') }}" class="sidebar-link {{ request()->routeIs('admin.vehicle-users.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <span>Access Control</span>
            </a>
            @endrole

            @hasrole('Admin')
            <div class="sidebar-section">Finance</div>
            <a href="{{ route('fuel.index') }}" class="sidebar-link {{ request()->routeIs('fuel.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>Fuel Logs</span>
            </a>
            <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Payments</span>
            </a>
            @hasrole('Admin')
            <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/></svg>
                <span>Expenses</span>
            </a>
            @endrole
            @endhasanyrole

            @hasrole('Admin')
            <div class="sidebar-section">Maintenance</div>
            <a href="{{ route('maintenances.index') }}" class="sidebar-link {{ request()->routeIs('maintenances.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Service Logs</span>
            </a>
            
            <div class="sidebar-section">Insights</div>
            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Reports</span>
            </a>
            <a href="{{ route('invoices.index') }}" class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Invoice</span>
            </a>

            <div class="sidebar-section">System</div>
            <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Settings</span>
            </a>
            @endrole

            @hasrole('Vehicle')
            <div class="sidebar-section">My Work</div>
            <a href="{{ route('trips.my') }}" class="sidebar-link {{ request()->routeIs('trips.my') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.382V5.618a2 2 0 011.106-1.789L9 1m0 0l6 3m-6-3v18m6-15l5.447 2.724A2 2 0 0121 8.618v9.764a2 2 0 01-1.106 1.789L15 22m0-18v18"/></svg>
                <span>My Trips</span>
            </a>
            @hasrole('Vehicle')
            <a href="{{ route('trips.create') }}" class="sidebar-link {{ request()->routeIs('trips.create') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Trip</span>
            </a>
            @endhasrole
            <a href="{{ route('maintenances.index') }}" class="sidebar-link {{ request()->routeIs('maintenances.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Maintenance</span>
            </a>
            @endhasanyrole

        </nav>

        {{-- Footer: User Info --}}
        <div class="shrink-0 border-t border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold shrink-0 text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->roles->first()?->name ?? 'User' }}</p>
                </div>
            </div>
            <div class="mt-4 flex gap-1">
                <a href="{{ route('profile.edit') }}" class="flex-1 text-center py-1.5 text-xs font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full text-center py-1.5 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Top Header Bar --}}
    <header :class="$store.sidebar.open ? 'lg:left-[260px]' : 'lg:left-0'" class="fixed top-0 right-0 left-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 transition-all duration-300">
        <div class="flex items-center gap-3">
            <button x-show="!$store.sidebar.open" @click.stop="$store.sidebar.open = true" class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 transition-colors" title="Open Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="text-sm font-semibold text-slate-800 tracking-tight" id="pageTitle">
                @yield('page_title', 'Vehicle Management')
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            {{-- Notifications --}}
            <div class="relative">
                <button @click="notificationsOpen = !notificationsOpen" class="relative p-1.5 rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if(isset($globalNotificationCount) && $globalNotificationCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                    @endif
                </button>

                <div x-show="notificationsOpen" x-cloak @click.away="notificationsOpen = false" x-transition class="absolute right-0 mt-3 w-screen max-w-sm sm:w-80 bg-white rounded-xl shadow-lg ring-1 ring-black/5 z-50 overflow-hidden origin-top-right">
                    <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Notifications</span>
                        @if(isset($globalNotificationCount) && $globalNotificationCount > 0)
                        <span class="text-[10px] bg-indigo-600 text-white px-2 py-0.5 rounded-full font-medium">{{ $globalNotificationCount }}</span>
                        @endif
                    </div>
                    <div class="max-h-[32rem] overflow-y-auto">
                        @if(isset($globalNotifications) && count($globalNotifications) > 0)
                            @foreach($globalNotifications as $notif)
                                <a href="{{ $notif['url'] }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                                    <div class="flex gap-3">
                                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-rose-50 text-rose-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-slate-900 truncate">{{ $notif['title'] }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $notif['message'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="px-4 py-8 text-center flex flex-col items-center">
                                <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                <p class="text-sm text-slate-500">You're all caught up!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </header>
</div>

<style>
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        color: #64748b; /* slate-500 */
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.15s ease;
        text-decoration: none;
        position: relative;
    }
    .sidebar-link:hover {
        background-color: #eef2ff; /* indigo-50 */
        color: #4f46e5; /* indigo-600 */
    }
    .sidebar-link.active {
        background-color: #eef2ff; /* indigo-50 */
        color: #4f46e5; /* indigo-600 */
        font-weight: 600;
    }
    .sidebar-link.active::before {
        content: '';
        position: absolute;
        left: -0.75rem;
        top: 0.25rem;
        bottom: 0.25rem;
        width: 3px;
        background-color: #4f46e5;
        border-radius: 0 4px 4px 0;
    }
    .sidebar-section {
        padding: 1.5rem 0.75rem 0.5rem;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        color: #94a3b8; /* slate-400 */
        text-transform: uppercase;
    }
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .sidebar-scroll:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
</style>
