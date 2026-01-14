@extends('layouts.kasir.index')

@section('page-title', 'Transaksi Penjualan')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')

    <!-- Held Transactions Card -->
    <div class="mb-6" id="heldTransactionsCard" style="display: none;">
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl shadow-lg border border-orange-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Transaksi Ditahan</h2>
                        <p class="text-xs text-gray-600">Klik untuk melanjutkan transaksi</p>
                    </div>
                </div>
                <span class="bg-orange-100 text-orange-700 text-sm px-3 py-1 rounded-lg font-semibold" id="heldCount">0
                    Transaksi</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3" id="heldTransactionsList">
                <!-- Held transactions will be inserted here -->
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Data Customer</h2>
                        <p class="text-xs text-gray-500">Pilih atau tambah customer untuk transaksi ini</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Select Existing Customer -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Pilih Customer</label>
                    <div class="relative">
                        <input type="text" id="customerSearchInput"
                            placeholder="Ketik untuk mencari kode member atau nama..." autocomplete="off"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors bg-white">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>

                        <!-- Dropdown Results -->
                        <div id="customerDropdown"
                            class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                            <div class="p-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                onclick="selectCustomerById('')">
                                <span class="text-gray-600 text-sm">-- Pilih Customer (Opsional) --</span>
                            </div>
                            <div class="p-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100"
                                onclick="selectCustomerById('walk-in')">
                                <span class="text-gray-700 text-sm font-medium">Walk-in Customer</span>
                            </div>
                            @foreach ($customers as $customer)
                                <div class="customer-option p-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                                    data-customer-id="{{ $customer->id }}" data-name="{{ $customer->nama }}"
                                    data-kode="{{ $customer->kode_member }}" data-poin="{{ $customer->total_poin ?? 0 }}"
                                    data-phone="{{ $customer->telepon }}" data-email="{{ $customer->email }}"
                                    data-search-text="{{ strtolower(($customer->kode_member ?? '') . ' ' . $customer->nama) }}"
                                    onclick="selectCustomerById('{{ $customer->id }}')">
                                    @if ($customer->kode_member)
                                        <span
                                            class="text-orange-600 font-semibold text-sm">[{{ $customer->kode_member }}]</span>
                                    @endif
                                    <span class="text-gray-700 text-sm ml-1">{{ $customer->nama }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Hidden select for compatibility -->
                        <select id="customerSelect" class="hidden">
                            <option value="">-- Pilih Customer (Opsional) --</option>
                            <option value="walk-in">Walk-in Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" data-name="{{ $customer->nama }}"
                                    data-kode="{{ $customer->kode_member }}" data-poin="{{ $customer->total_poin ?? 0 }}"
                                    data-phone="{{ $customer->telepon }}" data-email="{{ $customer->email }}">
                                    {{ $customer->kode_member ? '[' . $customer->kode_member . '] ' : '' }}{{ $customer->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Quick Add Customer -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Atau Tambah Baru</label>
                    <button onclick="openQuickAddCustomer()"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all flex items-center justify-center space-x-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-user-plus"></i>
                        <span class="font-medium">Tambah Customer Baru</span>
                    </button>
                </div>
            </div>

            <!-- Selected Customer Info -->
            <div id="selectedCustomerInfo"
                class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-xl hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-lg" id="selectedCustomerName">-</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800"
                                    id="selectedCustomerKode">
                                    <i class="fas fa-id-card mr-1"></i> -
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800"
                                    id="selectedCustomerPoin">
                                    <i class="fas fa-coins mr-1"></i> - Poin
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1" id="selectedCustomerContact">-</p>
                        </div>
                    </div>
                    <button onclick="clearCustomerSelection()"
                        class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                        <i class="fas fa-times mr-1"></i>Hapus
                    </button>
                </div>
                <!-- Informasi Poin yang Didapat -->
                <div id="poinEarnedInfo"
                    class="mt-3 p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-300 hidden">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-700 font-medium">
                            <i class="fas fa-star text-purple-600 mr-1"></i> Poin yang Didapat:
                        </span>
                        <span class="text-lg font-bold text-purple-700" id="poinEarned">0 Poin</span>
                    </div>
                    <p class="text-xs text-purple-600">💰 5 poin/Rp10k + 10 poin/bundle</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Transaksi -->
    <div id="deleteHeldModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 transform transition-all scale-95"
            id="deleteHeldModalContent">
            <!-- Icon -->
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                    <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Transaksi?</h3>
                <p class="text-sm text-gray-600 mb-6">Transaksi yang dihapus tidak dapat dikembalikan</p>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button onclick="closeDeleteHeldModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <button onclick="confirmDeleteHeldTransaction()"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl transition-all shadow-lg hover:shadow-xl">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Customer -->
    <div id="addCustomerModal"
        class="fixed inset-0 bg-black/50 z-[9999] hidden items-center justify-center backdrop-blur-sm"
        style="margin: 0 !important; padding: 0 !important;">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all relative z-[10000]">
            <!-- Header -->
            <div class="px-6 pt-6 pb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tambah Customer Baru</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Lengkapi data customer</p>
                        </div>
                    </div>
                    <button onclick="closeAddCustomerModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form id="addCustomerForm" class="px-6 pb-6 space-y-4">
                <!-- Nama -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="customerName" name="nama" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
                        placeholder="Masukkan nama customer">
                </div>

                <!-- Telepon -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">
                        No. Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="customerPhone" name="telepon" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
                        placeholder="Contoh: 08123456789">
                </div>

                <!-- Email -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">
                        Email (Optional)
                    </label>
                    <input type="email" id="customerEmail" name="email"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
                        placeholder="customer@email.com">
                </div>

                <!-- Footer Buttons -->
                <div class="flex space-x-3 pt-2">
                    <button type="button" onclick="closeAddCustomerModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">
                        <i class="fas fa-times mr-1.5"></i>Batal
                    </button>
                    <button type="button" onclick="saveNewCustomer()"
                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm">
                        <i class="fas fa-check mr-1.5"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaksi Page Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
        <!-- Customer Selection Card -->

        <!-- Card Katalog Produk -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 h-full flex flex-col">
                <!-- Header Card Katalog -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-store text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Katalog Produk</h2>
                                <p class="text-sm text-gray-500">Pilih produk untuk ditambahkan ke keranjang</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-green-100 text-green-600 text-sm px-3 py-1 rounded-lg font-semibold">{{ $totalProduk }}
                                Produk</span>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Ketik nama produk untuk mencari..."
                            class="w-full pl-12 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors bg-gray-50 focus:bg-white"
                            autocomplete="off" oninput="searchProduct()">
                        <button id="clearSearchBtn" class="absolute inset-y-0 right-0 pr-4 flex items-center hidden"
                            onclick="clearSearch()" title="Hapus pencarian">
                            <i class="fas fa-times-circle text-gray-400 hover:text-red-500 transition-colors"></i>
                        </button>
                    </div>

                    <!-- Categories -->
                    <div class="flex space-x-2 overflow-x-auto scrollbar-hide">
                        <button
                            class="category-btn px-6 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-xl whitespace-nowrap font-medium shadow-sm"
                            onclick="filterByCategory('semua')" data-category="semua">
                            <i class="fas fa-th-large mr-2"></i>Semua
                        </button>
                        <button
                            class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors"
                            onclick="filterByCategory('makanan')" data-category="makanan">
                            <i class="fas fa-utensils mr-2"></i>Makanan
                        </button>
                        <button
                            class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors"
                            onclick="filterByCategory('minuman')" data-category="minuman">
                            <i class="fas fa-coffee mr-2"></i>Minuman
                        </button>
                        <button
                            class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors"
                            onclick="filterByCategory('snack')" data-category="snack">
                            <i class="fas fa-cookie-bite mr-2"></i>Snack
                        </button>
                        <button
                            class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors"
                            onclick="filterByCategory('bundle')" data-category="bundle">
                            <i class="fas fa-box-open mr-2"></i>Bundle
                        </button>
                    </div>
                </div>

                <!-- Content Card Katalog -->
                <div class="flex-1 p-6 overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Produk</h3>
                            <div class="flex items-center space-x-2" id="viewToggleButtons">
                                <span class="text-sm text-gray-500">Tampilan:</span>
                                <button id="gridViewBtn" onclick="toggleView('grid')"
                                    class="p-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg transition-all"
                                    title="Tampilan Grid">
                                    <i class="fas fa-th text-sm"></i>
                                </button>
                                <button id="listViewBtn" onclick="toggleView('list')"
                                    class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all"
                                    title="Tampilan List">
                                    <i class="fas fa-list text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500" id="productCountDisplay">
                            Menampilkan 1-{{ $totalProduk }} dari {{ $totalProduk }} produk
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4"
                        id="productGrid">
                        <!-- No results message (hidden by default) -->
                        <div id="noResultsMessage" class="col-span-full text-center py-12 hidden">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                            <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau scan barcode produk</p>
                        </div>

                        <!-- Regular Products Container -->
                        <div id="regularProductsContainer"
                            class="col-span-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                            @forelse($produks as $index => $produk)
                                <!-- Product Card: {{ $produk->nama }} -->
                                <div class="product-card group bg-white border-2 border-green-200 rounded-2xl p-5 hover:shadow-xl hover:border-green-400 transition-all duration-300 cursor-pointer transform hover:-translate-y-1 relative"
                                    data-nama="{{ strtolower($produk->nama) }}" data-id="{{ $produk->id }}"
                                    data-price="{{ $produk->harga }}" data-stock="{{ $produk->stok }}"
                                    data-type="product" style="display: {{ $index < 24 ? 'block' : 'none' }};"
                                    onclick="addToCart({{ $produk->id }}, '{{ addslashes($produk->nama) }}', {{ $produk->harga }}, 'produk-{{ $produk->id }}.jpg', {{ $produk->stok }})">
                                    <!-- Double Circle Badge - Positioned at top right -->
                                    <div class="stock-badge-wrapper absolute -top-2 -right-2 z-10">
                                        <!-- Background circle (slightly higher) -->
                                        <div
                                            class="absolute -top-1 -right-1 w-14 h-14 bg-emerald-400 opacity-30 rounded-full">
                                        </div>
                                        <!-- Front circle with stock number -->
                                        <div
                                            class="relative w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-full flex items-center justify-center text-base font-bold product-stock-badge shadow-lg border-4 border-white">
                                            {{ $produk->stok }}
                                        </div>
                                    </div>

                                    <div class="product-details">
                                        <div class="product-info space-y-2">
                                            <h3
                                                class="font-semibold text-gray-900 text-base product-name leading-tight pr-8">
                                                {{ $produk->nama }}</h3>
                                            <p class="text-green-600 font-bold text-xl product-price">Rp
                                                {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                            <div class="flex items-center space-x-1 product-status pt-1">
                                                <span class="text-xs text-gray-500">Produk</span>
                                                <div
                                                    class="w-1.5 h-1.5 {{ $produk->stok > 0 ? 'bg-success' : 'bg-red-500' }} rounded-full">
                                                </div>
                                                <span
                                                    class="text-xs {{ $produk->stok > 0 ? 'text-success' : 'text-red-500' }} font-medium">{{ $produk->stok > 0 ? 'Tersedia' : 'Habis' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <div
                                        class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Produk</h3>
                                    <p class="text-sm text-gray-500">Belum ada produk yang tersedia</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Bundle Products Container -->
                        <div id="bundleProductsContainer"
                            class="col-span-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4"
                            style="display: none;">
                            @forelse($bundles as $bundle)
                                @php
                                    // Hitung total harga normal dari bundleProducts (untuk perbandingan)
                                    $totalHargaNormal = $bundle->bundleProducts->sum(function ($item) {
                                        return $item->produk->harga * $item->quantity;
                                    });

                                    // Untuk bundle, gunakan kolom 'nilai' sebagai harga bundle
                                    // Kolom 'nilai' di promo adalah harga bundle yang sudah ditentukan
                                    $hargaBundle = $bundle->nilai ?? $totalHargaNormal;
                                @endphp

                                <div class="product-card bundle-card group bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-300 rounded-2xl p-5 hover:shadow-xl hover:border-purple-500 transition-all duration-300 cursor-pointer transform hover:-translate-y-1 relative"
                                    data-nama="{{ strtolower($bundle->nama_promo) }}"
                                    data-id="bundle-{{ $bundle->id }}" data-bundle-id="{{ $bundle->id }}"
                                    data-bundle-name="{{ $bundle->nama_promo }}"
                                    data-bundle-products='@json($bundle->bundleProducts)'
                                    data-bundle-stock="{{ $bundle->stok }}" data-bundle-price="{{ $hargaBundle }}"
                                    data-stock="{{ $bundle->stok }}" data-type="bundle">

                                    <!-- Stock Badge -->
                                    <div class="stock-badge-wrapper absolute -top-2 -right-2 z-10">
                                        <!-- Background circle (slightly higher) -->
                                        <div
                                            class="absolute -top-1 -right-1 w-14 h-14 bg-purple-400 opacity-30 rounded-full">
                                        </div>
                                        <!-- Front circle with stock number -->
                                        <div
                                            class="relative w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-full flex items-center justify-center text-base font-bold product-stock-badge shadow-lg border-4 border-white">
                                            {{ $bundle->stok }}
                                        </div>
                                    </div>

                                    <div class="product-details">
                                        <div class="product-info space-y-2">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span
                                                    class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                                    <i class="fas fa-gift mr-1"></i>BUNDLE
                                                </span>
                                            </div>
                                            <h3
                                                class="font-semibold text-gray-900 text-base product-name leading-tight pr-8">
                                                {{ $bundle->nama_promo }}
                                            </h3>
                                            <div class="text-xs text-gray-600 space-y-1">
                                                <p class="font-medium">Paket berisi:</p>
                                                @foreach ($bundle->bundleProducts->take(3) as $item)
                                                    <p class="text-xs">• {{ $item->produk->nama }}
                                                        ({{ $item->quantity }}x)</p>
                                                @endforeach
                                                @if ($bundle->bundleProducts->count() > 3)
                                                    <p class="text-xs text-purple-600">+
                                                        {{ $bundle->bundleProducts->count() - 3 }} produk lainnya</p>
                                                @endif
                                            </div>

                                            <div class="pt-2 border-t border-purple-200">
                                                @if ($hargaBundle < $totalHargaNormal)
                                                    <p class="text-xs text-gray-500 line-through">Rp
                                                        {{ number_format($totalHargaNormal, 0, ',', '.') }}</p>
                                                    <p class="text-purple-600 font-bold text-xl product-price">
                                                        Rp {{ number_format($hargaBundle, 0, ',', '.') }}
                                                    </p>
                                                    <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">
                                                        Hemat Rp
                                                        {{ number_format($totalHargaNormal - $hargaBundle, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <p class="text-purple-600 font-bold text-xl product-price">
                                                        Rp {{ number_format($hargaBundle, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center space-x-1 product-status pt-1">
                                                <span class="text-xs text-gray-500">Bundle</span>
                                                <div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
                                                <span
                                                    class="text-xs text-purple-600 font-medium">{{ $bundle->bundleProducts->count() }}
                                                    Items</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-box-open text-purple-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Bundle</h3>
                                    <p class="text-sm text-gray-500">Belum ada bundle promo yang tersedia</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 items-center justify-between" id="paginationContainer" style="display: flex;">
                        <div class="text-sm text-gray-600" id="paginationInfo">
                            Memuat...
                        </div>
                        <div class="flex items-center space-x-2" id="paginationButtons">
                            <!-- Will be filled by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Keranjang -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 flex flex-col h-full">
                <!-- Header Card Keranjang -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-success text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Keranjang</h2>
                                <p class="text-sm text-gray-500" id="orderInfo">Belum ada order</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="bg-success/10 text-success text-sm px-3 py-1 rounded-lg font-bold"
                                id="cartCount">0 Item</span>
                            <button
                                class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors"
                                onclick="clearCart()">
                                <i class="fas fa-trash text-gray-500"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content Card Keranjang -->
                <div class="flex-1 overflow-hidden">
                    <div class="h-[calc(100vh-28rem)] overflow-y-auto px-4 py-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100"
                        id="cartItems">
                        <div class="text-center text-gray-500 py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 mb-2">Keranjang Kosong</h3>
                            <p class="text-xs text-gray-500 mb-3">Belum ada produk yang dipilih</p>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-400 mb-1">💡 Tips:</p>
                                <p class="text-xs text-gray-500">Klik produk di katalog untuk menambahkan ke keranjang</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Card Keranjang - Checkout -->
                <div class="border-t border-gray-100 p-4 bg-gray-50 rounded-b-2xl mt-auto">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg p-3 mb-3 shadow-sm">
                        <h4 class="text-xs font-semibold text-gray-900 mb-3">Ringkasan Pesanan</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium" id="subtotal">Rp 0</span>
                            </div>
                            @if ($pajak)
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">{{ $pajak->nama_pajak }} ({{ $pajak->persen }}%)</span>
                                    <span class="font-medium" id="tax">Rp 0</span>
                                </div>
                            @endif

                            <hr class="border-gray-200 my-2">

                            <!-- Diskon Section (Inline) -->
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-gray-700 block">
                                    <i class="fas fa-tag text-green-600 mr-1"></i>Diskon
                                </label>

                                <!-- Toggle between Promo Code and Manual Discount -->
                                <div class="flex gap-2">
                                    <button type="button" id="promoModeBtn" onclick="switchDiscountMode('promo')"
                                        class="flex-1 px-2 py-1.5 text-xs rounded-lg transition-all bg-gradient-to-r from-green-400 to-green-700 text-white font-medium">
                                        <i class="fas fa-tag mr-1"></i>Kode Promo
                                    </button>
                                    <button type="button" id="manualModeBtn" onclick="switchDiscountMode('manual')"
                                        class="flex-1 px-2 py-1.5 text-xs rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">
                                        <i class="fas fa-edit mr-1"></i>Input Manual
                                    </button>
                                </div>

                                <!-- Promo Code Select -->
                                @if ($promos && $promos->count() > 0)
                                    <div id="promoCodeSection">
                                        <div class="relative">
                                            <select id="promoCode"
                                                class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors text-xs"
                                                onchange="applyPromo()">
                                                <option value="">-- Pilih Promo --</option>
                                                @foreach ($promos as $promo)
                                                    <option value="{{ $promo->id }}" data-type="{{ $promo->jenis }}"
                                                        data-value="{{ $promo->nilai }}"
                                                        data-min="{{ $promo->min_transaksi }}"
                                                        data-max="{{ $promo->maks_potongan }}">
                                                        {{ $promo->kode_promo }} - {{ $promo->nama_promo }}
                                                        @if ($promo->jenis == 'diskon_persen')
                                                            ({{ $promo->nilai }}%)
                                                        @else
                                                            (Rp {{ number_format($promo->nilai, 0, ',', '.') }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1" id="promoInfo"></p>
                                    </div>
                                @else
                                    <div id="promoCodeSection">
                                        <p class="text-xs text-gray-500 italic">Tidak ada promo tersedia</p>
                                    </div>
                                @endif

                                <!-- Manual Discount Input -->
                                <div id="manualDiscountSection" style="display: none;">
                                    <div class="space-y-2">
                                        <!-- Discount Type -->
                                        <div class="flex gap-2">
                                            <button type="button" onclick="setManualDiscountType('persen')"
                                                id="percentBtn"
                                                class="flex-1 px-2 py-1.5 text-xs rounded-lg transition-all bg-gradient-to-r from-blue-400 to-blue-600 text-white font-medium">
                                                <i class="fas fa-percent mr-1"></i>Persen (%)
                                            </button>
                                            <button type="button" onclick="setManualDiscountType('nominal')"
                                                id="nominalBtn"
                                                class="flex-1 px-2 py-1.5 text-xs rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">
                                                <i class="fas fa-money-bill mr-1"></i>Nominal (Rp)
                                            </button>
                                        </div>

                                        <!-- Discount Value Input -->
                                        <div class="relative">
                                            <input type="number" id="manualDiscountValue"
                                                class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors text-xs"
                                                placeholder="Masukkan nilai diskon" min="0"
                                                oninput="applyManualDiscount()">
                                            <div
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                                <span class="text-gray-400 text-xs" id="discountSuffix">%</span>
                                            </div>
                                        </div>

                                        <button type="button" onclick="clearManualDiscount()"
                                            class="w-full px-2 py-1.5 text-xs rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all">
                                            <i class="fas fa-times mr-1"></i>Hapus Diskon
                                        </button>

                                        <p class="text-xs text-gray-500" id="manualDiscountInfo"></p>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-200 my-2">

                            <!-- Points Usage Section (Inline) -->
                            <div class="space-y-2" id="pointsSection">
                                <label class="text-xs font-semibold text-gray-700 block">
                                    <i class="fas fa-coins text-yellow-500 mr-1"></i>Gunakan Poin
                                </label>
                                <div
                                    class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-2 border border-yellow-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs text-gray-600">Poin Tersedia:</span>
                                        <span class="text-xs font-bold text-yellow-600" id="availablePoints">0 Poin</span>
                                    </div>
                                    <div class="relative mb-2">
                                        <input type="number" id="pointsInput"
                                            class="w-full px-2 py-1.5 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-200 focus:border-yellow-400 transition-colors text-xs"
                                            placeholder="Masukkan jumlah poin (1 poin = Rp 1)" min="0"
                                            value="0" oninput="applyPoints()">
                                    </div>
                                    <div class="flex gap-2 mb-2">
                                        <button type="button" onclick="useMaxPoints()"
                                            class="flex-1 px-2 py-1 text-xs rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 transition-all">
                                            <i class="fas fa-coins mr-1"></i>Gunakan Semua
                                        </button>
                                        <button type="button" onclick="clearPoints()"
                                            class="flex-1 px-2 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                                            <i class="fas fa-times mr-1"></i>Reset
                                        </button>
                                    </div>
                                    <div class="p-2 bg-white rounded-lg mb-1">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Potongan dari Poin:</span>
                                            <span class="text-xs font-bold text-green-600" id="pointsDiscount">Rp 0</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-yellow-600" id="pointsInfo"></p>
                                </div>
                            </div>

                            <hr class="border-gray-200 my-2">

                            <div class="flex justify-between">
                                <span class="font-bold text-gray-900 text-sm">Total Bayar</span>
                                <span class="font-bold text-lg text-green-600" id="total">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount Input -->
                    <div class="mb-4" id="paymentAmountSection">
                        <label class="text-sm font-semibold text-gray-900 mb-3 block">Nominal Bayar</label>
                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                            <!-- Total Amount Display -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total yang harus dibayar:</span>
                                    <span class="text-lg font-bold text-green-600" id="totalToPay">Rp 0</span>
                                </div>
                            </div>

                            <!-- Cash Payment Input -->
                            <div id="cashPaymentInput" class="space-y-3">
                                <div class="relative">
                                    <label class="text-xs text-gray-500 mb-1 block">Uang yang diterima</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                        <input type="text" id="cashAmount"
                                            class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors text-right font-semibold text-lg"
                                            placeholder="0">
                                    </div>
                                </div>


                                <!-- Change Information -->
                                <div id="changeInfo"
                                    class="p-3 rounded-lg border-2 border-dashed border-gray-200 text-center hidden">
                                    <div class="mb-2">
                                        <span class="text-sm text-gray-600">Kembalian:</span>
                                    </div>
                                    <div class="text-2xl font-bold" id="changeAmount">Rp 0</div>
                                    <div id="changeStatus" class="text-xs mt-1"></div>
                                </div>

                                <!-- Insufficient Payment Warning -->
                                <div id="insufficientWarning"
                                    class="p-3 bg-red-50 border border-red-200 rounded-lg text-center hidden">
                                    <div class="flex items-center justify-center space-x-2 text-red-600">
                                        <i class="fas fa-exclamation-triangle text-sm"></i>
                                        <span
                                            class="text-sm                                                                                                                                                                                            font-medium">Uang
                                            tidak mencukupi</span>
                                    </div>
                                    <div class="text-xs text-red-500 mt-1">
                                        Kurang: <span id="shortageAmount">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Non-cash Payment Info -->
                            <div id="nonCashPaymentInfo" class="hidden">
                                <div class="text-center py-4">
                                    <div
                                        class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-credit-card text-blue-600 text-2xl" id="paymentIcon"></i>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">Pembayaran dengan:</p>
                                    <p class="font-semibold text-gray-900" id="selectedPaymentMethod">Kartu Kredit
                                    </p>
                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                        <p class="text-xs text-blue-600">Total: <span id="nonCashTotal"
                                                class="font-bold">Rp 0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-900 mb-2 block">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <button
                                class="payment-method active p-2 border-2 border-green-500 bg-green-100 rounded-lg text-center transition-all"
                                data-method="tunai">
                                <i class="fas fa-money-bill text-green-600 mb-1 text-sm"></i>
                                <p class="text-xs font-medium text-green-600">Tunai</p>
                            </button>
                            <button
                                class="payment-method p-2 border-2 border-gray-200 rounded-lg text-center hover:border-gray-300 transition-all"
                                data-method="kartu">
                                <i class="fas fa-credit-card text-gray-500 mb-1 text-sm"></i>
                                <p class="text-xs font-medium text-gray-600">Kartu</p>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                class="payment-method p-2 border-2 border-gray-200 rounded-lg text-center hover:border-gray-300 transition-all"
                                data-method="qris">
                                <i class="fas fa-qrcode text-gray-500 mb-1 text-sm"></i>
                                <p class="text-xs font-medium text-gray-600">QRIS</p>
                            </button>
                            <button
                                class="payment-method p-2 border-2 border-gray-200 rounded-lg text-center hover:border-gray-300 transition-all"
                                data-method="transfer">
                                <i class="fas fa-mobile-alt text-gray-500 mb-1 text-sm"></i>
                                <p class="text-xs font-medium text-gray-600">Transfer</p>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- Printer Settings -->
                        <div class="bg-white rounded-xl p-3 mb-3" id="printerSettings">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Printer Bluetooth</span>
                                <button onclick="togglePrinterSettings()"
                                    class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-cog mr-1"></i>Atur
                                </button>
                            </div>
                            <div id="printerStatus" class="flex items-center space-x-2 text-xs">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                <span class="text-gray-500">Tidak terhubung</span>
                            </div>
                            <div id="printerSettingsPanel" class="hidden mt-3 pt-3 border-t border-gray-100">
                                <div class="space-y-2">
                                    <button onclick="connectBluetoothPrinter()"
                                        class="w-full bg-blue-100 text-blue-700 text-sm py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-bluetooth mr-2"></i>Hubungkan Printer
                                    </button>
                                    <button onclick="disconnectPrinter()"
                                        class="w-full bg-red-100 text-red-700 text-sm py-2 px-3 rounded-lg hover:bg-red-200 transition-colors">
                                        <i class="fas fa-times mr-2"></i>Putuskan Koneksi
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            class="w-full bg-gradient-to-r from-green-400 to-green-700 hover:from-green-500 hover:to-green-800 text-white font-bold py-4 px-4 rounded-xl transition-all transform hover:scale-[1.02] shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            id="checkoutBtn" onclick="processPaymentWithPrint()" disabled>
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-cash-register"></i>
                                <span>Bayar Sekarang</span>
                            </div>
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="holdTransaction()"
                                class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-3 rounded-lg transition-colors text-sm">
                                <i class="fas fa-save text-xs mr-1"></i>Hold
                            </button>
                            <a href="{{ route('kasir.laporan.index') }}"
                                class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-3 rounded-lg transition-colors text-sm inline-block">
                                <i class="fas fa-history text-xs mr-1"></i>Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Cart Toggle (Hidden on desktop) -->
    <div class="lg:hidden fixed bottom-4 right-4 z-40">
        <button
            class="bg-gradient-to-r from-green-400 to-green-700 hover:from-green-500 hover:to-green-800 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center"
            onclick="toggleMobileCart()">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span
                class="absolute -top-2 -right-2 bg-danger text-white text-xs w-6 h-6 rounded-full flex items-center justify-center"
                id="mobileCartCount">0</span>
        </button>
    </div>

    <!-- Mobile Cart Modal -->
    <div id="mobileCartModal" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[80vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold">Keranjang Belanja</h3>
                <button onclick="toggleMobileCart()"
                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-4" id="mobileCartItems">
                <!-- Cart items will be mirrored here -->
            </div>

            <!-- Modal Footer -->
            <div class="border-t p-4 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="font-semibold">Total:</span>
                    <span class="font-bold text-xl text-green-600" id="mobileTotal">Rp 0</span>
                </div>
                <button class="w-full bg-gradient-to-r from-green-400 to-green-700 text-white py-3 rounded-lg font-medium"
                    onclick="processPaymentWithPrint()">
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }

        /* List view specific styles */
        .product-card.list-view {
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        }

        .product-card.list-view .product-image-wrapper {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
        }

        .product-card.list-view .product-details {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .product-card.list-view .product-info {
            flex: 1;
        }

        .product-card.list-view .product-price {
            text-align: right;
            min-width: 150px;
        }

        /* Hidden product card */
        .product-card.hidden-card {
            display: none !important;
        }
    </style>

    <script>
        let cart = [];
        let currentCategory = 'semua';
        let currentView = 'grid'; // Default view
        let currentOrderNumber = null;
        let selectedCustomer = null; // Store selected customer data

        // Pagination variables
        let currentPage = 1;
        let itemsPerPage = 24; // Default for grid view

        // Discount variables
        let discountMode = 'promo'; // 'promo' or 'manual'
        let manualDiscountType = 'persen'; // 'persen' or 'nominal'
        let manualDiscountValue = 0;

        // Points usage variable
        let usedPoints = 0;

        // ============ DISCOUNT MANAGEMENT FUNCTIONS ============

        // Switch between promo code and manual discount
        function switchDiscountMode(mode) {
            discountMode = mode;

            const promoSection = document.getElementById('promoCodeSection');
            const manualSection = document.getElementById('manualDiscountSection');
            const promoModeBtn = document.getElementById('promoModeBtn');
            const manualModeBtn = document.getElementById('manualModeBtn');

            if (mode === 'promo') {
                // Show promo, hide manual
                if (promoSection) promoSection.style.display = 'block';
                if (manualSection) manualSection.style.display = 'none';

                // Update button styles
                if (promoModeBtn) {
                    promoModeBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gradient-to-r from-green-400 to-green-700 text-white font-medium';
                }
                if (manualModeBtn) {
                    manualModeBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200';
                }

                // Clear manual discount
                manualDiscountValue = 0;
                const manualInput = document.getElementById('manualDiscountValue');
                if (manualInput) manualInput.value = '';

            } else {
                // Show manual, hide promo
                if (promoSection) promoSection.style.display = 'none';
                if (manualSection) manualSection.style.display = 'block';

                // Update button styles
                if (promoModeBtn) {
                    promoModeBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200';
                }
                if (manualModeBtn) {
                    manualModeBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gradient-to-r from-green-400 to-green-700 text-white font-medium';
                }

                // Clear promo selection
                const promoSelect = document.getElementById('promoCode');
                if (promoSelect) promoSelect.value = '';
                const promoInfo = document.getElementById('promoInfo');
                if (promoInfo) promoInfo.textContent = '';
            }

            updateTotals();
        }

        // Set manual discount type (persen or nominal)
        function setManualDiscountType(type) {
            manualDiscountType = type;

            const percentBtn = document.getElementById('percentBtn');
            const nominalBtn = document.getElementById('nominalBtn');
            const discountSuffix = document.getElementById('discountSuffix');
            const manualInput = document.getElementById('manualDiscountValue');

            if (type === 'persen') {
                if (percentBtn) {
                    percentBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gradient-to-r from-blue-400 to-blue-600 text-white font-medium';
                }
                if (nominalBtn) {
                    nominalBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200';
                }
                if (discountSuffix) discountSuffix.textContent = '%';
                if (manualInput) {
                    manualInput.placeholder = 'Masukkan persen diskon (0-100)';
                    manualInput.max = '100';
                }
            } else {
                if (percentBtn) {
                    percentBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200';
                }
                if (nominalBtn) {
                    nominalBtn.className =
                        'flex-1 px-3 py-2 text-xs rounded-lg transition-all bg-gradient-to-r from-blue-400 to-blue-600 text-white font-medium';
                }
                if (discountSuffix) discountSuffix.textContent = 'Rp';
                if (manualInput) {
                    manualInput.placeholder = 'Masukkan nominal diskon';
                    manualInput.removeAttribute('max');
                }
            }

            applyManualDiscount();
        }

        // Apply manual discount
        function applyManualDiscount() {
            const manualInput = document.getElementById('manualDiscountValue');
            const manualInfo = document.getElementById('manualDiscountInfo');

            if (!manualInput) return;

            manualDiscountValue = parseFloat(manualInput.value) || 0;

            // Validate
            if (manualDiscountType === 'persen' && manualDiscountValue > 100) {
                manualDiscountValue = 100;
                manualInput.value = 100;
            }

            if (manualDiscountValue < 0) {
                manualDiscountValue = 0;
                manualInput.value = 0;
            }

            // Update info
            if (manualInfo) {
                if (manualDiscountValue > 0) {
                    if (manualDiscountType === 'persen') {
                        manualInfo.textContent = `Diskon ${manualDiscountValue}% diterapkan`;
                        manualInfo.className = 'text-xs text-green-600 mt-1';
                    } else {
                        manualInfo.textContent = `Diskon Rp ${manualDiscountValue.toLocaleString('id-ID')} diterapkan`;
                        manualInfo.className = 'text-xs text-green-600 mt-1';
                    }
                } else {
                    manualInfo.textContent = '';
                }
            }

            updateTotals();
        }

        // Clear manual discount
        function clearManualDiscount() {
            manualDiscountValue = 0;
            const manualInput = document.getElementById('manualDiscountValue');
            if (manualInput) manualInput.value = '';

            const manualInfo = document.getElementById('manualDiscountInfo');
            if (manualInfo) manualInfo.textContent = '';

            updateTotals();
        }

        // ============ END DISCOUNT MANAGEMENT FUNCTIONS ============

        // ============ POINTS MANAGEMENT FUNCTIONS ============

        // Apply points to reduce total
        function applyPoints() {
            const pointsInput = document.getElementById('pointsInput');
            const pointsInfo = document.getElementById('pointsInfo');

            if (!pointsInput) return;

            let requestedPoints = parseInt(pointsInput.value) || 0;

            // Get available points from selected customer
            const availablePoints = selectedCustomer ? selectedCustomer.poin : 0;

            // Get current total before points
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxRate = {{ $pajak ? $pajak->persen : 0 }} / 100;
            const tax = subtotal * taxRate;

            // Calculate discount from promo/manual
            let discount = 0;
            if (discountMode === 'promo') {
                const promoSelect = document.getElementById('promoCode');
                if (promoSelect && promoSelect.value) {
                    const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                    const promoType = selectedOption.getAttribute('data-type');
                    const promoValue = parseFloat(selectedOption.getAttribute('data-value'));
                    const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                    const maxDiscount = parseFloat(selectedOption.getAttribute('data-max')) || 0;

                    if (subtotal >= minTransaction) {
                        if (promoType === 'diskon_persen') {
                            discount = subtotal * (promoValue / 100);
                            if (maxDiscount > 0 && discount > maxDiscount) {
                                discount = maxDiscount;
                            }
                        } else if (promoType === 'cashback') {
                            discount = promoValue;
                        }
                    }
                }
            } else if (discountMode === 'manual') {
                if (manualDiscountValue > 0) {
                    if (manualDiscountType === 'persen') {
                        discount = subtotal * (manualDiscountValue / 100);
                    } else {
                        discount = manualDiscountValue;
                    }
                }
            }

            const totalBeforePoints = subtotal - discount + tax;

            // Validate points
            if (requestedPoints < 0) {
                requestedPoints = 0;
                pointsInput.value = 0;
            }

            if (!selectedCustomer) {
                if (pointsInfo) pointsInfo.textContent = 'Pilih member terlebih dahulu untuk menggunakan poin';
                pointsInput.value = 0;
                usedPoints = 0;
                updateTotals();
                return;
            }

            if (requestedPoints > availablePoints) {
                if (pointsInfo) pointsInfo.textContent =
                    `Poin tidak mencukupi. Maksimal: ${availablePoints.toLocaleString('id-ID')} poin`;
                pointsInput.value = availablePoints;
                requestedPoints = availablePoints;
            }

            if (requestedPoints > totalBeforePoints) {
                if (pointsInfo) pointsInfo.textContent =
                    `Poin melebihi total pembayaran. Maksimal: ${Math.floor(totalBeforePoints).toLocaleString('id-ID')} poin`;
                pointsInput.value = Math.floor(totalBeforePoints);
                requestedPoints = Math.floor(totalBeforePoints);
            }

            if (requestedPoints > 0 && requestedPoints <= availablePoints && requestedPoints <= totalBeforePoints) {
                if (pointsInfo) pointsInfo.textContent =
                    `Menggunakan ${requestedPoints.toLocaleString('id-ID')} poin (sisa ${(availablePoints - requestedPoints).toLocaleString('id-ID')} poin)`;
            } else {
                if (pointsInfo) pointsInfo.textContent = '';
            }

            usedPoints = requestedPoints;
            updateTotals();
        }

        // Use maximum available points
        function useMaxPoints() {
            if (!selectedCustomer) {
                alert('Pilih member terlebih dahulu untuk menggunakan poin');
                return;
            }

            const availablePoints = selectedCustomer.poin;

            // Get current total before points
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxRate = {{ $pajak ? $pajak->persen : 0 }} / 100;
            const tax = subtotal * taxRate;

            // Calculate discount
            let discount = 0;
            if (discountMode === 'promo') {
                const promoSelect = document.getElementById('promoCode');
                if (promoSelect && promoSelect.value) {
                    const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                    const promoType = selectedOption.getAttribute('data-type');
                    const promoValue = parseFloat(selectedOption.getAttribute('data-value'));
                    const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                    const maxDiscount = parseFloat(selectedOption.getAttribute('data-max')) || 0;

                    if (subtotal >= minTransaction) {
                        if (promoType === 'diskon_persen') {
                            discount = subtotal * (promoValue / 100);
                            if (maxDiscount > 0 && discount > maxDiscount) {
                                discount = maxDiscount;
                            }
                        } else if (promoType === 'cashback') {
                            discount = promoValue;
                        }
                    }
                }
            } else if (discountMode === 'manual') {
                if (manualDiscountValue > 0) {
                    if (manualDiscountType === 'persen') {
                        discount = subtotal * (manualDiscountValue / 100);
                    } else {
                        discount = manualDiscountValue;
                    }
                }
            }

            const totalBeforePoints = Math.floor(subtotal - discount + tax);

            // Use minimum of available points or total
            const maxUsablePoints = Math.min(availablePoints, totalBeforePoints);

            const pointsInput = document.getElementById('pointsInput');
            if (pointsInput) {
                pointsInput.value = maxUsablePoints;
            }

            applyPoints();
        }

        // Clear points usage
        function clearPoints() {
            usedPoints = 0;
            const pointsInput = document.getElementById('pointsInput');
            const pointsInfo = document.getElementById('pointsInfo');

            if (pointsInput) pointsInput.value = 0;
            if (pointsInfo) pointsInfo.textContent = '';

            updateTotals();
        }

        // Update available points display
        function updateAvailablePoints() {
            const availablePointsEl = document.getElementById('availablePoints');
            if (availablePointsEl) {
                if (selectedCustomer && selectedCustomer.poin) {
                    availablePointsEl.textContent = `${selectedCustomer.poin.toLocaleString('id-ID')} Poin`;
                } else {
                    availablePointsEl.textContent = '0 Poin';
                }
            }
        }

        // ============ END POINTS MANAGEMENT FUNCTIONS ============

        // ============ CUSTOMER MANAGEMENT FUNCTIONS ============

        // Initialize customer search dropdown
        function initializeCustomerSearch() {
            const searchInput = document.getElementById('customerSearchInput');
            const dropdown = document.getElementById('customerDropdown');

            if (!searchInput || !dropdown) {
                console.error('Customer search elements not found');
                return;
            }

            console.log('Customer search initialized');

            // Show dropdown on focus
            searchInput.addEventListener('focus', function() {
                console.log('Search input focused');
                dropdown.classList.remove('hidden');
                filterCustomers(''); // Show all options
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Filter customers as user types
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                console.log('Searching for:', searchText);
                filterCustomers(searchText);
            });
        }

        // Filter customer options based on search text
        function filterCustomers(searchText) {
            const customerOptions = document.querySelectorAll('.customer-option');
            let visibleCount = 0;

            console.log('Filtering with text:', searchText);
            console.log('Total customer options:', customerOptions.length);

            customerOptions.forEach(option => {
                const searchableText = option.dataset.searchText || '';
                console.log('Checking:', searchableText, 'against', searchText);

                if (searchableText.includes(searchText)) {
                    option.style.display = 'block';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });

            console.log('Visible options:', visibleCount);

            // Always show dropdown when input is focused
            const dropdown = document.getElementById('customerDropdown');
            dropdown.classList.remove('hidden');
        }

        // Select customer by ID
        function selectCustomerById(customerId) {
            const select = document.getElementById('customerSelect');
            const searchInput = document.getElementById('customerSearchInput');
            const dropdown = document.getElementById('customerDropdown');

            // Set the hidden select value
            if (select) select.value = customerId;

            if (customerId === '' || customerId === 'walk-in') {
                // Clear selection or walk-in customer
                selectedCustomer = customerId === 'walk-in' ? {
                    id: 'walk-in',
                    name: 'Walk-in Customer',
                    kode_member: null,
                    poin: 0,
                    phone: '-',
                    email: '-'
                } : null;

                if (searchInput) searchInput.value = customerId === 'walk-in' ? 'Walk-in Customer' : '';
            } else {
                // Find customer data from dropdown option div
                const customerOption = document.querySelector(`.customer-option[data-customer-id="${customerId}"]`);

                if (customerOption) {
                    // Store customer data from data attributes
                    selectedCustomer = {
                        id: customerId,
                        name: customerOption.dataset.name,
                        kode_member: customerOption.dataset.kode || null,
                        poin: parseInt(customerOption.dataset.poin) || 0,
                        phone: customerOption.dataset.phone,
                        email: customerOption.dataset.email
                    };

                    // Update search input with selected customer
                    const displayText = selectedCustomer.kode_member ?
                        `[${selectedCustomer.kode_member}] ${selectedCustomer.name}` :
                        selectedCustomer.name;
                    if (searchInput) searchInput.value = displayText;
                } else {
                    // Fallback to hidden select option
                    const selectedOption = select ? select.options[select.selectedIndex] : null;
                    if (selectedOption) {
                        selectedCustomer = {
                            id: customerId,
                            name: selectedOption.dataset.name,
                            kode_member: selectedOption.dataset.kode || null,
                            poin: parseInt(selectedOption.dataset.poin) || 0,
                            phone: selectedOption.dataset.phone,
                            email: selectedOption.dataset.email
                        };

                        const displayText = selectedCustomer.kode_member ?
                            `[${selectedCustomer.kode_member}] ${selectedCustomer.name}` :
                            selectedCustomer.name;
                        if (searchInput) searchInput.value = displayText;
                    }
                }
            }

            // Hide dropdown
            if (dropdown) dropdown.classList.add('hidden');

            updateCustomerInfoDisplay();
            updateAvailablePoints(); // Update display poin tersedia
            clearPoints(); // Reset poin usage saat ganti customer
            updatePoinEarned(); // Update poin yang akan didapat
        }

        // Select customer from dropdown (legacy function for compatibility)
        function selectCustomer() {
            const select = document.getElementById('customerSelect');
            const customerId = select.value;
            selectCustomerById(customerId);
        }

        // Update customer info display
        function updateCustomerInfoDisplay() {
            const infoDiv = document.getElementById('selectedCustomerInfo');
            const nameSpan = document.getElementById('selectedCustomerName');
            const kodeSpan = document.getElementById('selectedCustomerKode');
            const poinSpan = document.getElementById('selectedCustomerPoin');
            const contactSpan = document.getElementById('selectedCustomerContact');

            if (selectedCustomer) {
                infoDiv.classList.remove('hidden');
                nameSpan.textContent = selectedCustomer.name;

                // Tampilkan kode member
                if (selectedCustomer.kode_member) {
                    kodeSpan.innerHTML = `<i class="fas fa-id-card mr-1"></i>${selectedCustomer.kode_member}`;
                } else {
                    kodeSpan.innerHTML = `<i class="fas fa-id-card mr-1"></i>No Member`;
                }

                // Tampilkan total poin
                poinSpan.innerHTML =
                    `<i class="fas fa-coins mr-1"></i>${selectedCustomer.poin.toLocaleString('id-ID')} Poin`;

                contactSpan.textContent = selectedCustomer.phone + (selectedCustomer.email !== '-' ? ' • ' +
                    selectedCustomer.email : '');
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        // Clear customer selection
        function clearCustomerSelection() {
            selectedCustomer = null;
            document.getElementById('customerSelect').value = '';
            document.getElementById('customerSearchInput').value = '';
            updateCustomerInfoDisplay();
        }

        // Open add customer modal
        function openQuickAddCustomer() {
            const modal = document.getElementById('addCustomerModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // Reset form
                document.getElementById('addCustomerForm').reset();
                // Focus on first input
                setTimeout(() => {
                    const nameInput = document.getElementById('customerName');
                    if (nameInput) nameInput.focus();
                }, 100);
            }
        }

        // Close add customer modal
        function closeAddCustomerModal() {
            const modal = document.getElementById('addCustomerModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Save new customer
        async function saveNewCustomer() {
            const form = document.getElementById('addCustomerForm');
            const nama = document.getElementById('customerName').value.trim();
            const telepon = document.getElementById('customerPhone').value.trim();
            const email = document.getElementById('customerEmail').value.trim();

            // Validation
            if (!nama) {
                showErrorNotification('Nama customer harus diisi');
                return;
            }

            if (!telepon) {
                showErrorNotification('No. telepon harus diisi');
                return;
            }

            try {
                const response = await fetch('{{ route('kasir.customer.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nama: nama,
                        telepon: telepon,
                        email: email || null
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showSuccessNotification('Customer berhasil ditambahkan');

                    // Add new option to select
                    const select = document.getElementById('customerSelect');
                    const newOption = document.createElement('option');
                    newOption.value = result.customer.id;
                    newOption.dataset.name = result.customer.nama;
                    newOption.dataset.phone = result.customer.telepon;
                    newOption.dataset.email = result.customer.email || '';
                    newOption.textContent = result.customer.nama + ' - ' + result.customer.telepon;
                    select.appendChild(newOption);

                    // Select the new customer
                    select.value = result.customer.id;
                    selectCustomer();

                    // Close modal
                    closeAddCustomerModal();
                } else {
                    showErrorNotification('Gagal menambahkan customer: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorNotification('Terjadi kesalahan saat menyimpan customer');
            }
        }

        // ============ END CUSTOMER MANAGEMENT FUNCTIONS ============

        // Generate order number from database
        async function generateOrderNumber() {
            try {
                const response = await fetch('{{ route('kasir.transaksi.create') }}?action=next-id');
                const result = await response.json();
                if (result.success) {
                    return result.next_id;
                }
                return null;
            } catch (error) {
                console.error('Error fetching next ID:', error);
                return null;
            }
        }

        // Confirm order number (no longer needed with database IDs)
        function confirmOrderNumber() {
            // No action needed - ID is managed by database
        }

        // Update order info display
        function updateOrderInfo() {
            const orderInfo = document.getElementById('orderInfo');
            if (orderInfo) {
                if (currentOrderNumber) {
                    orderInfo.textContent = `ID ${currentOrderNumber}`;
                } else {
                    orderInfo.textContent = 'Belum ada order';
                }
            }
        }

        // Toggle between grid and list view
        function toggleView(viewType) {
            console.log('toggleView called with:', viewType);
            currentView = viewType;
            const regularContainer = document.getElementById('regularProductsContainer');
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');

            if (!regularContainer) {
                console.error('regularProductsContainer not found in toggleView');
                return;
            }

            const productCards = regularContainer.querySelectorAll('.product-card');
            console.log('Product cards in toggleView:', productCards.length);

            if (viewType === 'grid') {
                // Switch to grid view
                regularContainer.className =
                    'col-span-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4';

                // Update product cards for grid view
                productCards.forEach(card => {
                    card.className =
                        'product-card group bg-white border-2 border-green-200 rounded-2xl p-5 hover:shadow-xl hover:border-green-400 transition-all duration-300 cursor-pointer transform hover:-translate-y-1 relative';
                    card.style.display = 'block';

                    // Reset display based on current filter
                    const productName = card.getAttribute('data-nama');
                    const productCategory = getCategoryByName(productName);
                    const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;

                    if (!matchesCategory) {
                        card.style.display = 'none';
                    }

                    // Adjust stock badge wrapper - positioned at top right
                    const stockBadgeWrapper = card.querySelector('.stock-badge-wrapper');
                    if (stockBadgeWrapper) {
                        stockBadgeWrapper.className = 'stock-badge-wrapper absolute -top-2 -right-2 z-10';
                    }

                    // Adjust product details
                    const details = card.querySelector('.product-details');
                    if (details) {
                        details.className = 'product-details';
                    }

                    const info = card.querySelector('.product-info');
                    if (info) {
                        info.className = 'product-info space-y-2';
                    }

                    const name = card.querySelector('.product-name');
                    if (name) {
                        name.className = 'font-semibold text-gray-900 text-base product-name leading-tight pr-8';
                    }

                    const price = card.querySelector('.product-price');
                    if (price) {
                        price.className = 'text-green-600 font-bold text-xl product-price';
                    }

                    const status = card.querySelector('.product-status');
                    if (status) {
                        status.className = 'flex items-center space-x-1 product-status pt-1';
                    }
                });

                // Update button states
                if (gridBtn) gridBtn.className =
                    'p-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg transition-all';
                if (listBtn) listBtn.className =
                'p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all';
            } else {
                // Switch to list view
                regularContainer.className =
                    'col-span-full flex flex-col gap-3';

                // Update product cards for list view
                productCards.forEach(card => {
                    card.className =
                        'product-card list-view group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:border-green-400 transition-all duration-300 cursor-pointer relative';

                    // Reset display based on current filter  
                    const productName = card.getAttribute('data-nama');
                    const productCategory = getCategoryByName(productName);
                    const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;

                    // Show as flex for list view
                    card.style.display = 'flex';
                    if (!matchesCategory) {
                        card.style.display = 'none';
                    }

                    // Adjust stock badge wrapper for list - keep at top right
                    const stockBadgeWrapper = card.querySelector('.stock-badge-wrapper');
                    if (stockBadgeWrapper) {
                        stockBadgeWrapper.className = 'stock-badge-wrapper absolute -top-2 -right-2 z-10';
                    }

                    // Adjust product details for list
                    const details = card.querySelector('.product-details');
                    if (details) {
                        details.className = 'product-details flex-1';
                    }

                    const info = card.querySelector('.product-info');
                    if (info) {
                        info.className = 'product-info flex-1';
                    }

                    const name = card.querySelector('.product-name');
                    if (name) {
                        name.className = 'font-semibold text-gray-900 text-base product-name mb-1 pr-16';
                    }

                    const price = card.querySelector('.product-price');
                    if (price) {
                        price.className = 'text-green-600 font-bold text-xl product-price mb-1';
                    }

                    const status = card.querySelector('.product-status');
                    if (status) {
                        status.className = 'flex items-center space-x-1 product-status';
                    }
                });

                // Update button states
                if (gridBtn) gridBtn.className =
                'p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all';
                if (listBtn) listBtn.className =
                    'p-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg transition-all';
            }

            // Save preference to localStorage
            localStorage.setItem('productViewPreference', viewType);

            // Update pagination after view change
            updatePagination();
        }

        // ============ PAGINATION FUNCTIONS ============
        // Update items per page based on view type
        function updateItemsPerPage() {
            itemsPerPage = currentView === 'grid' ? 24 : 16;
        }

        // Get all visible product cards based on current filter
        function getVisibleProducts() {
            const regularContainer = document.getElementById('regularProductsContainer');
            if (!regularContainer) {
                console.error('regularProductsContainer not found in getVisibleProducts');
                return [];
            }

            const allCards = Array.from(regularContainer.querySelectorAll('.product-card'));
            console.log('getVisibleProducts - Total cards:', allCards.length, 'Current category:', currentCategory);

            // Filter by category
            const filtered = allCards.filter(card => {
                const productName = card.getAttribute('data-nama');
                const productCategory = getCategoryByName(productName);
                const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;
                return matchesCategory;
            });

            console.log('getVisibleProducts - Filtered cards:', filtered.length);
            return filtered;
        }

        // Render pagination
        function updatePagination() {
            console.log('updatePagination called - View:', currentView, 'Category:', currentCategory, 'Page:', currentPage);

            updateItemsPerPage();
            console.log('Items per page:', itemsPerPage);

            const regularContainer = document.getElementById('regularProductsContainer');
            if (!regularContainer) {
                console.error('regularProductsContainer not found');
                return;
            }

            const allCards = Array.from(regularContainer.querySelectorAll('.product-card'));
            console.log('Total cards found:', allCards.length);

            // First, hide all cards
            allCards.forEach(card => {
                card.style.display = 'none';
            });

            // Get visible products based on category filter
            const visibleProducts = getVisibleProducts();
            const totalProducts = visibleProducts.length;
            const totalPages = Math.ceil(totalProducts / itemsPerPage);

            console.log('Visible products after filter:', totalProducts, 'Total pages:', totalPages);

            // Reset to page 1 if current page exceeds total pages
            if (currentPage > totalPages && totalPages > 0) {
                currentPage = 1;
            }

            // Show products for current page
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            console.log('Showing items from index', startIndex, 'to', endIndex);

            visibleProducts.forEach((card, index) => {
                if (index >= startIndex && index < endIndex) {
                    card.style.display = currentView === 'list' ? 'flex' : 'block';
                }
            });

            // Update pagination info
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationButtons = document.getElementById('paginationButtons');
            const paginationContainer = document.getElementById('paginationContainer');

            console.log('Pagination elements found:', {
                paginationInfo: !!paginationInfo,
                paginationButtons: !!paginationButtons,
                paginationContainer: !!paginationContainer
            });

            // Update info text - always show
            const startItem = totalProducts > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
            const endItem = Math.min(currentPage * itemsPerPage, totalProducts);
            if (paginationInfo) {
                paginationInfo.textContent = `Menampilkan ${startItem} - ${endItem} dari ${totalProducts} produk`;
                console.log('Pagination info updated:', paginationInfo.textContent);
            }

            // Generate pagination buttons
            if (paginationButtons) {
                let buttonsHTML = '';

                if (totalPages > 1) {

                    // Previous button
                    if (currentPage === 1) {
                        buttonsHTML +=
                            '<span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>';
                    } else {
                        buttonsHTML +=
                            `<button onclick="goToPage(${currentPage - 1})" class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-chevron-left text-xs"></i></button>`;
                    }

                    // Page numbers
                    for (let i = 1; i <= totalPages; i++) {
                        if (i === currentPage) {
                            buttonsHTML +=
                                `<span class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg font-semibold">${i}</span>`;
                        } else {
                            buttonsHTML +=
                                `<button onclick="goToPage(${i})" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">${i}</button>`;
                        }
                    }

                    // Next button
                    if (currentPage === totalPages) {
                        buttonsHTML +=
                            '<span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>';
                    } else {
                        buttonsHTML +=
                            `<button onclick="goToPage(${currentPage + 1})" class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-chevron-right text-xs"></i></button>`;
                    }
                }

                paginationButtons.innerHTML = buttonsHTML;
                console.log('Pagination buttons generated:', totalPages, 'pages');
            }
        }

        // Go to specific page
        function goToPage(page) {
            currentPage = page;
            updatePagination();

            // Scroll to top of product container
            const regularContainer = document.getElementById('regularProductsContainer');
            if (regularContainer) {
                regularContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
        // ============ END PAGINATION FUNCTIONS ============

        // Determine category based on product name
        function getCategoryByName(productName) {
            const name = productName.toLowerCase();

            // Snack items
            const snackItems = [
                'kue buaya', 'roti pisang', 'roti black forest',
                'roti selai strowberry', 'kue sus', 'roti canai',
                'croissant', 'baguette'
            ];

            // Minuman items
            const minumanItems = [
                'air mineral', 'lemon tea'
            ];

            // Check if product is snack
            if (snackItems.some(item => name.includes(item))) {
                return 'snack';
            }

            // Check if product is minuman
            if (minumanItems.some(item => name.includes(item))) {
                return 'minuman';
            }

            // Default to makanan for other items
            return 'makanan';
        }

        // Search products
        function searchProduct() {
            const searchInput = document.getElementById('searchInput');
            if (!searchInput) return;

            const searchTerm = searchInput.value.toLowerCase().trim();
            const noResultsMessage = document.getElementById('noResultsMessage');
            const clearBtn = document.getElementById('clearSearchBtn');
            const paginationContainer = document.getElementById('paginationContainer');

            // Show/hide clear button
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', searchInput.value.length === 0);
            }

            // If searching in regular products, use pagination
            if (currentCategory !== 'bundle') {
                currentPage = 1; // Reset to first page on search

                // If no search term, restore pagination
                if (searchTerm === '') {
                    updatePagination();
                    if (noResultsMessage) noResultsMessage.classList.add('hidden');
                    return;
                }

                // Hide pagination during search
                if (paginationContainer) paginationContainer.style.display = 'none';

                const regularContainer = document.getElementById('regularProductsContainer');
                const productCards = regularContainer ? regularContainer.querySelectorAll('.product-card') : [];
                let visibleCount = 0;

                productCards.forEach(card => {
                    const productName = card.getAttribute('data-nama') || '';
                    const productCategory = getCategoryByName(productName);
                    const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;
                    const matchesSearch = productName.indexOf(searchTerm) !== -1;

                    if (matchesSearch && matchesCategory) {
                        card.style.display = currentView === 'list' ? 'flex' : 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide no results message
                if (noResultsMessage) {
                    if (visibleCount === 0) {
                        noResultsMessage.classList.remove('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }
                }
            } else {
                // For bundles, simple search without pagination
                const bundleContainer = document.getElementById('bundleProductsContainer');
                const bundleCards = bundleContainer ? bundleContainer.querySelectorAll('.bundle-card') : [];
                let visibleCount = 0;

                bundleCards.forEach(card => {
                    const productName = card.getAttribute('data-nama') || '';
                    const matchesSearch = searchTerm === '' || productName.indexOf(searchTerm) !== -1;

                    if (matchesSearch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide no results message
                if (noResultsMessage) {
                    if (visibleCount === 0) {
                        noResultsMessage.classList.remove('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }
                }
            }
        }

        // Clear search input
        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                searchProduct();
            }
        }

        // Filter products by category
        function filterByCategory(category) {
            currentCategory = category;
            currentPage = 1; // Reset to first page when changing category

            // Update button states
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => {
                const btnCategory = btn.getAttribute('data-category');
                if (btnCategory === category) {
                    btn.className =
                        'category-btn px-6 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-xl whitespace-nowrap font-medium shadow-sm';
                } else {
                    btn.className =
                        'category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors';
                }
            });

            // Clear search input when changing category
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                const clearBtn = document.getElementById('clearSearchBtn');
                if (clearBtn) {
                    clearBtn.classList.add('hidden');
                }
            }

            // Get containers
            const regularContainer = document.getElementById('regularProductsContainer');
            const bundleContainer = document.getElementById('bundleProductsContainer');
            const noResultsMessage = document.getElementById('noResultsMessage');
            const paginationContainer = document.getElementById('paginationContainer');
            const viewToggleButtons = document.getElementById('viewToggleButtons');

            // Hide no results message
            if (noResultsMessage) {
                noResultsMessage.classList.add('hidden');
            }

            if (category === 'bundle') {
                // Show only bundle, hide regular products and pagination
                if (regularContainer) regularContainer.style.display = 'none';
                if (bundleContainer) bundleContainer.style.display = 'grid';
                if (paginationContainer) paginationContainer.style.display = 'none';
                if (viewToggleButtons) viewToggleButtons.style.display = 'none';

                // Update count for bundles
                const bundleCards = bundleContainer ? bundleContainer.querySelectorAll('.bundle-card') : [];
                updateProductCountDisplay(bundleCards.length);
            } else {
                // Show regular products, hide bundles
                if (regularContainer) {
                    regularContainer.style.display = currentView === 'list' ? 'flex' : 'grid';
                    if (currentView === 'list') {
                        regularContainer.className = 'col-span-full flex flex-col gap-3';
                    } else {
                        regularContainer.className =
                            'col-span-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4';
                    }
                }
                if (bundleContainer) bundleContainer.style.display = 'none';
                if (viewToggleButtons) viewToggleButtons.style.display = 'flex';

                // Update pagination and count
                updatePagination();
            }
        }

        // Update product count display
        function updateProductCountDisplay(count) {
            const countDisplay = document.getElementById('productCountDisplay');
            if (countDisplay) {
                // Hide counter when bundle filter is active
                if (currentCategory === 'bundle') {
                    countDisplay.style.display = 'none';
                } else {
                    countDisplay.style.display = 'none'; // Hide this as pagination info will show the count
                }
            }
        }

        // Header action functions
        function showTransactionHistory() {
            // Redirect to laporan page
            window.location.href = "{{ route('kasir.laporan.index') }}";
        }

        function showSettings() {
            // closeSidebar();
            // Implement settings modal
            showErrorNotification('Fitur dalam pengembangan');
        }

        function showNotifications() {
            // closeSidebar();
            // Implement notifications panel
            showErrorNotification('Tidak ada notifikasi baru');
        }

        // Close sidebar when pressing Escape key (handled by layout)
        // document.addEventListener('keydown', function(e) {
        //     if (e.key === 'Escape') {
        //         toggleSidebar();
        //     }
        // });

        let isMobileCartOpen = false;

        // Update current date and time
        function updateDateTime() {
            const now = new Date();

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];

            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const dateElement = document.getElementById('currentDate');
            const timeElement = document.getElementById('currentTime');

            if (dateElement) {
                dateElement.textContent = `${dayName}, ${date} ${monthName} ${year}`;
            }

            if (timeElement) {
                timeElement.textContent = `${hours}:${minutes} WIB`;
            }
        }

        // Add product to cart
        function addToCart(id, name, price, image, stock) {
            const existingItem = cart.find(item => item.id === id);

            // Check stock availability
            if (existingItem) {
                // Check if adding one more would exceed stock
                if (existingItem.quantity >= stock) {
                    // Show error notification
                    showStockAlert(name, stock);
                    return;
                }
                existingItem.quantity += 1;
            } else {
                // Check if stock is available
                if (stock <= 0) {
                    showStockAlert(name, stock);
                    return;
                }
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    image: image,
                    stock: stock,
                    quantity: 1
                });

                // Generate order number untuk transaksi baru
                if (cart.length === 1) {
                    generateOrderNumber().then(nextId => {
                        currentOrderNumber = nextId;
                        updateOrderInfo();
                    });
                }
            }

            updateCartDisplay();
            updateOrderInfo();

            // Visual feedback
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                if (card.textContent.includes(name)) {
                    card.classList.add('bg-green-50', 'border-green-200');
                    setTimeout(() => {
                        card.classList.remove('bg-green-50', 'border-green-200');
                    }, 500);
                }
            });
        }

        // Add bundle to cart
        function addBundleToCart(bundleId, bundleName, bundleProducts, bundleStock, hargaBundle) {
            console.log('Adding bundle to cart:', {
                bundleId,
                bundleName,
                bundleProducts,
                bundleStock,
                hargaBundle
            });

            // Check if bundle already exists in cart
            const existingBundle = cart.find(item => item.id === 'bundle-' + bundleId && item.isBundle);

            // Check stock availability
            if (existingBundle) {
                // Check if adding one more would exceed stock
                if (existingBundle.quantity >= bundleStock) {
                    showStockAlert(bundleName, bundleStock);
                    return;
                }
                existingBundle.quantity += 1;
                console.log('Updated existing bundle quantity:', existingBundle);
            } else {
                // Check if stock is available
                if (bundleStock <= 0) {
                    showStockAlert(bundleName, bundleStock);
                    return;
                }

                // Gunakan harga bundle yang sudah ditentukan dari manajemen
                const bundlePrice = hargaBundle || bundleProducts.reduce((total, item) => {
                    return total + (item.produk.harga * item.quantity);
                }, 0);

                const newBundle = {
                    id: 'bundle-' + bundleId,
                    name: bundleName,
                    price: bundlePrice,
                    image: 'bundle-' + bundleId + '.jpg',
                    stock: bundleStock,
                    quantity: 1,
                    isBundle: true,
                    bundleId: bundleId,
                    bundleProducts: bundleProducts
                };

                cart.push(newBundle);
                console.log('Added new bundle to cart:', newBundle);
                console.log('Current cart:', cart);

                // Generate order number untuk transaksi baru
                if (cart.length === 1) {
                    generateOrderNumber().then(nextId => {
                        currentOrderNumber = nextId;
                        updateOrderInfo();
                    });
                }
            }

            updateCartDisplay();
            updateOrderInfo();

            // Visual feedback
            const bundleCard = document.querySelector(`[data-bundle-id="${bundleId}"]`);
            if (bundleCard) {
                bundleCard.classList.add('bg-purple-100', 'border-purple-400');
                setTimeout(() => {
                    bundleCard.classList.remove('bg-purple-100', 'border-purple-400');
                }, 500);
            }

            showSuccessNotification('Bundle ditambahkan ke keranjang');
        }

        // Show stock alert
        function showStockAlert(productName, availableStock) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className =
                'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
            toast.innerHTML = `
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">Stok Tidak Mencukupi!</p>
                    <p class="text-sm">${productName} hanya tersisa ${availableStock} item</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;

            document.body.appendChild(toast);

            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Remove item from cart
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        // Update item quantity
        function updateQuantity(index, change) {
            const item = cart[index];
            const newQuantity = item.quantity + change;

            // Check if trying to increase quantity
            if (change > 0) {
                // Check if new quantity would exceed stock
                if (newQuantity > item.stock) {
                    showStockAlert(item.name, item.stock);
                    return;
                }
            }

            item.quantity = newQuantity;

            if (item.quantity <= 0) {
                removeFromCart(index);
            } else {
                updateCartDisplay();
            }
        }

        // Set quantity manually from input field
        function setQuantity(index, value, maxStock) {
            const item = cart[index];
            let newQuantity = parseInt(value);

            // Validate input
            if (isNaN(newQuantity) || newQuantity < 1) {
                newQuantity = 1;
            }

            // Check if quantity exceeds stock
            if (newQuantity > maxStock) {
                showStockAlert(item.name, maxStock);
                newQuantity = maxStock;
            }

            item.quantity = newQuantity;
            updateCartDisplay();
        }

        // Show stock alert
        function showStockAlert(productName, availableStock) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className =
                'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
            toast.innerHTML = `
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">Stok Tidak Mencukupi!</p>
                    <p class="text-sm">${productName} hanya tersisa ${availableStock} item</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;

            document.body.appendChild(toast);

            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Calculate points based on transaction
        function calculatePoints() {
            let totalPoints = 0;

            // Hitung subtotal
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            // 1. Poin dari kelipatan 10k (5 poin per 10k)
            const kelipatanDizrib = Math.floor(subtotal / 10000);
            const poinDariTransaksi = kelipatanDizrib * 5;
            totalPoints += poinDariTransaksi;

            // 2. Poin dari bundle (10 poin per bundle)
            const jumlahBundle = cart.filter(item => item.isBundle).length;
            const poinDariBundle = jumlahBundle * 10;
            totalPoints += poinDariBundle;

            return totalPoints;
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            const mobileCartItemsContainer = document.getElementById('mobileCartItems');
            const cartCount = document.getElementById('cartCount');
            const mobileCartCount = document.getElementById('mobileCartCount');

            // Update cart count with null checks
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            if (cartCount) cartCount.textContent = totalItems;
            if (mobileCartCount) mobileCartCount.textContent = totalItems;

            if (cart.length === 0) {
                const emptyCartHTML = `
                    <div class="text-center text-gray-500 py-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-base font-medium text-gray-900 mb-2">Keranjang Kosong</h3>
                        <p class="text-xs text-gray-500 mb-3">Belum ada produk yang dipilih</p>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-400 mb-1">💡 Tips:</p>
                            <p class="text-xs text-gray-500">Klik produk di katalog untuk menambahkan ke keranjang</p>
                        </div>
                    </div>
                `;
                if (cartItemsContainer) cartItemsContainer.innerHTML = emptyCartHTML;
                if (mobileCartItemsContainer) mobileCartItemsContainer.innerHTML = emptyCartHTML;
            } else {
                const cartHTML = cart.map((item, index) => {
                    const isBundle = item.isBundle === true;
                    const iconClass = isBundle ? 'fa-box-open text-purple-500' : 'fa-utensils text-gray-400';
                    const borderClass = isBundle ? 'border-purple-200 bg-purple-50' : 'border-gray-100';
                    const bundleBadge = isBundle ?
                        '<span class="text-[10px] bg-purple-500 text-white px-1.5 py-0.5 rounded-full font-medium">Bundle</span>' :
                        '';

                    return `
                    <div class="flex items-center space-x-2 p-2 bg-white rounded-lg mb-2 border ${borderClass} hover:border-gray-200 transition-colors">
                        <div class="w-10 h-10 ${isBundle ? 'bg-purple-100' : 'bg-gray-100'} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas ${iconClass} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1 mb-0.5">
                                <h4 class="font-medium text-xs text-gray-900 truncate">${item.name}</h4>
                                ${bundleBadge}
                            </div>
                            <p class="${isBundle ? 'text-purple-600' : 'text-green-600'} font-semibold text-xs">Rp ${item.price.toLocaleString('id-ID')}</p>
                            <p class="text-gray-400 text-xs">Stok: ${item.stock}</p>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button onclick="updateQuantity(${index}, -1)" 
                                    class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fas fa-minus text-xs text-gray-600"></i>
                            </button>
                            <input type="number" 
                                   value="${item.quantity}" 
                                   min="1" 
                                   max="${item.stock}"
                                   onchange="setQuantity(${index}, this.value, ${item.stock})"
                                   onkeypress="return isNumber(event)"
                                   class="w-12 text-center font-medium text-xs border border-gray-200 rounded px-1 py-1 focus:outline-none focus:ring-1 focus:ring-green-400 focus:border-green-400"
                            />
                            <button onclick="updateQuantity(${index}, 1)" 
                                    ${item.quantity >= item.stock ? 'disabled' : ''}
                                    class="w-6 h-6 rounded-full ${item.quantity >= item.stock ? 'bg-gray-50 cursor-not-allowed opacity-50' : 'bg-gray-100 hover:bg-gray-200'} border border-gray-200 flex items-center justify-center transition-colors">
                                <i class="fas fa-plus text-xs text-gray-600"></i>
                            </button>
                        </div>
                        <button onclick="removeFromCart(${index})" 
                                class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition-colors flex-shrink-0">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                `;
                }).join('');

                if (cartItemsContainer) cartItemsContainer.innerHTML = cartHTML;
                if (mobileCartItemsContainer) mobileCartItemsContainer.innerHTML = cartHTML;
            }

            // Regenerate points when cart changes (only if there are items)
            if (cart.length > 0 && selectedCustomer && selectedCustomer.id !== 'walk-in') {
                // Hitung poin berdasarkan aturan baru
                window.currentGachaPoin = calculatePoints();
            } else {
                // Clear points if cart is empty or no customer selected
                window.currentGachaPoin = null;
            }

            // Update totals
            updateTotals();
        }

        // Update totals
        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            // Get tax rate from backend
            const taxRate = {{ $pajak ? $pajak->persen : 0 }} / 100;
            const tax = subtotal * taxRate;

            // Calculate discount
            let discount = 0;

            if (discountMode === 'promo') {
                // Calculate discount from promo code
                const promoSelect = document.getElementById('promoCode');
                if (promoSelect && promoSelect.value) {
                    const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                    const promoType = selectedOption.getAttribute('data-type');
                    const promoValue = parseFloat(selectedOption.getAttribute('data-value'));
                    const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                    const maxDiscount = parseFloat(selectedOption.getAttribute('data-max')) || 0;

                    // Check if subtotal meets minimum transaction
                    if (subtotal >= minTransaction) {
                        if (promoType === 'diskon_persen') {
                            discount = subtotal * (promoValue / 100);
                            // Apply max discount if set
                            if (maxDiscount > 0 && discount > maxDiscount) {
                                discount = maxDiscount;
                            }
                        } else if (promoType === 'cashback') {
                            discount = promoValue;
                        }
                    }
                }
            } else if (discountMode === 'manual') {
                // Calculate manual discount
                if (manualDiscountValue > 0) {
                    if (manualDiscountType === 'persen') {
                        discount = subtotal * (manualDiscountValue / 100);
                    } else {
                        discount = manualDiscountValue;
                        // Don't allow discount greater than subtotal
                        if (discount > subtotal) {
                            discount = subtotal;
                        }
                    }
                }
            }

            // Apply points discount (1 point = Rp 1)
            const pointsDiscount = usedPoints;

            const total = subtotal - discount - pointsDiscount + tax;

            // Update UI elements with null checks
            const subtotalEl = document.getElementById('subtotal');
            const taxEl = document.getElementById('tax');
            const totalEl = document.getElementById('total');
            const totalToPayEl = document.getElementById('totalToPay');
            const nonCashTotalEl = document.getElementById('nonCashTotal');
            const mobileTotalEl = document.getElementById('mobileTotal');
            const cashAmountEl = document.getElementById('cashAmount');

            if (subtotalEl) subtotalEl.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            if (taxEl) taxEl.textContent = `Rp ${Math.round(tax).toLocaleString('id-ID')}`;
            if (totalEl) totalEl.textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;
            if (totalToPayEl) totalToPayEl.textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;
            if (nonCashTotalEl) nonCashTotalEl.textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;
            if (mobileTotalEl) mobileTotalEl.textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;

            // Update points discount display
            const pointsDiscountEl = document.getElementById('pointsDiscount');
            if (pointsDiscountEl) pointsDiscountEl.textContent = `Rp ${pointsDiscount.toLocaleString('id-ID')}`;

            // Reset payment input when total changes
            if (cashAmountEl) cashAmountEl.value = '';
            calculateChange();

            // Enable/disable pay button
            updatePayButtonState();

            // Update poin yang didapat
            updatePoinEarned();
        }

        // Apply promo function
        function applyPromo() {
            const promoSelect = document.getElementById('promoCode');
            const promoInfo = document.getElementById('promoInfo');

            if (promoSelect && promoSelect.value) {
                const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

                if (subtotal < minTransaction) {
                    promoInfo.className = 'text-xs text-red-500 mt-1';
                    promoInfo.textContent = `Minimum transaksi: Rp ${minTransaction.toLocaleString('id-ID')}`;
                } else {
                    promoInfo.className = 'text-xs text-green-600 mt-1';
                    promoInfo.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Promo berhasil diterapkan!';
                }
            } else {
                promoInfo.textContent = '';
            }

            updateTotals();
        }

        // Update poin yang didapat berdasarkan total belanja
        function updatePoinEarned() {
            const poinEarnedInfo = document.getElementById('poinEarnedInfo');
            const poinEarnedSpan = document.getElementById('poinEarned');

            if (selectedCustomer && selectedCustomer.id !== 'walk-in' && cart.length > 0) {
                // Use gacha points that was already generated in updateCartDisplay
                const poinEarned = window.currentGachaPoin || 0;

                if (poinEarned > 0) {
                    poinEarnedInfo.classList.remove('hidden');
                    poinEarnedSpan.textContent = `${poinEarned.toLocaleString('id-ID')} Poin`;
                } else {
                    poinEarnedInfo.classList.add('hidden');
                }
            } else {
                poinEarnedInfo.classList.add('hidden');
            }
        }

        // Payment related functions
        let selectedPaymentMethod = 'tunai';
        let currentTotal = 0;

        // Handle payment method selection
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove active class from all methods
                    paymentMethods.forEach(m => {
                        m.classList.remove('active', 'border-green-500', 'bg-green-100');
                        m.classList.add('border-gray-200');
                        m.querySelector('i').className = m.querySelector('i').className
                            .replace(/text-green-\d+/, 'text-gray-500');
                        m.querySelector('p').className = m.querySelector('p').className
                            .replace(/text-green-\d+/, 'text-gray-600');
                    });

                    // Add active class to selected method
                    this.classList.add('active', 'border-green-500', 'bg-green-100');
                    this.classList.remove('border-gray-200');
                    this.querySelector('i').className = this.querySelector('i').className.replace(
                        /text-gray-\d+/, 'text-green-600');
                    this.querySelector('p').className = this.querySelector('p').className.replace(
                        /text-gray-\d+/, 'text-green-600');

                    selectedPaymentMethod = this.getAttribute('data-method');
                    togglePaymentInput();
                });
            });

            // Add event listener for bundle cards
            document.addEventListener('click', function(e) {
                const bundleCard = e.target.closest('.bundle-card');
                if (bundleCard) {
                    const bundleId = parseInt(bundleCard.getAttribute('data-bundle-id'));
                    const bundleName = bundleCard.getAttribute('data-bundle-name');
                    const bundleStock = parseInt(bundleCard.getAttribute('data-bundle-stock'));
                    const bundlePrice = parseFloat(bundleCard.getAttribute('data-bundle-price'));

                    try {
                        const bundleProductsJson = bundleCard.getAttribute('data-bundle-products');
                        const bundleProducts = JSON.parse(bundleProductsJson);

                        console.log('Bundle card clicked:', {
                            bundleId,
                            bundleName,
                            bundleProducts,
                            bundleStock,
                            bundlePrice
                        });
                        addBundleToCart(bundleId, bundleName, bundleProducts, bundleStock, bundlePrice);
                    } catch (error) {
                        console.error('Error parsing bundle data:', error);
                        showErrorNotification('Gagal menambahkan bundle: Data tidak valid');
                    }
                }
            });
        });

        // Toggle payment input based on method
        function togglePaymentInput() {
            const cashInput = document.getElementById('cashPaymentInput');
            const nonCashInfo = document.getElementById('nonCashPaymentInfo');
            const paymentIcon = document.getElementById('paymentIcon');
            const paymentMethodName = document.getElementById('selectedPaymentMethod');

            if (selectedPaymentMethod === 'tunai') {
                cashInput.classList.remove('hidden');
                nonCashInfo.classList.add('hidden');
            } else {
                cashInput.classList.add('hidden');
                nonCashInfo.classList.remove('hidden');

                // Update icon and method name
                switch (selectedPaymentMethod) {
                    case 'kartu':
                        paymentIcon.className = 'fas fa-credit-card text-blue-600 text-2xl';
                        paymentMethodName.textContent = 'Kartu Kredit/Debit';
                        break;
                    case 'qris':
                        paymentIcon.className = 'fas fa-qrcode text-purple-600 text-2xl';
                        paymentMethodName.textContent = 'QRIS';
                        break;
                    case 'transfer':
                        paymentIcon.className = 'fas fa-mobile-alt text-green-600 text-2xl';
                        paymentMethodName.textContent = 'Transfer Bank';
                        break;
                }
            }

            updatePayButtonState();
        }

        // Calculate change
        function calculateChange() {
            const cashAmountInput = document.getElementById('cashAmount');
            const changeInfo = document.getElementById('changeInfo');
            const changeAmount = document.getElementById('changeAmount');
            const changeStatus = document.getElementById('changeStatus');
            const insufficientWarning = document.getElementById('insufficientWarning');
            const shortageAmount = document.getElementById('shortageAmount');

            // Remove all non-digit characters and parse to number
            const cashValue = parseFloat(cashAmountInput.value.replace(/\D/g, '')) || 0;
            const total = getCurrentTotal();

            if (cashValue === 0) {
                changeInfo.classList.add('hidden');
                insufficientWarning.classList.add('hidden');
            } else if (cashValue >= total) {
                const change = cashValue - total;
                changeAmount.textContent = `Rp ${change.toLocaleString('id-ID')}`;

                if (change === 0) {
                    changeAmount.className = 'text-2xl font-bold text-blue-600';
                    changeStatus.textContent = 'Pembayaran pas';
                    changeStatus.className = 'text-xs mt-1 text-blue-600';
                } else {
                    changeAmount.className = 'text-2xl font-bold text-green-600';
                    changeStatus.textContent = 'Kembalian tersedia';
                    changeStatus.className = 'text-xs mt-1 text-green-600';
                }

                changeInfo.classList.remove('hidden');
                changeInfo.className = 'p-3 rounded-lg border-2 border-dashed border-green-200 bg-green-50 text-center';
                insufficientWarning.classList.add('hidden');
            } else {
                const shortage = total - cashValue;
                shortageAmount.textContent = `Rp ${shortage.toLocaleString('id-ID')}`;
                changeInfo.classList.add('hidden');
                insufficientWarning.classList.remove('hidden');
            }

            updatePayButtonState();
        }

        // Get current total
        function getCurrentTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxRate = {{ $pajak ? $pajak->persen : 0 }} / 100;
            const tax = subtotal * taxRate;

            // Calculate discount
            let discount = 0;
            if (discountMode === 'promo') {
                const promoSelect = document.getElementById('promoCode');
                if (promoSelect && promoSelect.value) {
                    const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                    const promoType = selectedOption.getAttribute('data-type');
                    const promoValue = parseFloat(selectedOption.getAttribute('data-value'));
                    const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                    const maxDiscount = parseFloat(selectedOption.getAttribute('data-max')) || 0;

                    if (subtotal >= minTransaction) {
                        if (promoType === 'diskon_persen') {
                            discount = subtotal * (promoValue / 100);
                            if (maxDiscount > 0 && discount > maxDiscount) {
                                discount = maxDiscount;
                            }
                        } else if (promoType === 'cashback') {
                            discount = promoValue;
                        }
                    }
                }
            } else if (discountMode === 'manual') {
                if (manualDiscountValue > 0) {
                    if (manualDiscountType === 'persen') {
                        discount = subtotal * (manualDiscountValue / 100);
                    } else {
                        discount = manualDiscountValue;
                    }
                }
            }

            // Apply points discount (1 point = Rp 1)
            const pointsDiscount = usedPoints || 0;

            // Total = Subtotal + Tax - Discount - Points
            return Math.round(subtotal + tax - discount - pointsDiscount);
        }

        // Set quick amount
        function setQuickAmount(type) {
            const cashAmountInput = document.getElementById('cashAmount');
            const total = getCurrentTotal();

            if (type === 'exact') {
                cashAmountInput.value = total.toLocaleString('id-ID');
            }

            calculateChange();
        }

        // Add quick amount
        function addQuickAmount(amount) {
            const cashAmountInput = document.getElementById('cashAmount');
            const currentValue = parseFloat(cashAmountInput.value.replace(/[^0-9]/g, '')) || 0;
            const newValue = currentValue + amount;

            cashAmountInput.value = newValue.toLocaleString('id-ID');
            calculateChange();
        }

        // Check if input is number
        function isNumber(evt) {
            const charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        // Update pay button state
        function updatePayButtonState() {
            const payButton = document.getElementById('checkoutBtn');
            let isEnabled = false;

            if (cart.length === 0) {
                isEnabled = false;
            } else if (selectedPaymentMethod === 'tunai') {
                const cashValue = parseFloat(document.getElementById('cashAmount').value.replace(/\D/g, '')) || 0;
                const total = getCurrentTotal();
                isEnabled = cashValue >= total;
            } else {
                isEnabled = true; // Non-cash payments are always enabled if cart has items
            }

            payButton.disabled = !isEnabled;
            payButton.classList.toggle('opacity-50', !isEnabled);
            payButton.classList.toggle('cursor-not-allowed', !isEnabled);
        }

        // Toggle mobile cart
        function toggleMobileCart() {
            const modal = document.getElementById('mobileCartModal');
            isMobileCartOpen = !isMobileCartOpen;

            if (isMobileCartOpen) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Process payment
        async function processPayment() {
            if (cart.length === 0) {
                showErrorNotification('Keranjang masih kosong');
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const ppn = Math.round(subtotal * 0.1);
            const finalTotal = subtotal + ppn;

            // Get payment method
            const paymentMethod = document.querySelector('.payment-method.active').dataset.method;

            // Get payment amount for cash
            let uangDiterima = finalTotal;
            let kembalian = 0;

            if (paymentMethod === 'tunai') {
                const cashInput = document.getElementById('cashAmount');
                const cashValue = cashInput.value.replace(/[^0-9]/g, '');

                if (!cashValue || parseInt(cashValue) < finalTotal) {
                    showErrorNotification('Uang tidak mencukupi');
                    return;
                }

                uangDiterima = parseInt(cashValue);
                kembalian = uangDiterima - finalTotal;
            }

            // Prepare data
            const transactionData = {
                metode: paymentMethod,
                id_customer: selectedCustomer && selectedCustomer.id !== 'walk-in' ? selectedCustomer.id : null,
                items: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price
                })),
                ppn: ppn,
                diskon: 0,
                bayar: finalTotal, // Simpan total yang harus dibayar, bukan uang yang diterima
                kembalian: kembalian
            };

            try {
                // Show loading
                const payButton = document.getElementById('checkoutBtn');
                const originalText = payButton.innerHTML;
                payButton.innerHTML = `
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Memproses...</span>
                    </div>
                `;
                payButton.disabled = true;

                // Send to backend
                const response = await fetch('{{ route('kasir.transaksi.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(transactionData)
                });

                // Check if response is OK (First payment function - processPayment)
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error response:', errorText);
                    throw new Error(`Server error (${response.status}): ${response.statusText}`);
                }

                // Try to parse JSON response
                let result;
                try {
                    result = await response.json();
                } catch (parseError) {
                    console.error('Failed to parse JSON response');
                    throw new Error('Server returned invalid response');
                }

                if (result.success) {

                    // Show success notification
                    showSuccessNotification('Pembayaran berhasil');

                    // Clear cart and reset order number
                    cart = [];
                    currentOrderNumber = null;
                    updateCartDisplay();
                    updateOrderInfo();

                    // Reset cash input
                    document.getElementById('cashAmount').value = '';
                    document.getElementById('changeInfo').classList.add('hidden');

                    // Close mobile cart if open
                    if (isMobileCartOpen) {
                        toggleMobileCart();
                    }

                    // Reload page to update product stock display
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);

                } else {
                    // Error
                    showErrorNotification('Gagal memproses transaksi: ' + result.message);
                }

                // Reset button
                payButton.innerHTML = originalText;
                payButton.disabled = false;

            } catch (error) {
                console.error('Error:', error);
                showErrorNotification('Terjadi kesalahan saat memproses pembayaran');

                // Reset button
                const payButton = document.getElementById('checkoutBtn');
                payButton.innerHTML = '<i class="fas fa-check mr-2"></i>Bayar';
                payButton.disabled = false;
            }
        }

        // Held Transactions Storage
        let heldTransactions = JSON.parse(localStorage.getItem('heldTransactions') || '[]');

        // Hold transaction
        function holdTransaction() {
            if (cart.length === 0) {
                showErrorNotification('Keranjang kosong! Tambahkan produk terlebih dahulu.');
                return;
            }

            const heldTransaction = {
                id: Date.now(),
                timestamp: new Date().toISOString(),
                cart: JSON.parse(JSON.stringify(cart)), // Deep copy
                customer: selectedCustomer ? JSON.parse(JSON.stringify(selectedCustomer)) : null,
                orderNumber: currentOrderNumber,
                itemCount: cart.reduce((sum, item) => sum + item.quantity, 0),
                total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
            };

            heldTransactions.push(heldTransaction);
            localStorage.setItem('heldTransactions', JSON.stringify(heldTransactions));

            // Clear current transaction
            cart = [];
            selectedCustomer = null;
            currentOrderNumber = null;
            updateCartDisplay();
            updateOrderInfo();
            clearCustomerSelection();

            // Update held transactions display
            updateHeldTransactionsDisplay();

            showSuccessNotification(`Transaksi ditahan (${heldTransaction.itemCount} item)`);
        }

        // Load held transaction
        function loadHeldTransaction(heldId) {
            const transaction = heldTransactions.find(t => t.id === heldId);
            if (!transaction) return;

            // Ask for confirmation if current cart is not empty
            if (cart.length > 0) {
                if (!confirm(
                        'Keranjang saat ini tidak kosong. Apakah Anda ingin mengganti dengan transaksi yang ditahan?')) {
                    return;
                }
            }

            // Load transaction data
            cart = JSON.parse(JSON.stringify(transaction.cart));
            selectedCustomer = transaction.customer ? JSON.parse(JSON.stringify(transaction.customer)) : null;
            currentOrderNumber = transaction.orderNumber;

            // Update displays
            updateCartDisplay();
            updateOrderInfo();

            // Update customer selection display
            if (selectedCustomer) {
                const infoDiv = document.getElementById('selectedCustomerInfo');
                const nameSpan = document.getElementById('selectedCustomerName');
                const contactSpan = document.getElementById('selectedCustomerContact');
                const customerSelect = document.getElementById('customerSelect');

                infoDiv.classList.remove('hidden');
                nameSpan.textContent = selectedCustomer.name;
                contactSpan.textContent = selectedCustomer.phone + (selectedCustomer.email !== '-' ? ' • ' +
                    selectedCustomer.email : '');

                if (selectedCustomer.id === 'walk-in') {
                    customerSelect.value = 'walk-in';
                } else {
                    customerSelect.value = selectedCustomer.id;
                }
            }

            // Remove from held transactions
            heldTransactions = heldTransactions.filter(t => t.id !== heldId);
            localStorage.setItem('heldTransactions', JSON.stringify(heldTransactions));
            updateHeldTransactionsDisplay();

            showSuccessNotification('Transaksi dimuat dari daftar ditahan');
        }

        // Store transaction ID to delete
        let transactionToDelete = null;

        // Delete held transaction
        function deleteHeldTransaction(heldId, event) {
            event.stopPropagation(); // Prevent loading the transaction

            transactionToDelete = heldId;
            openDeleteHeldModal();
        }

        // Open delete confirmation modal
        function openDeleteHeldModal() {
            const modal = document.getElementById('deleteHeldModal');
            const modalContent = document.getElementById('deleteHeldModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        // Close delete confirmation modal
        function closeDeleteHeldModal() {
            const modal = document.getElementById('deleteHeldModal');
            const modalContent = document.getElementById('deleteHeldModalContent');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                transactionToDelete = null;
            }, 200);
        }

        // Confirm delete held transaction
        function confirmDeleteHeldTransaction() {
            if (!transactionToDelete) return;

            heldTransactions = heldTransactions.filter(t => t.id !== transactionToDelete);
            localStorage.setItem('heldTransactions', JSON.stringify(heldTransactions));
            updateHeldTransactionsDisplay();
            closeDeleteHeldModal();

            showSuccessNotification('Transaksi ditahan dihapus');
        }

        // Update held transactions display
        function updateHeldTransactionsDisplay() {
            const heldCard = document.getElementById('heldTransactionsCard');
            const heldList = document.getElementById('heldTransactionsList');
            const heldCount = document.getElementById('heldCount');

            if (heldTransactions.length === 0) {
                heldCard.style.display = 'none';
                return;
            }

            heldCard.style.display = 'block';
            heldCount.textContent = `${heldTransactions.length} Transaksi`;

            heldList.innerHTML = heldTransactions.map(transaction => {
                const date = new Date(transaction.timestamp);
                const timeStr = date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                const customerName = transaction.customer ? transaction.customer.name : 'Walk-in Customer';

                return `
                    <div onclick="loadHeldTransaction(${transaction.id})" 
                         class="bg-white border-2 border-orange-200 rounded-xl p-4 hover:border-orange-400 hover:shadow-md transition-all cursor-pointer">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-receipt text-orange-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">${customerName}</p>
                                    <p class="text-xs text-gray-500">${timeStr}</p>
                                </div>
                            </div>
                            <button onclick="deleteHeldTransaction(${transaction.id}, event)" 
                                    class="text-red-500 hover:text-red-700 text-sm" 
                                    title="Hapus">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">${transaction.itemCount} Item</span>
                                <span class="font-semibold text-orange-600">Rp${transaction.total.toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Clear cart
        function clearCart() {
            if (cart.length === 0) return;

            // Langsung hapus tanpa konfirmasi
            currentOrderNumber = null;
            cart = [];
            updateCartDisplay();
            updateOrderInfo();

            // Tampilkan notifikasi sukses
            showSuccessNotification('Berhasil hapus item');
        }

        // Show success notification
        function showSuccessNotification(message) {
            const toast = document.createElement('div');
            toast.className =
                'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
            toast.innerHTML = `
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold">${message}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                `;

            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Show error notification
        function showErrorNotification(message) {
            const toast = document.createElement('div');
            toast.className =
                'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
            toast.innerHTML = `
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold">${message}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                `;

            document.body.appendChild(toast);

            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000); // Update every minute
            updateCartDisplay();
        });

        // Close mobile cart when clicking outside
        const mobileCartModal = document.getElementById('mobileCartModal');
        if (mobileCartModal) {
            mobileCartModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    toggleMobileCart();
                }
            });
        }

        // Close add customer modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('addCustomerModal');
            if (modal && e.target === modal) {
                closeAddCustomerModal();
            }
        });

        // Bluetooth Printer Functionality
        let bluetoothDevice = null;
        let bluetoothCharacteristic = null;
        let printerConnected = false;

        // Check if Bluetooth is supported
        function isBluetoothSupported() {
            return 'bluetooth' in navigator;
        }

        // Load saved printer connection
        function loadSavedPrinter() {
            const savedPrinter = localStorage.getItem('bluetoothPrinter');
            if (savedPrinter) {
                const printerData = JSON.parse(savedPrinter);
                updatePrinterStatus('Tersimpan: ' + printerData.name, 'saved');
            }
        }

        // Connect to Bluetooth printer
        async function connectBluetoothPrinter() {
            if (!isBluetoothSupported()) {
                showErrorNotification('Browser tidak mendukung Bluetooth');
                return;
            }

            try {
                // Show loading
                updatePrinterStatus('Mencari printer...', 'connecting');

                // Request Bluetooth device
                bluetoothDevice = await navigator.bluetooth.requestDevice({
                    filters: [{
                            services: ['000018f0-0000-1000-8000-00805f9b34fb']
                        }, // ESC/POS service
                        {
                            namePrefix: 'POS'
                        },
                        {
                            namePrefix: 'Printer'
                        },
                        {
                            namePrefix: 'EPPOS'
                        },
                        {
                            namePrefix: 'RPP'
                        }
                    ],
                    optionalServices: [
                        '000018f0-0000-1000-8000-00805f9b34fb',
                        '0000ff00-0000-1000-8000-00805f9b34fb',
                        '49535343-fe7d-4ae5-8fa9-9fafd205e455'
                    ]
                });

                // Connect to device
                const server = await bluetoothDevice.gatt.connect();
                updatePrinterStatus('Terhubung ke: ' + bluetoothDevice.name, 'connected');

                // Get service and characteristic
                try {
                    const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                    bluetoothCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                } catch (e) {
                    // Try alternative service UUID
                    try {
                        const service = await server.getPrimaryService('0000ff00-0000-1000-8000-00805f9b34fb');
                        bluetoothCharacteristic = await service.getCharacteristic(
                            '0000ff01-0000-1000-8000-00805f9b34fb');
                    } catch (e2) {
                        // Try another alternative
                        const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                        bluetoothCharacteristic = await service.getCharacteristic(
                            '49535343-1e4d-4bd9-ba61-23c647249616');
                    }
                }

                printerConnected = true;

                // Save printer info to localStorage
                const printerInfo = {
                    id: bluetoothDevice.id,
                    name: bluetoothDevice.name,
                    connected: true,
                    savedAt: new Date().toISOString()
                };
                localStorage.setItem('bluetoothPrinter', JSON.stringify(printerInfo));

                // Handle disconnection
                bluetoothDevice.addEventListener('gattserverdisconnected', onPrinterDisconnected);

                // Test connection with a small print
                await testPrinterConnection();

            } catch (error) {
                console.error('Bluetooth connection error:', error);
                updatePrinterStatus('Gagal terhubung', 'error');

                let errorMessage = 'Gagal terhubung ke printer';
                if (error.name === 'NotFoundError') {
                    errorMessage = 'Printer tidak ditemukan';
                } else if (error.name === 'SecurityError') {
                    errorMessage = 'Akses Bluetooth ditolak';
                }
                showErrorNotification(errorMessage);
            }
        }

        // Test printer connection
        async function testPrinterConnection() {
            if (!bluetoothCharacteristic) return;

            try {
                const testData = new Uint8Array([0x1B, 0x40]); // ESC @ (Initialize printer)
                await bluetoothCharacteristic.writeValue(testData);
                updatePrinterStatus('Terhubung & Siap: ' + bluetoothDevice.name, 'ready');
            } catch (error) {
                console.error('Test print failed:', error);
                updatePrinterStatus('Terhubung (Tidak responsif)', 'warning');
            }
        }

        // Reconnect to saved printer
        async function reconnectSavedPrinter() {
            const savedPrinter = localStorage.getItem('bluetoothPrinter');
            if (!savedPrinter) return false;

            try {
                const printerData = JSON.parse(savedPrinter);
                updatePrinterStatus('Menghubungkan ulang...', 'connecting');

                // Try to get the saved device
                const devices = await navigator.bluetooth.getDevices();
                bluetoothDevice = devices.find(device => device.id === printerData.id);

                if (bluetoothDevice && bluetoothDevice.gatt.connected) {
                    const server = bluetoothDevice.gatt;

                    // Get service and characteristic
                    try {
                        const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                        bluetoothCharacteristic = await service.getCharacteristic(
                            '00002af1-0000-1000-8000-00805f9b34fb');
                    } catch (e) {
                        try {
                            const service = await server.getPrimaryService('0000ff00-0000-1000-8000-00805f9b34fb');
                            bluetoothCharacteristic = await service.getCharacteristic(
                                '0000ff01-0000-1000-8000-00805f9b34fb');
                        } catch (e2) {
                            const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                            bluetoothCharacteristic = await service.getCharacteristic(
                                '49535343-1e4d-4bd9-ba61-23c647249616');
                        }
                    }

                    printerConnected = true;
                    updatePrinterStatus('Terhubung: ' + bluetoothDevice.name, 'connected');
                    await testPrinterConnection();
                    return true;
                } else if (bluetoothDevice) {
                    // Try to reconnect
                    const server = await bluetoothDevice.gatt.connect();
                    // Repeat connection process...
                    return await reconnectSavedPrinter();
                }
            } catch (error) {
                console.error('Reconnection failed:', error);
                updatePrinterStatus('Gagal terhubung ulang', 'error');
            }

            return false;
        }

        // Disconnect printer
        function disconnectPrinter() {
            if (bluetoothDevice && bluetoothDevice.gatt.connected) {
                bluetoothDevice.gatt.disconnect();
            }

            bluetoothDevice = null;
            bluetoothCharacteristic = null;
            printerConnected = false;

            // Remove from localStorage
            localStorage.removeItem('bluetoothPrinter');

            updatePrinterStatus('Tidak terhubung', 'disconnected');
        }

        // Handle printer disconnection
        function onPrinterDisconnected() {
            printerConnected = false;
            bluetoothCharacteristic = null;
            updatePrinterStatus('Terputus', 'disconnected');
        }

        // Update printer status UI
        function updatePrinterStatus(message, status) {
            const statusElement = document.getElementById('printerStatus');
            const dot = statusElement.querySelector('.w-2.h-2');
            const text = statusElement.querySelector('span');

            text.textContent = message;

            // Update status indicator color
            dot.className = 'w-2 h-2 rounded-full ';
            switch (status) {
                case 'connected':
                case 'ready':
                    dot.className += 'bg-green-500';
                    break;
                case 'connecting':
                case 'saved':
                    dot.className += 'bg-yellow-500';
                    break;
                case 'error':
                case 'disconnected':
                    dot.className += 'bg-red-500';
                    break;
                case 'warning':
                    dot.className += 'bg-orange-500';
                    break;
                default:
                    dot.className += 'bg-gray-400';
            }
        }

        // Toggle printer settings panel
        function togglePrinterSettings() {
            const panel = document.getElementById('printerSettingsPanel');
            panel.classList.toggle('hidden');
        }

        // Generate receipt text for ESC/POS printer
        function generateReceipt(transactionId = null) {
            try {
                const now = new Date();
                const dateStr = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: '2-digit'
                });
                const timeStr = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const taxRate = {{ $pajak ? $pajak->persen : 0 }} / 100;
                const taxPercent = {{ $pajak ? $pajak->persen : 0 }};
                const tax = Math.round(subtotal * taxRate);

                let discount = 0;

                if (discountMode === 'promo') {
                    const promoSelect = document.getElementById('promoCode');
                    if (promoSelect && promoSelect.value) {
                        const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                        const promoType = selectedOption.getAttribute('data-type');
                        const promoValue = parseFloat(selectedOption.getAttribute('data-value'));
                        const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                        const maxDiscount = parseFloat(selectedOption.getAttribute('data-max')) || 0;

                        if (subtotal >= minTransaction) {
                            if (promoType === 'diskon_persen') {
                                discount = Math.round(subtotal * (promoValue / 100));
                                if (maxDiscount > 0 && discount > maxDiscount) discount = maxDiscount;
                            } else if (promoType === 'cashback') {
                                discount = promoValue;
                            }
                        }
                    }
                } else if (discountMode === 'manual') {
                    if (manualDiscountType === 'persen') {
                        discount = Math.round(subtotal * (manualDiscountValue / 100));
                    } else {
                        discount = Math.round(manualDiscountValue);
                    }
                }

                const poinUsed = usedPoints || 0;
                const total = subtotal + tax - discount - poinUsed;

                // Format helpers - width 32 chars for thermal printer
                const fmt = (p) => {
                    const num = Math.round(p);
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                };
                const pad = (left, right, width = 32) => {
                    const spaces = width - left.length - right.length;
                    return left + (spaces > 0 ? ' '.repeat(spaces) : ' ') + right;
                };
                const center = (text, width = 32) => {
                    const spaces = Math.max(0, Math.floor((width - text.length) / 2));
                    return ' '.repeat(spaces) + text;
                };
                const line = (char = '=', width = 32) => {
                    return char.repeat(width);
                };

                let r = '';

                // Header
                r += center('ROTI & KUE SANJAYA') + '\n';
                r += center('0812-3456-7890') + '\n';
                r += line() + '\n';
                r += dateStr + '  ' + timeStr + '\n';
                r += 'No: ' + (transactionId || currentOrderNumber || '000233') + '\n';
                r += 'Kasir: {{ Auth::user()->name }}' + '\n';
                r += line() + '\n';

                // Items
                cart.forEach((item, index) => {
                    try {
                        if (!item || typeof item !== 'object') return;

                        const name = String(item.name || 'Item');
                        const qty = parseInt(item.quantity) || 1;
                        const price = parseInt(item.price) || 0;
                        const itemTotal = price * qty;

                        // Item name on first line
                        r += name + '\n';

                        // Quantity and calculation on second line with right-aligned total
                        const calcStr = qty + ' x ' + fmt(price);
                        r += pad(calcStr, fmt(itemTotal)) + '\n';

                    } catch (e) {
                        console.error('Item format error:', e);
                    }
                });

                // Show count if many items
                if (cart.length > 5) {
                    r += `... dan ${cart.length - 5} item lainnya\n`;
                }

                r += line() + '\n';

                // Summary
                r += pad('Subtotal:', fmt(subtotal)) + '\n';
                r += pad('Pjk ' + taxPercent + '%:', fmt(tax)) + '\n';

                if (discount > 0) {
                    r += pad('Diskon:', fmt(discount)) + '\n';
                }

                if (poinUsed > 0) {
                    r += pad('Poin:', fmt(poinUsed)) + '\n';
                }

                r += line() + '\n';
                r += pad('TOTAL:', fmt(total)) + '\n';
                r += line() + '\n';

                // Footer
                r += '\n';
                r += center('Terima kasih!') + '\n';
                r += '\n\n\n';

                return r;

            } catch (error) {
                console.error('Error generating receipt:', error);
                // Return minimal safe receipt on error
                return 'ROTI & KUE SANJAYA\n' +
                    'Error generating receipt\n' +
                    'Total: ' + (cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)).toLocaleString(
                    'id-ID') + '\n' +
                    '\n\n\n';
            }
        }

        // Print receipt
        async function printReceipt(transactionId = null) {
            try {
                console.log('Starting print for transaction:', transactionId);

                // Check if printer is connected
                if (!printerConnected || !bluetoothCharacteristic) {
                    throw new Error('Printer not connected. Please connect first.');
                }

                // Check GATT connection and try to reconnect if needed
                if (!bluetoothCharacteristic.service || !bluetoothCharacteristic.service.device || !
                    bluetoothCharacteristic.service.device.gatt.connected) {
                    console.log('GATT disconnected, attempting to reconnect...');
                    try {
                        // Try to reconnect
                        const device = bluetoothCharacteristic.service.device;
                        const server = await device.gatt.connect();
                        const service = await server.getPrimaryService(PRINTER_SERVICE_UUID);
                        bluetoothCharacteristic = await service.getCharacteristic(PRINTER_CHARACTERISTIC_UUID);
                        console.log('Printer reconnected successfully');
                    } catch (reconnectError) {
                        console.error('Failed to reconnect:', reconnectError);
                        throw new Error('Printer connection lost. Please reconnect manually.');
                    }
                }

                const receipt = generateReceipt(transactionId);

                // Validate receipt content
                if (!receipt || receipt.length === 0) {
                    throw new Error('Receipt content is empty');
                }

                console.log('Receipt generated successfully, length:', receipt.length, 'chars');

                const encoder = new TextEncoder();
                const data = encoder.encode(receipt);

                console.log('Receipt data size:', data.length, 'bytes');

                // Check connection status
                if (!bluetoothCharacteristic || !bluetoothCharacteristic.service || !bluetoothCharacteristic.service
                    .device) {
                    throw new Error('Printer not properly connected');
                }

                if (!bluetoothCharacteristic.service.device.gatt.connected) {
                    throw new Error('GATT connection not active');
                }

                // OPTIMIZED STRATEGY: Smaller chunks with longer delays
                console.log('Printing with optimized settings...');

                // Initial wait for printer ready
                await new Promise(resolve => setTimeout(resolve, 1500));

                const chunkSize = 10; // REDUCED from 20 to 10 for better stability
                let sentBytes = 0;
                let chunkCount = 0;
                const totalChunks = Math.ceil(data.length / chunkSize);
                let retryCount = 0;
                const maxRetries = 3;

                for (let i = 0; i < data.length; i += chunkSize) {
                    const chunk = data.slice(i, Math.min(i + chunkSize, data.length));
                    chunkCount++;

                    // Verify connection before write
                    if (!bluetoothCharacteristic.service.device.gatt.connected) {
                        throw new Error('Printer disconnected at byte ' + sentBytes);
                    }

                    let chunkSent = false;
                    retryCount = 0;

                    while (!chunkSent && retryCount < maxRetries) {
                        try {
                            // Write chunk
                            await bluetoothCharacteristic.writeValue(chunk);
                            sentBytes += chunk.length;
                            chunkSent = true;

                            // Progress logging
                            if (chunkCount % 5 === 0 || chunkCount === totalChunks) {
                                const progress = Math.round((sentBytes / data.length) * 100);
                                console.log(`Progress: ${sentBytes}/${data.length} bytes (${progress}%)`);
                            }

                            // INCREASED delay for printer buffer
                            await new Promise(resolve => setTimeout(resolve, 800));

                            // Extra buffer drain every 2 chunks (more frequent)
                            if (chunkCount % 2 === 0 && i + chunkSize < data.length) {
                                await new Promise(resolve => setTimeout(resolve, 800));
                            }

                        } catch (writeError) {
                            retryCount++;
                            console.error(`Write error at chunk ${chunkCount} (attempt ${retryCount}):`, writeError);

                            if (retryCount >= maxRetries) {
                                throw new Error(
                                    `Print failed at byte ${sentBytes}/${data.length} after ${maxRetries} retries`);
                            }

                            // Wait before retry
                            await new Promise(resolve => setTimeout(resolve, 1000));
                        }
                    }
                }

                // Final wait for printer to complete
                console.log('Finalizing print...');
                await new Promise(resolve => setTimeout(resolve, 2000));

                console.log('Print completed successfully');
                return true;
            } catch (error) {
                console.error('Print error:', error.message);
                console.error('Error details:', error);

                // Reset connection on error
                printerConnected = false;
                bluetoothCharacteristic = null;

                // Show user-friendly error
                showErrorNotification('Gagal mencetak struk: ' + error.message);

                return false;
            }
        }

        // Process payment with print
        async function processPaymentWithPrint() {
            if (cart.length === 0) {
                showErrorNotification('Keranjang masih kosong');
                return;
            }

            // Calculate subtotal and tax
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxRate = {{ $pajak ? $pajak->persen : 0 }} / 100;
            const ppn = Math.round(subtotal * taxRate);

            // Calculate discount
            let discount = 0;
            if (discountMode === 'promo') {
                const promoSelect = document.getElementById('promoCode');
                if (promoSelect && promoSelect.value) {
                    const selectedOption = promoSelect.options[promoSelect.selectedIndex];
                    const promoType = selectedOption.getAttribute('data-type');
                    const promoValue = parseFloat(selectedOption.getAttribute('data-value'));
                    const minTransaction = parseFloat(selectedOption.getAttribute('data-min')) || 0;
                    const maxDiscount = parseFloat(selectedOption.getAttribute('data-max')) || 0;

                    if (subtotal >= minTransaction) {
                        if (promoType === 'diskon_persen') {
                            discount = subtotal * (promoValue / 100);
                            if (maxDiscount > 0 && discount > maxDiscount) {
                                discount = maxDiscount;
                            }
                        } else if (promoType === 'cashback') {
                            discount = promoValue;
                        }
                    }
                }
            } else if (discountMode === 'manual') {
                if (manualDiscountValue > 0) {
                    if (manualDiscountType === 'persen') {
                        discount = subtotal * (manualDiscountValue / 100);
                    } else {
                        discount = manualDiscountValue;
                    }
                }
            }

            // Get points used
            const pointsUsed = usedPoints || 0;

            // Calculate final total: Subtotal + PPN - Discount - Points
            const finalTotal = Math.round(subtotal + ppn - discount - pointsUsed);

            // Get payment method
            const paymentMethod = document.querySelector('.payment-method.active').dataset.method;

            // Get payment amount for cash
            let bayar = finalTotal;
            let kembalian = 0;

            if (paymentMethod === 'tunai') {
                const cashInput = document.getElementById('cashAmount');
                const cashValue = cashInput.value.replace(/\D/g, '');

                if (!cashValue || parseInt(cashValue) < finalTotal) {
                    showErrorNotification('Uang tidak mencukupi');
                    return;
                }

                bayar = parseInt(cashValue);
                kembalian = bayar - finalTotal;
            }

            // Prepare data
            const transactionData = {
                metode: paymentMethod,
                id_customer: selectedCustomer && selectedCustomer.id !== 'walk-in' ? selectedCustomer.id : null,
                items: cart.map(item => {
                    if (item.isBundle) {
                        // For bundle, transform bundleProducts to match backend expectation
                        // bundleProducts can have either produk_id or id_produk
                        const transformedBundleProducts = item.bundleProducts.map(bp => {
                            // Try to get product ID from various possible locations
                            const productId = bp.produk_id || bp.id_produk || (bp.produk ? bp.produk
                                .id : null);
                            return {
                                id_produk: productId,
                                quantity: bp.quantity
                            };
                        });

                        return {
                            id: item.bundleId,
                            quantity: item.quantity,
                            price: item.price,
                            isBundle: true,
                            bundleProducts: transformedBundleProducts
                        };
                    } else {
                        // Regular product
                        return {
                            id: item.id,
                            quantity: item.quantity,
                            price: item.price,
                            isBundle: false,
                            bundleProducts: null
                        };
                    }
                }),
                ppn: ppn,
                diskon: Math.round(discount),
                bayar: bayar,
                kembalian: kembalian,
                poin_didapat: selectedCustomer && selectedCustomer.id !== 'walk-in' ? (window.currentGachaPoin ||
                    0) : 0,
                poin_digunakan: usedPoints || 0
            };

            try {
                // Show loading
                const payButton = document.getElementById('checkoutBtn');
                const originalText = payButton.innerHTML;
                payButton.innerHTML = `
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Memproses...</span>
                    </div>
                `;
                payButton.disabled = true;

                // Send to backend
                const response = await fetch('{{ route('kasir.transaksi.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(transactionData)
                });

                // Check if response is OK (Second payment function - processPaymentWithPrint)
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error response:', errorText);
                    throw new Error(`Server error (${response.status}): ${response.statusText}`);
                }

                // Try to parse JSON response
                let result;
                try {
                    result = await response.json();
                } catch (parseError) {
                    console.error('Failed to parse JSON response');
                    throw new Error('Server returned invalid response');
                }

                if (result.success) {
                    // Get transaction ID from response
                    const transactionId = result.data?.transaksi_id || null;

                    // Try to print receipt with actual transaction ID
                    try {
                        payButton.innerHTML = `
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-print"></i>
                                <span>Mencetak...</span>
                            </div>
                        `;
                        await printReceipt(transactionId);
                    } catch (printError) {
                        console.error('Print failed:', printError);
                        // Continue with success even if print fails
                    }

                    // Success message
                    showSuccessNotification('Pembayaran berhasil');

                    // Clear cart
                    cart = [];
                    updateCartDisplay();

                    // Reset gacha points for next transaction
                    window.currentGachaPoin = null;

                    // Reset points usage
                    usedPoints = 0;
                    const pointsInput = document.getElementById('pointsInput');
                    if (pointsInput) pointsInput.value = 0;

                    // Reset cash input
                    document.getElementById('cashAmount').value = '';
                    document.getElementById('changeInfo').classList.add('hidden');

                    // Close mobile cart if open
                    if (isMobileCartOpen) {
                        toggleMobileCart();
                    }

                    // Reload page to update product stock display
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);

                } else {
                    // Error
                    showErrorNotification('Gagal memproses transaksi: ' + result.message);
                }

                // Reset button
                payButton.innerHTML = originalText;
                payButton.disabled = false;

            } catch (error) {
                console.error('Error:', error);
                showErrorNotification('Terjadi kesalahan saat memproses pembayaran');

                // Reset button
                const payButton = document.getElementById('checkoutBtn');
                payButton.innerHTML = '<i class="fas fa-check mr-2"></i>Bayar';
                payButton.disabled = false;
            }
        }

        // Format cash input with thousands separator
        function formatCashInput() {
            const cashInput = document.getElementById('cashAmount');
            if (!cashInput) return;

            let isFormatting = false;

            cashInput.addEventListener('input', function(e) {
                if (isFormatting) return;
                isFormatting = true;

                // Remove all non-digit characters
                let value = e.target.value.replace(/\D/g, '');

                // Format with Indonesian locale (uses dot as thousand separator)
                if (value) {
                    const numericValue = parseInt(value, 10);
                    e.target.value = numericValue.toLocaleString('id-ID');
                } else {
                    e.target.value = '';
                }

                calculateChange();
                isFormatting = false;
            });
        }

        // Initialize printer on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedPrinter();
            updateDateTime();
            setInterval(updateDateTime, 60000);
            updateCartDisplay();
            updateOrderInfo();
            updateHeldTransactionsDisplay(); // Load held transactions on page load

            // Initialize customer search
            initializeCustomerSearch();

            // Initialize points display
            updateAvailablePoints();

            // Initialize payment input formatting
            formatCashInput();
            togglePaymentInput();

            // Initialize sidebar state (always hidden by default)
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');

            // Load saved view preference or default to grid
            const savedView = localStorage.getItem('productViewPreference') || 'grid';
            currentView = savedView; // Set current view first

            // Then apply the view and pagination
            toggleView(savedView); // This will also call updatePagination()

            console.log('Initial load complete - View:', currentView, 'Page:', currentPage, 'Items per page:',
                itemsPerPage);

            // Initialize search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                console.log('Search input found, attaching event listeners');

                // Remove any existing event listeners by cloning
                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                // Search on input (real-time)
                newSearchInput.addEventListener('input', function(e) {
                    console.log('Input event triggered, value:', e.target.value);
                    searchProduct();
                });

                // Also search on keyup for immediate feedback
                newSearchInput.addEventListener('keyup', function(e) {
                    if (e.key !== 'Enter') {
                        searchProduct();
                    }
                });

                // Search on Enter key
                newSearchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchProduct();
                    }
                });
            } else {
                console.error('Search input element not found!');
            }

            // Check if Bluetooth is supported
            if (!isBluetoothSupported()) {
                document.getElementById('printerSettings').innerHTML = `
                    <div class="text-center text-gray-500 py-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mb-2"></i>
                        <p class="text-xs">Browser tidak mendukung Bluetooth</p>
                    </div>
                `;
            }
        });
    </script>
    </body>

    </html>
@endsection
