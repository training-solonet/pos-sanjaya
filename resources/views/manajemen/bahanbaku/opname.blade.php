@extends('layouts.manajemen.index')

@section('content')
<!-- Main Content -->
<div class="content flex-1 lg:flex-1">
    <!-- Page Content -->
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
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button onclick="startNewOpname()"
                                class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all duration-200 flex items-center justify-center gap-2">
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
                         data-id="{{ $item['id'] }}">
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
                            <button onclick="openCountModal({{ $item['id'] }}, '{{ $item['nama'] }}', {{ $item['stok_sistem'] }}, {{ $item['stok_fisik'] ?? 'null' }}, '{{ $item['satuan'] }}', '{{ $item['catatan'] ?? '' }}')"
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
                                <tr class="hover:bg-gray-50">
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
                                        <button onclick="openCountModal({{ $item['id'] }}, '{{ $item['nama'] }}', {{ $item['stok_sistem'] }}, {{ $item['stok_fisik'] ?? 'null' }}, '{{ $item['satuan'] }}', '{{ $item['catatan'] ?? '' }}')"
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
            </div>
        </div>
    </main>
</div>

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
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Riwayat Stok Opname
                    </h3>
                    <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="space-y-4" id="historyContent">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto"></div>
                        <p class="mt-4 text-gray-600">Memuat riwayat...</p>
                    </div>
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

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Setup event listeners
    document.addEventListener('DOMContentLoaded', function() {
        setupSearchAndFilters();
        setupModalListeners();
        
        // Auto-refresh setiap 30 detik
        setInterval(loadOpnameData, 30000);
    });

    // Load data opname
    async function loadOpnameData() {
        try {
            const response = await fetch('{{ route("management.opname.index") }}?ajax=1', {
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
            } else {
                console.error('Failed to load data:', result.message);
            }
        } catch (error) {
            console.error('Error loading opname data:', error);
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
        const productItems = document.querySelectorAll('.product-item');
        products.forEach(product => {
            const item = Array.from(productItems).find(el => el.getAttribute('data-id') == product.id);
            if (item) {
                const status = product.status;
                const statusConfig = getStatusConfig(status);
                const selisih = product.selisih !== null ? parseFloat(product.selisih) : null;
                
                // Update status badge
                const badge = item.querySelector('.status-badge');
                if (badge) {
                    badge.className = `status-badge ${statusConfig.badgeClass} px-2 py-1 rounded-full text-xs font-medium`;
                    badge.innerHTML = `<i class="${statusConfig.icon} mr-1"></i>${statusConfig.text}`;
                }
                
                // Update stok fisik
                const stockPhysical = item.querySelectorAll('.flex.justify-between.items-center')[1];
                if (stockPhysical) {
                    const span = stockPhysical.querySelector('span.font-medium');
                    if (span) {
                        span.className = `font-medium ${product.stok_fisik !== null ? statusConfig.stockClass : 'text-gray-400'}`;
                        span.textContent = product.stok_fisik !== null ? 
                            `${parseFloat(product.stok_fisik).toFixed(2)} ${product.satuan}` : 'Belum dihitung';
                    }
                }
                
                // Update selisih
                const selisihElement = item.querySelectorAll('.flex.justify-between.items-center')[2];
                if (selisihElement) {
                    const span = selisihElement.querySelector('span.font-medium');
                    if (span) {
                        const selisihClass = selisih > 0 ? 'text-blue-600' : 
                                           selisih < 0 ? 'text-red-600' : 
                                           selisih === 0 ? 'text-green-600' : 'text-gray-400';
                        span.className = `font-medium ${product.stok_fisik !== null ? selisihClass : 'text-gray-400'}`;
                        span.textContent = product.stok_fisik !== null ? 
                            `${selisih > 0 ? '+' : ''}${selisih !== null ? selisih.toFixed(2) : 0} ${product.satuan}` : '-';
                    }
                }
            }
        });
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
                
                // Refresh data setelah 1 detik
                setTimeout(() => {
                    loadOpnameData();
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
                                    <strong>Perhatian:</strong> Semua data opname hari ini akan dihapus dan perhitungan akan dimulai dari awal.
                                </p>
                            </div>
                        </div>
                    </div>
                    <p>Apakah Anda yakin ingin melanjutkan?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Mulai Baru',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#EF4444',
            reverseButtons: true,
            customClass: {
                confirmButton: 'mr-2',
                cancelButton: 'ml-2'
            }
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
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response bukan JSON');
                    }
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                                <div class="text-center">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                        <i class="fas fa-check text-green-600 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sesi Opname Baru Dimulai</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Tanggal: <span class="font-semibold">${result.session_date}</span>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Data direset: <span class="font-semibold">${result.deleted_count} bahan baku</span>
                                        </p>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            confirmButtonText: 'OK'
                        });
                        
                        // Refresh data
                        setTimeout(() => {
                            loadOpnameData();
                        }, 1000);
                    } else {
                        showError(result.message || 'Gagal memulai sesi baru');
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
        document.getElementById('historyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        try {
            const response = await fetch('{{ route("management.opname.index") }}?history=1&ajax=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response bukan JSON');
            }
            
            const result = await response.json();
            renderHistory(result.data || []);
        } catch (error) {
            console.error('Load history error:', error);
            document.getElementById('historyContent').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                    <p class="text-red-700">Gagal memuat riwayat: ${error.message}</p>
                </div>
            `;
        }
    }

    // Render history
    function renderHistory(histories) {
        const historyContent = document.getElementById('historyContent');
        
        if (!histories || histories.length === 0) {
            historyContent.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-info-circle text-yellow-500 text-3xl mb-3"></i>
                    <p class="text-yellow-700">Belum ada riwayat opname.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        histories.forEach(history => {
            const selisih = parseFloat(history.selisih);
            const selisihClass = selisih > 0 ? 'text-blue-600' : 
                               selisih < 0 ? 'text-red-600' : 'text-green-600';
            const selisihBadgeClass = selisih > 0 ? 'bg-blue-100' : 
                                    selisih < 0 ? 'bg-red-100' : 'bg-green-100';
            
            html += `
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">${history.nama_bahan}</h4>
                            <p class="text-sm text-gray-500">${history.tgl}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${selisihClass} ${selisihBadgeClass}">
                            ${selisih > 0 ? '+' : ''}${selisih.toFixed(2)} ${history.satuan}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Stok Sistem</p>
                            <p class="text-lg font-semibold">${parseFloat(history.stok_sistem).toFixed(2)} ${history.satuan}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Stok Fisik</p>
                            <p class="text-lg font-semibold">${parseFloat(history.stok_fisik).toFixed(2)} ${history.satuan}</p>
                        </div>
                    </div>
                    ${history.catatan ? `
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-sm text-gray-600 mb-1">Catatan:</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">${history.catatan}</p>
                    </div>
                    ` : ''}
                </div>
            `;
        });
        
        historyContent.innerHTML = html;
    }

    // Close history modal
    function closeHistoryModal() {
        document.getElementById('historyModal').classList.add('hidden');
        document.body.style.overflow = '';
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
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.classList.add('bg-green-100', 'text-green-600');
            listBtn.classList.remove('bg-gray-100', 'text-gray-600');
            gridBtn.classList.add('bg-gray-100', 'text-gray-600');
            gridBtn.classList.remove('bg-green-100', 'text-green-600');
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
            const productName = item.querySelector('h4').textContent.toLowerCase();
            const productCode = item.querySelector('p').textContent.toLowerCase();
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
        }
        
        if (historyModal) {
            historyModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeHistoryModal();
                }
            });
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