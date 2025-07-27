<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Penilaian Kinerja Baru') }}
        </h2>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 md:p-8">
                    <form method="POST" action="{{ route('appraisals.store') }}">
                        @csrf

                        {{-- Bagian Informasi Penilaian Utama --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Informasi Penilaian</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Pilih karyawan yang akan dinilai dan tentukan tanggal penilaian.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Karyawan yang Dinilai -->
                            <div>
                                <x-input-label for="employee_id" :value="__('Karyawan yang Dinilai')" />
                                <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                            {{ $employee->user->name }} (NIP: {{ $employee->nip }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <!-- Tanggal Penilaian -->
                            <div>
                                <x-input-label for="appraisal_date" :value="__('Tanggal Penilaian')" />
                                <x-text-input id="appraisal_date" class="block mt-1 w-full" type="date" name="appraisal_date" :value="old('appraisal_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('appraisal_date')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Bagian Penilaian per Kriteria --}}
                        <div class="mt-10">
                             <div class="border-b border-gray-200 dark:border-gray-700 py-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Penilaian Kriteria</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Berikan penilaian untuk setiap kriteria di bawah ini.</p>
                            </div>
                            
                            <div class="space-y-6">
                                @forelse ($criteria as $index => $criterion)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ score: '{{ old('scores.'.$index.'.score', '') }}' }">
                                        <h4 class="font-semibold text-md text-gray-800 dark:text-white">{{ $loop->iteration }}. {{ $criterion->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $criterion->description }}</p>

                                        <input type="hidden" name="scores[{{ $index }}][criterion_id]" value="{{ $criterion->id }}">

                                        {{-- Tombol Visual untuk Skor --}}
                                        <div class="mt-2">
                                            <x-input-label :value="__('Skor')" />
                                            <div class="flex items-center gap-4 mt-1">
                                                <button type="button" @click="score = '1'"
                                                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 border rounded-md font-semibold text-sm transition"
                                                        :class="{ 'bg-green-600 text-white border-green-600 shadow-lg': score == '1', 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-green-50 dark:hover:bg-green-900/20': score != '1' }">
                                                    <x-heroicon-o-hand-thumb-up class="w-5 h-5"/>
                                                    <span>Baik</span>
                                                </button>
                                                <button type="button" @click="score = '-1'"
                                                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 border rounded-md font-semibold text-sm transition"
                                                        :class="{ 'bg-red-600 text-white border-red-600 shadow-lg': score == '-1', 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-red-50 dark:hover:bg-red-900/20': score != '-1' }">
                                                    <x-heroicon-o-hand-thumb-down class="w-5 h-5"/>
                                                    <span>Tidak Baik</span>
                                                </button>
                                            </div>
                                            {{-- Select tersembunyi yang dikontrol oleh tombol di atas --}}
                                            <select x-model="score" name="scores[{{ $index }}][score]" class="hidden" required>
                                                <option value="">-- Pilih Skor --</option>
                                                <option value="1">Baik (+1)</option>
                                                <option value="-1">Tidak Baik (-1)</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('scores.'.$index.'.score')" class="mt-2" />
                                        </div>

                                        {{-- Komentar Kriteria --}}
                                        <div class="mt-4">
                                            <x-input-label for="comments-{{ $criterion->id }}" :value="__('Komentar (Opsional)')" />
                                            <textarea id="comments-{{ $criterion->id }}" name="scores[{{ $index }}][comments]" rows="3" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('scores.'.$index.'.comments') }}</textarea>
                                            <x-input-error :messages="$errors->get('scores.'.$index.'.comments')" class="mt-2" />
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                                        <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400"/>
                                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Tidak Ada Kriteria</h3>
                                        <p class="mt-1 text-sm text-gray-500">Belum ada kriteria penilaian yang tersedia.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Umpan Balik Keseluruhan -->
                        <div class="mt-10">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Ringkasan</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Berikan umpan balik atau ringkasan keseluruhan dari penilaian ini.</p>
                            </div>
                            <x-input-label for="overall_feedback" :value="__('Umpan Balik Keseluruhan (Opsional)')" />
                            <textarea id="overall_feedback" name="overall_feedback" rows="5" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('overall_feedback') }}</textarea>
                            <x-input-error :messages="$errors->get('overall_feedback')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 gap-2 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('appraisals.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:underline mr-6">Batal</a>
                            <x-primary-button :disabled="$criteria->isEmpty()">
                                {{ __('Simpan Penilaian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>