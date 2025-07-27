<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Selamat Datang') }} - {{ $loggedInUser->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Pesan Sukses/Error dari Sesi --}}
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

                    {{-- Informasi Karyawan (hanya tampil jika $employee ada) --}}
                    @if ($employee)
                        <h3 class="text-xl font-semibold mb-2">Informasi Karyawan Anda:</h3>
                        <p class="text-lg">NIP: <span class="font-bold">{{ $employee->nip }}</span></p>
                        <p class="text-lg">Jabatan: <span class="font-bold">{{ $employee->position }}</span></p>
                        <p class="text-lg mb-4">Shift: <span class="font-bold">{{ $employee->shift->name ?? 'Belum Ditetapkan' }}</span></p>
                    @else
                        {{-- Pesan ini hanya tampil jika user seharusnya punya employee tapi tidak ada --}}
                        @if (in_array($loggedInUser->role, ['karyawan', 'administrator']))
                            <p class="text-lg mb-4 text-red-600">Profil karyawan Anda tidak ditemukan. Harap hubungi administrator.</p>
                        @else
                            {{-- Pesan untuk Owner/Admin/Manager yang tidak diharapkan memiliki employee di sini --}}
                            <p class="text-lg mb-4 text-gray-600">Anda tidak memiliki profil karyawan yang terkait.</p>
                        @endif
                    @endif

                    <h3 class="text-xl font-semibold mb-4">Status Absensi Hari Ini ({{ \Carbon\Carbon::today()->format('d M Y') }})</h3>

                    {{-- Informasi Hari Kerja/Libur Berdasarkan Shift (hanya tampil jika $employee ada) --}}
                    @if ($employee && $employee->shift)
                        @if ($isWorkingDayToday)
                            <p class="text-green-600 font-semibold mb-2">Hari ini adalah hari kerja Anda.</p>
                        @else
                            <p class="text-blue-600 font-semibold mb-2">Hari ini adalah hari libur Anda.</p>
                        @endif
                    @else
                        {{-- Pesan jika shift belum ditetapkan atau employee tidak ada --}}
                        <p class="text-yellow-600 font-semibold mb-2">Informasi shift belum ditetapkan atau tidak relevan.</p>
                    @endif

                    @if ($attendanceToday)
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-4">
                            <p class="text-lg">Check-in: <span class="font-bold">{{ \Carbon\Carbon::parse($attendanceToday->check_in_time)->format('H:i:s') }}</span></p>
                            @if ($attendanceToday->check_out_time)
                                <p class="text-lg">Check-out: <span class="font-bold">{{ \Carbon\Carbon::parse($attendanceToday->check_out_time)->format('H:i:s') }}</span></p>
                                @if ($attendanceToday->overtime_hours > 0)
                                    <p class="text-lg text-green-600">Lembur: <span class="font-bold">{{ $attendanceToday->overtime_hours }} jam</span></p>
                                @endif
                            @endif
                        </div>

                        @if (!$attendanceToday->check_out_time)
                            <form action="{{ route('absensi.checkout') }}" method="POST">
                                @csrf
                                <x-primary-button class="bg-red-600 hover:bg-red-700">
                                    {{ __('Check-out') }}
                                </x-primary-button>
                            </form>
                        @else
                            <p class="text-gray-600">Anda sudah check-out hari ini.</p>
                        @endif
                    @else
                        {{-- Tombol Check-in hanya muncul jika hari ini adalah hari kerja DAN employee ada --}}
                        @if ($employee && $isWorkingDayToday)
                            <p class="text-lg mb-4">Anda belum melakukan check-in hari ini.</p>
                            <form action="{{ route('absensi.checkin') }}" method="POST">
                                @csrf
                                <x-primary-button>
                                    {{ __('Check-in') }}
                                </x-primary-button>
                            </form>
                        @else
                            {{-- Pesan jika tidak perlu check-in (hari libur shift atau employee tidak ada) --}}
                            <p class="text-gray-600">Tidak perlu check-in hari ini (hari libur atau shift tidak ditetapkan).</p>
                        @endif
                    @endif

                    {{-- Riwayat Absensi Terbaru --}}
                    @if ($recentAttendances->isNotEmpty())
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold mb-4">Riwayat Absensi Terbaru (7 Hari Terakhir)</h3>
                            <ul class="space-y-2">
                                @foreach ($recentAttendances as $attendance)
                                    <li class="p-3 bg-gray-50 rounded-lg shadow-sm flex justify-between items-center">
                                        <div>
                                            <span class="font-semibold">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y') }}</span>:
                                            Check-in <span class="font-medium">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}</span>
                                            @if ($attendance->check_out_time)
                                                - Check-out <span class="font-medium">{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}</span>
                                            @else
                                                - <span class="text-yellow-600">Belum Check-out</span>
                                            @endif
                                            @if ($attendance->status)
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ['present' => 'bg-green-100 text-green-800', 'absent' => 'bg-red-100 text-red-800', 'Libur' => 'bg-blue-100 text-blue-800', 'Libur Nasional' => 'bg-purple-100 text-purple-800', 'Absen (Manual)' => 'bg-red-100 text-red-800'][$attendance->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($attendance->overtime_hours > 0)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                Lembur: {{ $attendance->overtime_hours }} jam
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="mt-8">
                            <p class="text-gray-600">Belum ada riwayat absensi dalam 7 hari terakhir.</p>
                        </div>
                    @endif

                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4">Logout</h3>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <x-primary-button class="bg-red-500 hover:bg-red-600">
                                {{ __('Logout Absensi') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
