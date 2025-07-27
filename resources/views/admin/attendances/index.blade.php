<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Kontainer Utama --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 md:p-8">

                    {{-- Bagian Filter --}}
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-700">
                        <form action="{{ route('admin.attendances.index') }}" method="GET">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                <div>
                                    <x-input-label for="date" :value="__('Pilih Tanggal')" />
                                    <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="$selectedDate->format('Y-m-d')" />
                                </div>
                                <div>
                                    <x-input-label for="employee_id" :value="__('Pilih Karyawan')" />
                                    <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">-- Semua Karyawan --</option>
                                        @foreach($allEmployees as $emp)
                                            <option value="{{ $emp->id }}" @selected($emp->id == $employeeIdFilter)>
                                                {{ $emp->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-primary-button class="w-full justify-center">
                                        <x-heroicon-o-funnel class="w-4 h-4 mr-2"/>
                                        {{ __('Filter') }}
                                    </x-primary-button>
                                </div>
                                <div class="flex items-center gap-2">
                                     <a href="{{ route('admin.attendances.monthly_report') }}" class="w-full justify-center inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <x-heroicon-o-document-arrow-down class="w-4 h-4 mr-2"/>
                                        Laporan Bulanan
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    {{-- Notifikasi --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    {{-- Tabel Laporan Absensi --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Karyawan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jam Kerja</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($attendanceData as $data)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        {{-- Info Karyawan --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($data['employee']->user->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $data['employee']->user->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">NIP: {{ $data['employee']->nip }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Status dengan Badge --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @switch($data['status'])
                                                    @case('Hadir') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 @break
                                                    @case('Absen') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @break
                                                    @case('Absen (Manual)') bg-red-200 text-red-900 dark:bg-red-900 dark:text-red-100 @break
                                                    @case('Libur') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 @break
                                                    @case('Libur Nasional') bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100 @break
                                                    @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100
                                                @endswitch
                                            ">
                                                {{ $data['status'] }}
                                            </span>
                                        </td>
                                        {{-- Jam Kerja --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if ($data['check_in_time'])
                                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($data['check_in_time'])->format('H:i') }}</span>
                                                -
                                                @if ($data['check_out_time'])
                                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($data['check_out_time'])->format('H:i') }}</span>
                                                @else
                                                     <span class="text-red-500">...</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        {{-- Total Jam --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                            @if (is_numeric($data['total_work_hours']))
                                                {{ $data['total_work_hours'] }} jam
                                            @else
                                                -
                                            @endif
                                        </td>
                                        {{-- Tombol Aksi --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if (in_array(Auth::user()->role, ['owner', 'admin']))
                                                @if ($data['status'] == 'Hadir' || $data['status'] == 'Absen' || $data['status'] == 'Absen (Manual)')
                                                    <a href="{{ route('admin.attendances.edit_or_create', ['employee' => $data['employee']->id, 'date' => $data['date']->format('Y-m-d')]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Edit
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2"/>
                                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tidak Ada Data</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada data absensi untuk filter yang dipilih.</p>
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