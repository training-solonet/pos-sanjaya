@extends('layouts.kasir.index')

@section('page-title', 'Transaksi Penjualan')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')
    <!-- Transaksi Page Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
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
                                <input type="text" 
                                       id="searchInput"
                                       placeholder="Ketik nama produk untuk mencari..." 
                                       class="w-full pl-12 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors bg-gray-50 focus:bg-white"
                                       autocomplete="off"
                                       oninput="searchProduct()">        
                                <button id="clearSearchBtn" class="absolute inset-y-0 right-0 pr-4 flex items-center hidden" onclick="clearSearch()" title="Hapus pencarian">
                                    <i class="fas fa-times-circle text-gray-400 hover:text-red-500 transition-colors"></i>
                                </button>
                            </div>

                            <!-- Categories -->
                            <div class="flex space-x-2 overflow-x-auto scrollbar-hide">
                                <button class="category-btn px-6 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-xl whitespace-nowrap font-medium shadow-sm" onclick="filterByCategory('semua')" data-category="semua">
                                    <i class="fas fa-th-large mr-2"></i>Semua
                                </button>
                                <button class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors" onclick="filterByCategory('makanan')" data-category="makanan">
                                    <i class="fas fa-utensils mr-2"></i>Makanan
                                </button>
                                <button class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors" onclick="filterByCategory('minuman')" data-category="minuman">
                                    <i class="fas fa-coffee mr-2"></i>Minuman
                                </button>
                                <button class="category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors" onclick="filterByCategory('snack')" data-category="snack">
                                    <i class="fas fa-cookie-bite mr-2"></i>Snack
                                </button>
                            </div>
                        </div>

                        <!-- Content Card Katalog -->
                        <div class="flex-1 p-6 overflow-hidden">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Daftar Produk</h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">Tampilan:</span>
                                        <button id="gridViewBtn" onclick="toggleView('grid')" class="p-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg transition-all" title="Tampilan Grid">
                                            <i class="fas fa-th text-sm"></i>
                                        </button>
                                        <button id="listViewBtn" onclick="toggleView('list')" class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all" title="Tampilan List">
                                            <i class="fas fa-list text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Menampilkan 1-{{ $totalProduk }} dari {{ $totalProduk }} produk
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4 overflow-y-auto max-h-[calc(100vh-16rem)] pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100" id="productGrid">
                                <!-- No results message (hidden by default) -->
                                <div id="noResultsMessage" class="col-span-full text-center py-12 hidden">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                                    <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau scan barcode produk</p>
                                </div>
                                
                                @forelse($produks as $produk)
                                <!-- Product Card: {{ $produk->nama }} -->
                                <div class="product-card group bg-white border-2 border-green-200 rounded-2xl p-5 hover:shadow-xl hover:border-green-400 transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
                                     data-nama="{{ strtolower($produk->nama) }}"
                                     data-id="{{ $produk->id }}"
                                     data-price="{{ $produk->harga }}"
                                     data-stock="{{ $produk->stok }}"
                                     onclick="addToCart({{ $produk->id }}, '{{ addslashes($produk->nama) }}', {{ $produk->harga }}, 'produk-{{ $produk->id }}.jpg', {{ $produk->stok }})">  
                                    <!-- Double Circle Badge with layered effect -->
                                    <div class="relative mb-4">
                                        <div class="absolute top-0 left-0">
                                            <!-- Background circle (slightly higher) -->
                                            <div class="absolute -top-2 left-0 w-12 h-12 bg-emerald-400 opacity-50 rounded-full"></div>
                                            <!-- Front circle with stock number -->
                                            <div class="relative w-12 h-12 bg-emerald-500 text-white rounded-full flex items-center justify-center text-base font-bold product-stock-badge shadow-lg">
                                                {{ $produk->stok }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-details pt-8">
                                        <div class="product-info space-y-2">
                                            <h3 class="font-semibold text-gray-900 text-base product-name leading-tight">{{ $produk->nama }}</h3>
                                            <p class="text-green-600 font-bold text-xl product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                            <div class="flex items-center space-x-1 product-status pt-1">
                                                <span class="text-xs text-gray-500">Produk</span>
                                                <div class="w-1.5 h-1.5 {{ $produk->stok > 0 ? 'bg-success' : 'bg-red-500' }} rounded-full"></div>
                                                <span class="text-xs {{ $produk->stok > 0 ? 'text-success' : 'text-red-500' }} font-medium">{{ $produk->stok > 0 ? 'Tersedia' : 'Habis' }}</span>
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
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
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
                                <h4 class="text-xs font-semibold text-gray-900 mb-2">Ringkasan Pesanan</h4>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-medium" id="subtotal">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">Pajak (10%)</span>
                                        <span class="font-medium" id="tax">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600">Diskon</span>
                                        <span class="text-success font-medium" id="discount">Rp 0</span>
                                    </div>
                                    <hr class="border-gray-200 my-1">
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
                                                    placeholder="0" oninput="calculateChange()"
                                                    onkeypress="return isNumber(event)">
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
                                    <button
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
                <button onclick="toggleMobileCart()" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
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
                <button class="w-full bg-gradient-to-r from-green-400 to-green-700 text-white py-3 rounded-lg font-medium" onclick="processPaymentWithPrint()">
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

        // Generate order number from database
        async function generateOrderNumber() {
            try {
                const response = await fetch('{{ route('kasir.transaksi.api.next-id') }}');
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
            currentView = viewType;
            const productGrid = document.getElementById('productGrid');
            const productCards = document.querySelectorAll('.product-card');
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');
            
            if (viewType === 'grid') {
                // Switch to grid view
                productGrid.className = 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4 overflow-y-auto max-h-[calc(100vh-16rem)] pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100';
                
                // Update product cards for grid view
                productCards.forEach(card => {
                    if (!card.id || card.id !== 'noResultsMessage') {
                        card.className = 'product-card group bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg hover:border-green-400 transition-all duration-300 cursor-pointer transform hover:-translate-y-1';
                        
                        // Reset display based on current filter
                        const productName = card.getAttribute('data-nama');
                        const productCategory = getCategoryByName(productName);
                        const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;
                        
                        if (!matchesCategory) {
                            card.classList.add('hidden-card');
                        }
                        
                        // Adjust image wrapper
                        const imageWrapper = card.querySelector('.product-image-wrapper');
                        if (imageWrapper) {
                            imageWrapper.className = 'relative product-image-wrapper';
                            const imgDiv = imageWrapper.querySelector('div:first-child');
                            if (imgDiv) {
                                imgDiv.className = 'aspect-square bg-gradient-to-br from-green-100 to-green-200 rounded-xl mb-3 flex items-center justify-center overflow-hidden';
                            }
                        }
                        
                        // Adjust product details
                        const details = card.querySelector('.product-details');
                        if (details) {
                            details.className = 'product-details';
                        }
                        
                        const info = card.querySelector('.product-info');
                        if (info) {
                            info.className = 'product-info space-y-1';
                        }
                        
                        const price = card.querySelector('.product-price');
                        if (price) {
                            price.className = 'text-green-600 font-bold text-lg product-price';
                        }
                        
                        const meta = card.querySelector('.product-meta');
                        if (meta) {
                            meta.className = 'flex items-center justify-between product-meta';
                        }
                    }
                });
                
                // Update button states
                gridBtn.className = 'p-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg transition-all';
                listBtn.className = 'p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all';
            } else {
                // Switch to list view
                productGrid.className = 'flex flex-col gap-3 overflow-y-auto max-h-[calc(100vh-16rem)] pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100';
                
                // Update product cards for list view
                productCards.forEach(card => {
                    if (!card.id || card.id !== 'noResultsMessage') {
                        card.className = 'product-card list-view group bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:border-green-400 transition-all duration-300 cursor-pointer';
                        
                        // Reset display based on current filter  
                        const productName = card.getAttribute('data-nama');
                        const productCategory = getCategoryByName(productName);
                        const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;
                        
                        // Show as flex for list view
                        card.style.display = 'flex';
                        if (!matchesCategory) {
                            card.classList.add('hidden-card');
                        }
                        
                        // Adjust image wrapper for list
                        const imageWrapper = card.querySelector('.product-image-wrapper');
                        if (imageWrapper) {
                            imageWrapper.className = 'relative product-image-wrapper flex-shrink-0';
                            const imgDiv = imageWrapper.querySelector('div:first-child');
                            if (imgDiv) {
                                imgDiv.className = 'w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center overflow-hidden';
                            }
                        }
                        
                        // Adjust product details for list
                        const details = card.querySelector('.product-details');
                        if (details) {
                            details.className = 'product-details flex-1 flex items-center justify-between gap-4';
                        }
                        
                        const info = card.querySelector('.product-info');
                        if (info) {
                            info.className = 'product-info flex-1';
                        }
                        
                        const name = card.querySelector('.product-name');
                        if (name) {
                            name.className = 'font-semibold text-gray-900 text-base product-name mb-1';
                        }
                        
                        const price = card.querySelector('.product-price');
                        if (price) {
                            price.className = 'text-green-600 font-bold text-xl product-price';
                        }
                        
                        const meta = card.querySelector('.product-meta');
                        if (meta) {
                            meta.className = 'flex items-center gap-3 product-meta mt-1';
                        }
                    }
                });
                
                // Update button states
                gridBtn.className = 'p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all';
                listBtn.className = 'p-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg transition-all';
            }
            
            // Save preference to localStorage
            localStorage.setItem('productViewPreference', viewType);
        }

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
            const productCards = document.querySelectorAll('.product-card');
            const noResultsMessage = document.getElementById('noResultsMessage');
            const clearBtn = document.getElementById('clearSearchBtn');
            let visibleCount = 0;
            
            // Show/hide clear button
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', searchInput.value.length === 0);
            }
            
            // Loop through all product cards
            productCards.forEach(card => {
                // Skip if this is the no results message
                if (card.id === 'noResultsMessage') return;
                
                const productName = card.getAttribute('data-nama') || '';
                
                // Check if product matches search term
                let matchesSearch = true;
                if (searchTerm !== '') {
                    matchesSearch = productName.indexOf(searchTerm) !== -1;
                }
                
                // Get product category for category filtering
                const productCategory = getCategoryByName(productName);
                const matchesCategory = currentCategory === 'semua' || productCategory === currentCategory;
                
                // Show or hide card based on search and category match
                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                    card.classList.remove('hidden-card');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.add('hidden-card');
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
            
            // Update product count display
            if (typeof updateProductCountDisplay === 'function') {
                updateProductCountDisplay(visibleCount);
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
            
            // Update button states
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => {
                const btnCategory = btn.getAttribute('data-category');
                if (btnCategory === category) {
                    btn.className = 'category-btn px-6 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-xl whitespace-nowrap font-medium shadow-sm';
                } else {
                    btn.className = 'category-btn px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors';
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
            
            // Apply category filter to all products
            const productCards = document.querySelectorAll('.product-card');
            const noResultsMessage = document.getElementById('noResultsMessage');
            let visibleCount = 0;
            
            productCards.forEach(card => {
                if (!card.id || card.id !== 'noResultsMessage') {
                    const productName = card.getAttribute('data-nama');
                    const productCategory = getCategoryByName(productName);
                    const matchesCategory = category === 'semua' || productCategory === category;
                    
                    if (matchesCategory) {
                        // Remove hidden class and set appropriate display
                        card.classList.remove('hidden-card');
                        card.style.display = currentView === 'list' ? 'flex' : 'block';
                        visibleCount++;
                    } else {
                        // Add hidden class to hide card
                        card.classList.add('hidden-card');
                        card.style.display = 'none';
                    }
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
            
            // Update product count display
            updateProductCountDisplay(visibleCount);
        }
        
        // Update product count display
        function updateProductCountDisplay(count) {
            const countDisplays = document.querySelectorAll('.text-sm.text-gray-500');
            countDisplays.forEach(display => {
                if (display.textContent.includes('Menampilkan')) {
                    display.textContent = `Menampilkan 1-${count} dari ${count} produk`;
                }
            });
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
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
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

        // Show stock alert
        function showStockAlert(productName, availableStock) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
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
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
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

            // Update cart display
            function updateCartDisplay() {
                const cartItemsContainer = document.getElementById('cartItems');
                const mobileCartItemsContainer = document.getElementById('mobileCartItems');
                const cartCount = document.getElementById('cartCount');
                const mobileCartCount = document.getElementById('mobileCartCount');

                // Update cart count
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = totalItems;
                mobileCartCount.textContent = totalItems;

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
                    cartItemsContainer.innerHTML = emptyCartHTML;
                    mobileCartItemsContainer.innerHTML = emptyCartHTML;
                } else {
                    const cartHTML = cart.map((item, index) => `
                    <div class="flex items-center space-x-2 p-2 bg-white rounded-lg mb-2 border border-gray-100 hover:border-gray-200 transition-colors">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-utensils text-gray-400 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-xs text-gray-900 truncate">${item.name}</h4>
                            <p class="text-green-600 font-semibold text-xs">Rp ${item.price.toLocaleString('id-ID')}</p>
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
                `).join('');

                    cartItemsContainer.innerHTML = cartHTML;
                    mobileCartItemsContainer.innerHTML = cartHTML;
                }

                // Update totals
                updateTotals();
            }

            // Update totals
            function updateTotals() {
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const discount = 0; // You can implement discount logic here
                const tax = subtotal * 0.1; // 10% tax
                const total = subtotal - discount + tax;

                document.getElementById('subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
                document.getElementById('discount').textContent = `Rp ${discount.toLocaleString('id-ID')}`;
                document.getElementById('tax').textContent = `Rp ${Math.round(tax).toLocaleString('id-ID')}`;
                document.getElementById('total').textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;
                document.getElementById('totalToPay').textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;
                document.getElementById('nonCashTotal').textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;
                document.getElementById('mobileTotal').textContent = `Rp ${Math.round(total).toLocaleString('id-ID')}`;

                // Reset payment input when total changes
                document.getElementById('cashAmount').value = '';
                calculateChange();

                // Enable/disable pay button
                updatePayButtonState();
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

                const cashValue = parseFloat(cashAmountInput.value.replace(/[^0-9]/g, '')) || 0;
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
                const tax = subtotal * 0.1;
                return Math.round(subtotal + tax);
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
                    const cashValue = parseFloat(document.getElementById('cashAmount').value.replace(/[^0-9]/g, '')) || 0;
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
                let bayar = finalTotal;
                let kembalian = 0;

                if (paymentMethod === 'tunai') {
                    const cashInput = document.getElementById('cashAmount');
                    const cashValue = cashInput.value.replace(/[^0-9]/g, '');

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
                    items: cart.map(item => ({
                        id: item.id,
                        quantity: item.quantity,
                        price: item.price
                    })),
                    ppn: ppn,
                    diskon: 0,
                    bayar: bayar,
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

                    const result = await response.json();

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

            // Hold transaction
            function holdTransaction() {
                if (cart.length === 0) return;

                // Save transaction logic here
                showSuccessNotification('Transaksi disimpan');
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
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
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
                toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 animate-slide-in';
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
            document.getElementById('mobileCartModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    toggleMobileCart();
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
            function generateReceipt() {
                const now = new Date();
                const dateStr = now.toLocaleDateString('id-ID');
                const timeStr = now.toLocaleTimeString('id-ID');

                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const tax = subtotal * 0.1;
                const total = subtotal + tax;

                let receipt = '';

                // ESC/POS Commands
                const ESC = '\x1B';
                const INIT = ESC + '@'; // Initialize printer
                const CENTER = ESC + 'a1'; // Center alignment
                const LEFT = ESC + 'a0'; // Left alignment
                const BOLD_ON = ESC + 'E1'; // Bold on
                const BOLD_OFF = ESC + 'E0'; // Bold off
                const CUT = ESC + 'm'; // Cut paper
                const FEED = '\n';

                receipt += INIT;
                receipt += CENTER + BOLD_ON;
                receipt += 'ROTI & KUE SANJAYA\n';
                receipt += BOLD_OFF;
                receipt += '================================\n';
                receipt += LEFT;
                receipt += `Tanggal: ${dateStr}\n`;
                receipt += `Waktu  : ${timeStr}\n`;
                receipt += `Kasir  : Admin\n`;
                receipt += `Order  : #001\n`;
                receipt += '================================\n';

                // Items
                cart.forEach(item => {
                    receipt += `${item.name}\n`;
                    receipt += `${item.quantity} x Rp${item.price.toLocaleString('id-ID')}`;
                    receipt +=
                        `${' '.repeat(Math.max(1, 32 - (`${item.quantity} x Rp${item.price.toLocaleString('id-ID')}` + `Rp${(item.price * item.quantity).toLocaleString('id-ID')}`).length))}`;
                    receipt += `Rp${(item.price * item.quantity).toLocaleString('id-ID')}\n`;
                });

                receipt += '================================\n';
                receipt += `Subtotal:${' '.repeat(15)}Rp${subtotal.toLocaleString('id-ID')}\n`;
                receipt += `Pajak (10%):${' '.repeat(13)}Rp${Math.round(tax).toLocaleString('id-ID')}\n`;
                receipt += '--------------------------------\n';
                receipt += BOLD_ON;
                receipt += `TOTAL:${' '.repeat(18)}Rp${Math.round(total).toLocaleString('id-ID')}\n`;
                receipt += BOLD_OFF;
                receipt += '================================\n';
                receipt += CENTER;
                receipt += 'Terima Kasih!\n';
                receipt += 'Selamat Berbelanja\n\n';
                receipt += LEFT;
                receipt += CUT;

                return receipt;
            }

            // Print receipt
            async function printReceipt() {
                if (!printerConnected || !bluetoothCharacteristic) {
                    // Try to reconnect to saved printer
                    const reconnected = await reconnectSavedPrinter();
                    if (!reconnected) {
                        // Printer tidak terhubung, skip printing dan lanjutkan pembayaran
                        return false;
                    }
                }

                try {
                    const receipt = generateReceipt();
                    const encoder = new TextEncoder();
                    const data = encoder.encode(receipt);

                    // Split data into chunks if too large
                    const chunkSize = 20; // Bluetooth LE characteristic limit
                    for (let i = 0; i < data.length; i += chunkSize) {
                        const chunk = data.slice(i, i + chunkSize);
                        await bluetoothCharacteristic.writeValue(chunk);
                        // Small delay between chunks
                        await new Promise(resolve => setTimeout(resolve, 50));
                    }

                    return true;
                } catch (error) {
                    console.error('Print error:', error);
                    // Tidak perlu alert, hanya log error - pembayaran tetap berhasil
                    return false;
                }
            }

            // Process payment with print
            async function processPaymentWithPrint() {
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
                let bayar = finalTotal;
                let kembalian = 0;

                if (paymentMethod === 'tunai') {
                    const cashInput = document.getElementById('cashAmount');
                    const cashValue = cashInput.value.replace(/[^0-9]/g, '');

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
                    items: cart.map(item => ({
                        id: item.id,
                        quantity: item.quantity,
                        price: item.price
                    })),
                    ppn: ppn,
                    diskon: 0,
                    bayar: bayar,
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

                    const result = await response.json();

                    if (result.success) {
                        // Try to print receipt
                        let printSuccess = false;
                        if (isBluetoothSupported()) {
                            payButton.innerHTML = `
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-print"></i>
                                <span>Mencetak...</span>
                            </div>
                        `;
                            printSuccess = await printReceipt();
                        }

                        // Success message
                        showSuccessNotification('Pembayaran berhasil');

                        // Clear cart
                        cart = [];
                        updateCartDisplay();

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
                cashInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    if (value) {
                        const formattedValue = parseInt(value).toLocaleString('id-ID');
                        e.target.value = formattedValue;
                    }
                    calculateChange();
                });

                // Handle backspace and delete keys
                cashInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' || e.key === 'Delete') {
                        setTimeout(() => {
                            let value = e.target.value.replace(/[^0-9]/g, '');
                            if (value) {
                                const formattedValue = parseInt(value).toLocaleString('id-ID');
                                e.target.value = formattedValue;
                            }
                            calculateChange();
                        }, 10);
                    }
                });
            }

        // Initialize printer on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedPrinter();
            updateDateTime();
            setInterval(updateDateTime, 60000);
            updateCartDisplay();
            updateOrderInfo();
            
            // Initialize payment input formatting
            formatCashInput();
            togglePaymentInput();
            
            // Initialize sidebar state (always hidden by default)
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            
            // Load saved view preference
            const savedView = localStorage.getItem('productViewPreference');
            if (savedView && (savedView === 'grid' || savedView === 'list')) {
                toggleView(savedView);
            }
            
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
