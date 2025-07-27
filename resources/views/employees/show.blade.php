<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Karyawan
            </h2>
            {{-- Tombol Aksi di Header --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    <x-heroicon-o-arrow-left class="w-8 h-8"/>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                {{-- Layout Dua Kolom --}}
                <div class="md:flex">
                    <div class="md:w-1/3 bg-gray-50 dark:bg-gray-700/50 p-6 border-r border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col items-center text-center">
                            <img class="h-24 w-24 rounded-full mb-4 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($employee->user->name) }}&size=128&background=EBF4FF&color=7F9CF5" alt="Foto Profil">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                            <p class="text-md text-indigo-600 dark:text-indigo-400 font-semibold">{{ $employee->position }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $employee->nip }}</p>
                        </div>
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-600 pt-6">
                            <dl class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <x-heroicon-o-envelope class="w-5 h-5 text-gray-400"/>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $employee->user->email ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-heroicon-o-phone class="w-5 h-5 text-gray-400"/>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $employee->nomor_telepon ?? '-' }}</span>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="md:w-2/3 p-6">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Rinci</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendidikan Terakhir</dt>
                                <dd class="mt-1 text-md text-gray-900 dark:text-white">{{ $employee->pendidikan_terakhir ?? '-' }}</dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Lahir</dt>
                                <dd class="mt-1 text-md text-gray-900 dark:text-white">{{ $employee->tanggal_lahir?->format('d M Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Masuk</dt>
                                <dd class="mt-1 text-md text-gray-900 dark:text-white">{{ $employee->hire_date->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role Sistem</dt>
                                <dd class="mt-1 text-md text-gray-900 dark:text-white">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($employee->user->role) }}
                                    </span>
                                </dd>
                            </div>
                             <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Pada</dt>
                                <dd class="mt-1 text-md text-gray-900 dark:text-white">{{ $employee->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                                <dd class="mt-1 text-md text-gray-900 dark:text-white">{{ $employee->updated_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>