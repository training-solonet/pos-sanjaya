@extends('layouts.manajemen.index')

@section('content')
    <!-- Page Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-green-400 to-green-700 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">
                            Selamat Datang di Sanjaya Bakery
                        </h2>
                        <p class="opacity-90">Dashboard Management</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-cash-register text-6xl text-black-200"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Penjualan Hari Ini -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Penjualan Hari Ini
                            </p>
                            <p class="text-2xl font-bold text-gray-900" id="todaySales">
                                Rp {{ number_format($todaySales, 0, ',', '.') }}
                            </p>
                            <p class="text-sm" id="salesGrowthText">
                                @if($salesGrowth >= 0)
                                    <i class="fas fa-arrow-up mr-1 text-green-600"></i>
                                    <span id="salesGrowth" class="text-green-600">{{ $salesGrowth }}</span>% dari kemarin
                                @else
                                    <i class="fas fa-arrow-down mr-1 text-red-600"></i>
                                    <span id="salesGrowth" class="text-red-600">{{ abs($salesGrowth) }}</span>% dari kemarin
                                @endif
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-700 bg-opacity-10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cash-register text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Total Transaksi
                            </p>
                            <p class="text-2xl font-bold text-gray-900" id="todayTransactions">
                                {{ $todayTransactions }}
                            </p>
                            <p class="text-sm" id="transactionsGrowthText">
                                @if($transactionsGrowth >= 0)
                                    <i class="fas fa-arrow-up mr-1 text-green-600"></i>
                                    <span id="transactionsGrowth" class="text-green-600">{{ $transactionsGrowth }}</span>% dari kemarin
                                @else
                                    <i class="fas fa-arrow-down mr-1 text-red-600"></i>
                                    <span id="transactionsGrowth" class="text-red-600">{{ abs($transactionsGrowth) }}</span>% dari kemarin
                                @endif
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-success text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Produk Terjual -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Produk Terjual
                            </p>
                            <p class="text-2xl font-bold text-gray-900" id="todayProductsSold">
                                {{ number_format($todayProductsSold, 0, ',', '.') }}
                            </p>
                            <p class="text-sm" id="productsSoldGrowthText">
                                @if($productsSoldGrowth >= 0)
                                    <i class="fas fa-arrow-up mr-1 text-green-600"></i>
                                    <span id="productsSoldGrowth" class="text-green-600">{{ $productsSoldGrowth }}</span>% dari kemarin
                                @else
                                    <i class="fas fa-arrow-down mr-1 text-red-600"></i>
                                    <span id="productsSoldGrowth" class="text-red-600">{{ abs($productsSoldGrowth) }}</span>% dari kemarin
                                @endif
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-accent text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Stok Rendah -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                            <p class="text-2xl font-bold text-gray-900" id="lowStockCount">
                                {{ $lowStockCount }}
                            </p>
                            <p class="text-sm text-danger">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Perlu restok
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-danger/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-danger text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                    <span class="text-xs text-gray-500">Akses fitur utama dengan cepat</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <a href="{{ route('management.jurnal.index') }}"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-400 to-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            <i class="fas fa-book text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900 text-center">Jurnal Harian</span>
                        <span class="text-xs text-gray-500 mt-1">Kelola keuangan</span>
                    </a>

                    <a href="{{ route('management.laporan.index') }}"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-amber-400 to-amber-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900 text-center">Lihat Laporan</span>
                        <span class="text-xs text-gray-500 mt-1">Analisis bisnis</span>
                    </a>

                    <a href="{{ route('management.bahanbaku.index') }}"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            <i class="fas fa-boxes text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900 text-center">Stok Bahan</span>
                        <span class="text-xs text-gray-500 mt-1">Monitor stok</span>
                    </a>

                    <a href="{{ route('management.opname.index') }}"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-red-400 to-red-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900 text-center">Stok Opname</span>
                        <span class="text-xs text-gray-500 mt-1">Audit stok</span>
                    </a>

                    <a href="{{ route('management.konversi.index') }}"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            <i class="fas fa-balance-scale text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900 text-center">Konversi Satuan</span>
                        <span class="text-xs text-gray-500 mt-1">Kelola satuan</span>
                    </a>

                    <!-- Export Actions -->
                    <div class="relative group">
                        <div class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 cursor-pointer">
                            <div
                                class="w-12 h-12 bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                <i class="fas fa-file-export text-white text-xl"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 text-center">Export Data</span>
                            <span class="text-xs text-gray-500 mt-1">Download laporan</span>
                        </div>
                        
                        <!-- Export Options Dropdown -->
                        <div class="absolute hidden group-hover:block w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10 mt-2">
                            <a href="{{ route('management.dashboard.index', ['export' => 'excel']) }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100">
                                <i class="fas fa-file-excel text-green-600 mr-3"></i>
                                <div>
                                    <div class="font-medium">Export Excel</div>
                                    <div class="text-xs text-gray-500">Format .xls</div>
                                </div>
                            </a>
                            <a href="{{ route('management.dashboard.index', ['export' => 'csv']) }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100">
                                <i class="fas fa-file-csv text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-medium">Export CSV</div>
                                    <div class="text-xs text-gray-500">Format .csv</div>
                                </div>
                            </a>
                            <a href="{{ route('management.dashboard.index', ['export' => 'pdf']) }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-pdf text-red-600 mr-3"></i>
                                <div>
                                    <div class="font-medium">Export PDF</div>
                                    <div class="text-xs text-gray-500">Format .pdf</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis Section - Produk dan Bahan Baku -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Low Stock Products -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Stok Produk Menipis
                            </h3>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Perlu Restok
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4" id="lowStockProductsContainer">
                            @forelse($lowStockProducts as $product)
                                <div class="flex items-center justify-between p-3 bg-{{ $product->status_color }}-50 rounded-lg border border-{{ $product->status_color }}-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-{{ $product->status_color }}-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-bread-slice text-{{ $product->status_color }}-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $product->nama }}
                                            </p>
                                            <p class="text-xs text-{{ $product->status_color }}-600">
                                                Sisa {{ $product->stok }} {{ $product->satuan }} • Min: {{ $product->min_stok }} {{ $product->satuan }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-{{ $product->status_color }}-600">{{ $product->status_text }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->percentage }}%</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-gray-500">Tidak ada stok produk yang rendah</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('management.produk.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">Kelola Stok Produk →</a>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Bahan Baku -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Bahan Baku Stok Menipis
                            </h3>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Perlu Restok
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4" id="lowStockBahanBakuContainer">
                            @forelse($lowStockBahanBaku as $bahan)
                                <div class="flex items-center justify-between p-3 bg-{{ $bahan->status_color }}-50 rounded-lg border border-{{ $bahan->status_color }}-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-{{ $bahan->status_color }}-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-{{ $bahan->status_color }}-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $bahan->nama }}
                                            </p>
                                            <p class="text-xs text-{{ $bahan->status_color }}-600">
                                                Sisa {{ $bahan->stok_display }} • Min: {{ $bahan->min_stok }} {{ $bahan->satuan_kecil }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-{{ $bahan->status_color }}-600">{{ $bahan->status_text }}</p>
                                        <p class="text-xs text-gray-500">{{ $bahan->percentage }}%</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-gray-500">Tidak ada bahan baku stok rendah</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('management.bahanbaku.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">Kelola Stok Bahan Baku →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products & Recent Transactions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Products -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Produk Roti Terlaris (7 Hari)
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4" id="topProductsContainer">
                            @forelse($topProducts as $product)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-bread-slice text-amber-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $product->nama }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ number_format($product->total_qty ?? 0, 0, ',', '.') }} terjual</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($product->harga ?? 0, 0, ',', '.') }}</p>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-gray-500">Belum ada data produk terjual dalam 7 hari terakhir</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('management.laporan.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">Lihat Laporan Lengkap →</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Transaksi Terbaru
                            </h3>
                            <span class="text-xs text-gray-500">Hari ini</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="pb-3 px-2">No. Transaksi</th>
                                        <th class="pb-3 px-2">Waktu</th>
                                        <th class="pb-3 px-2">Total</th>
                                        <th class="pb-3 px-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">#{{ $transaction->kode_transaksi ?? $transaction->id }}</span>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <span class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <span class="text-sm font-medium text-green-600">
                                                Rp {{ number_format($transaction->total ?? $transaction->bayar ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Selesai
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fas fa-receipt text-4xl text-gray-300 mb-2"></i>
                                                <p class="text-gray-500">Belum ada transaksi hari ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Transaction Summary -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center justify-center space-x-2 mb-1">
                                        <i class="fas fa-cash-register text-blue-600"></i>
                                        <span class="text-sm font-medium text-gray-600">Rata-rata/Transaksi</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900" id="avgTransaction">
                                        @if($todayTransactions > 0)
                                            Rp {{ number_format($todaySales / $todayTransactions, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center justify-center space-x-2 mb-1">
                                        <i class="fas fa-clock text-green-600"></i>
                                        <span class="text-sm font-medium text-gray-600">Transaksi/Jam</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900" id="transactionsPerHour">
                                        @php
                                            $currentHour = now()->hour;
                                            $hoursOpen = max(1, $currentHour - 6); // Asumsi buka jam 6 pagi
                                            $perHour = $hoursOpen > 0 ? $todayTransactions / $hoursOpen : 0;
                                        @endphp
                                        {{ number_format($perHour, 1) }}
                                    </p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center justify-center space-x-2 mb-1">
                                        <i class="fas fa-box text-purple-600"></i>
                                        <span class="text-sm font-medium text-gray-600">Items/Transaksi</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900" id="itemsPerTransaction">
                                        @if($todayTransactions > 0)
                                            {{ number_format($todayProductsSold / $todayTransactions, 1) }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route("management.laporan.index") }}" class="text-sm text-green-600 hover:text-green-800 font-medium">Lihat Semua Transaksi →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sales by Day (7 Days) -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Performa Penjualan (7 Hari)
                            </h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $maxSales = max($penjualan7Hari) ?: 1;
                            @endphp
                            @for($i = 0; $i < count($labels7Hari); $i++)
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="font-medium text-gray-700">{{ $labels7Hari[$i] }}</span>
                                        <div class="flex items-center space-x-4">
                                            <span class="text-gray-600">{{ $transaksi7Hari[$i] }} transaksi</span>
                                            <span class="font-bold text-gray-900">Rp {{ number_format($penjualan7Hari[$i], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" 
                                            style="width: {{ ($penjualan7Hari[$i] / $maxSales) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        
                        <!-- Week Summary -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 mb-1">Total 7 Hari</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        Rp {{ number_format(array_sum($penjualan7Hari), 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 mb-1">Rata-rata/Hari</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        Rp {{ number_format(array_sum($penjualan7Hari) / 7, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best Selling Hours -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Jam Terbaik Penjualan
                            </h3>
                            <span class="text-xs text-gray-500">Berdasarkan rata-rata</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-6">
                            <div class="grid grid-cols-4 gap-2 mb-2">
                                @php
                                    $peakHours = [
                                        'Pagi' => ['06:00-11:00', 'bg-blue-100 text-blue-800'],
                                        'Siang' => ['11:00-14:00', 'bg-green-100 text-green-800'],
                                        'Sore' => ['14:00-18:00', 'bg-amber-100 text-amber-800'],
                                        'Malam' => ['18:00-21:00', 'bg-purple-100 text-purple-800']
                                    ];
                                @endphp
                                @foreach($peakHours as $period => $data)
                                <div class="text-center p-2 rounded-lg {{ $data[1] }}">
                                    <p class="text-xs font-medium">{{ $period }}</p>
                                    <p class="text-xs">{{ $data[0] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Hourly Performance Tips -->
                        <div class="space-y-4">
                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-start">
                                    <i class="fas fa-lightbulb text-blue-600 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">Tips Optimasi:</p>
                                        <p class="text-xs text-gray-600">
                                            • Pastikan stok cukup selama jam sibuk ({{ $busiestHour ?? '11:00-14:00' }})<br>
                                            • Siapkan promosi untuk jam sepi ({{ $quietHour ?? '14:00-16:00' }})<br>
                                            • Optimalkan penjualan online di malam hari
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-start">
                                    <i class="fas fa-chart-line text-green-600 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 mb-1">Target Hari Ini:</p>
                                        <p class="text-xs text-gray-600">
                                            @php
                                                $target = $yesterdaySales * 1.1; // 10% lebih tinggi dari kemarin
                                                $progress = ($target > 0 && $todaySales > 0) ? min(100, ($todaySales / $target) * 100) : 0;
                                            @endphp
                                            <span class="font-medium">Rp {{ number_format($target, 0, ',', '.') }}</span>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <div 
                                                    class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" 
                                                    style="width: {{ $progress }}%">
                                                </div>
                                            </div>
                                            <span class="text-xs mt-1 block">{{ number_format($progress, 1) }}% tercapai</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('styles')
<style>
    .relative.group:hover .group-hover\:block {
        display: block !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard Management Loaded');
        
        // Export dropdown handling
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative.group')) {
                document.querySelectorAll('.group-hover\\:block').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });
        
        // Auto-refresh dashboard every 60 seconds
        setInterval(function() {
            refreshDashboardStats();
        }, 60000);
    });
    
    // Function to refresh dashboard stats via AJAX
    function refreshDashboardStats() {
        fetch('{{ route("management.dashboard.index") }}?json=true')
            .then(response => response.json())
            .then(data => {
                // Update stats cards
                document.getElementById('todaySales').textContent = formatCurrency(data.todaySales);
                document.getElementById('todayTransactions').textContent = data.todayTransactions;
                document.getElementById('todayProductsSold').textContent = formatNumber(data.todayProductsSold);
                document.getElementById('lowStockCount').textContent = data.lowStockCount;
                
                // Update growth indicators
                updateGrowthIndicator('salesGrowth', data.salesGrowth);
                updateGrowthIndicator('transactionsGrowth', data.transactionsGrowth);
                updateGrowthIndicator('productsSoldGrowth', data.productsSoldGrowth);
                
                // Update average transaction
                if (data.todayTransactions > 0) {
                    const avg = data.todaySales / data.todayTransactions;
                    document.getElementById('avgTransaction').textContent = formatCurrency(avg);
                }
                
                // Update items per transaction
                if (data.todayTransactions > 0) {
                    const itemsPer = data.todayProductsSold / data.todayTransactions;
                    document.getElementById('itemsPerTransaction').textContent = itemsPer.toFixed(1);
                }
                
                console.log('Dashboard stats refreshed');
            })
            .catch(error => {
                console.error('Error refreshing dashboard:', error);
            });
    }
    
    // Helper function to format currency
    function formatCurrency(amount) {
        return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
    }
    
    // Helper function to format number
    function formatNumber(num) {
        return Math.round(num).toLocaleString('id-ID');
    }
    
    // Helper function to update growth indicators
    function updateGrowthIndicator(elementId, growthValue) {
        const element = document.getElementById(elementId);
        const container = document.getElementById(elementId + 'Text');
        
        if (element && container) {
            element.textContent = Math.abs(growthValue);
            
            if (growthValue >= 0) {
                container.innerHTML = '<i class="fas fa-arrow-up mr-1 text-green-600"></i>' +
                    '<span id="' + elementId + '" class="text-green-600">' + growthValue + '</span>% dari kemarin';
            } else {
                container.innerHTML = '<i class="fas fa-arrow-down mr-1 text-red-600"></i>' +
                    '<span id="' + elementId + '" class="text-red-600">' + Math.abs(growthValue) + '</span>% dari kemarin';
            }
        }
    }
</script>
@endpush