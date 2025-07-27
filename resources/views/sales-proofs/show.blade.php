<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Bukti Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Informasi Bukti Penjualan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-sm text-gray-500">Judul:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $salesProof->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Diunggah Oleh:</p>
                            {{-- Perbaikan di sini: akses nama dari $salesProof->uploadedBy->user->name --}}
                            <p class="text-lg font-medium text-gray-900">
                                {{ $salesProof->uploadedBy?->user?->name }} (NIP: {{ $salesProof->uploadedBy?->nip }})
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Unggah:</p>
                            <p class="text-lg font-medium text-gray-900">{{ $salesProof->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Validasi:</p>
                            <p class="text-lg font-medium text-gray-900">
                                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ ['pending' => 'bg-yellow-100 text-yellow-800', 'validated' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'][$salesProof->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($salesProof->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Deskripsi / Catatan</h3>
                    <p class="mb-8 text-gray-700">{{ $salesProof->description ?? 'Tidak ada deskripsi.' }}</p>

                    <h3 class="font-semibold text-lg text-gray-800 mb-4">File Bukti Penjualan</h3>
                    @if ($salesProof->file_path)
                        <div class="border p-4 rounded-lg mb-4 bg-gray-50">
                            <p class="text-md font-semibold text-indigo-700">
                                <a href="{{ asset('storage/' . $salesProof->file_path) }}" target="_blank" class="underline hover:text-indigo-900">Lihat File Bukti</a>
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada file bukti yang diunggah.</p>
                    @endif

                    <h3 class="font-semibold text-lg text-gray-800 mt-8 mb-4">Riwayat Validasi</h3>
                    @forelse ($salesProof->validations as $validation)
                        <div class="border p-4 rounded-lg mb-4 bg-gray-50">
                            {{-- Perbaikan di sini: akses nama dari $validation->validatedBy->user->name --}}
                            <p class="text-sm text-gray-600">Divalidasi Oleh: {{ $validation->validatedBy?->user?->name }}</p>
                            <p class="text-sm text-gray-600">Status:
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ['validated' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'][$validation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($validation->status) }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600">Tanggal Validasi: {{ $validation->created_at->format('d M Y H:i') }}</p>
                            <p class="text-sm text-gray-700 mt-2">Komentar: {{ $validation->validation_notes ?? 'Tidak ada komentar.' }}</p> {{-- Menggunakan validation_notes --}}
                        </div>
                    @empty
                        <p class="text-gray-500">Belum ada riwayat validasi untuk bukti penjualan ini.</p>
                    @endforelse

                    <div class="mt-6 flex justify-start">
                        <a href="{{ route('sales-proofs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Daftar Bukti
                        </a>
                        @if (Auth::user()->role === 'administrator' && $salesProof->status === 'pending')
                            <a href="{{ route('sales-proofs.edit', $salesProof) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Edit Bukti
                            </a>
                            <form action="{{ route('sales-proofs.destroy', $salesProof) }}" method="POST" class="inline-block ml-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Apakah Anda yakin ingin menghapus bukti penjualan ini?')">
                                    Hapus Bukti
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
