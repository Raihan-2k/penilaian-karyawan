<x-guest-layout>
    {{-- Ini adalah bagian di mana Anda akan menambahkan teks kustom --}}
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Sistem Penilaian Karyawan</h1>
        <p>Silakan masuk dengan akun manager Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

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

        {{-- ... sisa form login di bawah ini ... --}}

        <div class="flex items-center justify-end mt-4">
            {{-- Bagian Remember Me dan Forgot Password bisa tetap di sini, atau dihapus/dikomentari jika tidak diperlukan --}}
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>