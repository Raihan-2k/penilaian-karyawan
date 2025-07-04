<nav x-data="{ open: false }" class="bg-indigo-50 shadow-sm border-b border-indigo-100"> {{-- Desain navbar --}}
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    {{-- Logo akan mengarah ke dashboard yang sesuai role --}}
                    @auth {{-- Memastikan user sudah login sebelum mengecek role --}}
                        @if (Auth::user()->role === 'manager')
                            <a href="{{ route('dashboard') }}">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                            </a>
                        @elseif (Auth::user()->role === 'karyawan')
                            <a href="{{ route('absensi.dashboard') }}">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Navigasi untuk Manager --}}
                    @if (Auth::check() && Auth::user()->role === 'manager') {{-- Auth::check() untuk memastikan user login --}}
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                            {{ __('Karyawan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('appraisal-criteria.index')" :active="request()->routeIs('appraisal-criteria.*')">
                            {{ __('Kriteria Penilaian') }}
                        </x-nav-link>
                        <x-nav-link :href="route('appraisals.index')" :active="request()->routeIs('appraisals.*')">
                            {{ __('Penilaian') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.attendances.index')" :active="request()->routeIs('admin.attendances.index')">
                            {{ __('Laporan Absensi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                            {{ __('Manajemen Tugas') }}
                        </x-nav-link>
                    @endif

                    {{-- Navigasi untuk Karyawan --}}
                    @if (Auth::check() && Auth::user()->role === 'karyawan')
                        <x-nav-link :href="route('absensi.dashboard')" :active="request()->routeIs('absensi.dashboard')">
                            {{ __('Dashboard Absensi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('employee-tasks.index')" :active="request()->routeIs('employee-tasks.*')"> {{-- TAMBAHAN: Tugas Saya --}}
                            {{ __('Tugas Saya') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown (Pengaturan Profil & Logout) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            {{-- Nama User berdasarkan Role yang aktif --}}
                            @auth {{-- Memastikan user sudah login sebelum mengecek role --}}
                                @if (Auth::user()->role === 'manager')
                                    <div>{{ Auth::user()->name }}</div>
                                @elseif (Auth::user()->role === 'karyawan')
                                    <div>{{ Auth::user()->name }}</div>
                                @endif
                            @endauth

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Link Profil & Logout berdasarkan Role --}}
                        @if (Auth::check() && Auth::user()->role === 'manager')
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @elseif (Auth::check() && Auth::user()->role === 'karyawan')
                            <x-dropdown-link :href="route('absensi.change-password')">
                                {{ __('Ganti Password') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out Absensi') }}
                                </x-dropdown-link>
                            </form>
                        @endif
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile Menu) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile View) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Navigasi Responsif untuk Manager --}}
            @if (Auth::check() && Auth::user()->role === 'manager')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                    {{ __('Karyawan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('appraisal-criteria.index')" :active="request()->routeIs('appraisal-criteria.*')">
                    {{ __('Kriteria Penilaian') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('appraisals.index')" :active="request()->routeIs('appraisals.*')">
                    {{ __('Penilaian') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.attendances.index')" :active="request()->routeIs('admin.attendances.index')">
                    {{ __('Laporan Absensi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                    {{ __('Manajemen Tugas') }}
                </x-responsive-nav-link>
            @endif

            {{-- Navigasi Responsif untuk Karyawan --}}
            @if (Auth::check() && Auth::user()->role === 'karyawan')
                <x-responsive-nav-link :href="route('absensi.dashboard')" :active="request()->routeIs('absensi.dashboard')">
                    {{ __('Dashboard Absensi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employee-tasks.index')" :active="request()->routeIs('employee-tasks.*')"> {{-- TAMBAHAN: Tugas Saya --}}
                    {{ __('Tugas Saya') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options (Mobile View) -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                {{-- Nama dan NIP User berdasarkan Role --}}
                @auth
                    @if (Auth::user()->role === 'manager')
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    @elseif (Auth::user()->role === 'karyawan')
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->nip }}</div>
                    @endif
                @endauth
            </div>

            <div class="mt-3 space-y-1">
                {{-- Link Profil & Logout Responsif berdasarkan Role --}}
                @if (Auth::check() && Auth::user()->role === 'manager')
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                @elseif (Auth::check() && Auth::user()->role === 'karyawan')
                    <x-responsive-nav-link :href="route('absensi.change-password')">
                        {{ __('Ganti Password') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out Absensi') }}
                        </x-responsive-nav-link>
                    </form>
                @endif
            </div>
        </div>
    </div>
</nav>
