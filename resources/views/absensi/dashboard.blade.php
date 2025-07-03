<x-app-layout>
    <x-slot name="header">
        {{-- Menggunakan Auth::user()->name karena Auth::user() sudah mengembalikan objek Employee --}}
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Absensi') }} - {{ Auth::user()->name }}
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

                    <h3 class="text-xl font-semibold mb-4">Status Absensi Hari Ini ({{ \Carbon\Carbon::today()->format('d M Y') }})</h3>

                    @if ($todayAttendance)
                        <div class="flex items-center gap-4 mb-4">
                            <p class="text-lg">Check-in: <span class="font-bold">{{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i:s') }}</span></p>
                            @if ($todayAttendance->check_out_time)
                                <p class="text-lg">Check-out: <span class="font-bold">{{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i:s') }}</span></p>
                                @if ($todayAttendance->overtime_hours > 0)
                                    <p class="text-lg text-green-600">Lembur: <span class="font-bold">{{ $todayAttendance->overtime_hours }} jam</span></p>
                                @endif
                            @endif
                        </div>

                        @if (!$todayAttendance->check_out_time)
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
                        <p class="text-lg mb-4">Anda belum melakukan check-in hari ini.</p>
                        <form action="{{ route('absensi.checkin') }}" method="POST">
                            @csrf
                            <x-primary-button>
                                {{ __('Check-in') }}
                            </x-primary-button>
                        </form>
                    @endif

                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4">Logout</h3>
                        {{-- Menggunakan route logout umum --}}
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