<x-guest-layout>
    {{-- Bagian untuk Menampilkan Judul Aplikasi dan Pesan Selamat Datang --}}
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Sistem Penilaian Karyawan</h1>
        <p>Silakan masuk dengan akun manager Anda.</p>
    </div>

    {{-- Bagian untuk Menampilkan Status Sesi (misal: "Anda telah berhasil keluar.") --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Formulir Login --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

        {{-- Bagian Tombol Login dan Link Lupa Password (jika diaktifkan) --}}
        <div class="flex items-center justify-end mt-4">
            {{-- Link "Forgot your password?" --}}
            {{-- Bagian ini bisa dihapus atau dikomentari jika Anda tidak ingin ada fitur lupa password --}}
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            {{-- Tombol Login --}}
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>