@extends('layouts.manajemen.index')

@section('content')
<!-- Main Content -->
<main class="p-4 sm:p-6 lg:p-8">
    <div class="space-y-6">
        <!-- Page Header dengan Back Button dan Action Buttons -->
        <div class="flex flex-col gap-4">
            <!-- Back Button -->
            <div>
                <a href="{{ route("management.bahanbaku.index") }}" 
                   class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Stok Bahan
                </a>
            </div>
            
            <!-- Title and Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Page Title -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Stok Opname Bahan Baku
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Kelola dan monitor stok fisik bahan baku vs sistem
                    </p>
                    @if($has_opname_today)
                        <div class="mt-2 flex items-center text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full inline-flex">
                            <i class="fas fa-info-circle mr-2"></i>
                            Opname hari ini sudah dimulai - {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <button onclick="startNewOpname()"
                            id="startNewOpnameBtn"
                            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $has_opname_today ? 'disabled' : '' }}>
                        <i class="fas fa-plus"></i>
                        <span>Mulai Opname Baru</span>
                    </button>
                    <button onclick="showHistory()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-history"></i>
                        <span>Riwayat</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Bahan Baku</p>
                        <p class="text-2xl font-bold text-gray-900" id="totalBahan">{{ $summary['total_bahan'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Sudah Dihitung</p>
                        <p class="text-2xl font-bold" id="dihitung">{{ $summary['dihitung'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Selisih Ditemukan</p>
                        <p class="text-2xl font-bold" id="selisihCount">{{ $summary['selisih'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Progress</p>
                        <p class="text-2xl font-bold" id="progress">{{ $summary['progress'] }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Cari bahan baku..."
                               class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <select id="categoryFilter"
                            class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ strtolower(str_replace(' ', '-', $category)) }}">{{ $category }}</option>
                        @endforeach
                    </select>
                    <select id="statusFilter"
                            class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        <option value="">Semua Status</option>
                        <option value="pending">Belum Dihitung</option>
                        <option value="counted">Sudah Dihitung</option>
                        <option value="discrepancy">Ada Selisih</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Product List -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Daftar Bahan Baku
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button onclick="toggleView('grid')" id="gridViewBtn"
                                class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button onclick="toggleView('list')" id="listViewBtn"
                                class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grid View -->
            <div id="gridView" class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($opname_data as $item)
                <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                     data-status="{{ $item['status'] }}" 
                     data-kategori="{{ strtolower(str_replace(' ', '-', $item['kategori'])) }}"
                     data-id="{{ $item['id'] }}"
                     data-nama="{{ strtolower($item['nama']) }}"
                     data-kode="{{ strtolower($item['kode']) }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $item['nama'] }}</h4>
                            <p class="text-sm text-gray-500">Kode: {{ $item['kode'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">Kategori: {{ $item['kategori'] }}</p>
                        </div>
                        @php
                            $statusConfig = [
                                'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock', 'text' => 'Pending'],
                                'counted' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check', 'text' => 'Selesai'],
                                'discrepancy' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation-triangle', 'text' => 'Selisih'],
                            ][$item['status']] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-question', 'text' => 'Unknown'];
                        @endphp
                        <span class="status-badge {{ $statusConfig['class'] }} px-2 py-1 rounded-full text-xs font-medium">
                            <i class="{{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['text'] }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Stok Sistem:</span>
                            <span class="font-medium">{{ number_format($item['stok_sistem'], 2) }} {{ $item['satuan'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Stok Fisik:</span>
                            <span class="font-medium @if($item['status'] == 'counted') text-green-600 @elseif($item['status'] == 'discrepancy') text-red-600 @else text-gray-400 @endif">
                                {{ $item['stok_fisik'] !== null ? number_format($item['stok_fisik'], 2) . ' ' . $item['satuan'] : 'Belum dihitung' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Selisih:</span>
                            <span class="font-medium @if($item['selisih'] > 0) text-blue-600 @elseif($item['selisih'] < 0) text-red-600 @elseif($item['selisih'] === 0) text-green-600 @else text-gray-400 @endif">
                                @if($item['selisih'] !== null)
                                    {{ $item['selisih'] > 0 ? '+' : '' }}{{ number_format($item['selisih'], 2) }} {{ $item['satuan'] }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        @if($item['tgl_opname_terakhir'])
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Terakhir dihitung:</span>
                                <span>{{ \Carbon\Carbon::parse($item['tgl_opname_terakhir'])->translatedFormat('d M Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <button onclick="openCountModal({{ $item['id'] }}, '{{ addslashes($item['nama']) }}', {{ $item['stok_sistem'] }}, {{ $item['stok_fisik'] ?? 'null' }}, '{{ $item['satuan'] }}', '{{ addslashes($item['catatan'] ?? '') }}')"
                                class="w-full py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                            <i class="fas fa-calculator mr-2"></i>Hitung Stok
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- List View (Hidden by default) -->
            <div id="listView" class="hidden p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bahan Baku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sistem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Fisik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="listViewContent">
                            @foreach ($opname_data as $item)
                            <tr class="hover:bg-gray-50 product-item"
                                data-status="{{ $item['status'] }}" 
                                data-kategori="{{ strtolower(str_replace(' ', '-', $item['kategori'])) }}"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ strtolower($item['nama']) }}"
                                data-kode="{{ strtolower($item['kode']) }}">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['nama'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item['kode'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item['kategori'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($item['stok_sistem'], 2) }} {{ $item['satuan'] }}</td>
                                <td class="px-6 py-4 text-sm @if($item['status'] == 'counted') text-green-600 @elseif($item['status'] == 'discrepancy') text-red-600 @else text-gray-400 @endif">
                                    {{ $item['stok_fisik'] !== null ? number_format($item['stok_fisik'], 2) . ' ' . $item['satuan'] : 'Belum dihitung' }}
                                </td>
                                <td class="px-6 py-4 text-sm @if($item['selisih'] > 0) text-blue-600 @elseif($item['selisih'] < 0) text-red-600 @elseif($item['selisih'] === 0) text-green-600 @else text-gray-400 @endif">
                                    @if($item['selisih'] !== null)
                                        {{ $item['selisih'] > 0 ? '+' : '' }}{{ number_format($item['selisih'], 2) }} {{ $item['satuan'] }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock', 'text' => 'Pending'],
                                            'counted' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check', 'text' => 'Selesai'],
                                            'discrepancy' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation-triangle', 'text' => 'Selisih'],
                                        ][$item['status']] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-question', 'text' => 'Unknown'];
                                    @endphp
                                    <span class="status-badge {{ $statusConfig['class'] }} px-2 py-1 rounded-full text-xs font-medium">
                                        <i class="{{ $statusConfig['icon'] }} mr-1"></i>{{ $statusConfig['text'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="openCountModal({{ $item['id'] }}, '{{ addslashes($item['nama']) }}', {{ $item['stok_sistem'] }}, {{ $item['stok_fisik'] ?? 'null' }}, '{{ $item['satuan'] }}', '{{ addslashes($item['catatan'] ?? '') }}')"
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                                        <i class="fas fa-calculator mr-1"></i>Hitung
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($bahan_baku->count() > 0)
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="mb-2 sm:mb-0">
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $bahan_baku->firstItem() }}</span>
                                -
                                <span class="font-medium">{{ $bahan_baku->lastItem() }}</span>
                                dari
                                <span class="font-medium">{{ $bahan_baku->total() }}</span>
                                bahan baku
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Tombol Previous --}}
                                @if ($bahan_baku->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <span class="sr-only">Sebelumnya</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $bahan_baku->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Sebelumnya</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Tombol Halaman --}}
                                @php
                                    $current = $bahan_baku->currentPage();
                                    $last = $bahan_baku->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $bahan_baku->url(1) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        1
                                    </a>
                                    @if($start > 2)
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                            ...
                                        </span>
                                    @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $current)
                                        <span class="relative inline-flex items-center px-4 py-2 border border-green-500 bg-green-50 text-sm font-medium text-green-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $bahan_baku->url($page) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endfor

                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                            ...
                                        </span>
                                    @endif
                                    <a href="{{ $bahan_baku->url($last) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        {{ $last }}
                                    </a>
                                @endif

                                {{-- Tombol Next --}}
                                @if ($bahan_baku->hasMorePages())
                                    <a href="{{ $bahan_baku->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Berikutnya</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <span class="sr-only">Berikutnya</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Count Modal -->
<div id="countModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Hitung Stok Fisik</h3>
                    <button onclick="closeCountModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h4 id="productName" class="font-semibold text-gray-900"></h4>
                        <p id="productSku" class="text-sm text-gray-500"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Stok Sistem:</span>
                            <span id="systemStock" class="font-medium"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stok Fisik</label>
                        <div class="flex items-center space-x-3">
                            <button onclick="decreaseCount()"
                                    class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="physicalCount" min="0" step="0.01"
                                   class="flex-1 text-center p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 text-lg font-medium" />
                            <button onclick="increaseCount()"
                                    class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div id="differenceDisplay" class="hidden">
                        <div class="flex justify-between items-center p-4 rounded-lg">
                            <span class="text-sm font-medium">Selisih:</span>
                            <span id="difference" class="font-bold"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea id="countNotes" rows="3" placeholder="Tambahkan catatan tentang kondisi stok..."
                                  class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"></textarea>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t">
                <div class="flex space-x-3">
                    <button onclick="closeCountModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button onclick="saveCount()" id="saveButton"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Riwayat Stok Opname
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Data riwayat perhitungan stok fisik bahan baku
                        </p>
                    </div>
                    <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Filter Section -->
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                            <select id="periodFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                <option value="daily">Harian</option>
                                <option value="weekly" selected>Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" id="startDateFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" id="endDateFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                        </div>
                        <div class="flex items-end">
                            <button onclick="loadHistoryWithFilter()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                <div class="space-y-6" id="historyContent">
                    <!-- Konten akan dimuat secara dinamis -->
                </div>
                <div id="noHistoryResults" class="hidden text-center py-8">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-8">
                        <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-700 font-medium mb-2">Belum ada riwayat opname</p>
                        <p class="text-gray-600 text-sm">Tidak ada data riwayat untuk periode yang dipilih.</p>
                    </div>
                </div>
                <div id="historyLoading" class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Memuat riwayat...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let currentProductId = null;
    let currentProductData = null;
    let currentUnit = '';
    let hasOpnameToday = {{ $has_opname_today ? 'true' : 'false' }};
    let currentView = 'grid';
    let allHistories = null;
    let currentPage = {{ $bahan_baku->currentPage() }};
    let lastPage = {{ $bahan_baku->lastPage() }};

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Setup event listeners
    document.addEventListener('DOMContentLoaded', function() {
        setupSearchAndFilters();
        setupModalListeners();
        
        // Update tombol berdasarkan status
        updateStartButton();
        
        // Set default view
        toggleView('grid');
        
        // Auto-refresh setiap 30 detik
        setInterval(loadOpnameData, 30000);
        
        console.log('✅ Aplikasi Stok Opname siap digunakan');
        console.log('📊 Status Opname Hari Ini:', hasOpnameToday ? 'SUDAH DIMULAI' : 'BELUM DIMULAI');
    });

    // Update tombol mulai opname
    function updateStartButton() {
        const startBtn = document.getElementById('startNewOpnameBtn');
        if (startBtn) {
            if (hasOpnameToday) {
                startBtn.disabled = true;
                startBtn.title = "Opname hari ini sudah dilakukan. Anda hanya dapat melakukan opname sekali per hari.";
            } else {
                startBtn.disabled = false;
                startBtn.title = "Mulai opname baru untuk hari ini";
            }
        }
    }

    // Load data opname dengan pagination
    async function loadOpnameData(page = currentPage) {
        try {
            const response = await fetch('{{ route("management.opname.index") }}?ajax=1&page=' + page, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            
            if (result.success) {
                updateStatistics(result.summary);
                updateProductItems(result.data);
                updatePagination(result.pagination);
                
                // Update status opname hari ini
                hasOpnameToday = result.has_opname_today;
                updateStartButton();
            } else {
                console.error('Failed to load data:', result.message);
            }
        } catch (error) {
            console.error('Error loading opname data:', error);
        }
    }

    // Update pagination info
    function updatePagination(pagination) {
        if (pagination) {
            currentPage = pagination.current_page;
            lastPage = pagination.last_page;
        }
    }

    // Update statistics
    function updateStatistics(summary) {
        document.getElementById('totalBahan').textContent = summary.total_bahan;
        document.getElementById('dihitung').textContent = summary.dihitung;
        document.getElementById('dihitung').className = `text-2xl font-bold ${summary.dihitung > 0 ? 'text-green-600' : 'text-gray-900'}`;
        document.getElementById('selisihCount').textContent = summary.selisih;
        document.getElementById('selisihCount').className = `text-2xl font-bold ${summary.selisih > 0 ? 'text-red-600' : 'text-gray-900'}`;
        document.getElementById('progress').textContent = `${summary.progress}%`;
        document.getElementById('progress').className = `text-2xl font-bold ${summary.progress > 0 ? 'text-blue-600' : 'text-gray-900'}`;
    }

    // Update product items
    function updateProductItems(products) {
        // Update grid view
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listViewContent');
        
        if (currentView === 'grid') {
            let gridHtml = '';
            products.forEach(product => {
                const statusConfig = getStatusConfig(product.status);
                const selisih = product.selisih !== null ? parseFloat(product.selisih) : null;
                
                gridHtml += `
                    <div class="product-item border border-gray-200 rounded-xl p-4 hover:border-green-400 transition-all cursor-pointer"
                         data-status="${product.status}" 
                         data-kategori="${product.kategori ? product.kategori.toLowerCase().replace(/ /g, '-') : ''}"
                         data-id="${product.id}"
                         data-nama="${product.nama.toLowerCase()}"
                         data-kode="${product.kode.toLowerCase()}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">${product.nama}</h4>
                                <p class="text-sm text-gray-500">Kode: ${product.kode}</p>
                                <p class="text-sm text-gray-600 mt-1">Kategori: ${product.kategori}</p>
                            </div>
                            <span class="status-badge ${statusConfig.badgeClass} px-2 py-1 rounded-full text-xs font-medium">
                                <i class="${statusConfig.icon} mr-1"></i>${statusConfig.text}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Stok Sistem:</span>
                                <span class="font-medium">${parseFloat(product.stok_sistem).toFixed(2)} ${product.satuan}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Stok Fisik:</span>
                                <span class="font-medium ${statusConfig.stockClass}">
                                    ${product.stok_fisik !== null ? parseFloat(product.stok_fisik).toFixed(2) + ' ' + product.satuan : 'Belum dihitung'}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Selisih:</span>
                                <span class="font-medium ${selisih > 0 ? 'text-blue-600' : selisih < 0 ? 'text-red-600' : selisih === 0 ? 'text-green-600' : 'text-gray-400'}">
                                    ${product.stok_fisik !== null ? 
                                        (selisih > 0 ? '+' : '') + parseFloat(selisih).toFixed(2) + ' ' + product.satuan : 
                                        '-'}
                                </span>
                            </div>
                            ${product.tgl_opname_terakhir ? `
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Terakhir dihitung:</span>
                                <span>${new Date(product.tgl_opname_terakhir).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                            ` : ''}
                        </div>
                        <div class="mt-4">
                            <button onclick="openCountModal(${product.id}, '${product.nama.replace(/'/g, "\\'")}', ${product.stok_sistem}, ${product.stok_fisik || 'null'}, '${product.satuan}', '${(product.catatan || '').replace(/'/g, "\\'")}')"
                                    class="w-full py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                <i class="fas fa-calculator mr-2"></i>Hitung Stok
                            </button>
                        </div>
                    </div>
                `;
            });
            gridView.innerHTML = gridHtml;
        } else {
            let listHtml = '';
            products.forEach(product => {
                const statusConfig = getStatusConfig(product.status);
                const selisih = product.selisih !== null ? parseFloat(product.selisih) : null;
                
                listHtml += `
                    <tr class="hover:bg-gray-50 product-item"
                        data-status="${product.status}" 
                        data-kategori="${product.kategori ? product.kategori.toLowerCase().replace(/ /g, '-') : ''}"
                        data-id="${product.id}"
                        data-nama="${product.nama.toLowerCase()}"
                        data-kode="${product.kode.toLowerCase()}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${product.nama}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">${product.kode}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">${product.kategori}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${parseFloat(product.stok_sistem).toFixed(2)} ${product.satuan}</td>
                        <td class="px-6 py-4 text-sm ${statusConfig.stockClass}">
                            ${product.stok_fisik !== null ? parseFloat(product.stok_fisik).toFixed(2) + ' ' + product.satuan : 'Belum dihitung'}
                        </td>
                        <td class="px-6 py-4 text-sm ${selisih > 0 ? 'text-blue-600' : selisih < 0 ? 'text-red-600' : selisih === 0 ? 'text-green-600' : 'text-gray-400'}">
                            ${product.stok_fisik !== null ? 
                                (selisih > 0 ? '+' : '') + parseFloat(selisih).toFixed(2) + ' ' + product.satuan : 
                                '-'}
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge ${statusConfig.badgeClass} px-2 py-1 rounded-full text-xs font-medium">
                                <i class="${statusConfig.icon} mr-1"></i>${statusConfig.text}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openCountModal(${product.id}, '${product.nama.replace(/'/g, "\\'")}', ${product.stok_sistem}, ${product.stok_fisik || 'null'}, '${product.satuan}', '${(product.catatan || '').replace(/'/g, "\\'")}')"
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                                <i class="fas fa-calculator mr-1"></i>Hitung
                            </button>
                        </td>
                    </tr>
                `;
            });
            listViewContent.innerHTML = listHtml;
        }
        
        // Setup search and filters untuk data baru
        setupSearchAndFilters();
    }

    // Open count modal
    function openCountModal(id, nama, stokSistem, stokFisik, satuan, catatan) {
        currentProductId = id;
        currentProductData = { stok_sistem: stokSistem };
        currentUnit = satuan;
        
        document.getElementById('modalTitle').textContent = 'Hitung Stok Fisik';
        document.getElementById('productName').textContent = nama;
        document.getElementById('productSku').textContent = `Kode: BB-${String(id).padStart(3, '0')}`;
        document.getElementById('systemStock').textContent = `${parseFloat(stokSistem).toFixed(2)} ${satuan}`;
        
        const physicalCount = stokFisik !== null && stokFisik !== 'null' ? parseFloat(stokFisik) : '';
        document.getElementById('physicalCount').value = physicalCount !== '' ? physicalCount.toFixed(2) : '';
        document.getElementById('countNotes').value = catatan || '';
        
        updateDifference();
        
        document.getElementById('countModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Focus input
        setTimeout(() => {
            document.getElementById('physicalCount').focus();
            document.getElementById('physicalCount').select();
        }, 100);
    }

    // Close count modal
    function closeCountModal() {
        document.getElementById('countModal').classList.add('hidden');
        document.body.style.overflow = '';
        currentProductId = null;
        currentProductData = null;
        currentUnit = '';
    }

    // Increase count
    function increaseCount() {
        const input = document.getElementById('physicalCount');
        let value = parseFloat(input.value) || 0;
        value = Math.max(0, value + 1);
        input.value = value.toFixed(2);
        updateDifference();
    }

    // Decrease count
    function decreaseCount() {
        const input = document.getElementById('physicalCount');
        let value = parseFloat(input.value) || 0;
        value = Math.max(0, value - 1);
        input.value = value.toFixed(2);
        updateDifference();
    }

    // Update difference display
    function updateDifference() {
        const physicalCount = parseFloat(document.getElementById('physicalCount').value) || 0;
        const systemStock = currentProductData ? parseFloat(currentProductData.stok_sistem) : 0;
        const difference = physicalCount - systemStock;
        
        const differenceDisplay = document.getElementById('differenceDisplay');
        const differenceSpan = document.getElementById('difference');
        
        if (!isNaN(physicalCount)) {
            differenceDisplay.classList.remove('hidden');
            differenceSpan.textContent = `${difference > 0 ? '+' : ''}${difference.toFixed(2)} ${currentUnit}`;
            
            // Set color based on difference
            if (difference > 0) {
                differenceDisplay.className = 'bg-blue-50 border border-blue-200 rounded-lg';
                differenceSpan.className = 'font-bold text-blue-700';
            } else if (difference < 0) {
                differenceDisplay.className = 'bg-red-50 border border-red-200 rounded-lg';
                differenceSpan.className = 'font-bold text-red-700';
            } else {
                differenceDisplay.className = 'bg-green-50 border border-green-200 rounded-lg';
                differenceSpan.className = 'font-bold text-green-700';
            }
        } else {
            differenceDisplay.classList.add('hidden');
        }
    }

    // Save count
    async function saveCount() {
        const physicalCount = parseFloat(document.getElementById('physicalCount').value);
        const notes = document.getElementById('countNotes').value;
        
        if (isNaN(physicalCount) || physicalCount < 0) {
            showError('Jumlah stok fisik harus berupa angka yang valid dan tidak negatif!');
            return;
        }
        
        if (!currentProductId) {
            showError('Produk tidak valid!');
            return;
        }
        
        // Disable save button and show loading
        const saveButton = document.getElementById('saveButton');
        const originalText = saveButton.innerHTML;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        saveButton.disabled = true;
        
        try {
            console.log('💾 Menyimpan stok fisik untuk bahan ID:', currentProductId);
            
            const response = await fetch('{{ route("management.opname.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id_bahan: currentProductId,
                    stok_fisik: physicalCount,
                    catatan: notes
                })
            });
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response bukan JSON');
            }
            
            const result = await response.json();
            
            if (result.success) {
                closeCountModal();
                showSuccess('Stok fisik berhasil disimpan!');
                
                console.log('✅ Stok berhasil disimpan, akan direfresh dalam 1 detik');
                
                // Refresh data setelah 1 detik
                setTimeout(() => {
                    loadOpnameData(currentPage);
                }, 1000);
            } else {
                showError(result.message || 'Gagal menyimpan data');
            }
        } catch (error) {
            console.error('Save error:', error);
            showError('Terjadi kesalahan saat menyimpan data: ' + error.message);
        } finally {
            // Restore button
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        }
    }

    // Start new opname session
    async function startNewOpname() {
        // Cek apakah sudah ada opname hari ini
        if (hasOpnameToday) {
            Swal.fire({
                title: 'Tidak Dapat Memulai Opname Baru',
                html: `
                    <div class="text-left">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Opname hari ini sudah dilakukan.</strong><br>
                                        Anda hanya dapat melakukan opname sekali per hari.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600">Silakan lanjutkan dengan menghitung stok fisik bahan baku yang belum dihitung.</p>
                    </div>
                `,
                icon: 'warning',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#10B981',
                customClass: {
                    confirmButton: 'px-4 py-2'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Mulai Opname Baru?',
            html: `
                <div class="text-left">
                    <p class="mb-3">Anda akan memulai sesi opname baru untuk hari ini.</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Pastikan semua stok fisik sudah siap untuk dihitung.</strong><br>
                                    Sesi opname baru akan dimulai untuk tanggal {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <p>Apakah Anda yakin ingin memulai opname baru?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Mulai Opname',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#EF4444',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch('{{ route("management.opname.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            action: 'start_new_session'
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                                <div class="text-center">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                        <i class="fas fa-check text-green-600 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sesi Opname Dimulai</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Tanggal: <span class="font-semibold">${result.session_date}</span>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Anda sekarang dapat mulai menghitung stok fisik bahan baku.
                                        </p>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            confirmButtonText: 'Mulai Hitung Stok'
                        });
                        
                        // Update status
                        hasOpnameToday = true;
                        updateStartButton();
                        
                        // Refresh data
                        setTimeout(() => {
                            loadOpnameData(currentPage);
                        }, 1000);
                    } else {
                        showError(result.message || 'Gagal memulai sesi opname');
                    }
                } catch (error) {
                    console.error('Start new opname error:', error);
                    showError('Terjadi kesalahan: ' + error.message);
                }
            }
        });
    }

    // Show history
    async function showHistory() {
        console.log('📚 Membuka modal riwayat opname...');
        
        document.getElementById('historyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Show loading
        document.getElementById('historyLoading').classList.remove('hidden');
        document.getElementById('historyContent').innerHTML = '';
        document.getElementById('noHistoryResults').classList.add('hidden');
        
        // Set tanggal default: 7 hari terakhir
        const today = new Date();
        const oneWeekAgo = new Date();
        oneWeekAgo.setDate(today.getDate() - 7);
        
        document.getElementById('startDateFilter').value = oneWeekAgo.toISOString().split('T')[0];
        document.getElementById('endDateFilter').value = today.toISOString().split('T')[0];
        
        // Load data dengan filter default
        await loadHistoryWithFilter();
    }

    // Load history dengan filter
    async function loadHistoryWithFilter() {
        const period = document.getElementById('periodFilter').value;
        const startDate = document.getElementById('startDateFilter').value;
        const endDate = document.getElementById('endDateFilter').value;
        
        console.log('🔍 Mengambil data riwayat dengan filter:', { period, startDate, endDate });
        
        // Show loading
        document.getElementById('historyLoading').classList.remove('hidden');
        document.getElementById('historyContent').innerHTML = '';
        document.getElementById('noHistoryResults').classList.add('hidden');
        
        try {
            // Build URL dengan parameter
            const url = new URL('{{ route("management.opname.index") }}', window.location.origin);
            url.searchParams.append('history', 1);
            url.searchParams.append('ajax', 1);
            url.searchParams.append('period', period);
            url.searchParams.append('start_date', startDate);
            url.searchParams.append('end_date', endDate);
            
            console.log('📡 URL request:', url.toString());
            
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            console.log('📦 Response status:', response.status);
            
            const result = await response.json();
            console.log('📊 Data riwayat diterima:', result);
            
            if (result.success && result.data) {
                allHistories = result.data;
                console.log('✅ Data riwayat valid, total records:', allHistories.length);
                
                if (allHistories.length > 0) {
                    console.log('🔍 Struktur data pertama:', allHistories[0]);
                }
                
                renderHistoryData(allHistories);
            } else {
                console.error('❌ Gagal memuat riwayat:', result.message);
                throw new Error(result.message || 'Gagal memuat riwayat');
            }
        } catch (error) {
            console.error('🔴 Load history error:', error);
            document.getElementById('historyLoading').classList.add('hidden');
            document.getElementById('historyContent').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                    <p class="text-red-700 font-medium mb-2">Gagal memuat riwayat</p>
                    <p class="text-red-600 text-sm">${error.message}</p>
                    <button onclick="loadHistoryWithFilter()" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        <i class="fas fa-sync mr-2"></i>Coba Lagi
                    </button>
                </div>
            `;
        }
    }

    // Render history data
    function renderHistoryData(histories) {
        console.log('🎨 Rendering riwayat dengan data:', histories);
        
        const historyContent = document.getElementById('historyContent');
        const noResults = document.getElementById('noHistoryResults');
        const historyLoading = document.getElementById('historyLoading');
        
        historyLoading.classList.add('hidden');
        
        // Cek jika data kosong
        if (!histories || !Array.isArray(histories) || histories.length === 0) {
            console.log('📭 Tidak ada data riwayat');
            historyContent.innerHTML = '';
            noResults.classList.remove('hidden');
            return;
        }
        
        console.log(`📊 Jumlah record riwayat: ${histories.length}`);
        
        // Kelompokkan data berdasarkan tanggal
        const groupedByDate = {};
        histories.forEach(item => {
            const date = item.tgl;
            if (!date) {
                console.warn('⚠️ Item tanpa tanggal:', item);
                return;
            }
            
            if (!groupedByDate[date]) {
                groupedByDate[date] = [];
            }
            groupedByDate[date].push(item);
        });
        
        console.log(`📅 Jumlah hari dengan riwayat: ${Object.keys(groupedByDate).length}`);
        console.log('📆 Tanggal yang ditemukan:', Object.keys(groupedByDate));
        
        let html = '';
        let totalItems = 0;
        
        // Urutkan tanggal descending
        const sortedDates = Object.keys(groupedByDate).sort((a, b) => new Date(b) - new Date(a));
        
        // Loop melalui setiap tanggal
        sortedDates.forEach(date => {
            const items = groupedByDate[date];
            
            if (!Array.isArray(items) || items.length === 0) {
                return;
            }
            
            totalItems += items.length;
            
            // Format tanggal
            let formattedDate;
            let dayName;
            try {
                const dateObj = new Date(date + 'T00:00:00');
                formattedDate = dateObj.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                dayName = dateObj.toLocaleDateString('id-ID', { weekday: 'long' });
            } catch (e) {
                formattedDate = date;
                dayName = '';
            }
            
            // Hitung statistik per hari
            const totalItemsDay = items.length;
            const totalSelisihPositif = items.filter(item => parseFloat(item.selisih) > 0).length;
            const totalSelisihNegatif = items.filter(item => parseFloat(item.selisih) < 0).length;
            const totalSelisihNol = items.filter(item => parseFloat(item.selisih) === 0).length;
            
            html += `
                <div class="history-day mb-6 bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-lg">${dayName}, ${formattedDate}</h4>
                                        <p class="text-sm text-gray-500 mt-1">${totalItemsDay} item dihitung</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full flex items-center">
                                    <i class="fas fa-calendar-alt mr-1"></i> ${date}
                                </span>
                                ${totalSelisihNol > 0 ? `
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full flex items-center">
                                    <i class="fas fa-check mr-1"></i> ${totalSelisihNol} sesuai
                                </span>
                                ` : ''}
                                ${totalSelisihPositif > 0 ? `
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i> +${totalSelisihPositif}
                                </span>
                                ` : ''}
                                ${totalSelisihNegatif > 0 ? `
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full flex items-center">
                                    <i class="fas fa-arrow-down mr-1"></i> ${totalSelisihNegatif}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bahan Baku</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sistem</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Fisik</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
            `;
            
            // Loop melalui setiap item dalam tanggal tersebut
            items.forEach(item => {
                const selisih = parseFloat(item.selisih) || 0;
                const selisihClass = selisih > 0 ? 'text-blue-600' : 
                                   selisih < 0 ? 'text-red-600' : 
                                   'text-green-600';
                const selisihIcon = selisih > 0 ? 'fa-arrow-up' : 
                                  selisih < 0 ? 'fa-arrow-down' : 'fa-equals';
                const selisihLabel = selisih > 0 ? 'Lebih' : selisih < 0 ? 'Kurang' : 'Sesuai';
                
                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">${item.nama_bahan || item.nama || 'Unknown'}</div>
                            <div class="text-sm text-gray-500">${item.kode || ''}</div>
                            ${item.catatan ? `<div class="text-xs text-gray-600 mt-1"><i class="fas fa-sticky-note mr-1"></i>${item.catatan}</div>` : ''}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            ${parseFloat(item.stok_sistem || 0).toFixed(2)} ${item.satuan || 'unit'}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium">
                            ${parseFloat(item.stok_fisik || 0).toFixed(2)} ${item.satuan || 'unit'}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center ${selisihClass}">
                                <i class="fas ${selisihIcon} mr-1"></i>
                                <span class="font-medium">${selisih > 0 ? '+' : ''}${selisih.toFixed(2)} ${item.satuan || 'unit'}</span>
                            </div>
                            <div class="text-xs text-gray-500">${selisihLabel}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            ${item.waktu || '00:00'}
                        </td>
                    </tr>
                `;
            });
            
            html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        });
        
        console.log(`✅ Total item yang dirender: ${totalItems}`);
        
        if (totalItems === 0) {
            historyContent.innerHTML = '';
            noResults.classList.remove('hidden');
        } else {
            historyContent.innerHTML = html;
            noResults.classList.add('hidden');
        }
    }

    // Close history modal
    function closeHistoryModal() {
        document.getElementById('historyModal').classList.add('hidden');
        document.body.style.overflow = '';
        // Reset konten
        document.getElementById('historyContent').innerHTML = '';
        document.getElementById('historyLoading').classList.add('hidden');
        document.getElementById('noHistoryResults').classList.add('hidden');
    }

    // Toggle view
    function toggleView(view) {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        
        if (view === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
            gridBtn.classList.add('bg-green-100', 'text-green-600');
            gridBtn.classList.remove('bg-gray-100', 'text-gray-600');
            listBtn.classList.add('bg-gray-100', 'text-gray-600');
            listBtn.classList.remove('bg-green-100', 'text-green-600');
            currentView = 'grid';
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.classList.add('bg-green-100', 'text-green-600');
            listBtn.classList.remove('bg-gray-100', 'text-gray-600');
            gridBtn.classList.add('bg-gray-100', 'text-gray-600');
            gridBtn.classList.remove('bg-green-100', 'text-green-600');
            currentView = 'list';
        }
    }

    // Setup search and filters
    function setupSearchAndFilters() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filterProducts(searchTerm, categoryFilter.value, statusFilter.value);
            });
        }
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                filterProducts(searchInput.value.toLowerCase(), this.value, statusFilter.value);
            });
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                filterProducts(searchInput.value.toLowerCase(), categoryFilter.value, this.value);
            });
        }
    }
    
    // Filter products
    function filterProducts(searchTerm, category, status) {
        const productItems = document.querySelectorAll('.product-item');
        
        productItems.forEach(item => {
            const productName = item.getAttribute('data-nama');
            const productCode = item.getAttribute('data-kode');
            const itemCategory = item.getAttribute('data-kategori');
            const itemStatus = item.getAttribute('data-status');
            
            const nameMatch = productName.includes(searchTerm) || productCode.includes(searchTerm);
            const categoryMatch = !category || itemCategory === category;
            const statusMatch = !status || itemStatus === status;
            
            item.style.display = nameMatch && categoryMatch && statusMatch ? 'block' : 'none';
        });
    }
    
    // Setup modal listeners
    function setupModalListeners() {
        const countModal = document.getElementById('countModal');
        const historyModal = document.getElementById('historyModal');
        const physicalCountInput = document.getElementById('physicalCount');
        
        if (countModal) {
            countModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeCountModal();
                }
            });
            
            // Allow click inside modal
            const modalContent = countModal.querySelector('.bg-white');
            if (modalContent) {
                modalContent.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }
        
        if (historyModal) {
            historyModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeHistoryModal();
                }
            });
            
            // Allow click inside modal
            const modalContent = historyModal.querySelector('.bg-white');
            if (modalContent) {
                modalContent.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }
        
        if (physicalCountInput) {
            physicalCountInput.addEventListener('input', updateDifference);
            physicalCountInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveCount();
                }
            });
        }
        
        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCountModal();
                closeHistoryModal();
            }
        });
    }
    
    // Get status config
    function getStatusConfig(status) {
        const configs = {
            'pending': {
                badgeClass: 'bg-yellow-100 text-yellow-800',
                icon: 'fas fa-clock',
                text: 'Pending',
                stockClass: 'text-gray-400'
            },
            'counted': {
                badgeClass: 'bg-green-100 text-green-800',
                icon: 'fas fa-check',
                text: 'Selesai',
                stockClass: 'text-green-600'
            },
            'discrepancy': {
                badgeClass: 'bg-red-100 text-red-800',
                icon: 'fas fa-exclamation-triangle',
                text: 'Selisih',
                stockClass: 'text-red-600'
            }
        };
        return configs[status] || configs.pending;
    }
    
    // Show success message
    function showSuccess(message) {
        Swal.fire({
            title: 'Sukses!',
            text: message,
            icon: 'success',
            confirmButtonColor: '#10B981',
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    // Show error message
    function showError(message) {
        Swal.fire({
            title: 'Terjadi Kesalahan',
            text: message,
            icon: 'error',
            confirmButtonColor: '#EF4444'
        });
    }
</script>
@endsection