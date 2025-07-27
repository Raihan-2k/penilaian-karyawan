<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-white leading-tight tracking-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Konten --}}
    <div class="py-10 bg-gradient-to-br from-white via-indigo-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Notifikasi --}}
            @if (session('success') || session('info') || session('error'))
                <div class="mb-6">
                    @foreach (['success' => 'green', 'info' => 'blue', 'error' => 'red'] as $type => $color)
                        @if (session($type))
                            <div class="bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded relative mb-4" role="alert">
                                {{ session($type) }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Profil Pengguna --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Halo, {{ $user->name }} 👋</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Selamat datang di dashboard Anda.</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">Email: <strong>{{ $user->email }}</strong></p>
                <p class="text-sm text-gray-700 dark:text-gray-400">Peran: <strong>{{ ucfirst($user->role) }}</strong></p>

                @if ($employeeData)
                    <div class="mt-4 bg-gray-100 dark:bg-gray-700 p-4 rounded-xl shadow-lg outline-red-800 outline-2  ">
                        <h4 class="font-semibold text-gray-800 dark:text-white text-md mb-2">Detail Karyawan:</h4>
                        <p class="text-gray-700 dark:text-gray-300">Jabatan: <strong>{{ $employeeData->position }}</strong></p>
                    </div>
                @else
                    <div class="mt-4 p-4 bg-yellow-100 text-yellow-800 rounded-lg text-sm">
                        Anda belum terdaftar sebagai karyawan di sistem HR.
                    </div>
                @endif
            </div>

            {{-- Panel Admin, hanya untuk owner dan manager --}}
        @if (in_array(Auth::user()->role, ['owner', 'manager']))
            <div class="bg-cyan-200 dark:bg-gray-800 p-6 rounded-xl shadow-md mb-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">🔧 Panel Manajemen</h3>
                <p class="text-sm text-gray-700 dark:text-gray-400 mb-4">
                    Anda memiliki akses manajemen karena peran Anda: <strong>{{ ucfirst(Auth::user()->role) }}</strong>.
                </p>
                <div class="flex flex-wrap gap-4">
                    @if (Auth::user()->role === 'manager')
                        <a href="{{ route('sales-validations.index') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-800 rounded-lg hover:bg-orange-200 font-semibold text-sm transition-colors dark:bg-orange-500/20 dark:text-orange-300 dark:hover:bg-orange-500/30">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            <span>Validasi Penjualan</span>
                        </a>
                    @endif
                    @if (Auth::user()->role === 'owner')
                        <a href="{{ route('sales-proofs.index') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 font-semibold text-sm transition-colors dark:bg-purple-500/20 dark:text-purple-300 dark:hover:bg-purple-500/30">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                        <span>Kelola Bukti Penjualan</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

            {{-- Kartu 1: Total Karyawan --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex items-center gap-6">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-300 p-4 rounded-full">
                    <x-heroicon-o-user-group class="w-8 h-8"/>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Karyawan</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalEmployees }}</p>
                </div>
            </div>

            {{-- Kartu 2: Penilaian Selesai Bulan Ini --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex items-center gap-6">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-300 p-4 rounded-full">
                    <x-heroicon-o-clipboard-document-check class="w-8 h-8"/>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penilaian Selesai</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $appraisalsThisMonth }}</p>
                </div>
            </div>

            {{-- Kartu 3: Karyawan Belum Check-in Hari Ini --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex items-center gap-6">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-300 p-4 rounded-full">
                    <x-heroicon-o-clock class="w-8 h-8"/>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum Check-in</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $employeesNotCheckedInToday }}</p>
                </div>
            </div>
            
        </div>
                    
            {{-- Aktivitas & Aksi Cepat --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Aktivitas Terbaru --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📌 Aktivitas Terbaru</h3>
                    <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        @forelse ($recentAppraisals as $appraisal)
                            <li class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg shadow-sm">
                                <span class="font-semibold">{{ $appraisal->employee?->user?->name }}</span> - Penilaian oleh <span class="font-semibold">{{ $appraisal->appraiser?->user?->name }}</span> ({{ $appraisal->appraisal_date->format('d M Y') }})
                                <span class="ml-2 inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $appraisal->overall_score >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    Skor: {{ $appraisal->overall_score }}
                                </span>
                                <a href="{{ route('appraisals.show', $appraisal) }}" class="text-indigo-600 hover:underline ml-2">Lihat</a>
                            </li>
                        @empty
                            <li class="text-center text-gray-500">Belum ada penilaian terbaru.</li>
                        @endforelse

                        @forelse ($recentAttendances as $attendance)
                            <li class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg shadow-sm">
                                <strong>{{ $attendance->employee?->user?->name }}</strong> -
                                @if ($attendance->check_out_time)
                                    Check-out ({{ \Carbon\Carbon::parse($attendance->check_out_time)->format('d M Y, H:i') }})
                                @else
                                    Check-in ({{ \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y, H:i') }})
                                @endif
                            </li>
                        @empty
                            @if ($recentAppraisals->isEmpty())
                                <li class="text-center text-gray-500">Belum ada aktivitas absensi terbaru.</li>
                            @endif
                        @endforelse
                    </ul>
                </div>

                {{-- Aksi Cepat --}}
               <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⚡ Aksi Cepat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <a href="{{ route('employees.create') }}" class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <div class="bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400 p-2 rounded-lg">
                                    <x-heroicon-o-user-plus class="w-5 h-5" />
                                </div>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Tambah Karyawan</span>
                            </a>
                            <a href="{{ route('admin.attendances.index') }}" class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <div class="bg-yellow-100 text-yellow-600 dark:bg-yellow-900/40 dark:text-yellow-400 p-2 rounded-lg">
                                <x-heroicon-o-calendar-days class="w-5 h-5" />
                            </div>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Lihat Absensi</span>
                            </a>
                            <a href="{{ route('appraisals.index') }}"class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <div class="bg-green-100 text-green-500 dark:bg-green-900/40 dark:text-green-400 p-2 rounded-lg">
                                    <x-heroicon-o-clipboard-document class="w-5 h-5" />
                                </div>
                                    <span class="font-semibold text-gray-700 dark:text-gray-200">Lihat Absensi</span> 
                            </a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
