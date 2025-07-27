<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Bukti Penjualan') }} - {{ $salesProof->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('sales-proofs.update', $salesProof) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Judul Bukti Penjualan -->
                        <div>
                            <x-input-label for="title" :value="__('Judul Bukti Penjualan')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $salesProof->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Deskripsi / Catatan -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Deskripsi / Catatan (Opsional)')" />
                            <textarea id="description" name="description" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('description', $salesProof->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Input File Bukti Penjualan (Opsional saat edit) -->
                        <div class="mt-4">
                            <x-input-label for="proof_file" :value="__('Pilih File Bukti Penjualan (Biarkan kosong jika tidak ingin mengubah)')" />
                            <input id="proof_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" type="file" name="proof_file" />
                            <p class="mt-1 text-sm text-gray-500">File saat ini: <a href="{{ asset('storage/' . $salesProof->file_path) }}" target="_blank" class="underline">{{ basename($salesProof->file_path) }}</a></p>
                            <x-input-error :messages="$errors->get('proof_file')" class="mt-2" />
                        </div>
                        

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Perbarui Bukti') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
