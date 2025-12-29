<header class="bg-gradient-to-r from-white to-gray-50 shadow-md border-b-2 border-green-200 sticky top-0 z-30 backdrop-blur-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left Section: Menu Button -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Menu Button -->
                <button onclick="toggleSidebar()" title="Menu"
                    class="lg:hidden w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 flex items-center justify-center shadow-md">
                    <i class="fas fa-bars text-white"></i>
                </button>
            </div>

            <!-- Right Section: DateTime & Actions -->
            <div class="flex items-center space-x-3">
                <!-- DateTime Display -->
                <div class="hidden md:block text-right">
                    <p class="text-xs font-semibold text-gray-800 leading-tight" id="currentDate"></p>
                    <p class="text-xs font-medium text-green-600 leading-tight" id="currentTime"></p>
                </div>
                
                <!-- Notification Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="relative w-11 h-11 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-300">
                        <i class="fas fa-bell text-gray-600"></i>
                        @if($notificationCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[22px] h-5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center px-1.5 shadow-lg">
                            {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                        </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
                         style="display: none;">
                    
                        <!-- Header with Gradient -->
                        <div class="px-5 py-4 bg-gradient-to-r from-green-500 to-green-600 flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-bell text-white text-lg"></i>
                                <h3 class="text-sm font-bold text-white">Notifikasi Stok</h3>
                            </div>
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-xs font-semibold text-white rounded-full">
                                {{ $notificationCount }} peringatan
                            </span>
                        </div>

                        <!-- Notification List -->
                        <div class="max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            @forelse($notifications as $notif)
                            <div class="block px-5 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        @if($notif['color'] === 'red')
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center shadow-sm">
                                            <i class="fas fa-cookie-bite text-red-600 text-lg"></i>
                                        </div>
                                        @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center shadow-sm">
                                            <i class="fas fa-cookie-bite text-yellow-600 text-lg"></i>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $notif['nama'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                                            <i class="fas fa-tag mr-1"></i>
                                            Produk
                                        </p>
                                        <div class="flex items-center mt-2 space-x-2">
                                            <span class="text-xs {{ $notif['color'] === 'red' ? 'text-red-600' : 'text-yellow-600' }} font-semibold flex items-center">
                                                <i class="fas fa-cube mr-1"></i>
                                                {{ $notif['stok'] }} pcs
                                            </span>
                                            <span class="text-xs text-gray-300">•</span>
                                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $notif['color'] === 'red' ? 'bg-gradient-to-r from-red-100 to-red-200 text-red-700' : 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-700' }}">
                                                {{ $notif['status'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="px-5 py-10 text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                                    <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Semua Stok Aman! 🎉</p>
                                <p class="text-xs text-gray-500 mt-1">Tidak ada stok yang menipis atau habis</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- Footer Action Buttons -->
                        @if($notificationCount > 0)
                        <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
                            <div class="flex justify-between items-center gap-2">
                                <a href="{{ route('kasir.transaksi.index') }}" 
                                   class="flex-1 text-center px-3 py-2.5 bg-white text-green-600 hover:bg-green-600 hover:text-white text-xs font-semibold rounded-xl shadow-sm border border-green-200 flex items-center justify-center space-x-1">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Transaksi</span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
