<!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Mobile Menu Button & Page Title -->
          <div class="flex items-center space-x-4">
            <button onclick="toggleSidebar()" title="Menu"
              class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
              <i class="fas fa-bars text-gray-600"></i>
            </button>
          </div>

          <!-- Header Actions -->
          <div class="flex items-center space-x-4">
            <div class="hidden md:block text-right">
              <p class="text-sm font-medium text-gray-900">{{ ucfirst(Auth::user()->role) }}: {{ Auth::user()->name }}</p>
              <p class="text-xs text-gray-500" id="currentDateTime"></p>
            </div>
            
            <!-- Notification Dropdown -->
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" @click.away="open = false"
                class="relative w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-bell text-gray-600"></i>
                @if($notificationCount > 0)
                  <span class="absolute -top-1 -right-1 min-w-[20px] h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1">
                    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                  </span>
                @endif
              </button>

              <!-- Dropdown Panel -->
              <div x-show="open" 
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 scale-95"
                   x-transition:enter-end="opacity-100 scale-100"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 scale-100"
                   x-transition:leave-end="opacity-0 scale-95"
                   class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                   style="display: none;">
                
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                  <h3 class="text-sm font-semibold text-gray-900">Notifikasi Stok</h3>
                  <span class="text-xs text-gray-500">{{ $notificationCount }} peringatan</span>
                </div>

                <!-- Notification List -->
                <div class="max-h-96 overflow-y-auto">
                  @forelse($notifications as $notif)
                    <div class="block px-4 py-3 border-b border-gray-100">
                      <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                          @if($notif['type'] === 'bahan_baku')
                            @if($notif['color'] === 'red')
                              <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-red-600"></i>
                              </div>
                            @else
                              <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-yellow-600"></i>
                              </div>
                            @endif
                          @else
                            @if($notif['color'] === 'red')
                              <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cookie-bite text-red-600"></i>
                              </div>
                            @else
                              <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cookie-bite text-yellow-600"></i>
                              </div>
                            @endif
                          @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-medium text-gray-900 truncate">{{ $notif['nama'] }}</p>
                          <p class="text-xs text-gray-500 mt-0.5">{{ $notif['type'] === 'bahan_baku' ? 'Bahan Baku' : 'Produk' }}</p>
                          <div class="flex items-center mt-1 space-x-2">
                            <span class="text-xs {{ $notif['color'] === 'red' ? 'text-red-600' : 'text-yellow-600' }} font-medium">
                              Stok: {{ $notif['stok'] }} {{ $notif['satuan'] }}
                            </span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $notif['color'] === 'red' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                              {{ $notif['status'] }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="px-4 py-8 text-center">
                      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                      </div>
                      <p class="text-sm font-medium text-gray-900">Semua Stok Aman</p>
                      <p class="text-xs text-gray-500 mt-1">Tidak ada stok yang menipis atau habis</p>
                    </div>
                  @endforelse
                </div>

                <!-- Footer -->
                @if($notificationCount > 0)
                  <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <div class="flex justify-between items-center text-xs">
                      <a href="{{ route('management.bahanbaku.index') }}" class="text-green-600 hover:text-green-700 font-medium">
                        Lihat Bahan Baku →
                      </a>
                      <a href="{{ route('management.produk.index') }}" class="text-green-600 hover:text-green-700 font-medium">
                        Lihat Produk →
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
