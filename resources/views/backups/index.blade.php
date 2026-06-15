<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Database Backups') }}
            </h2>
            <form action="{{ route('backups.create') }}" method="POST">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-bold transition">
                    🔄 Create Manual Backup
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100  text-green-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100  text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 text-slate-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">File Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Created At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($backups as $backup)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $backup['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $backup['size'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $backup['date'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('backups.download', $backup['name']) }}" class="text-blue-600 hover:text-blue-900">Download</a>
                                        <form action="{{ route('backups.destroy', $backup['name']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this backup?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">No backups found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                <p class="text-sm text-yellow-700">
                    <strong>Note:</strong> Backups are stored in <code>storage/app/backups</code>. Only the last 7 backups are kept automatically.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
