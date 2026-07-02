<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts: Inter for a sleek SaaS look -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#ffffff">
        
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                const activeLink = document.querySelector('.sidebar-link.active');
                if (activeLink && document.getElementById('pageTitle')) {
                    const titleText = activeLink.querySelector('span').innerText;
                    document.getElementById('pageTitle').innerText = titleText;
                }
            });
        </script>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#FAFAFA]" @keydown.escape.window="$store.sidebar.close()">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Toast Notifications -->
            <div x-data="{ 
                show: false, 
                message: '', 
                type: 'success',
                init() {
                    @if(session('success'))
                        this.showToast('{{ session('success') }}', 'success');
                    @endif
                    @if(session('error'))
                        this.showToast('{{ session('error') }}', 'error');
                    @endif
                },
                showToast(msg, type) {
                    this.message = msg;
                    this.type = type;
                    this.show = true;
                    setTimeout(() => { this.show = false }, 4000);
                }
            }" 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
            class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex w-full max-w-sm flex-col space-y-4"
            style="display: none;">
                <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <template x-if="type === 'success'">
                                    <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </template>
                                <template x-if="type === 'error'">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </template>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-slate-900" x-text="type === 'success' ? 'Success' : 'Error'"></p>
                                <p class="mt-1 text-sm text-slate-500" x-text="message"></p>
                            </div>
                            <div class="ml-4 flex flex-shrink-0">
                                <button @click="show = false" type="button" class="inline-flex rounded-md bg-white text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sleek Page Loading Animation -->
            <div id="pageLoader" class="fixed inset-0 z-[100] flex items-center justify-center bg-white/80 backdrop-blur-sm" style="display:none;">
                <div class="flex flex-col items-center">
                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-zinc-900"></div>
                </div>
            </div>

            <!-- Global Form Loading Spinner -->
            <div x-data="{ loading: false }" 
                 x-show="loading" 
                 @loading.window="loading = $event.detail"
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-white/50 backdrop-blur-sm"
                 style="display: none;">
                <div class="flex flex-col items-center p-6 bg-white rounded-2xl shadow-xl border border-slate-100">
                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-zinc-900"></div>
                    <p class="mt-4 text-sm font-medium text-slate-600 tracking-tight">Processing...</p>
                </div>
            </div>

            <!-- PWA Offline Indicator -->
            <div x-data="{ online: navigator.onLine }" 
                 @online.window="online = true" 
                 @offline.window="online = false" 
                 x-show="!online"
                 class="bg-rose-50 border-b border-rose-200 text-rose-700 text-center py-2 text-sm font-medium sticky top-0 z-[60]"
                 style="display: none;">
                You are currently offline. Some features may be unavailable.
            </div>

            <!-- Main Content Area -->
            @hasrole('Vehicle')
            <div class="pt-16 min-h-screen transition-all duration-300 flex flex-col">
            @else
            <div class="pt-16 min-h-screen transition-all duration-300 flex flex-col">
            @endhasrole
                
                {{-- Global Urgent Alerts Banner --}}
                @hasanyrole('Admin|Staff|Vehicle|Driver')
                @if(isset($globalNotifications) && count(array_filter($globalNotifications, fn($n) => $n['urgent'] ?? false)) > 0)
                    <div class="bg-amber-50 border-b border-amber-200 py-2 px-4 sticky top-16 z-20">
                        <div class="flex items-center gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 text-xs font-bold">!</span>
                            <div class="flex-1 whitespace-nowrap overflow-hidden">
                                <div class="inline-flex gap-8 animate-marquee hover:pause whitespace-nowrap">
                                    @php $urgentAlerts = array_filter($globalNotifications, fn($n) => $n['urgent'] ?? false); @endphp
                                    @foreach($urgentAlerts as $notif)
                                        <a href="{{ $notif['url'] }}" class="text-amber-800 hover:text-amber-900 font-medium text-sm">
                                            {{ $notif['title'] }}: {{ $notif['message'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
                        .animate-marquee { display: inline-flex; animation: marquee 40s linear infinite; }
                        .hover\:pause:hover { animation-play-state: paused; }
                    </style>
                @endif
                @endhasanyrole

                <!-- Page Heading (Minimalist) -->
                @isset($header)
                    <header class="bg-transparent pt-8 pb-4">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-4">
                            @if(!request()->routeIs('dashboard') && !request()->routeIs('trips.my'))
                            <button onclick="window.history.back()" class="text-slate-400 hover:text-slate-900 transition focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            </button>
                            @endif
                            <div class="flex-1 text-2xl font-semibold tracking-tight text-slate-900">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 pb-12">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            // Global Form Loading Spinner
            document.addEventListener('submit', function(e) {
                if (!e.target.classList.contains('no-loader')) {
                    window.dispatchEvent(new CustomEvent('loading', { detail: true }));
                }
            });

            // Page Navigation Loader
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.startsWith('javascript') && !link.hasAttribute('download') && !e.ctrlKey && !e.metaKey && link.target !== '_blank') {
                    const url = new URL(link.href);
                    if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                        document.getElementById('pageLoader').style.display = 'flex';
                    }
                }
            });
            window.addEventListener('pageshow', function() {
                document.getElementById('pageLoader').style.display = 'none';
            });
        </script>
    </body>
</html>
