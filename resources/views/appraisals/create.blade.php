<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Penilaian Kinerja Baru') }}
        </h2>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form Penilaian --}}
                    <form method="POST" action="{{ route('appraisals.store') }}">
                        @csrf

                        {{-- Bagian Informasi Penilaian Utama --}}
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Informasi Penilaian</h3>

                        <div class="mb-4">
                            <x-input-label for="employee_id" :value="__('Karyawan yang Dinilai')" />
                            <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                        {{ $employee->name }} ({{ $employee->nip }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                        </div>

                        <div class="mt-4 mb-4">
                            <x-input-label for="appraisal_date" :value="__('Tanggal Penilaian')" />
                            <x-text-input id="appraisal_date" class="block mt-1 w-full" type="date" name="appraisal_date" :value="old('appraisal_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('appraisal_date')" class="mt-2" />
                        </div>

                        <div class="mt-4 mb-4">
                            <x-input-label for="overall_feedback" :value="__('Umpan Balik Keseluruhan (Opsional)')" />
                            <textarea id="overall_feedback" name="overall_feedback" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('overall_feedback') }}</textarea>
                            <x-input-error :messages="$errors->get('overall_feedback')" class="mt-2" />
                        </div>

                        {{-- Bagian Penilaian per Kriteria --}}
                        <h3 class="font-semibold text-lg text-gray-800 mt-8 mb-4">Penilaian per Kriteria</h3>

                        @forelse ($criteria as $index => $criterion)
                            <div class="border p-4 rounded-lg mb-4 bg-gray-50">
                                <h4 class="font-medium text-md text-gray-700">{{ $loop->iteration }}. {{ $criterion->name }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $criterion->description }}</p>

                                <input type="hidden" name="scores[{{ $index }}][criterion_id]" value="{{ $criterion->id }}">

                                <div>
                                    <x-input-label for="score-{{ $criterion->id }}" :value="__('Skor (1-5)')" />
                                    <select id="score-{{ $criterion->id }}" name="scores[{{ $index }}][score]" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Skor --</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" @selected(old('scores.'.$index.'.score') == $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('scores.'.$index.'.score')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="comments-{{ $criterion->id }}" :value="__('Komentar (Opsional)')" />
                                    <textarea id="comments-{{ $criterion->id }}" name="scores[{{ $index }}][comments]" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('scores.'.$index.'.comments') }}</textarea>
                                    <x-input-error :messages="$errors->get('scores.'.$index.'.comments')" class="mt-2" />
                                </div>
                            </div>
                        @empty
                            {{-- Pesan jika tidak ada kriteria penilaian --}}
                            <p class="text-red-500">Belum ada kriteria penilaian yang tersedia. Harap tambahkan kriteria terlebih dahulu melalui menu "Kriteria Penilaian".</p>
                        @endforelse

                        {{-- Tombol Submit --}}
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4" :disabled="$criteria->isEmpty()">
                                {{ __('Simpan Penilaian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
