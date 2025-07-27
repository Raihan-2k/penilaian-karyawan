<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Penilaian Kinerja') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Kontainer Utama --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    {{-- Header dengan Tombol Aksi --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Penilaian</h3>
                        <a href="{{ route('appraisals.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            <span>Buat Penilaian Baru</span>
                        </a>
                    </div>

                    {{-- Notifikasi --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Tabel Penilaian --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Karyawan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Penilai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Skor Akhir</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($appraisals as $appraisal)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        {{-- Info Karyawan --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($appraisal->employee?->user?->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appraisal->employee?->user?->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">NIP: {{ $appraisal->employee?->nip }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Info Penilai --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($appraisal->appraiser?->user?->name) }}&color=059669&background=D1FAE5" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appraisal->appraiser?->user?->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appraisal->appraiser?->position }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Tanggal Penilaian --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $appraisal->appraisal_date->format('d M Y') }}
                                        </td>
                                        {{-- Skor Akhir (dengan asumsi ada field 'overall_score') --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $score = $appraisal->overall_score ?? 0;
                                                $scoreColor = 'bg-gray-100 text-gray-800'; // Default
                                                if ($score >= 85) $scoreColor = 'bg-green-100 text-green-800';
                                                else if ($score >= 70) $scoreColor = 'bg-yellow-100 text-yellow-800';
                                                else if ($score > 0) $scoreColor = 'bg-red-100 text-red-800';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full {{ $scoreColor }}">
                                                {{ $score }}
                                            </span>
                                        </td>
                                        {{-- Tombol Aksi --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('appraisals.show', $appraisal) }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                <span>Lihat Detail</span>
                                                <x-heroicon-o-arrow-right class="w-4 h-4"/>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-clipboard-document-list class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-2"/>
                                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Belum Ada Penilaian</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Silakan buat penilaian kinerja baru.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>