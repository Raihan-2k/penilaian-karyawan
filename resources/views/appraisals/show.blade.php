<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Penilaian Kinerja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Informasi Utama</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-sm text-gray-500">Karyawan:</p>
                            {{-- Perbaikan di sini: akses nama dari $appraisal->employee?->user?->name --}}
                            <p class="text-lg font-medium text-gray-900">
                                {{ $appraisal->employee?->user?->name }} (NIP: {{ $appraisal->employee?->nip }})
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Penilai:</p>
                            {{-- Perbaikan di sini: akses nama dari $appraisal->appraiser?->user?->name --}}
                            <p class="text-lg font-medium text-gray-900">
                                {{ $appraisal->appraiser?->user?->name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Penilaian:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $appraisal->appraisal_date->format('d M Y') }}</p>
                        </div>
                        @if ($appraisal->overall_score !== null) {{-- Menggunakan !== null untuk membedakan 0 dari null --}}
                        <div>
                            <p class="text-sm text-gray-500">Skor Keseluruhan:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $appraisal->overall_score }}</p>
                        </div>
                        @endif
                    </div>

                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Umpan Balik Keseluruhan</h3>
                    <p class="mb-8 text-gray-700">{{ $appraisal->overall_feedback ?? 'Tidak ada umpan balik keseluruhan.' }}</p>

                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Detail Penilaian per Kriteria</h3>
                    @forelse ($appraisal->criterionScores as $score)
                        <div class="border p-4 rounded-lg mb-4 bg-gray-50">
                            <h4 class="font-medium text-md text-gray-700">{{ $loop->iteration }}. {{ $score->criterion?->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $score->criterion?->description }}</p>
                            {{-- Perbaikan di sini: Skor 1 atau -1, bukan / 5 --}}
                            <p class="text-md font-semibold text-indigo-700">Skor: {{ $score->score }}</p>
                            <p class="text-sm text-gray-700 mt-2">Komentar: {{ $score->comments ?? 'Tidak ada komentar.' }}</p>
                        </div>
                    @empty
                        <p class="text-red-500">Tidak ada skor kriteria ditemukan untuk penilaian ini.</p>
                    @endforelse

                    <div class="mt-6 flex justify-start">
                        <a href="{{ route('appraisals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Daftar Penilaian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
