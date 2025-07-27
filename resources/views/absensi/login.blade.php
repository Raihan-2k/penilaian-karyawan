<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Sistem Absensi Karyawan</h1>
        <p>Silakan masuk menggunakan NIP dan password anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('absensi.login') }}">
        @csrf

        <div>
            <x-input-label for="nip" :value="__('NIP')" />
            <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip')" required autofocus />
            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- Bisa tambahkan link lupa password untuk absensi jika mau --}}
            <x-primary-button class="ms-3">
                {{ __('Login Absensi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>