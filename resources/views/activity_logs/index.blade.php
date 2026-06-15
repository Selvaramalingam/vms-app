<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('System Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
             
+            <!-- Filter Bar -->
+            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
+                <form action="{{ route('activity-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
+                    <div>
+                        <label class="block text-xs font-bold text-slate-700 uppercase">User</label>
+                        <select name="user_id" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
+                            <option value="">All Users</option>
+                            @foreach($users as $user)
+                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
+                            @endforeach
+                        </select>
+                    </div>
+                    <div>
+                        <label class="block text-xs font-bold text-slate-700 uppercase">Module</label>
+                        <select name="module" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
+                            <option value="">All Modules</option>
+                            @foreach($modules as $module)
+                                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
+                            @endforeach
+                        </select>
+                    </div>
+                    <div>
+                        <label class="block text-xs font-bold text-slate-700 uppercase">Action</label>
+                        <select name="action" class="mt-1 block w-full rounded-md border-slate-200 text-sm shadow-sm text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-1 focus:ring-offset-0">
+                            <option value="">All Actions</option>
+                            @foreach($actions as $action)
+                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
+                            @endforeach
+                        </select>
+                    </div>
+                    <div class="flex gap-2">
+                        <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded shadow hover:bg-gray-900 transition">Filter</button>
+                        <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-slate-700 font-medium rounded shadow hover:bg-gray-300 transition text-center">Clear</a>
+                    </div>
+                </form>
+            </div>

             <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 text-slate-900">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @forelse($logs as $log)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full ring-8 ring-white {{ $log->action == 'created' ? 'bg-green-100' : ($log->action == 'updated' ? 'bg-blue-100' : 'bg-red-100') }}">
                                                    <span class="text-xs font-bold {{ $log->action == 'created' ? 'text-green-700' : ($log->action == 'updated' ? 'text-blue-700' : 'text-red-700') }}">
                                                        {{ strtoupper(substr($log->action, 0, 1)) }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-black text-slate-900">{{ $log->user->name ?? 'System' }}</span>
                                                        <span class="text-slate-500">{{ $log->action }}</span>
                                                        <span class="font-bold text-slate-900">{{ $log->module }}</span>
                                                    </div>
                                                    <p class="mt-0.5 text-xs text-gray-400">
                                                        {{ $log->created_at->diffForHumans() }} ({{ $log->created_at->format('d M, h:i A') }})
                                                    </p>
                                                </div>
                                                <div class="mt-2 text-sm text-slate-700 italic bg-slate-50/50 p-2 rounded">
                                                    <p>{{ $log->description }}</p>
                                                </div>
                                                <div class="mt-1 text-[10px] text-gray-400">
                                                    IP: {{ $log->ip_address }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center py-10 text-slate-500 italic">No activity logs found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-200 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
