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

            <!-- Top Products & Sales Chart -->
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

                <!-- Sales Chart -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Grafik Penjualan
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button onclick="changeChartPeriod('7days')" class="chart-period-btn active px-3 py-1 text-xs bg-green-100 text-green-600 rounded-lg">7 Hari</button>
                                <button onclick="changeChartPeriod('30days')" class="chart-period-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">30 Hari</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="h-64 relative">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <!-- Chart Summary -->
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <div class="flex items-center justify-center space-x-2 mb-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Total</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900" id="totalSalesDisplay">
                                    Rp {{ number_format($chartSummary['7days']['totalSales'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center space-x-2 mb-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Rata-rata</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900" id="avgDailyDisplay">
                                    Rp {{ number_format($chartSummary['7days']['avgDaily'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center space-x-2 mb-2">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Transaksi</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900" id="totalTransactionsDisplay">
                                    {{ number_format($chartSummary['7days']['totalTransactions'], 0, ',', '.') }}
                                </p>
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
    
    .chart-period-btn.active {
        background-color: #d1fae5 !important;
        color: #059669 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Pastikan Chart.js sudah dimuat (sudah ada di layout)
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard Management Loaded');
        
        // Debug data dari PHP
        console.log('PHP Chart Data:', {
            labels7Hari: @json($labels7Hari),
            penjualan7Hari: @json($penjualan7Hari),
            penjualan30Hari: @json($penjualan30Hari)
        });
        
        // Initialize chart setelah DOM siap
        setTimeout(function() {
            initializeDashboardChart();
        }, 100);
    });

    // Sales Chart Data
    const salesChartData = {
        '7days': {
            labels: @json($labels7Hari),
            sales: @json($penjualan7Hari),
            transactions: @json($transaksi7Hari),
        },
        '30days': {
            labels: @json($labels30Hari),
            sales: @json($penjualan30Hari),
            transactions: @json($transaksi30Hari),
        }
    };

    let currentChart = null;
    let currentPeriod = '7days';

    // Initialize Dashboard Chart
    function initializeDashboardChart() {
        console.log('Initializing dashboard chart...');
        
        // Cek apakah Chart.js tersedia
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded!');
            // Coba load Chart.js
            loadChartJS();
            return;
        }
        
        createSalesChart('7days');
    }

    // Load Chart.js jika belum dimuat
    function loadChartJS() {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = function() {
            console.log('Chart.js loaded successfully');
            createSalesChart('7days');
        };
        script.onerror = function() {
            console.error('Failed to load Chart.js');
        };
        document.head.appendChild(script);
    }

    // Create Sales Chart
    function createSalesChart(period = '7days') {
        console.log('Creating chart for period:', period);
        
        const canvas = document.getElementById('salesChart');
        if (!canvas) {
            console.error('Canvas element not found!');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        const data = salesChartData[period];
        
        console.log('Chart data:', data);
        
        // Destroy existing chart if it exists
        if (currentChart) {
            currentChart.destroy();
        }

        // Jika semua data 0, beri placeholder data
        const allZero = data.sales.every(val => val === 0);
        if (allZero) {
            console.log('All sales data is zero, using placeholder');
            // Buat data dummy untuk testing
            const dummyData = data.labels.map(() => Math.floor(Math.random() * 1000000) + 500000);
            data.sales = dummyData;
        }

        try {
            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: data.sales,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(34, 197, 94)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Penjualan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                    } else {
                                        return 'Rp ' + value;
                                    }
                                },
                                color: 'rgb(107, 114, 128)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgb(107, 114, 128)'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
            
            console.log('Chart created successfully');
            
            // Update summary
            updateSalesStats(period);
            
        } catch (error) {
            console.error('Error creating chart:', error);
        }
    }

    // Update Sales Statistics
    function updateSalesStats(period) {
        const data = salesChartData[period];
        const totalSales = data.sales.reduce((sum, value) => sum + value, 0);
        const totalTransactions = data.transactions.reduce((sum, value) => sum + value, 0);
        const days = period === '7days' ? 7 : 30;
        const avgDaily = totalSales / days;
        
        document.getElementById('totalSalesDisplay').textContent = 'Rp ' + Math.round(totalSales).toLocaleString('id-ID');
        document.getElementById('avgDailyDisplay').textContent = 'Rp ' + Math.round(avgDaily).toLocaleString('id-ID');
        document.getElementById('totalTransactionsDisplay').textContent = totalTransactions.toLocaleString('id-ID');
    }

    // Change Chart Period
    function changeChartPeriod(period) {
        console.log('Changing chart period to:', period);
        
        currentPeriod = period;
        
        // Update button states
        document.querySelectorAll('.chart-period-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-green-100', 'text-green-600');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        });
        
        event.target.classList.remove('bg-gray-100', 'text-gray-600');
        event.target.classList.add('active', 'bg-green-100', 'text-green-600');
        
        // Update chart
        createSalesChart(period);
    }

    // Export dropdown handling
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative.group')) {
            document.querySelectorAll('.group-hover\\:block').forEach(el => {
                el.classList.add('hidden');
            });
        }
    });
</script>
@endpush