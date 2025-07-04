<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Informasi Tugas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-sm text-gray-500">Judul Tugas:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $task->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Diberikan Oleh:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $task->assignedBy->name }} ({{ $task->assignedBy->nip }})</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Untuk Karyawan:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $task->assignedTo->name }} ({{ $task->assignedTo->nip }})</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Deadline:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $task->deadline?->format('d M Y') ?? 'Tidak ada deadline' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Tugas:</p>
                            <p class="text-lg font-medium text-gray-900">
                                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ ['pending' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'submitted' => 'bg-purple-100 text-purple-800'][$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Deskripsi Tugas</h3>
                    <p class="mb-8 text-gray-700">{{ $task->description ?? 'Tidak ada deskripsi.' }}</p>

                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Submission Anda</h3>
                    @if ($task->submissions->isNotEmpty())
                        @foreach ($task->submissions as $submission)
                            <div class="border p-4 rounded-lg mb-4 bg-gray-50">
                                <p class="text-sm text-gray-600">Dikumpulkan Pada: {{ $submission->submission_date->format('d M Y H:i') }}</p>
                                <p class="text-md font-semibold text-indigo-700">
                                    File: <a href="{{ asset('storage/' . $submission->submission_file_path) }}" target="_blank" class="underline hover:text-indigo-900">Lihat File</a>
                                </p>
                                <p class="text-sm text-gray-700 mt-2">Komentar: {{ $submission->comments ?? 'Tidak ada komentar.' }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">Anda belum mengumpulkan tugas ini.</p>
                    @endif

                    <div class="mt-6 flex justify-start">
                        <a href="{{ route('employee-tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Daftar Tugas
                        </a>
                        @if($task->status !== 'completed' && $task->status !== 'submitted')
                            <a href="{{ route('employee-tasks.submit_form', $task) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Kumpulkan Tugas
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>