<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kumpulkan Tugas') }} - {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employee-tasks.submit', $task) }}" enctype="multipart/form-data"> {{-- PENTING: enctype untuk upload file --}}
                        @csrf

                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Informasi Tugas</h3>
                        <p class="text-gray-700 mb-2"><strong>Judul:</strong> {{ $task->title }}</p>
                        <p class="text-gray-700 mb-2"><strong>Deskripsi:</strong> {{ $task->description ?? '-' }}</p>
                        <p class="text-gray-700 mb-4"><strong>Deadline:</strong> {{ $task->deadline?->format('d M Y') ?? 'Tidak ada deadline' }}</p>

                        <!-- Input File Tugas -->
                        <div>
                            <x-input-label for="submission_file" :value="__('Pilih File Tugas')" />
                            <input id="submission_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="submission_file" required />
                            <p class="mt-1 text-sm text-gray-500">Format yang diizinkan: PDF, DOC, DOCX, ZIP, RAR, PPT, PPTX, XLS, XLSX. Maks: 20MB.</p>
                            <x-input-error :messages="$errors->get('submission_file')" class="mt-2" />
                        </div>

                        <!-- Komentar Tambahan -->
                        <div class="mt-4">
                            <x-input-label for="comments" :value="__('Komentar (Opsional)')" />
                            <textarea id="comments" name="comments" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('comments') }}</textarea>
                            <x-input-error :messages="$errors->get('comments')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Kumpulkan Tugas') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>