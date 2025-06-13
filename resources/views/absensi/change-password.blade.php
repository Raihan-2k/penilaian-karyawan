<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Ganti Password Absensi</h1>
        <p>Anda harus mengganti password default Anda untuk melanjutkan.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />
    <x-input-error :messages="$errors->get('password')" class="mb-4" />

    <form method="POST" action="{{ route('absensi.change-password.store') }}">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Ganti Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>