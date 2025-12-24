@extends('layouts.manajemen.index')

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')
    <!-- Main Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-2xl font-bold text-gray-900">Stok Produk Jadi</h2>
                <div class="flex gap-2">
                    <a href="{{ route('management.updateproduk.index') }}"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                        <i class="fas fa-clipboard-check mr-2"></i>Stok Produk
                    </a>
                    <button onclick="openAddProductModal()"
                        class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
                        <i class="fas fa-plus mr-2"></i> Produk Baru
                    </button>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Cari produk..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="">Tersedia</option>
                        <option value="rendah">Stok Rendah</option>
                        <option value="habis">Habis</option>
                    </select>
                    <select id="expiryFilter" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Semua</option>
                        <option value="expired_sangat_dekat">Expired < 3 Hari</option>
                        <option value="expired_dekat">Expired < 7 Hari</option>
                        <option value="expired">Sudah Expired</option>
                    </select>
                </div>
            </div>

            <!-- Product Table -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min. Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expired Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="productTableBody">
                            @forelse ($produk as $item)
                                @php
                                    // Generate SKU
                                    $sku = 'PROD-' . str_pad($item->id, 6, '0', STR_PAD_LEFT);
                                    
                                    // Hitung sisa hari dengan benar
                                    $kadaluarsa = \Carbon\Carbon::parse($item->kadaluarsa);
                                    $today = \Carbon\Carbon::today();
                                    $daysUntilExpired = $today->diffInDays($kadaluarsa, false);
                                    
                                    // Tentukan status expired
                                    $isExpired = $daysUntilExpired < 0;
                                    $isExpiredToday = $daysUntilExpired === 0;
                                    $isExpiredVerySoon = $daysUntilExpired > 0 && $daysUntilExpired <= 3; // Kurang dari 3 hari
                                    $isExpiredSoon = $daysUntilExpired > 3 && $daysUntilExpired <= 7;
                                    
                                    $stockStatus = $item->status_stok;
                                    
                                    // Warna untuk stok
                                    if ($stockStatus === 'habis') {
                                        $stockColor = 'text-red-600 font-medium';
                                        $stockBg = 'bg-red-100';
                                        $stockIcon = 'fas fa-times-circle';
                                        $stockText = 'Habis';
                                    } elseif ($stockStatus === 'rendah') {
                                        $stockColor = 'text-orange-600 font-medium';
                                        $stockBg = 'bg-orange-100';
                                        $stockIcon = 'fas fa-exclamation-triangle';
                                        $stockText = 'Rendah';
                                    } else {
                                        $stockColor = 'text-green-600 font-medium';
                                        $stockBg = 'bg-green-100';
                                        $stockIcon = 'fas fa-check-circle';
                                        $stockText = 'Aman';
                                    }
                                    
                                    // Warna untuk expired - PERUBAHAN UTAMA
                                    if ($isExpired) {
                                        $expiryColor = 'text-red-600 font-medium';
                                        $expiryBg = 'bg-red-100';
                                        $expiryIcon = 'fas fa-exclamation-triangle';
                                        $expiryText = 'Expired';
                                        $rowBgColor = 'bg-red-50'; // Warna merah untuk baris
                                    } elseif ($isExpiredVerySoon) {
                                        $expiryColor = 'text-red-600 font-medium';
                                        $expiryBg = 'bg-red-100';
                                        $expiryIcon = 'fas fa-exclamation-circle';
                                        $expiryText = $daysUntilExpired . ' hari';
                                        $rowBgColor = 'bg-red-50'; // Warna merah untuk baris
                                    } elseif ($isExpiredToday) {
                                        $expiryColor = 'text-orange-600 font-medium';
                                        $expiryBg = 'bg-orange-100';
                                        $expiryIcon = 'fas fa-exclamation-circle';
                                        $expiryText = 'Hari Ini';
                                        $rowBgColor = '';
                                    } elseif ($isExpiredSoon) {
                                        $expiryColor = 'text-orange-600 font-medium';
                                        $expiryBg = 'bg-orange-100';
                                        $expiryIcon = 'fas fa-clock';
                                        $expiryText = $daysUntilExpired . ' hari';
                                        $rowBgColor = '';
                                    } else {
                                        $expiryColor = 'text-green-600 font-medium';
                                        $expiryBg = 'bg-green-100';
                                        $expiryIcon = 'fas fa-check-circle';
                                        $expiryText = $daysUntilExpired . ' hari';
                                        $rowBgColor = '';
                                    }
                                @endphp
                                
                                <tr class="product-row {{ $rowBgColor }}"
                                    data-status="{{ $stockStatus }}"
                                    data-expiry="{{ $isExpired ? 'expired' : ($isExpiredVerySoon ? 'expired_very_soon' : ($isExpiredToday ? 'today' : ($isExpiredSoon ? 'soon' : 'safe'))) }}"
                                    data-expiry-days="{{ $daysUntilExpired }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            @php
                                                $iconColor = 'amber';
                                                $iconClass = 'fas fa-bread-slice';

                                                // Determine icon based on product name
                                                if (
                                                    str_contains(strtolower($item->nama), 'cokelat') ||
                                                    str_contains(strtolower($item->nama), 'chocolate')
                                                ) {
                                                    $iconColor = 'orange';
                                                    $iconClass = 'fas fa-cookie-bite';
                                                } elseif (
                                                    str_contains(strtolower($item->nama), 'keju') ||
                                                    str_contains(strtolower($item->nama), 'cheese')
                                                ) {
                                                    $iconColor = 'yellow';
                                                    $iconClass = 'fas fa-cheese';
                                                } elseif (str_contains(strtolower($item->nama), 'strawberry')) {
                                                    $iconColor = 'pink';
                                                    $iconClass = 'fas fa-ice-cream';
                                                } elseif (
                                                    str_contains(strtolower($item->nama), 'kismis') ||
                                                    str_contains(strtolower($item->nama), 'raisin')
                                                ) {
                                                    $iconColor = 'purple';
                                                    $iconClass = 'fas fa-candy-cane';
                                                } elseif (
                                                    str_contains(strtolower($item->nama), 'pisang') ||
                                                    str_contains(strtolower($item->nama), 'banana')
                                                ) {
                                                    $iconColor = 'yellow';
                                                    $iconClass = 'fas fa-drumstick-bite';
                                                } elseif (
                                                    str_contains(strtolower($item->nama), 'donat') ||
                                                    str_contains(strtolower($item->nama), 'donut')
                                                ) {
                                                    $iconColor = 'brown';
                                                    $iconClass = 'fas fa-circle';
                                                } elseif (str_contains(strtolower($item->nama), 'abon')) {
                                                    $iconColor = 'red';
                                                    $iconClass = 'fas fa-hotdog';
                                                } elseif (str_contains(strtolower($item->nama), 'sobek')) {
                                                    $iconColor = 'amber';
                                                    $iconClass = 'fas fa-bread-slice';
                                                } elseif (str_contains(strtolower($item->nama), 'croissant')) {
                                                    $iconColor = 'orange';
                                                    $iconClass = 'fas fa-moon';
                                                }
                                            @endphp
                                            <div
                                                class="w-10 h-10 bg-{{ $iconColor }}-100 rounded-lg flex items-center justify-center">
                                                <i class="{{ $iconClass }} text-{{ $iconColor }}-600"></i>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-900 block">{{ $item->nama }}</span>
                                                <!-- SKU Display -->
                                                <span class="text-xs text-gray-500">{{ $sku }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <span class="{{ $stockColor }}">{{ $item->stok }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $stockBg }} {{ $stockColor }}">
                                                <i class="{{ $stockIcon }} mr-1"></i>{{ $stockText }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->min_stok }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <div>
                                                <span class="text-sm {{ $expiryColor }}">
                                                    {{ \Carbon\Carbon::parse($item->kadaluarsa)->format('d M Y') }}
                                                </span>
                                                <div class="text-xs {{ $expiryColor }}">
                                                    @if($isExpired)
                                                        Sudah expired
                                                    @elseif($isExpiredToday)
                                                        Kadaluarsa hari ini
                                                    @elseif($isExpiredVerySoon)
                                                        <strong>Hanya {{ $daysUntilExpired }} hari lagi!</strong>
                                                    @else
                                                        {{ $daysUntilExpired }} hari lagi
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $expiryBg }} {{ $expiryColor }}">
                                                <i class="{{ $expiryIcon }}"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <!-- Tombol Detail -->
                                            <button onclick="showProductDetail({{ $item->id }})"
                                                class="text-blue-600 hover:text-blue-700" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <!-- Tombol Edit -->
                                            <button onclick="openEditProductModal({{ $item->id }})"
                                                class="text-green-600 hover:text-green-700" title="Edit Produk">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <!-- Tombol Hapus -->
                                            <button onclick="deleteProduct({{ $item->id }})"
                                                class="text-red-600 hover:text-red-700" title="Hapus Produk">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-box-open text-3xl mb-3 text-gray-300"></i>
                                        <p>Tidak ada produk ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PERBAIKAN: Tambahkan ini untuk debugging pagination -->
            @if($produk->count() > 0)
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="mb-2 sm:mb-0">
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $produk->firstItem() }}</span>
                                -
                                <span class="font-medium">{{ $produk->lastItem() }}</span>
                                dari
                                <span class="font-medium">{{ $produk->total() }}</span>
                                produk
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Tombol Previous --}}
                                @if ($produk->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <span class="sr-only">Sebelumnya</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $produk->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Sebelumnya</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Tombol Halaman --}}
                                @php
                                    $current = $produk->currentPage();
                                    $last = $produk->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $produk->url(1) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                                        <a href="{{ $produk->url($page) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                                    <a href="{{ $produk->url($last) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        {{ $last }}
                                    </a>
                                @endif

                                {{-- Tombol Next --}}
                                @if ($produk->hasMorePages())
                                    <a href="{{ $produk->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
    </main>

    <!-- Add Product Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Tambah Produk Baru</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="addProductForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="nama" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal</label>
                            <input type="number" name="stok" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok</label>
                            <input type="number" name="min_stok" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                        <input type="number" name="harga" value="0" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                        <input type="date" name="kadaluarsa" required min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeAddModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Edit Produk</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editProductForm" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="nama" id="edit_nama" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                            <input type="number" name="stok" id="edit_stok" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min. Stok</label>
                            <input type="number" name="min_stok" id="edit_min_stok" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                        <input type="number" name="harga" id="edit_harga" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kedaluwarsa</label>
                        <input type="date" name="kadaluarsa" id="edit_kadaluarsa" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal Detail Produk -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">Detail Produk - Log Stok</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="detailContent" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Generate SKU function
    function generateSKU(id) {
        return 'PROD-' + id.toString().padStart(6, '0');
    }

    // Format date function
    function formatDateForDetail(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 
                       'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[date.getDay()];
        const day = date.getDate();
        const monthName = months[date.getMonth()];
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        
        return `${dayName}, ${day} ${monthName} ${year} ${hours}:${minutes}`;
    }

    // Format simple date
    function formatSimpleDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const day = date.getDate();
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 
                       'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthName = months[date.getMonth()];
        const year = date.getFullYear();
        
        return `${day} ${monthName} ${year}`;
    }

    // Format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        showNotifications();
        filterProducts();
    });

    // Tampilkan notifikasi dari session
    function showNotifications() {
        @if(session('notifications'))
            const notifications = @json(session('notifications'));
            
            notifications.forEach(notification => {
                setTimeout(() => {
                    Swal.fire({
                        title: notification.title,
                        html: `
                            <div class="text-left">
                                <p class="mb-2">${notification.message}</p>
                                ${notification.products.length > 0 ? 
                                    `<div class="mt-3">
                                        <p class="text-sm font-medium mb-1">Daftar Produk:</p>
                                        <ul class="list-disc pl-5 text-sm">
                                            ${notification.products.map(product => `<li>${product}</li>`).join('')}
                                        </ul>
                                    </div>` : ''
                                }
                            </div>
                        `,
                        icon: notification.type === 'danger' ? 'error' : 'warning',
                        confirmButtonColor: notification.type === 'danger' ? '#EF4444' : '#F59E0B',
                        confirmButtonText: 'Mengerti'
                    });
                }, 500);
            });
        @endif
    }

    // Modal functions
    function openAddProductModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.getElementById('addProductForm').reset();
        document.body.style.overflow = 'auto';
    }

    function openEditProductModal(productId) {
        fetch(`/management/produk/${productId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Produk tidak ditemukan');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_nama').value = data.nama;
                document.getElementById('edit_stok').value = data.stok;
                document.getElementById('edit_min_stok').value = data.min_stok;
                document.getElementById('edit_harga').value = data.harga;
                document.getElementById('edit_kadaluarsa').value = data.kadaluarsa;

                document.getElementById('editModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', error.message || 'Gagal memuat data produk', 'error');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Enhanced Show Product Detail dengan log detail sesuai permintaan
    async function showProductDetail(productId) {
        try {
            Swal.fire({
                title: 'Memuat...',
                text: 'Sedang memuat detail produk...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const response = await fetch(`/management/produk/${productId}`);
            const product = await response.json();
            
            Swal.close();
            
            if (product.error) {
                Swal.fire('Error', product.error, 'error');
                return;
            }
            
            const sku = generateSKU(product.id);
            const history = product.update_stok_history || [];
            
            // Urutkan history berdasarkan tanggal terbaru
            history.sort((a, b) => new Date(b.tanggal_update) - new Date(a.tanggal_update));
            
            let content = `
                <div class="space-y-6">
                    <!-- Product Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg p-6 text-white">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h2 class="text-2xl font-bold mb-2">${product.nama}</h2>
                                <div class="flex items-center space-x-3">
                                    <span class="bg-blue-800 bg-opacity-30 px-3 py-1 rounded-full text-sm">
                                        <i class="fas fa-barcode mr-2"></i>${sku}
                                    </span>
                                    <span class="text-blue-200 text-sm">ID: ${product.id}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold">${product.stok} pcs</p>
                                <p class="text-blue-100">Stok Akhir</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock History -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Log Perubahan Stok (FEFO System)</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Awal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perubahan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Akhir</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluarsa</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;
            
            if (history.length > 0) {
                history.forEach(record => {
                    const isIncrease = record.stok_baru > 0;
                    const changeColor = isIncrease ? 'text-green-600' : 'text-red-600';
                    const changeIcon = isIncrease ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
                    const changeSign = isIncrease ? '+' : '';
                    
                    // Tentukan warna badge berdasarkan sumber - PERBAIKAN UNTUK TRANSAKSI
                    let badgeClass = 'bg-gray-100 text-gray-800';
                    if (record.sumber === 'Penambahan dari halaman stok produk') {
                        badgeClass = 'bg-green-100 text-green-800';
                    } else if (record.sumber === 'Update dari halaman produk') {
                        badgeClass = 'bg-blue-100 text-blue-800';
                    } else if (record.sumber === 'Update dari halaman update stok produk') {
                        badgeClass = 'bg-purple-100 text-purple-800';
                    } else if (record.sumber === 'Pengurangan dari transaksi') {
                        badgeClass = 'bg-red-100 text-red-800';
                    } else if (record.sumber === 'Penambahan dari daily update') {
                        badgeClass = 'bg-yellow-100 text-yellow-800';
                    } else if (record.sumber === 'Update stok manual') {
                        badgeClass = 'bg-indigo-100 text-indigo-800';
                    }
                    
                    content += `
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3 text-sm">
                                ${formatDateForDetail(record.tanggal_update)}
                            </td>
                            <td class="px-4 py-3 text-sm">${record.stok_awal} pcs</td>
                            <td class="px-4 py-3 text-sm font-bold ${changeColor}">
                                <i class="${changeIcon} mr-1"></i>
                                ${changeSign}${record.stok_baru} pcs
                            </td>
                            <td class="px-4 py-3 text-sm font-bold">${record.total_stok} pcs</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full ${badgeClass}">
                                    ${record.sumber}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                ${record.kadaluarsa ? formatSimpleDate(record.kadaluarsa) : '-'}
                            </td>
                        </tr>
                    `;
                });
            } else {
                content += `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                            <p>Belum ada riwayat stok untuk produk ini.</p>
                            <p class="text-sm mt-1">Riwayat akan muncul setelah ada penambahan atau perubahan stok.</p>
                        </td>
                    </tr>
                `;
            }
            
            content += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Stock Summary -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Stok</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">+${product.total_increase || 0}</div>
                                <div class="text-sm text-gray-600">Total Masuk</div>
                                <div class="text-xs text-gray-500">Penambahan stok</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">-${product.total_decrease || 0}</div>
                                <div class="text-sm text-gray-600">Total Keluar</div>
                                <div class="text-xs text-gray-500">Pengurangan stok</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold ${product.net_change >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${product.net_change >= 0 ? '+' : ''}${product.net_change || 0}
                                </div>
                                <div class="text-sm text-gray-600">Perubahan Bersih</div>
                                <div class="text-xs text-gray-500">Net change</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500">Sistem: FEFO (First Expired First Out)</p>
                                <p class="text-xs text-gray-400">Produk dengan kadaluarsa terdekat akan diprioritaskan</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Total Riwayat: ${history.length} perubahan</p>
                                <p class="text-xs text-gray-400">${new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detailContent').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
            
        } catch (error) {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat detail produk', 'error');
        }
    }

    // Form submission handlers
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Convert numbers
        data.stok = parseInt(data.stok);
        data.min_stok = parseInt(data.min_stok);
        data.harga = parseInt(data.harga);
        
        // Validate kadaluarsa
        const kadaluarsa = new Date(data.kadaluarsa);
        const today = new Date();
        if (kadaluarsa < today) {
            Swal.fire('Error', 'Tanggal kadaluarsa tidak boleh kurang dari hari ini!', 'error');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan produk...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/management/produk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message || 'Produk berhasil ditambahkan',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                    }).then(() => {
                        closeAddModal();
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal menambahkan produk', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan produk', 'error');
            });
    });

    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const productId = document.getElementById('edit_id').value;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Remove _token and _method from data
        delete data._token;
        delete data._method;

        // Convert numbers
        data.stok = parseInt(data.stok);
        data.min_stok = parseInt(data.min_stok);
        data.harga = parseInt(data.harga);
        
        // Validate kadaluarsa
        const kadaluarsa = new Date(data.kadaluarsa);
        const today = new Date();
        if (kadaluarsa < today) {
            Swal.fire('Error', 'Tanggal kadaluarsa tidak boleh kurang dari hari ini!', 'error');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Mengupdate...',
            text: 'Sedang mengupdate produk...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/management/produk/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: data.message || 'Produk berhasil diupdate',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                    }).then(() => {
                        closeEditModal();
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal mengupdate produk', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat mengupdate produk', 'error');
            });
    });

    // Delete product
    function deleteProduct(productId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Produk yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang menghapus produk...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`/management/produk/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: data.message || 'Produk berhasil dihapus.',
                                icon: 'success',
                                confirmButtonColor: '#10B981',
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menghapus produk', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    });
            }
        });
    }

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterProducts();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterProducts();
    });

    document.getElementById('expiryFilter').addEventListener('change', function() {
        filterProducts();
    });

    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const expiryFilter = document.getElementById('expiryFilter').value;
        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const productName = row.querySelector('td:first-child .font-medium').textContent.toLowerCase();
            const sku = row.querySelector('td:first-child .text-xs').textContent.toLowerCase();
            const productStatus = row.getAttribute('data-status');
            const expiryStatus = row.getAttribute('data-expiry');
            const expiryDays = parseInt(row.getAttribute('data-expiry-days'));

            const matchesSearch = productName.includes(searchTerm) || sku.includes(searchTerm);
            const matchesStatus = !statusFilter || productStatus === statusFilter;
            
            let matchesExpiry = true;
            if (expiryFilter === 'expired_sangat_dekat') {
                matchesExpiry = expiryDays > 0 && expiryDays <= 3;
            } else if (expiryFilter === 'expired_dekat') {
                matchesExpiry = expiryDays > 0 && expiryDays <= 7;
            } else if (expiryFilter === 'expired') {
                matchesExpiry = expiryDays < 0;
            }

            if (matchesSearch && matchesStatus && matchesExpiry) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        const detailModal = document.getElementById('detailModal');
        
        if (e.target === addModal) {
            closeAddModal();
        }
        if (e.target === editModal) {
            closeEditModal();
        }
        if (e.target === detailModal) {
            closeDetailModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDetailModal();
        }
    });
</script>
@endsection