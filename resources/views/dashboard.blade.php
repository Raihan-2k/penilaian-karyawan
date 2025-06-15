<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    {{-- Bagian Selamat Datang --}}
                    <div class="text-2xl font-bold text-gray-900 mb-6">
                        Selamat Datang, {{ Auth::user()->name }}!
                    </div>
                    <p class="text-gray-700 text-lg">
                        Ringkasan kinerja dan absensi karyawan Anda.
                    </p>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6 lg:p-8">
                    {{-- Kartu Statistik --}}

                    {{-- Kartu 1: Total Karyawan --}}
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                        <div>
                            <div class="text-indigo-600 text-3xl font-bold">{{ $totalEmployees }}</div>
                            <div class="text-gray-600 text-sm mt-1">Total Karyawan</div>
                        </div>
                        <div class="text-indigo-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-5m-5 0h5m-5 0a2 2 0 112-2v2m-2-2a2 2 0 00-2 2v2m2-2h2.5M10 20v-2a2 2 0 10-4 0v2m4 0H7m0 0a2 2 0 012-2h1m-1 2a2 2 0 002 2h1m-1-2v2a2 2 0 002 2h1m-1-2h2.5M17 20v-2a2 2 0 10-4 0v2"></path></svg>
                        </div>
                    </div>

                    {{-- Kartu 2: Penilaian Selesai Bulan Ini --}}
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                        <div>
                            <div class="text-green-600 text-3xl font-bold">{{ $appraisalsThisMonth }}</div>
                            <div class="text-gray-600 text-sm mt-1">Penilaian Selesai Bulan Ini</div>
                        </div>
                        <div class="text-green-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    {{-- Kartu 3: Total Jam Lembur Bulan Ini --}}
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                        <div>
                            <div class="text-red-600 text-3xl font-bold">{{ $overtimeThisMonth }}</div>
                            <div class="text-gray-600 text-sm mt-1">Total Jam Lembur Bulan Ini</div>
                        </div>
                        <div class="text-red-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    {{-- Kartu 4: Karyawan Belum Check-in Hari Ini --}}
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between">
                        <div>
                            <div class="text-yellow-600 text-3xl font-bold">{{ $employeesNotCheckedInToday }}</div>
                            <div class="text-gray-600 text-sm mt-1">Karyawan Belum Check-in Hari Ini</div>
                        </div>
                        <div class="text-yellow-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- Bagian Aktivitas Terbaru & Aksi Cepat --}}
                <div class="bg-white grid grid-cols-1 md:grid-cols-2 gap-6 p-6 lg:p-8 border-t border-gray-200">
                    {{-- Kolom Aktivitas Terbaru --}}
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                        <ul class="space-y-3 text-gray-700">
                            @forelse ($recentAppraisals as $appraisal)
                                <li class="p-3 bg-gray-50 rounded-lg shadow-sm">
                                    <span class="font-semibold">{{ $appraisal->employee->name }}</span> - Penilaian kinerja selesai oleh <span class="font-semibold">{{ $appraisal->appraiser->name }}</span> ({{ $appraisal->appraisal_date->format('d M Y') }})
                                    <a href="{{ route('appraisals.show', $appraisal) }}" class="text-indigo-600 hover:underline ml-2 text-sm">Lihat</a>
                                </li>
                            @empty
                                <li class="p-3 bg-gray-50 rounded-lg shadow-sm text-center text-gray-500">
                                    Belum ada penilaian terbaru.
                                </li>
                            @endforelse

                            @forelse ($recentAttendances as $attendance)
                                <li class="p-3 bg-gray-50 rounded-lg shadow-sm">
                                    <span class="font-semibold">{{ $attendance->employee->name }}</span> -
                                    @if($attendance->check_out_time)
                                        Check-out ({{ \Carbon\Carbon::parse($attendance->check_out_time)->format('d M Y, H:i') }})
                                        @if($attendance->overtime_hours > 0)
                                            <span class="text-green-600 font-semibold">(Lembur: {{ $attendance->overtime_hours }} jam)</span>
                                        @endif
                                    @else
                                        Check-in ({{ \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y, H:i') }})
                                    @endif
                                </li>
                            @empty
                                {{-- Hanya tampilkan ini jika kedua recentAppraisals DAN recentAttendances kosong --}}
                                @if($recentAppraisals->isEmpty())
                                    <li class="p-3 bg-gray-50 rounded-lg shadow-sm text-center text-gray-500">
                                        Belum ada aktivitas absensi terbaru.
                                    </li>
                                @endif
                            @endforelse
                        </ul>
                    </div>

                    {{-- Kolom Aksi Cepat --}}
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Aksi Cepat</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('employees.create') }}" class="block px-6 py-4 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 text-center font-semibold transition ease-in-out duration-150">
                                Tambah Karyawan Baru
                            </a>
                            <a href="{{ route('appraisals.create') }}" class="block px-6 py-4 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 text-center font-semibold transition ease-in-out duration-150">
                                Buat Penilaian Baru
                            </a>
                            <a href="{{ route('admin.attendances.index') }}" class="block px-6 py-4 bg-yellow-600 text-white rounded-lg shadow-md hover:bg-yellow-700 text-center font-semibold transition ease-in-out duration-150">
                                Lihat Laporan Absensi
                            </a>
                            <a href="{{ route('employees.index') }}" class="block px-6 py-4 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 text-center font-semibold transition ease-in-out duration-150">
                                Kelola Karyawan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>