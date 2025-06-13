<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filter Section --}}
                    <form action="{{ route('admin.attendances.index') }}" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <x-input-label for="date" :value="__('Filter Tanggal')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="$date" />
                            </div>
                            <div>
                                <x-input-label for="employee_id" :value="__('Filter Karyawan')" />
                                <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" @selected($emp->id == $employeeId)>
                                            {{ $emp->name }} ({{ $emp->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-primary-button class="ms-4">
                                    {{ __('Terapkan Filter') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    {{-- Attendance Table --}}
                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam Kerja</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lembur (Jam)</th>
                                    {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->employee->nip }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->employee->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($attendance->check_out_time)
                                                {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') }}
                                            @else
                                                <span class="text-red-500">Belum Check-out</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($attendance->check_in_time && $attendance->check_out_time)
                                                {{ \Carbon\Carbon::parse($attendance->check_out_time)->diffInMinutes(\Carbon\Carbon::parse($attendance->check_in_time)) / 60 }} jam
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 {{ $attendance->overtime_hours > 0 ? 'text-green-600 font-semibold' : '' }}">
                                            {{ $attendance->overtime_hours }}
                                        </td>
                                        {{-- Kolom aksi untuk edit/hapus/setujui lembur bisa ditambahkan di sini --}}
                                        {{-- <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <form action="#" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus record ini?')">Hapus</button>
                                            </form>
                                        </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data absensi yang ditemukan.</td>
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