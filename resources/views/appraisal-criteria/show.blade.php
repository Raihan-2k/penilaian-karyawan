<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Kriteria Penilaian') }} - {{ $appraisalCriterion->name }}
        </h2>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama Kriteria:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $appraisalCriterion->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Deskripsi:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $appraisalCriterion->description ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Dibuat pada:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $appraisalCriterion->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Terakhir Diperbarui:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $appraisalCriterion->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-start">
                        {{-- Tombol Edit Kriteria --}}
                        <a href="{{ route('appraisal-criteria.edit', $appraisalCriterion) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Edit Kriteria
                        </a>
                        {{-- Tombol Kembali ke Daftar --}}
                        <a href="{{ route('appraisal-criteria.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>