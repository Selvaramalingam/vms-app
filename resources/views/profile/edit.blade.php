<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-between items-center bg-gradient-to-r from-red-50 to-white border-l-4 border-red-600">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Account Access</h3>
                    <p class="text-xs text-slate-500">Sign out of your vehicle account securely.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-8 py-3 rounded-xl shadow-xl hover:bg-red-700 font-black transition-all transform active:scale-95 flex items-center gap-3">
                        🚪 LOGOUT NOW
                    </button>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-t-4 border-red-500">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-bold text-slate-900">
                                {{ __('Log Out') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Once you log out, you will need to enter your credentials again to access the vehicle management system.') }}
                            </p>
                        </header>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-red-700 font-black transition-all flex items-center gap-2">
                                🚪 {{ __('Log Out Now') }}
                            </button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
