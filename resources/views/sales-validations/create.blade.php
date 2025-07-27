<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validasi Bukti Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Perbaikan: Form action hanya ke route 'store' tanpa parameter model di URL --}}
                    {{-- sales_proof_id akan dikirim melalui hidden input --}}
                    <form method="POST" action="{{ route('sales-validations.store') }}">
                        @csrf

                        {{-- Hidden input untuk mengirim sales_proof_id ke controller --}}
                        <input type="hidden" name="sales_proof_id" value="{{ $salesProof->id }}">

                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Detail Bukti Penjualan</h3>
                        <p class="text-gray-700 mb-2"><strong>Judul:</strong> {{ $salesProof->title }}</p>
                        {{-- Perbaikan: akses nama dari $salesProof->uploadedBy->user->name --}}
                        <p class="text-gray-700 mb-2"><strong>Diunggah Oleh:</strong> {{ $salesProof->uploadedBy?->user?->name }}</p>
                        <p class="text-gray-700 mb-4"><strong>Tanggal Unggah:</strong> {{ $salesProof->created_at->format('d M Y H:i') }}</p>
                        @if ($salesProof->file_path)
                            <p class="text-md font-semibold text-indigo-700 mb-4">
                                File: <a href="{{ asset('storage/' . $salesProof->file_path) }}" target="_blank" class="underline hover:text-indigo-900">Lihat File Bukti</a>
                            </p>
                        @endif

                        <!-- Status Validasi -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status Validasi')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="validated" @selected(old('status') == 'validated')>Validasi</option>
                                <option value="rejected" @selected(old('status') == 'rejected')>Tolak</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Komentar Manager -->
                        <div class="mt-4">
                            {{-- Perbaikan: name 'comments' diubah menjadi 'notes' agar sesuai dengan controller --}}
                            <x-input-label for="notes" :value="__('Komentar (Opsional)')" />
                            <textarea id="notes" name="notes" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Simpan Validasi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
