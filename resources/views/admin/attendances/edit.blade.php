<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.attendances.update', $attendance) }}">
                        @csrf
                        @method('PUT') {{-- Menggunakan metode PUT untuk update --}}

                        <h3 class="font-semibold text-lg text-gray-800 mb-4">
                            Absensi untuk {{ $attendance->employee->name }} (NIP: {{ $attendance->employee->nip }}) pada Tanggal {{ $attendance->date->format('d M Y') }}
                        </h3>

                        <div>
                            <x-input-label for="check_in_time" :value="__('Waktu Check-in')" />
                            {{-- value diisi dari $check_in_time_str yang sudah diformat di controller --}}
                            <x-text-input id="check_in_time" class="block mt-1 w-full" type="time" name="check_in_time" :value="old('check_in_time', $check_in_time_str)" />
                            <x-input-error :messages="$errors->get('check_in_time')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="check_out_time" :value="__('Waktu Check-out')" />
                            {{-- value diisi dari $check_out_time_str yang sudah diformat di controller --}}
                            <x-text-input id="check_out_time" class="block mt-1 w-full" type="time" name="check_out_time" :value="old('check_out_time', $check_out_time_str)" />
                            <x-input-error :messages="$errors->get('check_out_time')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Perbarui Absensi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>