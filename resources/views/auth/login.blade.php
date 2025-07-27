<x-guest-layout>
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        {{-- Logo Perusahaan --}}
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        {{-- Kartu Login Utama --}}
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
            
            {{-- Header Kartu --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Selamat Datang Kembali
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Masuk untuk mengakses dashboard Anda.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="space-y-6">
                    {{-- Input NIP --}}
                    <div>
                        <x-input-label for="nip" :value="__('NIP')" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center ps-3">
                                <x-heroicon-o-user class="w-5 h-5 text-gray-400 dark:text-gray-600" />
                            </div>
                            <x-text-input id="nip" class="block w-full ps-10" type="text" name="nip" :value="old('nip')" required autofocus autocomplete="username" placeholder="NIP" />
                        </div>
                        <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                    </div>

                    {{-- Input Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center ps-3">
                                <x-heroicon-o-lock-closed class="w-5 h-5 text-gray-400 dark:text-gray-600" />
                            </div>
                            <x-text-input id="password" class="block w-full ps-10"
                                          type="password"
                                          name="password"
                                          required
                                          placeholder="••••••••"
                                          autocomplete="current-password" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>
                
                {{-- Tombol Login --}}
                <div class="mt-8">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        Log In
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
