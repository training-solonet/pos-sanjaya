@extends('layouts.manajemen.index')

@section('content')
    {{-- <div class="content flex-1 lg:flex-1"> --}}
        <main class="p-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detail Resep</h2>
                        <p class="text-gray-600">Menampilkan detail lengkap resep</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Export Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                 style="display: none;">
                                <a href="{{ route('management.resep.show', ['resep' => $recipe['id'], 'export' => 'excel']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i>Export Excel
                                </a>
                                <a href="{{ route('management.resep.show', ['resep' => $recipe['id'], 'export' => 'pdf']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i>Export PDF
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('management.resep.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Kembali</a>
                        <a href="{{ route('management.resep.index', ['edit' => $recipe['id']]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Edit</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="col-span-2">
                        <h3 class="text-lg font-semibold mb-2">{{ $recipe['name'] }}</h3>
                        <div class="text-sm text-gray-600 mb-4">
                            <span class="inline-block mr-4"><strong>Kategori:</strong> {{ $recipe['category'] }}</span>
                            <span class="inline-block mr-4"><strong>Porsi:</strong> {{ $recipe['yield'] }}</span>
                            <span class="inline-block"><strong>Durasi:</strong> {{ $recipe['duration'] }}</span>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border mb-4">
                            <h4 class="font-medium mb-2">Bahan</h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500">
                                        <th>Nama Bahan</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Unit</th>
                                        <th class="text-right">Harga/unit</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recipe['ingredients'] as $ing)
                                        <tr class="border-t">
                                            <td class="py-2">{{ $ing['name'] }}</td>
                                            <td class="py-2 text-right">{{ $ing['quantity'] }}</td>
                                            <td class="py-2 text-right">{{ $ing['unit'] }}</td>
                                            <td class="py-2 text-right">Rp {{ number_format($ing['price'] ?? 0, 0, ',', '.') }}</td>
                                            <td class="py-2 text-right">Rp {{ number_format($ing['subtotal'] ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-4 text-center text-gray-500">Tidak ada bahan</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white p-4 border rounded-lg">
                                <div class="text-xs text-gray-500">Total Food Cost</div>
                                <div class="font-bold text-gray-900">Rp {{ number_format($recipe['foodCost'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-white p-4 border rounded-lg">
                                <div class="text-xs text-gray-500">Harga Jual</div>
                                <div class="font-bold text-gray-900">Rp {{ number_format($recipe['sellingPrice'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-white p-4 border rounded-lg">
                                <div class="text-xs text-gray-500">Margin</div>
                                <div class="font-bold text-gray-900">{{ $recipe['margin'] ?? 0 }}%</div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg border mb-4">
                            <h4 class="font-medium mb-2">Instruksi / Langkah</h4>
                            <div class="text-sm text-gray-700">{!! nl2br(e($recipe['instructions'] ?? '-')) !!}</div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-medium mb-2">Catatan</h4>
                            <div class="text-sm text-gray-700">{!! nl2br(e($recipe['notes'] ?? '-')) !!}</div>
                        </div>
                    </div>

                    <aside class="bg-white border rounded-lg p-4">
                        <div class="mb-4">
                            <div class="text-xs text-gray-500">Status</div>
                            <div class="font-semibold">{{ $recipe['status'] }}</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-xs text-gray-500">Dibuat pada</div>
                            <div class="text-sm">{{ \Carbon\Carbon::parse(\App\Models\Resep::find($recipe['id'])->created_at)->translatedFormat('d F Y H:i') }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Diubah pada</div>
                            <div class="text-sm">{{ \Carbon\Carbon::parse(\App\Models\Resep::find($recipe['id'])->updated_at)->translatedFormat('d F Y H:i') }}</div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    {{-- </div> --}}
@endsection
