<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Selamat Datang di Halaman Pengaturan Sistem</h3>
                    <p class="text-gray-700 mb-4">Di sini Anda dapat mengelola berbagai pengaturan aplikasi. Fitur-fitur pengaturan akan ditambahkan di sini.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">Manajemen Pengguna Admin</h4>
                            <p class="text-gray-600">Kelola akun-akun dengan role Admin dan Manager.</p>
                            <a href="{{ route('employees.index') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Kelola Akun
                            </a>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">Pengaturan Umum</h4>
                            <p class="text-gray-600">Konfigurasi jam kerja normal, notifikasi, dll.</p>
                            <a href="#" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Atur Umum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
