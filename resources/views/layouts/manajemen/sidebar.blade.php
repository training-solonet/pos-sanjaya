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
    {{-- <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <a href="index.html"
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg">
        <i class="fas fa-home text-white mr-3"></i>
        Dashboard Manajemen
      </a>
      <a href="{{ route("management.jurnal.index") }}"
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
        <i class="fas fa-book text-gray-400 group-hover:text-green-600 mr-3"></i>
        Jurnal Harian
      </a>
      <a href="{{ route("management.bahanbaku.index") }}"
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
        <i class="fas fa-boxes text-gray-400 group-hover:text-green-600 mr-3"></i>
        Stok Bahan Baku
      </a>
      <a href='{{ route("management.produk.index") }}'
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
        <i class="fas fa-cookie-bite text-gray-400 group-hover:text-green-600 mr-3"></i>
        Stok Produk
      </a>
      <a href="{{ route("management.konversi.index") }}"
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
        <i class="fas fa-exchange-alt text-gray-400 group-hover:text-green-600 mr-3"></i>
        Konversi Satuan
      </a>
      <a href="{{ route("management.resep.index") }}"
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
        <i class="fas fa-utensils text-gray-400 group-hover:text-green-600 mr-3"></i>
        Resep & Produksi
      </a>
      <a href="{{ route("management.laporan.index") }}"
        class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
        <i class="fas fa-chart-line text-gray-400 group-hover:text-green-600 mr-3"></i>
        Laporan
      </a>
    </nav> --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <a href="index.html"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.dashboard*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-home {{ request()->routeIs('management.dashboard*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Dashboard Manajemen
        </a>

        <a href="{{ route('management.jurnal.index') }}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.jurnal.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-book {{ request()->routeIs('management.jurnal.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Jurnal Harian
        </a>

        <a href="{{ route('management.bahanbaku.index') }}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.bahanbaku.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-boxes {{ request()->routeIs('management.bahanbaku.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Stok Bahan Baku
        </a>

        <a href="{{ route('management.produk.index') }}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.produk.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-cookie-bite {{ request()->routeIs('management.produk.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Stok Produk
        </a>

        <a href="{{ route('management.konversi.index') }}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.konversi.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-exchange-alt {{ request()->routeIs('management.konversi.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Konversi Satuan
        </a>

        <a href="{{ route('management.resep.index') }}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.resep.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-utensils {{ request()->routeIs('management.resep.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Resep & Produksi
        </a>

        <a href="{{ route('management.laporan.index') }}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium {{ request()->routeIs('management.laporan.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg">
            <i
                class="fas fa-chart-line {{ request()->routeIs('management.laporan.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3"></i>
            Laporan
        </a>
    </nav>


    <!-- User Profile -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-red-100 hover:text-red-600 transition-colors duration-200">
                    <i class="fas fa-sign-out-alt text-gray-600 text-sm hover:text-red-600"></i>
                </button>
            </form>
        </div>
    </div>
</div>
