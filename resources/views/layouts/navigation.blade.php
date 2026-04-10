<nav x-data="{ open: false }"
    class="w-64 bg-emerald-700 h-screen text-white flex flex-col shadow-xl fixed top-0 left-0 overflow-y-auto border-r border-emerald-800 z-50">

    <div class="px-6 py-8 flex-1">
        <div class="flex flex-col items-center mb-10 px-2 gap-4">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-center gap-3">

                <div class="p-1.5 bg-white rounded-xl shadow-sm">
                    <img src="{{ asset('logo-pegadaian.png') }}" alt="Logo Pegadaian" class="w-16 h-16 object-contain" />
                </div>

                <span class="text-2xl font-bold tracking-tight uppercase text-white">PEGADAIAN</span>

                <span class="text-sm text-gray-400 opacity-70">Cabang Kebayoran Baru</span>
            </a>
        </div>

        <div class="space-y-3">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="w-full">
                {{ __('Dashboard') }}
            </x-nav-link>
            <x-nav-link :href="route('import.index')" :active="request()->routeIs('import.*')" class="w-full">
                <div class="flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> {{ __('Import Excel') }}
                </div>
            </x-nav-link>
        </div>
    </div>

    <div class="p-6 border-t border-gray-800 bg-[#080e29]">
        <div class="px-4 mb-4">
            <div class="font-bold text-sm text-indigo-400">{{ Auth::user()->name }}</div>
            <div class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</div>
        </div>

        <div class="space-y-1">
            {{-- <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-400 hover:text-white border-none">
                {{ __('Profile') }}
            </x-responsive-nav-link> --}}

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault();
                                    this.closest('form').submit();"
                    class="text-red-400 hover:text-red-300 border-none">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
