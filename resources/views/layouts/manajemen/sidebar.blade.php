<div id="sidebar"
    class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:relative lg:flex-shrink-0">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-cash-register text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">Sanjaya Bakery</h1>
            </div>
        </div>
        <button onclick="toggleSidebar()"
            class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
            <i class="fas fa-times text-gray-600"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @php
            $isDashboard = request()->routeIs('manajemen.dashboard');
        @endphp

        <!-- Dashboard -->
        <a href="{{ route('manajemen.dashboard') }}"
            class="{{ $isDashboard
                ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg'
                : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
            <i
                class="{{ $isDashboard ? 'fas fa-home text-white mr-3' : 'fas fa-home text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
            Dashboard Manajemen
        </a>

        <!-- Jurnal Harian -->
        <a href="{{ route('manajemen.jurnal.index') }}"
            class="{{ request()->routeIs('manajemen.jurnal.*')
                ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg'
                : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
            <i
                class="{{ request()->routeIs('manajemen.jurnal.*') ? 'fas fa-book text-white mr-3' : 'fas fa-book text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
            Jurnal Harian
        </a>

        <!-- Stok Bahan Baku -->
        <a href="{{ route('manajemen.bahanbaku.index') }}"
            class="{{ request()->routeIs('manajemen.bahanbaku.*')
                ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg'
                : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
            <i
                class="{{ request()->routeIs('manajemen.bahanbaku.*') ? 'fas fa-boxes text-white mr-3' : 'fas fa-boxes text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
            Stok Bahan Baku
        </a>

        <!-- Stok Produk -->
        <a href="{{ route('manajemen.produk.index') }}"
            class="{{ request()->routeIs('manajemen.produk.*')
                ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg'
                : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
            <i
                class="{{ request()->routeIs('manajemen.produk.*') ? 'fas fa-cookie-bite text-white mr-3' : 'fas fa-cookie-bite text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
            Stok Produk
        </a>

        <!-- Konversi Satuan -->
        <a href="{{ route('manajemen.konversi.index') }}"
            class="{{ request()->routeIs('manajemen.konversi.*')
                ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg'
                : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
            <i
                class="{{ request()->routeIs('manajemen.konversi.*') ? 'fas fa-exchange-alt text-white mr-3' : 'fas fa-exchange-alt text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
            Konversi Satuan
        </a>

        <!-- Resep & Produksi -->
        <a href="{{ route('manajemen.resep.index') }}"
            class="{{ request()->routeIs('manajemen.resep.*')
                ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg'
                : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
            <i
                class="{{ request()->routeIs('manajemen.resep.*') ? 'fas fa-utensils text-white mr-3' : 'fas fa-utensils text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
            Resep & Produksi
        </a>
    </nav>


    {{-- <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      @php
        $isDashboard = request()->routeIs('manajemen_dashboard') || request()->routeIs('manajemen.index') || request()->routeIs('manajemen.dashboard') || request()->is('manajemen');
      @endphp
      <a href="index.html"
        class="{{ $isDashboard ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ $isDashboard ? 'fas fa-home text-white mr-3' : 'fas fa-home text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Dashboard Manajemen
      </a>
      <a href="{{ route('manajemen_jurnal') }}"
        class="{{ request()->routeIs('manajemen_jurnal') ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ request()->routeIs('manajemen_jurnal') ? 'fas fa-book text-white mr-3' : 'fas fa-book text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Jurnal Harian
      </a>
      <a href="{{ route('manajemen_bahanbaku') }}"
        class="{{ request()->routeIs('manajemen_bahanbaku') ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ request()->routeIs('manajemen_bahanbaku') ? 'fas fa-boxes text-white mr-3' : 'fas fa-boxes text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Stok Bahan Baku
      </a>
      <a href='{{ route("manajemen_produk") }}'
        class="{{ request()->routeIs('manajemen_produk') ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ request()->routeIs('manajemen_produk') ? 'fas fa-cookie-bite text-white mr-3' : 'fas fa-cookie-bite text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Stok Produk
      </a>
      <a href="{{ route('manajemen_konversi') }}"
        class="{{ request()->routeIs('manajemen_konversi') ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ request()->routeIs('manajemen_konversi') ? 'fas fa-exchange-alt text-white mr-3' : 'fas fa-exchange-alt text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Konversi Satuan
      </a>
      <a href="{{ route('manajemen_resep') }}"
        class="{{ request()->routeIs('manajemen_resep') ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ request()->routeIs('manajemen_resep') ? 'fas fa-utensils text-white mr-3' : 'fas fa-utensils text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Resep & Produksi
      </a>
      <a href="{{ route('manajemen_laporan') }}"
        class="{{ request()->routeIs('manajemen_laporan') ? 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg' : 'nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100' }}">
        <i class="{{ request()->routeIs('manajemen_laporan') ? 'fas fa-chart-line text-white mr-3' : 'fas fa-chart-line text-gray-400 group-hover:text-green-600 mr-3' }}"></i>
        Laporan
      </a>
    </nav> --}}

    <!-- User Profile -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">Admin</p>
                <p class="text-xs text-gray-500">Manager</p>
            </div>
            <a href="../index.html"
                class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-red-100 hover:text-red-600">
                <i class="fas fa-sign-out-alt text-gray-600 text-sm"></i>
            </a>
        </div>
    </div>
</div>
