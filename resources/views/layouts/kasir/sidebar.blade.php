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
    <nav class="mt-6 px-3">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route("kasir.dashboard.index") }}"
                class="nav-item group flex items-center px-3 py-3 text-sm font-medium rounded-lg bg-gradient-to-r from-green-400 to-green-700 text-white">
                <i class="fas fa-tachometer-alt mr-3 text-lg"></i>
                Dashboard Kasir
            </a>

            <!-- Transaksi -->
            <a href="{{ route("kasir.transaksi.index") }}"
                class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-cash-register mr-3 text-lg"></i>
                Transaksi Penjualan
            </a>

            <!-- Laporan -->
            <a href="{{ route("kasir.laporan.index") }}"
                class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-file-alt mr-3 text-lg"></i>
                Laporan Penjualan
            </a>

            <!-- Jurnal Harian -->
            <a href="{{ route("kasir.jurnal.index") }}"
                class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-book mr-3 text-lg"></i>
                Jurnal Harian
            </a>
        </div>
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
