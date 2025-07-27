<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Filter Form --}}
                    <form action="{{ route('admin.attendances.monthly_report') }}" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <x-input-label for="month" :value="__('Bulan')" />
                                <select id="month" name="month" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($availableMonths as $num => $name)
                                        <option value="{{ $num }}" @selected($num == $selectedMonth)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="year" :value="__('Tahun')" />
                                <select id="year" name="year" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" @selected($year == $selectedYear)>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="employee_id" :value="__('Filter Karyawan')" />
                                <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach($allEmployeesForFilter as $emp)
                                        <option value="{{ $emp->id }}" @selected($emp->id == $employeeIdFilter)>
                                            {{ $emp->user->name }} ({{ $emp->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-primary-button>
                                    {{ __('Tampilkan Laporan') }}
                                </x-primary-button>
                                {{-- --- PERBAIKAN DI SINI --- --}}
                                {{-- Tombol Ekspor PDF --}}
                                <a href="{{ route('admin.attendances.monthly_report.export_pdf', ['year' => $selectedYear, 'month' => $selectedMonth, 'employee_id' => $employeeIdFilter]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Ekspor PDF') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    @forelse ($reportData as $employeeReport)
                        <div class="mb-8 p-6 border rounded-lg shadow-md bg-gray-50">
                            <h4 class="text-xl font-bold text-gray-800 mb-4">
                                Laporan untuk {{ $employeeReport['employee']->user->name }} (NIP: {{ $employeeReport['employee']->nip }})
                            </h4>
                            <p class="text-gray-700 mb-2">Shift: <span class="font-bold">{{ $employeeReport['employee']->shift->name ?? 'Belum Ditetapkan' }}</span></p>
                            <p class="text-gray-700 mb-4">Tanggal Masuk: <span class="font-bold">{{ $employeeReport['employee']->hire_date->format('d M Y') }}</span></p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-center mb-4">
                                <div class="p-3 bg-indigo-100 rounded-lg">
                                    <div class="text-2xl font-bold text-indigo-700">{{ round($employeeReport['total_work_hours'], 2) }}</div>
                                    <div class="text-sm text-indigo-600">Total Jam Kerja</div>
                                </div>
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <div class="text-2xl font-bold text-green-700">{{ $employeeReport['days_present'] }}</div>
                                    <div class="text-sm text-green-600">Hari Hadir</div>
                                </div>
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <div class="text-2xl font-bold text-red-700">{{ $employeeReport['days_absent'] }}</div>
                                    <div class="text-sm text-red-600">Hari Absen</div>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-700">{{ $employeeReport['days_off'] }}</div>
                                    <div class="text-sm text-blue-600">Hari Libur</div>
                                </div>
                            </div>

                            <h5 class="font-semibold text-md text-gray-700 mb-2">Detail Harian:</h5>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lembur</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($employeeReport['daily_records'] as $record)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record['date']->format('d M') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                                    @php
                                                        $statusClass = '';
                                                        switch ($record['status']) {
                                                            case 'Hadir': $statusClass = 'text-green-600'; break;
                                                            case 'Libur': $statusClass = 'text-blue-600'; break;
                                                            case 'Libur Nasional': $statusClass = 'text-purple-600'; break;
                                                            case 'Absen': $statusClass = 'text-red-600'; break;
                                                            case 'Absen (Manual)': $statusClass = 'text-red-600 font-bold'; break;
                                                            case 'Belum Bekerja': $statusClass = 'text-gray-500 italic'; break;
                                                            default: $statusClass = 'text-gray-600'; break;
                                                        }
                                                    @endphp
                                                    <span class="{{ $statusClass }}">{{ $record['status'] }}</span>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record['check_in'] ? $record['check_in']->format('H:i') : '-' }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record['check_out'] ? $record['check_out']->format('H:i') : '-' }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record['work_hours'] ?? '-' }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record['overtime_hours'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600">Tidak ada data absensi untuk bulan yang dipilih.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
