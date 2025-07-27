<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Ganti Password Absensi</h1>
        <p>Anda harus mengganti password default Anda untuk melanjutkan.</p>
    </div>

    {{-- Menampilkan pesan status dari sesi (success/error) --}}
    @if (session('status'))
        <x-auth-session-status class="mb-4" :status="session('status')" />
    @endif
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

    {{-- Menampilkan error validasi umum --}}
    <x-input-error :messages="$errors->get('password')" class="mb-4" />
    <x-input-error :messages="$errors->get('current_password')" class="mb-4" />
    <x-input-error :messages="$errors->get('password_confirmation')" class="mb-4" />


    <form method="POST" action="{{ route('absensi.change-password.store') }}">
        @csrf

        {{-- Input untuk Password Saat Ini --}}
        <div>
            <x-input-label for="current_password" :value="__('Password Saat Ini')" />
            <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        {{-- Input untuk Password Baru --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Input untuk Konfirmasi Password Baru --}}
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
