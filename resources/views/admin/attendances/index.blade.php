<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Karyawan') }}
        </h2>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Pesan Sukses/Error (dari session flash data) --}}
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
                    @if (session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('info') }}
                        </div>
                    @endif

                    {{-- Bagian Filter --}}
                    <form action="{{ route('admin.attendances.index') }}" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            {{-- Filter Tanggal --}}
                            <div>
                                <x-input-label for="date" :value="__('Filter Tanggal')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="$selectedDate->format('Y-m-d')" />
                            </div>
                            {{-- Filter Karyawan --}}
                            <div>
                                <x-input-label for="employee_id" :value="__('Filter Karyawan')" />
                                <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach($allEmployees as $emp)
                                        <option value="{{ $emp->id }}" @selected($emp->id == $employeeIdFilter)>
                                            {{ $emp->name }} ({{ $emp->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Tombol Terapkan Filter --}}
                            <div>
                                <x-primary-button class="ms-4">
                                    {{ __('Terapkan Filter') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    {{-- Tabel Laporan Absensi --}}
                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam Kerja</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($attendanceData as $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['date']->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['employee']->nip }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $data['employee']->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @php
                                                $statusClass = '';
                                                switch ($data['status']) {
                                                    case 'Hadir': $statusClass = 'text-green-600'; break;
                                                    case 'Hadir (Otomatis)': $statusClass = 'text-yellow-600'; break;
                                                    case 'Absen': $statusClass = 'text-red-600'; break;
                                                    case 'Libur Akhir Pekan': $statusClass = 'text-blue-600'; break;
                                                    case 'Libur Nasional': $statusClass = 'text-purple-600'; break;
                                                    case 'Absen (Manual)': $statusClass = 'text-red-600 font-bold'; break; // Status setelah di-override jadi absen
                                                    default: $statusClass = 'text-gray-600'; break;
                                                }
                                            @endphp
                                            <span class="{{ $statusClass }} font-semibold">{{ $data['status'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($data['check_in_time'])
                                                {{ $data['check_in_time']->format('H:i:s') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($data['check_out_time'])
                                                {{ $data['check_out_time']->format('H:i:s') }}
                                            @else
                                                @if ($data['status'] == 'Hadir')
                                                    <span class="text-red-500">Belum Check-out</span>
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if (is_numeric($data['total_work_hours']))
                                                {{ $data['total_work_hours'] }} jam
                                            @else
                                                -
                                            @endif
                                        </td>
                                      
                                        {{-- Kolom Aksi --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            {{-- Tombol Edit Terpadu untuk semua status yang bisa diubah/diisi --}}
                                            @if ($data['status'] == 'Hadir' || $data['status'] == 'Hadir (Otomatis)' || $data['status'] == 'Absen' || $data['status'] == 'Absen (Manual)')
                                                <a href="{{ route('admin.attendances.edit_or_create', ['employee' => $data['employee']->id, 'date' => $data['date']->format('Y-m-d')]) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Edit
                                                </a>
                                            @else
                                                - {{-- Untuk Libur Akhir Pekan/Nasional --}}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data absensi untuk tanggal yang dipilih atau karyawan yang difilter.</td>
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