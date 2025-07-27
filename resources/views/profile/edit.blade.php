<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- Bagian Update Profile Information --}}
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Informasi Profil') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
                            </p>
                        </header>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Nama')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="nip" :value="__('NIP')" />
                                <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip', $user->nip)" required autocomplete="nip" />
                                <x-input-error class="mt-2" :messages="$errors->get('nip')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div>
                                        <p class="text-sm mt-2 text-gray-800">
                                            {{ __('Alamat email Anda belum diverifikasi.') }}
                                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                                            </button>
                                        </p>
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-green-600">
                                                {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div>
                                <x-input-label for="pendidikan_terakhir" :value="__('Pendidikan Terakhir')" />
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" autocomplete="off">
                                    <option value="">-- Pilih Pendidikan Terakhir --</option>
                                    <option value="SMA Sederajat" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'SMA Sederajat')>SMA Sederajat</option>
                                    <option value="D3" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'D3')>D3</option>
                                    <option value="S1" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'S1')>S1</option>
                                    <option value="S2" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'S2')>S2</option>
                                    <option value="S3" @selected(old('pendidikan_terakhir', $user->pendidikan_terakhir) == 'S3')>S3</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('pendidikan_terakhir')" />
                            </div>

                            <div>
                                <x-input-label for="nomor_telepon" :value="__('Nomor Telepon')" />
                                <x-text-input id="nomor_telepon" name="nomor_telepon" type="text" class="mt-1 block w-full" :value="old('nomor_telepon', $user->nomor_telepon)" autocomplete="tel" />
                                <x-input-error class="mt-2" :messages="$errors->get('nomor_telepon')" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                                <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" :value="old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '')" autocomplete="bday" />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_lahir')" />
                            </div>

                            <div>
                                <x-input-label for="position" :value="__('Posisi')" />
                                <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" required autocomplete="position" />
                                <x-input-error class="mt-2" :messages="$errors->get('position')" />
                            </div>

                            <div>
                                <x-input-label for="hire_date" :value="__('Tanggal Masuk')" />
                                <x-text-input id="hire_date" name="hire_date" type="date" class="mt-1 block w-full" :value="old('hire_date', $user->hire_date ? $user->hire_date->format('Y-m-d') : '')" required autocomplete="hire_date" />
                                <x-input-error class="mt-2" :messages="$errors->get('hire_date')" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" :value="ucfirst($user->role)" disabled />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Disimpan.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

           
           
        </div>
    </div>
</x-app-layout>