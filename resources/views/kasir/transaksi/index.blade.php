@extends('layouts.kasir.index')

@section('page-title', 'Transaksi Penjualan')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')
    <!-- Main Content -->
    <div class="main-content min-h-screen flex flex-col">
        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 bg-gray-50">
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
                                    <span class="bg-green-100 text-green-600 text-sm px-3 py-1 rounded-lg font-semibold">{{ $totalProduk }} Produk</span>
                                </div>
                            </div>
                            
                            <!-- Search Bar -->
                            <div class="relative mb-4">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       placeholder="Cari produk atau scan barcode..." 
                                       class="w-full pl-12 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors bg-gray-50 focus:bg-white">
                                <button class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <i class="fas fa-qrcode text-gray-400 hover:text-green-600 transition-colors"></i>
                                </button>
                            </div>
                            
                            <!-- Categories -->
                            <div class="flex space-x-2 overflow-x-auto scrollbar-hide">
                                <button class="px-6 py-2.5 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-xl whitespace-nowrap font-medium shadow-sm">
                                    <i class="fas fa-th-large mr-2"></i>Semua
                                </button>
                                <button class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors">
                                    <i class="fas fa-utensils mr-2"></i>Makanan
                                </button>
                                <button class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors">
                                    <i class="fas fa-coffee mr-2"></i>Minuman
                                </button>
                                <button class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl whitespace-nowrap hover:border-green-400 hover:text-green-600 transition-colors">
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
                                        <button class="p-2 bg-gradient-to-r from-green-400 to-green-700 bg-opacity-10 text-white rounded-lg">
                                            <i class="fas fa-th text-sm"></i>
                                        </button>
                                        <button class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-list text b-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Menampilkan 1-{{ $totalProduk }} dari {{ $totalProduk }} produk
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 2xl:grid-cols-4 gap-4 overflow-y-auto max-h-[calc(100vh-16rem)] pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                @forelse($produks as $produk)
                                <!-- Product Card: {{ $produk->nama }} -->
                                <div class="product-card group bg-white border border-gray-200 rounded-2xl p-4 hover:shadow-lg hover:border-green-400 transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
                                     onclick="addToCart({{ $produk->id }}, '{{ addslashes($produk->nama) }}', {{ $produk->harga }}, 'produk-{{ $produk->id }}.jpg')">
                                    <div class="relative">
                                        <div class="aspect-square bg-gradient-to-br from-green-100 to-green-200 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-bread-slice text-green-500 text-2xl group-hover:scale-110 transition-transform"></i>
                                        </div>
                                        <div class="absolute top-2 right-2 w-6 h-6 bg-success text-white rounded-full flex items-center justify-center text-xs font-bold">{{ $produk->stok }}</div>
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="font-semibold text-gray-900 text-sm">{{ $produk->nama }}</h3>
                                        <p class="text-green-600 font-bold text-lg">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Produk</span>
                                            <div class="flex items-center space-x-1">
                                                <div class="w-2 h-2 {{ $produk->stok > 0 ? 'bg-success' : 'bg-red-500' }} rounded-full"></div>
                                                <span class="text-xs {{ $produk->stok > 0 ? 'text-success' : 'text-red-500' }} font-medium">{{ $produk->stok > 0 ? 'Tersedia' : 'Habis' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
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
                                        <p class="text-sm text-gray-500">Order #001 - Hari ini</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="bg-success/10 text-success text-sm px-3 py-1 rounded-lg font-bold" id="cartCount">0 Item</span>
                                    <button class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors" onclick="clearCart()">
                                        <i class="fas fa-trash text-gray-500"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content Card Keranjang -->
                        <div class="flex-1 overflow-hidden">
                            <div class="h-[calc(100vh-28rem)] overflow-y-auto px-4 py-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100" id="cartItems">
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
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                                <input type="text" 
                                                       id="cashAmount" 
                                                       class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-colors text-right font-semibold text-lg"
                                                       placeholder="0"
                                                       oninput="calculateChange()"
                                                       onkeypress="return isNumber(event)">
                                            </div>
                                        </div>
                                    
                                        
                                        <!-- Change Information -->
                                        <div id="changeInfo" class="p-3 rounded-lg border-2 border-dashed border-gray-200 text-center hidden">
                                            <div class="mb-2">
                                                <span class="text-sm text-gray-600">Kembalian:</span>
                                            </div>
                                            <div class="text-2xl font-bold" id="changeAmount">Rp 0</div>
                                            <div id="changeStatus" class="text-xs mt-1"></div>
                                        </div>
                                        
                                        <!-- Insufficient Payment Warning -->
                                        <div id="insufficientWarning" class="p-3 bg-red-50 border border-red-200 rounded-lg text-center hidden">
                                            <div class="flex items-center justify-center space-x-2 text-red-600">
                                                <i class="fas fa-exclamation-triangle text-sm"></i>
                                                <span class="text-sm                                                                                                                                                                                            font-medium">Uang tidak mencukupi</span>
                                            </div>
                                            <div class="text-xs text-red-500 mt-1">
                                                Kurang: <span id="shortageAmount">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Non-cash Payment Info --> 
                                    <div id="nonCashPaymentInfo" class="hidden">
                                        <div class="text-center py-4">
                                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-credit-card text-blue-600 text-2xl" id="paymentIcon"></i>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Pembayaran dengan:</p>
                                            <p class="font-semibold text-gray-900" id="selectedPaymentMethod">Kartu Kredit</p>
                                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                                <p class="text-xs text-blue-600">Total: <span id="nonCashTotal" class="font-bold">Rp 0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="mb-3">
                                <label class="text-xs font-semibold text-gray-900 mb-2 block">Metode Pembayaran</label>
                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <button class="payment-method active p-2 border-2 border-green-500 bg-green-100 rounded-lg text-center transition-all" data-method="tunai">
                                        <i class="fas fa-money-bill text-green-600 mb-1 text-sm"></i>
                                        <p class="text-xs font-medium text-green-600">Tunai</p>
                                    </button>
                                    <button class="payment-method p-2 border-2 border-gray-200 rounded-lg text-center hover:border-gray-300 transition-all" data-method="kartu">
                                        <i class="fas fa-credit-card text-gray-500 mb-1 text-sm"></i>
                                        <p class="text-xs font-medium text-gray-600">Kartu</p>
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button class="payment-method p-2 border-2 border-gray-200 rounded-lg text-center hover:border-gray-300 transition-all" data-method="qris">
                                        <i class="fas fa-qrcode text-gray-500 mb-1 text-sm"></i>
                                        <p class="text-xs font-medium text-gray-600">QRIS</p>
                                    </button>
                                    <button class="payment-method p-2 border-2 border-gray-200 rounded-lg text-center hover:border-gray-300 transition-all" data-method="transfer">
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
                                        <button onclick="togglePrinterSettings()" class="text-xs text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-cog mr-1"></i>Atur
                                        </button>
                                    </div>
                                    <div id="printerStatus" class="flex items-center space-x-2 text-xs">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                        <span class="text-gray-500">Tidak terhubung</span>
                                    </div>
                                    <div id="printerSettingsPanel" class="hidden mt-3 pt-3 border-t border-gray-100">
                                        <div class="space-y-2">
                                            <button onclick="connectBluetoothPrinter()" class="w-full bg-blue-100 text-blue-700 text-sm py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors">
                                                <i class="fas fa-bluetooth mr-2"></i>Hubungkan Printer
                                            </button>
                                            <button onclick="disconnectPrinter()" class="w-full bg-red-100 text-red-700 text-sm py-2 px-3 rounded-lg hover:bg-red-200 transition-colors">
                                                <i class="fas fa-times mr-2"></i>Putuskan Koneksi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="w-full bg-gradient-to-r from-green-400 to-green-700 hover:from-green-500 hover:to-green-800 text-white font-bold py-4 px-4 rounded-xl transition-all transform hover:scale-[1.02] shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" id="checkoutBtn" onclick="processPaymentWithPrint()" disabled>
                                    <div class="flex items-center justify-center space-x-2">
                                        <i class="fas fa-cash-register"></i>
                                        <span>Bayar Sekarang</span>
                                    </div>
                                </button>
                                <div class="grid grid-cols-3 gap-2">
                                    <button class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-3 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-save text-xs mr-1"></i>Hold
                                    </button>
                                    <button class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-3 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-print text-xs mr-1"></i>Print
                                    </button>
                                    <button class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-3 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-history text-xs mr-1"></i>Riwayat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

    <!-- Mobile Cart Toggle (Hidden on desktop) -->
    <div class="lg:hidden fixed bottom-4 right-4">
        <button class="bg-gradient-to-r from-green-400 to-green-700 hover:from-green-500 hover:to-green-800 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center"
                onclick="toggleMobileCart()">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span class="absolute -top-2 -right-2 bg-danger text-white text-xs w-6 h-6 rounded-full flex items-center justify-center" id="mobileCartCount">0</span>
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

    <script>
        let cart = [];
        let sidebarOpen = false;

        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburger = document.getElementById('hamburgerBtn');
            
            sidebarOpen = !sidebarOpen;
            
            if (sidebarOpen) {
                sidebar.classList.add('show');
                overlay.classList.add('show');
                hamburger.classList.add('active');
            } else {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                hamburger.classList.remove('active');
            }
        }

        // Close sidebar
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburger = document.getElementById('hamburgerBtn');
            
            sidebarOpen = false;
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            hamburger.classList.remove('active');
        }

        // Header action functions
        function showTransactionHistory() {
            // Close sidebar first
            closeSidebar();
            // Implement transaction history modal/page
            alert('Fitur riwayat transaksi akan segera hadir');
        }

        function showSettings() {
            closeSidebar();
            // Implement settings modal
            alert('Fitur pengaturan akan segera hadir');
        }

        function showNotifications() {
            closeSidebar();
            // Implement notifications panel
            alert('Tidak ada notifikasi baru');
        }

        // Close sidebar when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebarOpen) {
                closeSidebar();
            }
        });

        // Auto-close sidebar when window is resized to large screens
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024 && sidebarOpen) {
                closeSidebar();
            }
        });
        let isMobileCartOpen = false;

        // Update current date and time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('id-ID', options);
        }

        // Add product to cart
        function addToCart(id, name, price, image) {
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    image: image,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
            
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

        // Remove item from cart
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        // Update item quantity
        function updateQuantity(index, change) {
            cart[index].quantity += change;
            if (cart[index].quantity <= 0) {
                removeFromCart(index);
            } else {
                updateCartDisplay();
            }
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
                        </div>
                        <div class="flex items-center space-x-1">
                            <button onclick="updateQuantity(${index}, -1)" 
                                    class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center hover:bg-gray-200 transition-colors">
                                <i class="fas fa-minus text-xs text-gray-600"></i>
                            </button>
                            <span class="font-medium text-xs w-6 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)" 
                                    class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center hover:bg-gray-200 transition-colors">
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
                        m.querySelector('i').className = m.querySelector('i').className.replace(/text-green-\d+/, 'text-gray-500');
                        m.querySelector('p').className = m.querySelector('p').className.replace(/text-green-\d+/, 'text-gray-600');
                    });
                    
                    // Add active class to selected method
                    this.classList.add('active', 'border-green-500', 'bg-green-100');
                    this.classList.remove('border-gray-200');
                    this.querySelector('i').className = this.querySelector('i').className.replace(/text-gray-\d+/, 'text-green-600');
                    this.querySelector('p').className = this.querySelector('p').className.replace(/text-gray-\d+/, 'text-green-600');
                    
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
                alert('Keranjang masih kosong!');
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
                    alert('Uang yang diterima tidak mencukupi!');
                    return;
                }
                
                bayar = parseInt(cashValue);
                kembalian = bayar - finalTotal;
            }
            
            // Show confirmation
            if (!confirm(`Konfirmasi pembayaran sebesar Rp ${finalTotal.toLocaleString('id-ID')}?`)) {
                return;
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
                const response = await fetch('{{ route("kasir.transaksi.store") }}', {
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
                    // Success
                    alert(`Pembayaran berhasil!\n\nInvoice: ${result.data.invoice}\nTotal: Rp ${result.data.total.toLocaleString('id-ID')}\nKembalian: Rp ${result.data.kembalian.toLocaleString('id-ID')}\n\nTerima kasih!`);
                    
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
                    }, 1500);
                    
                } else {
                    // Error
                    alert('Gagal memproses transaksi: ' + result.message);
                }
                
                // Reset button
                payButton.innerHTML = originalText;
                payButton.disabled = false;
                
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran: ' + error.message);
                
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
            alert('Transaksi disimpan untuk nanti.');
        }

        // Clear cart
        function clearCart() {
            if (cart.length === 0) return;
            
            if (confirm('Hapus semua item dari keranjang?')) {
                cart = [];
                updateCartDisplay();
            }
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
                alert('Browser Anda tidak mendukung Bluetooth API');
                return;
            }

            try {
                // Show loading
                updatePrinterStatus('Mencari printer...', 'connecting');

                // Request Bluetooth device
                bluetoothDevice = await navigator.bluetooth.requestDevice({
                    filters: [
                        { services: ['000018f0-0000-1000-8000-00805f9b34fb'] }, // ESC/POS service
                        { namePrefix: 'POS' },
                        { namePrefix: 'Printer' },
                        { namePrefix: 'EPPOS' },
                        { namePrefix: 'RPP' }
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
                        bluetoothCharacteristic = await service.getCharacteristic('0000ff01-0000-1000-8000-00805f9b34fb');
                    } catch (e2) {
                        // Try another alternative
                        const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                        bluetoothCharacteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');
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
                
                let errorMessage = 'Gagal menghubungkan printer';
                if (error.name === 'NotFoundError') {
                    errorMessage = 'Printer tidak ditemukan. Pastikan printer dalam mode pairing.';
                } else if (error.name === 'SecurityError') {
                    errorMessage = 'Akses Bluetooth ditolak. Periksa pengaturan browser.';
                }
                alert(errorMessage);
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
                        bluetoothCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                    } catch (e) {
                        try {
                            const service = await server.getPrimaryService('0000ff00-0000-1000-8000-00805f9b34fb');
                            bluetoothCharacteristic = await service.getCharacteristic('0000ff01-0000-1000-8000-00805f9b34fb');
                        } catch (e2) {
                            const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                            bluetoothCharacteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');
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
            const INIT = ESC + '@';           // Initialize printer
            const CENTER = ESC + 'a1';        // Center alignment
            const LEFT = ESC + 'a0';          // Left alignment
            const BOLD_ON = ESC + 'E1';       // Bold on
            const BOLD_OFF = ESC + 'E0';      // Bold off
            const CUT = ESC + 'm';            // Cut paper
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
                receipt += `${' '.repeat(Math.max(1, 32 - (`${item.quantity} x Rp${item.price.toLocaleString('id-ID')}` + `Rp${(item.price * item.quantity).toLocaleString('id-ID')}`).length))}`;
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
                    if (confirm('Printer tidak terhubung. Hubungkan sekarang?')) {
                        await connectBluetoothPrinter();
                        if (!printerConnected) return false;
                    } else {
                        return false;
                    }
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
                alert('Gagal mencetak struk: ' + error.message);
                return false;
            }
        }

        // Process payment with print
        async function processPaymentWithPrint() {
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
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
                    alert('Uang yang diterima tidak mencukupi!');
                    return;
                }
                
                bayar = parseInt(cashValue);
                kembalian = bayar - finalTotal;
            }
            
            // Show confirmation
            if (!confirm(`Konfirmasi pembayaran sebesar Rp ${finalTotal.toLocaleString('id-ID')}?`)) {
                return;
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
                const response = await fetch('{{ route("kasir.transaksi.store") }}', {
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
                    if (printSuccess) {
                        alert(`Pembayaran berhasil! Struk telah dicetak.\n\nInvoice: ${result.data.invoice}\nTotal: Rp ${result.data.total.toLocaleString('id-ID')}\nKembalian: Rp ${result.data.kembalian.toLocaleString('id-ID')}\n\nTerima kasih!`);
                    } else {
                        alert(`Pembayaran berhasil! (Struk gagal dicetak)\n\nInvoice: ${result.data.invoice}\nTotal: Rp ${result.data.total.toLocaleString('id-ID')}\nKembalian: Rp ${result.data.kembalian.toLocaleString('id-ID')}\n\nTerima kasih!`);
                    }
                    
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
                    alert('Gagal memproses transaksi: ' + result.message);
                }
                
                // Reset button
                payButton.innerHTML = originalText;
                payButton.disabled = false;
                
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran: ' + error.message);
                
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
            
            // Initialize payment input formatting
            formatCashInput();
            togglePaymentInput();
            
            // Initialize sidebar state (always hidden by default)
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            sidebarOpen = false;
            
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