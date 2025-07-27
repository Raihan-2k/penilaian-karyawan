<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Kontainer Utama --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    {{-- Header dengan Tombol Aksi dan Pencarian --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Semua Karyawan</h3>
                        {{-- Tombol Tambah Karyawan (untuk Owner, Admin, Manager) --}}
                        @if (in_array(Auth::user()->role, ['owner', 'admin', 'manager']))
                            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <x-heroicon-o-user-plus class="w-4 h-4" />
                                <span>Tambah Karyawan</span>
                            </a>
                        @endif
                    </div>

                    {{-- Notifikasi --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Tabel Karyawan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Posisi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Masuk</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($employees as $employee)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($employee->user->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->user->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->user->email ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $employee->position }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($employee->user->role)
                                                    @case('owner') bg-red-100 text-red-800 @break
                                                    @case('admin') bg-purple-100 text-purple-800 @break
                                                    @case('manager') bg-blue-100 text-blue-800 @break
                                                    @case('administrator') bg-yellow-100 text-yellow-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch
                                            ">
                                                {{ ucfirst($employee->user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $employee->hire_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('employees.show', $employee) }}" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 p-1 rounded-full transition-colors">
                                                    <x-heroicon-o-eye class="w-5 h-5"/>
                                                </a>

                                                @php
                                                    $loggedInUserRole = Auth::user()->role;
                                                    $employeeRole = $employee->user->role;
                                                    $loggedInUserId = Auth::id();
                                                    $employeeUserId = $employee->user->id;

                                                    $canEdit = false;
                                                    $canDelete = false;

                                                    // Logika untuk Owner
                                                    if ($loggedInUserRole === 'owner') {
                                                        if (in_array($employeeRole, ['admin', 'manager'])) {
                                                            $canEdit = true;
                                                            $canDelete = true;
                                                        }
                                                    }
                                                    // Logika untuk Admin
                                                    elseif ($loggedInUserRole === 'admin') {
                                                        if (in_array($employeeRole, ['manager', 'administrator', 'karyawan'])) {
                                                            $canEdit = true;
                                                            $canDelete = true;
                                                        }
                                                    }
                                                    // Logika untuk Manager
                                                    elseif ($loggedInUserRole === 'manager') {
                                                        if (in_array($employeeRole, ['administrator', 'karyawan'])) {
                                                            $canEdit = true; // Manager bisa edit Administrator dan Karyawan
                                                        }
                                                        // Manager TIDAK BISA HAPUS siapa pun
                                                    }

                                                    // Tidak bisa mengedit/menghapus akun sendiri (kecuali jika owner mengedit owner lain, dll.)
                                                    // Ini adalah pengecekan umum untuk mencegah user menghapus/mengedit dirinya sendiri kecuali otorisasi eksplisit mengizinkan
                                                    if ($loggedInUserId === $employeeUserId) {
                                                        $canEdit = false; // Tidak bisa edit diri sendiri dari sini (profil terpisah)
                                                        $canDelete = false; // Tidak bisa hapus diri sendiri
                                                    }
                                                @endphp

                                                @if ($canEdit)
                                                    <a href="{{ route('employees.edit', $employee) }}" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 p-1 rounded-full transition-colors">
                                                        <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                                    </a>
                                                @endif

                                                @if ($canDelete)
                                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-1 rounded-full transition-colors">
                                                            <x-heroicon-o-trash class="w-5 h-5"/>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-users class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2"/>
                                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tidak Ada Karyawan</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data karyawan yang ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
