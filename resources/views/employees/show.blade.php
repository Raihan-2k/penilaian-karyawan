<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Karyawan') }} - {{ $employee->name }}
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">NIP:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->nip }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Pendidikan Terakhir:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->pendidikan_terakhir ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nomor Telepon:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->nomor_telepon ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Lahir:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->tanggal_lahir?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Posisi:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->position }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Role:</p>
                            <p class="text-lg font-medium text-gray-900">{{ ucfirst($employee->role) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Masuk:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->hire_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Dibuat pada:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Diperbarui pada:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $employee->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-start items-center">
                        <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Edit Karyawan
                        </a>
                        <a href="{{ route('employees.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Daftar
                        </a>

                        {{-- HAPUS SELURUH BLOK INI KARENA SUDAH TIDAK RELEVAN SETELAH REFACTOR AUTENTIKASI --}}
                        {{--
                        <div class="ml-8">
                            @if ($employee->loginAccount)
                                <span class="text-sm text-green-600 font-semibold">Akun Absensi Sudah Dibuat</span>
                            @else
                                <form action="{{ route('employees.create-attendance-account', $employee) }}" method="POST" class="inline-block">
                                    @csrf
                                    <x-primary-button class="bg-purple-600 hover:bg-purple-700">
                                        {{ __('Buat Akun Absensi') }}
                                    </x-primary-button>
                                </form>
                            @endif
                        </div>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>