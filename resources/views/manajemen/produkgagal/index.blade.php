@extends('layouts.manajemen.index')

@php
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')
    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[9999] space-y-2" style="max-width: 400px;">
        <!-- Toast notifications will be inserted here -->
    </div>

    <!-- Main Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Produk Gagal</h2>
                    <p class="text-gray-600">Manajemen produk gagal dan analisis kerugian bahan</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="openAddModal()"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-700 text-white text-sm font-medium rounded-lg hover:from-red-600 hover:to-red-800 shadow-sm transition-all">
                        <i class="fas fa-times-circle mr-2"></i>
                        Input Produk Gagal
                    </button>
                </div>
            </div>

            {{-- <!-- Analytics Dashboard -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Produk Gagal</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $produkGagal->total() }}</p>
                            <p class="text-xs text-gray-500">Data keseluruhan</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Unit Gagal</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalUnitGagal ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Jumlah keseluruhan</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bahan Terpakai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalBahanTerpakai ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Jenis bahan</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-utensils text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Kerugian</p>
                            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalKerugian ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">Estimasi biaya</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Search & Filter Panel -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <form action="{{ route('management.produkgagal.index') }}" method="GET" class="flex gap-2">
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="search" id="searchInput" placeholder="Cari produk gagal..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                    value="{{ request('search') }}">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('management.produkgagal.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                    <div class="flex gap-3">
                        <select id="dateFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                            onchange="filterByDate()">
                            <option value="">Semua Tanggal</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                        </select>
                        <select id="produkFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                            onchange="filterByProduk()">
                            <option value="">Semua Produk</option>
                            @foreach($produkList ?? [] as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Table -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Produk Gagal</h3>
                        <div class="flex items-center space-x-3">
                            <button onclick="toggleView('table')" id="tableViewBtn"
                                class="p-2 text-gray-500 hover:text-gray-700 bg-gray-100 rounded-lg">
                                <i class="fas fa-table"></i>
                            </button>
                            <button onclick="toggleView('grid')" id="gridViewBtn"
                                class="p-2 text-gray-500 hover:text-gray-700 rounded-lg">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table View -->
                <div id="tableView" class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">
                                    Produk Gagal
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">
                                    Jumlah
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">
                                    Tanggal
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">
                                    Bahan Terpakai
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">
                                    Kerugian
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-200">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="produkGagalTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse ($produkGagal as $item)
                                <tr class="hover:bg-red-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gradient-to-r from-red-400 to-red-600 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                                                <i class="fas fa-times-circle text-white"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $item->nama_produk }}</div>
                                                @if($item->produk && $item->produk->sku)
                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                        <i class="fas fa-barcode mr-1"></i>{{ $item->produk->sku }}
                                                    </div>
                                                @endif
                                                @if($item->keterangan)
                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                        <i class="fas fa-sticky-note mr-1"></i>{{ Str::limit($item->keterangan, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold bg-red-100 text-red-800 rounded-full shadow-sm">
                                            <i class="fas fa-box mr-1.5"></i>{{ $item->jumlah_gagal }} pcs
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($item->tanggal_gagal)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $item->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <i class="fas fa-utensils mr-1.5"></i>{{ $item->detail->count() }} bahan
                                        </div>
                                        <div class="text-xs text-red-600">
                                            Rp {{ number_format($item->getTotalBiayaBahanAttribute(), 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-red-700">
                                            Rp {{ number_format($item->getTotalBiayaBahanAttribute(), 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button onclick="showDetail({{ $item->id }})" 
                                                class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="deleteProdukGagal({{ $item->id }})" 
                                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-times-circle text-3xl mb-3 text-gray-300"></i>
                                        <p>
                                            @if(request()->has('search') && !empty(request('search')))
                                                Tidak ada produk gagal ditemukan dengan kata kunci "{{ request('search') }}"
                                            @else
                                                Tidak ada data produk gagal.
                                            @endif
                                        </p>
                                        <p class="text-sm mt-2">Klik "Input Produk Gagal" untuk menambah data baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Grid View -->
                <div id="gridView" class="hidden p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="produkGagalGrid">
                        @forelse ($produkGagal as $item)
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-r from-red-400 to-red-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-times-circle text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $item->nama_produk }}</h3>
                                                @if($item->produk && $item->produk->sku)
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">{{ $item->produk->sku }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                                            <div class="text-gray-600 text-xs">Jumlah Gagal</div>
                                            <div class="font-bold text-red-900">{{ $item->jumlah_gagal }} pcs</div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                                            <div class="text-gray-600 text-xs">Tanggal</div>
                                            <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal_gagal)->format('d M') }}</div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                                            <div class="text-gray-600 text-xs">Bahan</div>
                                            <div class="font-bold text-gray-900">{{ $item->detail->count() }}</div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                                            <div class="text-gray-600 text-xs">Kerugian</div>
                                            <div class="font-bold text-red-600">Rp {{ number_format($item->getTotalBiayaBahanAttribute(), 0, ',', '.') }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="showDetail({{ $item->id }})" class="text-blue-600 hover:text-blue-700" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="deleteProdukGagal({{ $item->id }})" class="text-red-600 hover:text-red-700" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                <i class="fas fa-times-circle text-3xl mb-3 text-gray-300"></i>
                                <p>Tidak ada data produk gagal.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                @if($produkGagal->count() > 0)
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between">
                            <div class="mb-2 sm:mb-0">
                                <p class="text-sm text-gray-700">
                                    Menampilkan
                                    <span class="font-medium">{{ $produkGagal->firstItem() }}</span>
                                    -
                                    <span class="font-medium">{{ $produkGagal->lastItem() }}</span>
                                    dari
                                    <span class="font-medium">{{ $produkGagal->total() }}</span>
                                    data
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    {{-- Tombol Previous --}}
                                    @if ($produkGagal->onFirstPage())
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <span class="sr-only">Sebelumnya</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $produkGagal->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Sebelumnya</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    {{-- Tombol Halaman --}}
                                    @php
                                        $current = $produkGagal->currentPage();
                                        $last = $produkGagal->lastPage();
                                        $start = max(1, $current - 2);
                                        $end = min($last, $current + 2);
                                    @endphp

                                    @if($start > 1)
                                        <a href="{{ $produkGagal->url(1) }}{{ request('search') ? '&search=' . request('search') : '' }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                                            <span class="relative inline-flex items-center px-4 py-2 border border-red-500 bg-red-50 text-sm font-medium text-red-600">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $produkGagal->url($page) }}{{ request('search') ? '&search=' . request('search') : '' }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                                        <a href="{{ $produkGagal->url($last) }}{{ request('search') ? '&search=' . request('search') : '' }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            {{ $last }}
                                        </a>
                                    @endif

                                    {{-- Tombol Next --}}
                                    @if ($produkGagal->hasMorePages())
                                        <a href="{{ $produkGagal->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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

    <!-- Add Produk Gagal Modal -->
    <div id="addModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-lg max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Input Produk Gagal</h3>
                <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="addForm" class="px-6 py-6 overflow-auto" style="max-height:calc(90vh - 120px);">
                @csrf
                <!-- Informasi Produk Gagal -->
                <div class="bg-gradient-to-br from-red-50 to-white rounded-xl p-6 mb-6 border border-red-100 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-times-circle text-white"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">Informasi Produk Gagal</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Produk *</label>
                            <select id="produk_id" name="produk_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                onchange="loadResepProduk()">
                                <option value="">Pilih Produk</option>
                            </select>
                            <input type="hidden" name="nama_produk" id="nama_produk">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Gagal *</label>
                            <div class="relative">
                                <input type="number" id="jumlah_gagal" name="jumlah_gagal" required min="1"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                    placeholder="Contoh: 10"
                                    onchange="updateJumlahBahan()">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500">pcs</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Gagal *</label>
                            <input type="date" name="tanggal_gagal" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                placeholder="Alasan kegagalan (opsional)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Bahan Baku dari Resep -->
                <div class="bg-gradient-to-br from-amber-50 to-white rounded-xl p-6 mb-6 border border-amber-100 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-basket text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Bahan Baku yang Digunakan</h4>
                        </div>
                        <span id="resepInfo" class="text-sm text-gray-500">Pilih produk terlebih dahulu</span>
                    </div>

                    <div id="loadingBahan" class="hidden text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-amber-500 mb-3"></div>
                        <p class="text-gray-500">Memuat data resep...</p>
                    </div>

                    <div id="bahanContainer" class="space-y-4 hidden">
                        <!-- Bahan akan ditampilkan di sini -->
                    </div>

                    <div id="noResepMessage" class="hidden text-center py-8 bg-gray-50 rounded-lg">
                        <i class="fas fa-utensils text-gray-300 text-3xl mb-3"></i>
                        <p class="text-gray-500" id="resepMessage"></p>
                    </div>

                    <div id="warningMessage" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-lg mt-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                            <div>
                                <p class="text-sm text-yellow-800" id="warningText"></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <p class="text-sm text-blue-800">
                                    <strong>Informasi:</strong> Sistem akan otomatis menampilkan bahan baku dari resep produk yang dipilih.
                                    Jumlah bahan akan dikalikan dengan jumlah produk gagal.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white z-10 pb-4">
                    <button type="button" onclick="closeAddModal()"
                        class="flex-1 px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" id="submitButton" disabled
                        class="flex-1 px-5 py-2.5 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-lg cursor-not-allowed font-medium shadow-sm">
                        Simpan Produk Gagal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Detail Produk Gagal</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="detailContent" class="px-6 py-6 overflow-auto" style="max-height:calc(90vh - 120px);">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="confirmDeleteModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Hapus Produk Gagal?</h3>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Data produk gagal dan pengembalian stok bahan akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex space-x-3">
                    <button onclick="closeConfirmDelete()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                        Batal
                    </button>
                    <button onclick="confirmDeleteProdukGagal()" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let produkList = [];
    let currentResep = null;
    let bahanBakuList = [];
    let currentView = 'table';
    let deleteProdukGagalId = null;

    // Toast Notification System
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        
        const toastId = 'toast-' + Date.now();
        const icons = {
            success: '<i class="fas fa-check-circle text-green-500"></i>',
            error: '<i class="fas fa-exclamation-circle text-red-500"></i>',
            warning: '<i class="fas fa-exclamation-triangle text-yellow-500"></i>',
            info: '<i class="fas fa-info-circle text-blue-500"></i>'
        };
        
        const colors = {
            success: 'bg-green-50 border-green-200',
            error: 'bg-red-50 border-red-200',
            warning: 'bg-yellow-50 border-yellow-200',
            info: 'bg-blue-50 border-blue-200'
        };
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `${colors[type]} border-l-4 rounded-lg shadow-lg p-4 mb-2 transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 text-xl">
                    ${icons[type]}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <button onclick="removeToast('${toastId}')" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 10);
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                removeToast(toastId);
            }, duration);
        }
    }
    
    function removeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (!toast) return;
        
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }

    // Load data untuk form
    async function loadFormData() {
        try {
            const response = await fetch('{{ route("management.produkgagal.create") }}', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                produkList = data.produk;
                populateProdukDropdown();
            } else {
                throw new Error(data.message || 'Gagal memuat data');
            }
        } catch (error) {
            console.error('Error loading form data:', error);
            showToast('Gagal memuat data produk', 'error');
        }
    }

    // Populate produk dropdown
    function populateProdukDropdown() {
        const select = document.getElementById('produk_id');
        select.innerHTML = '<option value="">Pilih Produk</option>';
        
        produkList.forEach(produk => {
            const option = document.createElement('option');
            option.value = produk.id;
            option.textContent = `${produk.nama} (${produk.sku || 'No SKU'})`;
            select.appendChild(option);
        });
    }

    // Load resep berdasarkan produk yang dipilih
    async function loadResepProduk() {
        const produkId = document.getElementById('produk_id').value;
        const produkSelect = document.getElementById('produk_id');
        const selectedOption = produkSelect.options[produkSelect.selectedIndex];
        
        if (produkId) {
            // Set nama produk untuk hidden input
            const namaProduk = selectedOption.textContent.split(' (')[0];
            document.getElementById('nama_produk').value = namaProduk;
            
            // Show loading
            document.getElementById('loadingBahan').classList.remove('hidden');
            document.getElementById('bahanContainer').classList.add('hidden');
            document.getElementById('noResepMessage').classList.add('hidden');
            document.getElementById('warningMessage').classList.add('hidden');
            
            try {
                // Menggunakan route create dengan parameter produk_id
                const response = await fetch('{{ route("management.produkgagal.create") }}?produk_id=' + produkId, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                document.getElementById('loadingBahan').classList.add('hidden');
                
                if (data.success && data.bahan_resep) {
                    currentResep = data;
                    bahanBakuList = data.bahan_resep;
                    
                    // Tampilkan bahan baku
                    displayBahanBaku();
                    
                    document.getElementById('resepInfo').textContent = `Resep: ${data.produk_nama}`;
                    document.getElementById('submitButton').disabled = false;
                    document.getElementById('submitButton').className = 'flex-1 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-700 text-white rounded-lg hover:from-red-600 hover:to-red-800 font-medium shadow-sm hover:shadow';
                    
                    // Cek jika ada bahan dengan warning
                    const hasWarning = data.bahan_resep.some(b => b.warning);
                    if (hasWarning) {
                        document.getElementById('warningMessage').classList.remove('hidden');
                        document.getElementById('warningText').textContent = 'Beberapa bahan baku tidak ditemukan di database. Bahan tersebut tidak akan mempengaruhi stok.';
                    }
                    
                } else {
                    document.getElementById('noResepMessage').classList.remove('hidden');
                    document.getElementById('resepMessage').textContent = data.message || 'Produk yang dipilih tidak memiliki resep';
                    document.getElementById('resepInfo').textContent = 'Tidak ada resep';
                    currentResep = null;
                    bahanBakuList = [];
                    document.getElementById('submitButton').disabled = true;
                    document.getElementById('submitButton').className = 'flex-1 px-5 py-2.5 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-lg cursor-not-allowed font-medium';
                }
            } catch (error) {
                console.error('Error loading resep:', error);
                document.getElementById('loadingBahan').classList.add('hidden');
                document.getElementById('noResepMessage').classList.remove('hidden');
                document.getElementById('resepMessage').textContent = 'Terjadi kesalahan saat memuat resep';
                document.getElementById('resepInfo').textContent = 'Error memuat resep';
                currentResep = null;
                bahanBakuList = [];
            }
        } else {
            resetBahanBaku();
        }
    }

    // Display bahan baku dari resep
    function displayBahanBaku() {
        const container = document.getElementById('bahanContainer');
        container.innerHTML = '';
        
        if (!bahanBakuList || bahanBakuList.length === 0) {
            container.classList.add('hidden');
            document.getElementById('noResepMessage').classList.remove('hidden');
            document.getElementById('resepMessage').textContent = 'Tidak ada bahan baku ditemukan';
            return;
        }
        
        const jumlahGagal = parseInt(document.getElementById('jumlah_gagal').value) || 1;
        
        bahanBakuList.forEach((bahan, index) => {
            const jumlahTotal = bahan.quantity_per_produk * jumlahGagal;
            const hasWarning = bahan.warning || false;
            const bgColor = hasWarning ? 'bg-yellow-50' : 'bg-white';
            const borderColor = hasWarning ? 'border-yellow-200' : 'border-gray-200';
            
            // Format jumlah untuk display tanpa desimal jika tidak perlu
            const formatQuantity = (qty) => {
                if (qty % 1 === 0) return qty.toString();
                return qty.toFixed(3).replace(/\.?0+$/, '');
            };
            
            // Format stok display
            const formatStokDisplay = (stok, satuan) => {
                if (stok >= 1000 && satuan === 'gram') {
                    return `${(stok / 1000).toFixed(1).replace(/\.0$/, '')} kg`;
                }
                if (stok >= 1000 && satuan === 'ml') {
                    return `${(stok / 1000).toFixed(1).replace(/\.0$/, '')} liter`;
                }
                return `${formatQuantity(stok)} ${satuan}`;
            };
            
            const row = document.createElement('div');
            row.className = `bahan-row ${bgColor} p-4 rounded-lg border ${borderColor}`;
            row.dataset.index = index;
            
            row.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-semibold ${hasWarning ? 'text-yellow-700' : 'text-gray-700'} mb-1">
                            Bahan Baku ${hasWarning ? '(⚠ Tidak ditemukan)' : ''}
                        </label>
                        <input type="text" value="${bahan.nama}" readonly
                            class="w-full px-3 py-2 text-sm border ${hasWarning ? 'border-yellow-300 bg-yellow-50' : 'border-gray-300 bg-gray-50'} rounded-lg">
                        <input type="hidden" name="bahan_baku[${index}][id]" value="${bahan.id}">
                        <input type="hidden" name="bahan_baku[${index}][unit]" value="${bahan.unit}">
                        ${hasWarning ? '<input type="hidden" name="bahan_baku[' + index + '][warning]" value="true">' : ''}
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Per 1 Produk</label>
                        <div class="text-sm font-medium text-gray-900 bg-gray-50 px-3 py-2 rounded-lg text-center">
                            ${formatQuantity(bahan.quantity_per_produk)} ${bahan.unit}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Digunakan *</label>
                        <div class="relative">
                            <input type="text" name="bahan_baku[${index}][jumlah_digunakan]" 
                                value="${formatQuantity(jumlahTotal)}" step="0.001" min="0.001" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 jumlah-input"
                                oninput="validateStock(${index})" ${hasWarning ? 'readonly' : ''}>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 text-sm">${bahan.unit}</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Stok Tersedia</label>
                        <div class="text-sm font-medium ${bahan.stok > 0 ? 'text-green-600' : 'text-red-600'} bg-gray-50 px-3 py-2 rounded-lg text-center">
                            ${formatStokDisplay(bahan.stok, bahan.satuan_kecil)}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs" id="stockInfo_${index}"></div>
                    </div>
                </div>
            `;
            
            container.appendChild(row);
        });
        
        container.classList.remove('hidden');
        validateAllStocks();
    }

    // Update jumlah bahan ketika jumlah gagal berubah
    function updateJumlahBahan() {
        if (!currentResep || !bahanBakuList.length) return;
        
        const jumlahGagal = parseInt(document.getElementById('jumlah_gagal').value) || 1;
        
        bahanBakuList.forEach((bahan, index) => {
            if (bahan.warning) return; // Skip bahan dengan warning
            
            const input = document.querySelector(`input[name="bahan_baku[${index}][jumlah_digunakan]"]`);
            if (input && !input.readOnly) {
                const jumlahTotal = bahan.quantity_per_produk * jumlahGagal;
                // Format tanpa desimal jika tidak perlu
                if (jumlahTotal % 1 === 0) {
                    input.value = jumlahTotal.toString();
                } else {
                    input.value = jumlahTotal.toFixed(3).replace(/\.?0+$/, '');
                }
                validateStock(index);
            }
        });
    }

    // Validate stock untuk satu bahan
    function validateStock(index) {
        const input = document.querySelector(`input[name="bahan_baku[${index}][jumlah_digunakan]"]`);
        const infoDiv = document.getElementById(`stockInfo_${index}`);
        
        if (!input || !infoDiv || input.readOnly) return true;
        
        const bahan = bahanBakuList[index];
        if (!bahan || bahan.warning) return true;
        
        const jumlah = parseFloat(input.value) || 0;
        const unit = bahan.unit;
        
        // Konversi ke satuan kecil untuk validasi
        let jumlahDalamKecil = jumlah;
        if (unit === bahan.satuan_besar) {
            jumlahDalamKecil = jumlah * bahan.konversi;
        }
        
        if (jumlahDalamKecil > bahan.stok) {
            infoDiv.innerHTML = `<p class="text-red-500 text-xs font-medium">⚠️ Stok tidak cukup! Maks: ${bahan.stok} ${bahan.satuan_kecil}</p>`;
            return false;
        } else if (jumlahDalamKecil <= 0) {
            infoDiv.innerHTML = `<p class="text-yellow-500 text-xs font-medium">⚠️ Jumlah harus lebih dari 0</p>`;
            return false;
        } else {
            infoDiv.innerHTML = `<p class="text-green-500 text-xs font-medium">✓ Stok tersedia</p>`;
            return true;
        }
    }

    // Validate semua stok
    function validateAllStocks() {
        let allValid = true;
        
        bahanBakuList.forEach((bahan, index) => {
            if (!bahan.warning && !validateStock(index)) {
                allValid = false;
            }
        });
        
        return allValid;
    }

    // Reset bahan baku
    function resetBahanBaku() {
        document.getElementById('bahanContainer').innerHTML = '';
        document.getElementById('bahanContainer').classList.add('hidden');
        document.getElementById('noResepMessage').classList.add('hidden');
        document.getElementById('warningMessage').classList.add('hidden');
        document.getElementById('resepInfo').textContent = 'Pilih produk terlebih dahulu';
        currentResep = null;
        bahanBakuList = [];
        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').className = 'flex-1 px-5 py-2.5 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-lg cursor-not-allowed font-medium';
    }

    // View toggle
    function toggleView(view) {
        currentView = view;
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const tableBtn = document.getElementById('tableViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');

        if (view === 'table') {
            if (tableView) tableView.classList.remove('hidden');
            if (gridView) gridView.classList.add('hidden');
            if (tableBtn) {
                tableBtn.classList.add('bg-gray-100');
                tableBtn.classList.remove('text-gray-500');
                tableBtn.classList.add('text-gray-700');
            }
            if (gridBtn) {
                gridBtn.classList.remove('bg-gray-100');
                gridBtn.classList.remove('text-gray-700');
                gridBtn.classList.add('text-gray-500');
            }
        } else {
            if (tableView) tableView.classList.add('hidden');
            if (gridView) gridView.classList.remove('hidden');
            if (gridBtn) {
                gridBtn.classList.add('bg-gray-100');
                gridBtn.classList.remove('text-gray-500');
                gridBtn.classList.add('text-gray-700');
            }
            if (tableBtn) {
                tableBtn.classList.remove('bg-gray-100');
                tableBtn.classList.remove('text-gray-700');
                tableBtn.classList.add('text-gray-500');
            }
        }
    }

    // Filter functions
    function filterByDate() {
        // Implementasi filter berdasarkan tanggal
        console.log('Filter by date');
    }

    function filterByProduk() {
        // Implementasi filter berdasarkan produk
        console.log('Filter by produk');
    }

    // Modal functions
    function openAddModal() {
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        loadFormData();
    }

    function closeAddModal() {
        const modal = document.getElementById('addModal');
        modal.classList.add('hidden');
        const form = document.getElementById('addForm');
        if (form) form.reset();
        resetBahanBaku();
        document.body.style.overflow = 'auto';
    }

    // Detail modal functions
    async function showDetail(id) {
        try {
            const response = await fetch(`{{ route("management.produkgagal.index") }}/${id}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error('Gagal memuat data');
            
            const data = await response.json();
            
            if (data.success) {
                const item = data.data;
                
                let detailHTML = `
                    <div class="space-y-6">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-red-500 to-red-700 rounded-xl p-6 text-white">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h2 class="text-2xl font-bold mb-2">${item.nama_produk}</h2>
                                    <div class="flex items-center space-x-3">
                                        <span class="bg-red-800 bg-opacity-30 px-3 py-1 rounded-full text-sm">
                                            <i class="fas fa-times-circle mr-2"></i>Produk Gagal
                                        </span>
                                        <span class="text-red-200 text-sm">ID: PG-${item.id.toString().padStart(6, '0')}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold">${item.jumlah_gagal} pcs</p>
                                    <p class="text-red-100">Jumlah Gagal</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tanggal Gagal:</span>
                                        <span class="font-medium">${item.formatted_tanggal}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Dibuat Oleh:</span>
                                        <span class="font-medium">${item.created_by || 'System'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Waktu Input:</span>
                                        <span class="font-medium">${item.created_at}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Kerugian:</span>
                                        <span class="font-medium text-red-600">Rp ${item.total_biaya}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Keterangan</h4>
                                <p class="text-gray-700">${item.keterangan || 'Tidak ada keterangan'}</p>
                            </div>
                        </div>
                        
                        <!-- Bahan Baku -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Bahan Baku yang Digunakan</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bahan Baku</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dalam Satuan Kecil</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sebelum</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sesudah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;
                
                if (item.detail && item.detail.length > 0) {
                    item.detail.forEach(detail => {
                        const satuanText = detail.satuan === 'besar' ? detail.satuan_besar : detail.satuan_kecil;
                        detailHTML += `
                            <tr class="border-t border-gray-100">
                                <td class="px-4 py-3 text-sm font-medium">${detail.nama_bahan}</td>
                                <td class="px-4 py-3 text-sm">${detail.jumlah_digunakan}</td>
                                <td class="px-4 py-3 text-sm">${satuanText}</td>
                                <td class="px-4 py-3 text-sm font-bold">${detail.jumlah_dalam_kecil} ${detail.satuan_kecil}</td>
                                <td class="px-4 py-3 text-sm">${detail.stok_sebelum} ${detail.satuan_kecil}</td>
                                <td class="px-4 py-3 text-sm font-bold ${detail.stok_sesudah < detail.stok_sebelum * 0.2 ? 'text-red-600' : ''}">
                                    ${detail.stok_sesudah} ${detail.satuan_kecil}
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    detailHTML += `
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data bahan baku
                            </td>
                        </tr>
                    `;
                }
                
                detailHTML += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                <p><strong>Catatan:</strong> Data ini mencatat produk gagal dan pengurangan stok bahan baku yang terjadi.</p>
                                <p class="mt-1 text-xs text-gray-400">Sistem akan otomatis mengurangi stok bahan baku sesuai data di atas.</p>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('detailContent').innerHTML = detailHTML;
                document.getElementById('detailModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                throw new Error(data.message || 'Gagal memuat detail');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat memuat detail', 'error');
        }
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Delete confirmation
    function deleteProdukGagal(id) {
        deleteProdukGagalId = id;
        document.getElementById('confirmDeleteModal').classList.remove('hidden');
    }

    function closeConfirmDelete() {
        deleteProdukGagalId = null;
        document.getElementById('confirmDeleteModal').classList.add('hidden');
    }

    async function confirmDeleteProdukGagal() {
        if (!deleteProdukGagalId) return;
        
        try {
            const response = await fetch(`{{ route("management.produkgagal.index") }}/${deleteProdukGagalId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            closeConfirmDelete();
            
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(result.message || 'Gagal menghapus produk gagal', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            closeConfirmDelete();
            showToast('Terjadi kesalahan saat menghapus', 'error');
        }
    }

    // Form submission
    document.getElementById('addForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validasi semua stok
        if (!validateAllStocks()) {
            showToast('Ada bahan baku dengan stok tidak cukup. Silahkan periksa kembali.', 'error');
            return;
        }
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Convert bahan_baku to array
        const bahanBakuArray = [];
        const bahanInputs = document.querySelectorAll('.bahan-row');
        
        bahanInputs.forEach((row, index) => {
            const id = row.querySelector(`[name="bahan_baku[${index}][id]"]`).value;
            const jumlahInput = row.querySelector(`[name="bahan_baku[${index}][jumlah_digunakan]"]`);
            const unit = row.querySelector(`[name="bahan_baku[${index}][unit]"]`).value;
            
            if (id && jumlahInput && jumlahInput.value) {
                const jumlah = parseFloat(jumlahInput.value) || 0;
                if (jumlah > 0) {
                    const bahan = {
                        id: parseInt(id),
                        jumlah_digunakan: jumlah,
                        unit: unit
                    };
                    
                    // Tambahkan warning flag jika ada
                    const warningInput = row.querySelector(`[name="bahan_baku[${index}][warning]"]`);
                    if (warningInput && warningInput.value === 'true') {
                        bahan.warning = true;
                    }
                    
                    bahanBakuArray.push(bahan);
                }
            }
        });
        
        data.bahan_baku = bahanBakuArray;
        
        // Validate
        if (bahanBakuArray.length === 0) {
            showToast('Tidak ada bahan baku yang digunakan', 'error');
            return;
        }

        try {
            const response = await fetch('{{ route("management.produkgagal.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    closeAddModal();
                    location.reload();
                }, 1500);
            } else {
                showToast(result.message || 'Gagal menyimpan produk gagal', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menyimpan', 'error');
        }
    });

    // Close modals
    window.addEventListener('click', function(e) {
        if (e.target.id === 'addModal') closeAddModal();
        if (e.target.id === 'detailModal') closeDetailModal();
        if (e.target.id === 'confirmDeleteModal') closeConfirmDelete();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeDetailModal();
            closeConfirmDelete();
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Show session notifications
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        
        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
        
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('{{ $error }}', 'error');
            @endforeach
        @endif
        
        // Initialize view
        toggleView('table');
    });
</script>
@endsection