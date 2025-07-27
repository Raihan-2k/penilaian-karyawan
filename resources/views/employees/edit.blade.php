<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employees.update', $employee) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nama -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Karyawan')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $employee->user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- NIP -->
                        <div class="mb-4">
                            <x-input-label for="nip" :value="__('NIP')" />
                            <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip', $employee->nip)" required autocomplete="nip" />
                            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $employee->user->email)" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password (Opsional) -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak ingin mengubah)')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Role --</option>
                                {{-- Loop melalui allowedRolesForEdit yang dikirim dari controller --}}
                                @foreach($allowedRolesForEdit as $roleOption)
                                    <option value="{{ $roleOption }}" @selected(old('role', $employee->user->role) == $roleOption)>
                                        {{ ucfirst($roleOption) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Posisi -->
                        <div class="mb-4">
                            <x-input-label for="position" :value="__('Posisi')" />
                            <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position', $employee->position)" required autocomplete="position" />
                            <x-input-error :messages="$errors->get('position')" class="mt-2" />
                        </div>

                        <!-- Shift -->
                        <div class="mb-4">
                            <x-input-label for="shift_id" :value="__('Shift Kerja')" />
                            <select id="shift_id" name="shift_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Shift --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" @selected(old('shift_id', $employee->shift_id) == $shift->id)>
                                        {{ $shift->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('shift_id')" class="mt-2" />
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="mb-4">
                            <x-input-label for="hire_date" :value="__('Tanggal Masuk')" />
                            <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date" :value="old('hire_date', $employee->hire_date?->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div class="mb-4">
                            <x-input-label for="pendidikan_terakhir" :value="__('Pendidikan Terakhir')" />
                            <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SMA Sederajat" @selected(old('pendidikan_terakhir', $employee->pendidikan_terakhir) == 'SMA Sederajat')>SMA Sederajat</option>
                                <option value="D3" @selected(old('pendidikan_terakhir', $employee->pendidikan_terakhir) == 'D3')>D3</option>
                                <option value="S1" @selected(old('pendidikan_terakhir', $employee->pendidikan_terakhir) == 'S1')>S1</option>
                                <option value="S2" @selected(old('pendidikan_terakhir', $employee->pendidikan_terakhir) == 'S2')>S2</option>
                                <option value="S3" @selected(old('pendidikan_terakhir', $employee->pendidikan_terakhir) == 'S3')>S3</option>
                            </select>
                            <x-input-error :messages="$errors->get('pendidikan_terakhir')" class="mt-2" />
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="mb-4">
                            <x-input-label for="nomor_telepon" :value="__('Nomor Telepon')" />
                            <x-text-input id="nomor_telepon" class="block mt-1 w-full" type="text" name="nomor_telepon" :value="old('nomor_telepon', $employee->nomor_telepon)" autocomplete="tel" />
                            <x-input-error :messages="$errors->get('nomor_telepon')" class="mt-2" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="mb-4">
                            <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                            <x-text-input id="tanggal_lahir" class="block mt-1 w-full" type="date" name="tanggal_lahir" :value="old('tanggal_lahir', $employee->tanggal_lahir?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Perbarui Karyawan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
