<div id="sidebar"
        class="sidebar fixed inset-y-0 left-0 z-50 bg-white shadow-lg h-screen
           transform -translate-x-full transition-transform duration-300 ease-in-out
           flex flex-col">

    <!-- Header -->
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
        <a href="{{route('management.dashboard.index')}}"
            class="nav-item group flex items-center px-3 py-3 text-sm font-medium
            {{ request()->routeIs('management.dashboard*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Dashboard Manajemen">
            <i class="sidebar-icon fas fa-home {{ request()->routeIs('management.dashboard*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 transition-all duration-300"></i>
            <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Dashboard Manajemen</span>
        </a>

        <!-- Master Data Section -->
        <div class="pt-4 pb-2">
            <div class="flex items-center space-x-2 px-3 mb-2">
                <span class="sidebar-text text-xs font-bold text-gray-500 uppercase tracking-wider transition-opacity duration-300">Master Data</span>
                <span class="sidebar-text bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full transition-opacity duration-300">Isi Dulu!</span>
            </div>
            <div class="space-y-1 border-l-2 border-red-200 ml-3 pl-2">
                <a href="{{ route('management.konversi.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.konversi.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="1. Konversi Satuan - Atur satuan terlebih dahulu">
                    <span class="sidebar-icon sidebar-number w-6 h-6 flex items-center justify-center {{ request()->routeIs('management.konversi.*') ? 'bg-white text-green-600' : 'bg-red-100 text-red-600' }} rounded-full mr-3 text-xs font-bold flex-shrink-0">1</span>
                    <i class="sidebar-icon fas fa-exchange-alt {{ request()->routeIs('management.konversi.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-2 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Konversi Satuan</span>
                </a>

                <a href="{{ route('management.bahanbaku.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.bahanbaku.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="2. Bahan Baku - Kelola bahan baku">
                    <span class="sidebar-icon sidebar-number w-6 h-6 flex items-center justify-center {{ request()->routeIs('management.bahanbaku.*') ? 'bg-white text-green-600' : 'bg-red-100 text-red-600' }} rounded-full mr-3 text-xs font-bold flex-shrink-0">2</span>
                    <i class="sidebar-icon fas fa-boxes {{ request()->routeIs('management.bahanbaku.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-2 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Bahan Baku</span>
                </a>

                <a href="{{ route('management.produk.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.produk.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="3. Produk - Kelola produk jadi">
                    <span class="sidebar-icon sidebar-number w-6 h-6 flex items-center justify-center {{ request()->routeIs('management.produk.*') ? 'bg-white text-green-600' : 'bg-red-100 text-red-600' }} rounded-full mr-3 text-xs font-bold flex-shrink-0">3</span>
                    <i class="sidebar-icon fas fa-cookie-bite {{ request()->routeIs('management.produk.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-2 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Produk</span>
                </a>

                <a href="{{ route('management.resep.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.resep.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="4. Resep - Buat resep produk">
                    <span class="sidebar-icon sidebar-number w-6 h-6 flex items-center justify-center {{ request()->routeIs('management.resep.*') ? 'bg-white text-green-600' : 'bg-red-100 text-red-600' }} rounded-full mr-3 text-xs font-bold flex-shrink-0">4</span>
                    <i class="sidebar-icon fas fa-utensils {{ request()->routeIs('management.resep.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-2 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Resep & Produksi</span>
                </a>
            </div>
        </div>

        <!-- Operational Section -->
        <div class="pt-4 pb-2">
            <div class="flex items-center space-x-2 px-3 mb-2">
                <span class="sidebar-text text-xs font-bold text-gray-500 uppercase tracking-wider transition-opacity duration-300">Operasional</span>
                <span class="sidebar-text bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full transition-opacity duration-300">Harian</span>
            </div>
            <div class="space-y-1 border-l-2 border-blue-200 ml-3 pl-2">
                <a href="{{ route('management.jurnal.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.jurnal.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Jurnal Harian">
                    <i class="sidebar-icon fas fa-book {{ request()->routeIs('management.jurnal.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Jurnal Harian</span>
                </a>

                <a href="{{ route('management.laporan.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.laporan.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Laporan">
                    <i class="sidebar-icon fas fa-chart-line {{ request()->routeIs('management.laporan.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Laporan</span>
                </a>

                 <a href="{{ route("management.shiftman.index") }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.shiftman.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Shiftman">
                    <i class="sidebar-icon fas fa-clock mr-3 text-lg {{ request()->routeIs('management.laporan.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Shift</span>
                </a>

                {{-- <a href="{{ route("management.produkgagal.index") }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.shiftman.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Shiftman">
                    <i class="sidebar-icon fas fa-clock mr-3 text-lg {{ request()->routeIs('management.laporan.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Produkgagal</span>
                </a> --}}
            </div>
        </div>

        <!-- Setting Section -->
        <div class="pt-4 pb-2">
            <div class="flex items-center space-x-2 px-3 mb-2">
                <span class="sidebar-text text-xs font-bold text-gray-500 uppercase tracking-wider transition-opacity duration-300">Pengaturan</span>
                <span class="sidebar-text bg-purple-100 text-purple-600 text-[10px] font-bold px-2 py-0.5 rounded-full transition-opacity duration-300">Sistem</span>
            </div>
            <div class="space-y-1 border-l-2 border-purple-200 ml-3 pl-2">
                <a href="{{ route('management.setting.index') }}"
                    class="nav-item group flex items-center px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('management.setting.*') ? 'text-white bg-gradient-to-r from-green-400 to-green-700' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-300" title="Pajak dan Promo - Kelola Pajak & Promo">
                    <i class="sidebar-icon fas fa-cog {{ request()->routeIs('management.setting.*') ? 'text-white' : 'text-gray-400 group-hover:text-green-600' }} mr-3 transition-all duration-300"></i>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Pajak dan Promo</span>
                </a>
            </div>
        </div>
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
