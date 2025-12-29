<div id="sidebar"
    class="sidebar fixed inset-y-0 left-0 z-50 bg-white shadow-lg h-screen
           transform -translate-x-full transition-transform duration-300 ease-in-out
           flex flex-col">

    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 gap-2">
        <div class="flex items-center space-x-3 flex-1 overflow-hidden">
            <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-700 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-cash-register text-white text-lg"></i>
            </div>
            <h1 class="sidebar-title text-lg font-bold text-gray-900 whitespace-nowrap transition-opacity duration-300">Sanjaya Bakery</h1>
        </div>
        <div class="flex items-center flex-shrink-0 ml-2">
            <!-- Desktop Toggle Button -->
            <button onclick="toggleSidebar()" title="Toggle Sidebar"
                class="hidden lg:flex w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 items-center justify-center transition-colors">
                <i id="desktopToggleIcon" class="fas fa-chevron-left text-gray-600 rotate-icon"></i>
            </button>
            <!-- Mobile Close Button -->
            <button onclick="toggleSidebar()"
                class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>
    </div>

    <!-- NAV MENU -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-hide">
        <a href="{{ route('kasir.dashboard.index') }}"
            class="nav-item group flex items-center px-4 py-3.5 text-sm font-medium {{ request()->routeIs('kasir.dashboard*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Dashboard Kasir">
            <i class="sidebar-icon fas fa-tachometer-alt {{ request()->routeIs('kasir.dashboard*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 text-lg transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Dashboard Kasir</span>
        </a>

        <a href="{{ route('kasir.transaksi.index') }}"
            class="nav-item group flex items-center px-4 py-3.5 text-sm font-medium {{ request()->routeIs('kasir.transaksi*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Transaksi Penjualan">
            <i class="sidebar-icon fas fa-cash-register {{ request()->routeIs('kasir.transaksi*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 text-lg transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Transaksi Penjualan</span>
        </a>

        <a href="{{ route('kasir.laporan.index') }}"
            class="nav-item group flex items-center px-4 py-3.5 text-sm font-medium {{ request()->routeIs('kasir.laporan*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Laporan Penjualan">
            <i class="sidebar-icon fas fa-file-alt {{ request()->routeIs('kasir.laporan*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 text-lg transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Laporan Penjualan</span>
        </a>

        <a href="{{ route('kasir.jurnal.index') }}"
            class="nav-item group flex items-center px-4 py-3.5 text-sm font-medium {{ request()->routeIs('kasir.jurnal*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Jurnal Harian">
            <i class="sidebar-icon fas fa-book {{ request()->routeIs('kasir.jurnal*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 text-lg transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Jurnal Harian</span>
        </a>

        <a href="{{ route('kasir.custommer.index') }}"
            class="nav-item group flex items-center px-4 py-3.5 text-sm font-medium {{ request()->routeIs('kasir.custommer*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Jurnal Harian">
            <i class="fas fa-users mr-3 text-lg {{ request()->routeIs('kasir.custommer*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 text-lg transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Data Custommer</span>
        </a>

        <a href="{{ route('kasir.shift.index') }}"
            class="nav-item group flex items-center px-4 py-3.5 text-sm font-medium {{ request()->routeIs('kasir.shift*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Jurnal Harian">
            <i class="fas fa-clock mr-3 text-lg {{ request()->routeIs('kasir.shift*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 text-lg transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Shift Kasir</span>
        </a>
    </nav>

    <!-- USER PROFILE -->
    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-gray-600"></i>
            </div>
            <div class="sidebar-user-info flex-1 overflow-hidden transition-opacity duration-300">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-btn flex-shrink-0">
                @csrf
                <button type="submit" title="Logout"
                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-red-100 hover:text-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt text-gray-600"></i>
                </button>
            </form>
        </div>
    </div>

</div>
