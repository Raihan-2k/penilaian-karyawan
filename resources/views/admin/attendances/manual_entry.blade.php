<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($attendance->id) ? __('Edit Absensi Karyawan') : __('Isi Absensi Manual') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form action akan berbeda tergantung apakah ini mode edit atau create --}}
                    {{-- Menggunakan Alpine.js untuk mengelola status dan tampilan input waktu --}}
                    <form method="POST" action="{{ isset($attendance->id) ? route('admin.attendances.update', $attendance) : route('admin.attendances.store_manual') }}" x-data="{ selectedStatus: '{{ old('status_manual', $initialStatus) }}' }">
                        @csrf
                        {{-- Jika mode edit, gunakan metode PUT --}}
                        @if (isset($attendance->id))
                            @method('PUT')
                        @endif

                        <h3 class="font-semibold text-lg text-gray-800 mb-4">
                            Absensi untuk {{ $employee->name }} (NIP: {{ $employee->nip }}) pada Tanggal {{ $date->format('d M Y') }}
                        </h3>

                        {{-- Hidden inputs untuk employee_id dan date, penting untuk storeManual --}}
                        @if (!isset($attendance->id))
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                        @endif

                        <div>
                            <x-input-label for="status_manual" :value="__('Status Absensi')" />
                            <select id="status_manual" name="status_manual" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-model="selectedStatus" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Hadir" @selected(old('status_manual', $initialStatus) == 'Hadir')>Hadir</option>
                                <option value="Absen" @selected(old('status_manual', $initialStatus) == 'Absen')>Absen</option>
                                @if (true) {{-- Ubah ini jika ingin mengaktifkan/menonaktifkan opsi libur nasional --}}
                                    <option value="Libur Akhir Pekan" @selected(old('status_manual', $initialStatus) == 'Libur Akhir Pekan')>Libur Akhir Pekan</option>
                                    {{-- Hapus/komentari baris ini jika fitur hari libur nasional tidak aktif --}}
                                    {{-- <option value="Libur Nasional" @selected(old('status_manual', $initialStatus) == 'Libur Nasional')>Libur Nasional</option> --}}
                                @endif
                            </select>
                            <x-input-error :messages="$errors->get('status_manual')" class="mt-2" />
                        </div>

                        <div class="mt-4" x-show="selectedStatus === 'Hadir'">
                            <x-input-label for="check_in_time" :value="__('Waktu Check-in')" />
                            <x-text-input id="check_in_time" class="block mt-1 w-full" type="time" name="check_in_time" :value="old('check_in_time', $check_in_time_str)" x-bind:required="selectedStatus === 'Hadir'" />
                            <x-input-error :messages="$errors->get('check_in_time')" class="mt-2" />
                        </div>

                        <div class="mt-4" x-show="selectedStatus === 'Hadir'">
                            <x-input-label for="check_out_time" :value="__('Waktu Check-out')" />
                            <x-text-input id="check_out_time" class="block mt-1 w-full" type="time" name="check_out_time" :value="old('check_out_time', $check_out_time_str)" x-bind:required="selectedStatus === 'Hadir'" />
                            <x-input-error :messages="$errors->get('check_out_time')" class="mt-2" /> {{-- KOREKSI: check_out_out jadi check_out_time --}}
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ isset($attendance->id) ? __('Perbarui Absensi') : __('Simpan Absensi Manual') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>