<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Bukti Penjualan') }}
            </h2>

            @if (Auth::user()->role === 'administrator')
                <a href="{{ route('sales-proofs.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow">
                    + Unggah Bukti Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alert sukses --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Judul</th>
                                <th class="px-6 py-3 text-left font-semibold">Diunggah Oleh</th>
                                <th class="px-6 py-3 text-left font-semibold">Status</th>
                                <th class="px-6 py-3 text-left font-semibold">Tanggal Unggah</th>
                                <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($salesProofs as $proof)
                                <tr>
                                    <td class="px-6 py-4">{{ $proof->title }}</td>
                                    <td class="px-6 py-4">{{ $proof->uploadedBy?->user?->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                            {{
                                                match($proof->status) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'validated' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                }
                                            }}">
                                            {{ ucfirst($proof->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $proof->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 text-right space-x-2 flex justify-around">
                                        <a href="{{ route('sales-proofs.show', $proof) }}" class="text-indigo-600 hover:underline">
                                            <x-heroicon-o-eye class="w-5 h-5"/>
                                        </a>

                                        @if (Auth::user()->role === 'administrator')
                                            <a href="{{ route('sales-proofs.edit', $proof) }}" class="text-blue-600 hover:underline">
                                                <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                            </a>

                                            <form action="{{ route('sales-proofs.destroy', $proof) }}" method="POST" class="inline-block"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus bukti penjualan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">
                                                    <x-heroicon-o-trash class="w-5 h-5"/>
                                            </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada bukti penjualan yang diunggah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
