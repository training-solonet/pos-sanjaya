@extends('layouts.manajemen.index')

@section('content')
        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="exportToExcel()" class="px-4 py-2 bg-success text-white rounded-lg hover:bg-success/90 transition-colors">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </button>
                        <button onclick="window.print()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <form method="GET" action="{{ route('management.laporan.index') }}" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="startDate" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="endDate" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <a href="{{ route('management.laporan.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-redo mr-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>&nbsp;</p>
                            </div>
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-primary text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalTransactions }}</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>&nbsp;</p>
                            </div>
                            <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-receipt text-success text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Rata-rata per Transaksi</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($avgPerTransaction ?? 0, 0, ',', '.') }}</p>
                                <p class="text-sm text-danger"><i class="fas fa-arrow-down mr-1"></i>&nbsp;</p>
                            </div>
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calculator text-accent text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Produk Terjual</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProductsSold ?? 0, 0, ',', '.') }}</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>&nbsp;</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Sales Chart -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Grafik Penjualan Harian</h3>
                                <p class="text-sm text-gray-500" id="salesSubtitle">Penjualan 30 hari terakhir</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="changeSalesView('daily')"
                                    class="sales-view-btn active px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg">Harian</button>
                                <button onclick="changeSalesView('weekly')"
                                    class="sales-view-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Mingguan</button>
                            </div>
                        </div>
                        <div class="h-80 relative">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Tertinggi</p>
                                <p class="text-lg font-bold text-green-600" id="maxSales">Rp 2.950.000</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Terendah</p>
                                <p class="text-lg font-bold text-red-600" id="minSales">Rp 1.450.000</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Rata-rata</p>
                                <p class="text-lg font-bold text-blue-600" id="avgSales">Rp 2.185.000</p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Chart -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                                <p class="text-sm text-gray-500">Top 6 produk bulan ini</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="changeProductView('quantity')"
                                    class="product-view-btn active px-3 py-1 text-xs bg-green-100 text-green-600 rounded-lg">Qty</button>
                                <button onclick="changeProductView('revenue')"
                                    class="product-view-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Revenue</button>
                            </div>
                        </div>
                        <div class="h-80 relative flex items-center justify-center">
                            <canvas id="productsChart" style="max-height: 300px;"></canvas>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-2">Top Seller</p>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                        <span class="text-sm font-medium">{{ $topSellerName ?? '-' }}</span>
                                        <span class="ml-auto text-sm text-gray-600">{{ number_format($topSellerQty ?? 0,0,',','.') }} pcs</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-2">Highest Revenue</p>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                        <span class="text-sm font-medium">{{ $highestRevenueName ?? '-' }}</span>
                                        <span class="ml-auto text-sm text-gray-600">Rp {{ number_format($highestRevenueValue ?? 0,0,',','.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products Table -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                        <p class="text-sm text-gray-500 mt-1">Menampilkan produk dengan penjualan tertinggi</p>
                    </div>
                    <div class="overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terjual
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($topProducts as $prod)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-blue-600"></i>
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $prod->nama }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ number_format($prod->total_qty,0,',','.') }} pcs</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">Rp {{ number_format($prod->revenue,0,',','.') }}</td>
                                        <td class="px-6 py-4 text-sm text-green-600 font-semibold">Rp {{ number_format(($prod->revenue * 0.5) ?? 0,0,',','.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fas fa-box-open text-gray-300 text-4xl mb-2"></i>
                                                <p class="text-gray-500">Tidak ada data produk terlaris</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Monthly Report -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Laporan Bulanan</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar text-primary text-2xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">{{ $monthlyReport['monthLabel'] ?? now()->format('F Y') }}</h4>
                                <p class="text-sm text-gray-500 mb-2">Total Penjualan</p>
                                <p class="text-2xl font-bold text-primary">Rp {{ number_format($monthlyReport['total'] ?? 0,0,',','.') }}</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-success/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-trending-up text-success text-2xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">Growth</h4>
                                <p class="text-sm text-gray-500 mb-2">Vs Bulan Lalu</p>
                                <p class="text-2xl font-bold text-success">{{ is_null($monthlyReport['growthPercent']) ? '-' : ($monthlyReport['growthPercent'] > 0 ? '+' : '') . $monthlyReport['growthPercent'] . '%' }}</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-accent/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-star text-accent text-2xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">Profit Margin</h4>
                                <p class="text-sm text-gray-500 mb-2">Rata-rata</p>
                                <p class="text-2xl font-bold text-accent">{{ is_null($monthlyReport['profitMargin']) ? '-' : $monthlyReport['profitMargin'] . '%' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

<script>
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
        const dateTimeElement = document.getElementById('currentDateTime');
        if (dateTimeElement) {
            dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }
    }

    // Chart variables
    let salesChart, productsChart;
    // Server-provided datasets (if available)
    let serverSales = @json($salesChart ?? null);
    let serverProducts = @json($productsChart ?? null);
    let serverMonthly = @json($monthlyReport ?? null);

    // Get current month and year label in Indonesian
    function getMonthYearLabel() {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const currentDate = new Date();
        return months[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
    }

    // Generate realistic sales data based on current month
    function generateSalesData(type = 'daily') {
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const today = currentDate.getDate(); // Tanggal hari ini
        const labels = [];
        const data = [];

        if (type === 'daily') {
            // Generate data only up to today
            for (let i = 1; i <= today; i++) {
                labels.push(i);

                // Generate realistic sales with patterns
                let baseAmount = 1800000; // Base 1.8M
                
                const date = new Date(year, month, i);
                const dayOfWeek = date.getDay();

                // Weekend boost (Sabtu-Minggu)
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    baseAmount *= 1.4; // 40% increase on weekends
                }

                // Peak days (mid-month bonus)
                if (i >= 15 && i <= 20) {
                    baseAmount *= 1.2; // 20% increase mid-month
                }

                // Add random variation ±300k
                const variation = (Math.random() - 0.5) * 600000;
                data.push(Math.round(baseAmount + variation));
            }
        } else if (type === 'weekly') {
            // Weekly data - aggregate daily data by week (up to today)
            const dailySales = [];
            
            // Generate daily sales first (only up to today)
            for (let i = 1; i <= today; i++) {
                let baseAmount = 1800000;
                const date = new Date(year, month, i);
                const dayOfWeek = date.getDay();
                
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    baseAmount *= 1.4;
                }
                if (i >= 15 && i <= 20) {
                    baseAmount *= 1.2;
                }
                
                const variation = (Math.random() - 0.5) * 600000;
                dailySales.push(Math.round(baseAmount + variation));
            }
            
            // Group by weeks
            let weekNumber = 1;
            let weekTotal = 0;
            let daysInWeek = 0;
            
            for (let i = 0; i < dailySales.length; i++) {
                weekTotal += dailySales[i];
                daysInWeek++;
                
                // Every 7 days or last day of month
                if (daysInWeek === 7 || i === dailySales.length - 1) {
                    labels.push('Minggu ' + weekNumber);
                    data.push(weekTotal);
                    weekTotal = 0;
                    daysInWeek = 0;
                    weekNumber++;
                }
            }
        }

        return {
            labels,
            data
        };
    }

    // Generate product sales data (fallback) - kept for demo if server data not present
    function generateProductData(type = 'quantity') {
        const productColors = [
            '#EF4444', // Red
            '#F59E0B', // Amber
            '#10B981', // Green
            '#3B82F6', // Blue
            '#8B5CF6', // Purple
            '#EC4899'  // Pink
        ];
        
        return {
            labels: ['Kue Sus', 'Croissant', 'Roti Tawar', 'Donat', 'Brownies', 'Cookies'],
            data: type === 'quantity' ? [145, 120, 98, 87, 76, 54] : [2175000, 1800000, 1470000, 1305000, 1140000, 810000],
            colors: productColors
        };
    }

    // Create sales chart (uses server data when available)
    function createSalesChart(type = 'daily') {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        let labels = [];
        let data = [];

        if (type === 'daily') {
            // Use server data (from database) for daily view
            if (serverSales && serverSales.labels && serverSales.data) {
                labels = serverSales.labels;
                data = serverSales.data;
            } else {
                // Fallback to generated data if server data not available
                const generated = generateSalesData(type);
                labels = generated.labels;
                data = generated.data;
            }
        } else {
            // For weekly view, aggregate server data into weeks
            if (serverSales && serverSales.labels && serverSales.data) {
                labels = [];
                data = [];
                let weekNumber = 1;
                let weekTotal = 0;
                let daysInWeek = 0;
                
                for (let i = 0; i < serverSales.data.length; i++) {
                    weekTotal += serverSales.data[i];
                    daysInWeek++;
                    
                    // Every 7 days or last day
                    if (daysInWeek === 7 || i === serverSales.data.length - 1) {
                        labels.push('Minggu ' + weekNumber);
                        data.push(weekTotal);
                        weekTotal = 0;
                        daysInWeek = 0;
                        weekNumber++;
                    }
                }
            } else {
                // Fallback to generated data
                const generated = generateSalesData(type);
                labels = generated.labels;
                data = generated.data;
            }
        }

        if (salesChart) {
            salesChart.destroy();
        }

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: type === 'daily' ? 'Penjualan Harian' : 'Penjualan Mingguan',
                    data: data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        borderColor: '#3B82F6',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Penjualan: ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        title: { 
                            display: true, 
                            text: type === 'daily' ? getMonthYearLabel() : 'Periode', 
                            font: { size: 12, weight: 'bold' } 
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        title: { 
                            display: true, 
                            text: 'Penjualan (Rp)', 
                            font: { size: 12, weight: 'bold' } 
                        },
                        ticks: { 
                            callback: function(value) { 
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                },
                animation: { duration: 800, easing: 'easeInOutQuart' }
            }
        });

        // Update statistics
        // Filter out zero or invalid values for more accurate min calculation
        const validData = data.filter(val => val > 0);
        const max = validData.length ? Math.max(...validData) : 0;
        const min = validData.length ? Math.min(...validData) : 0;
        const total = validData.length ? validData.reduce((a, b) => a + b, 0) : 0;
        const avg = validData.length ? Math.round(total / validData.length) : 0;

        const maxEl = document.getElementById('maxSales');
        const minEl = document.getElementById('minSales');
        const avgEl = document.getElementById('avgSales');
        
        if (maxEl) maxEl.textContent = formatCurrency(max);
        if (minEl) minEl.textContent = formatCurrency(min);
        if (avgEl) avgEl.textContent = formatCurrency(avg);
    }

    // Create products chart (uses server data when available)
    function createProductsChart(type = 'quantity') {
        const ctx = document.getElementById('productsChart');
        if (!ctx) return;

        let labels = [];
        let data = [];
        let colors = [];

        if (serverProducts && serverProducts.labels && serverProducts.labels.length > 0) {
            labels = serverProducts.labels;
            data = (type === 'quantity') ? (serverProducts.dataQty || []) : (serverProducts.dataRevenue || []);
            colors = serverProducts.colors || [];
            
            // Generate colors if not provided
            if (colors.length === 0) {
                const defaultColors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899'];
                colors = labels.map((_, i) => defaultColors[i % defaultColors.length]);
            }
        } else {
            const generated = generateProductData(type);
            labels = generated.labels;
            data = generated.data;
            colors = generated.colors;
        }
        
        // Pastikan data tidak kosong
        if (labels.length === 0 || data.length === 0) {
            console.warn('No data available for products chart');
            return;
        }

        if (productsChart) {
            productsChart.destroy();
        }

        productsChart = new Chart(ctx, {
            type: 'doughnut',
            data: { 
                labels: labels, 
                datasets: [{ 
                    data: data, 
                    backgroundColor: colors, 
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverBorderWidth: 3, 
                    hoverBorderColor: '#FFFFFF', 
                    hoverOffset: 10 
                }] 
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'right', 
                        labels: { 
                            boxWidth: 15, 
                            font: { size: 12 }, 
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            generateLabels: function(chart) { 
                                const data = chart.data; 
                                return data.labels.map((label, i) => ({ 
                                    text: label, 
                                    fillStyle: data.datasets[0].backgroundColor[i], 
                                    hidden: false, 
                                    index: i 
                                })); 
                            } 
                        } 
                    },
                    tooltip: { 
                        backgroundColor: 'rgba(0,0,0,0.8)', 
                        titleColor: '#FFF', 
                        bodyColor: '#FFF',
                        padding: 12,
                        displayColors: true,
                        callbacks: { 
                            label: function(context) { 
                                const value = context.parsed; 
                                const total = context.dataset.data.reduce((a,b)=>a+b,0); 
                                const percentage = ((value/total)*100).toFixed(1); 
                                if (type === 'quantity') {
                                    return context.label + ': ' + value + ' pcs (' + percentage + '%)';
                                }
                                return context.label + ': ' + formatCurrency(value) + ' (' + percentage + '%)'; 
                            } 
                        } 
                    }
                },
                cutout: '60%',
                animation: { 
                    animateRotate: true, 
                    duration: 800 
                }
            }
        });
    }

    // Change sales view
    function changeSalesView(type) {
        document.querySelectorAll('.sales-view-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-100', 'text-blue-600');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        });

        event.target.classList.add('active', 'bg-blue-100', 'text-blue-600');
        event.target.classList.remove('bg-gray-100', 'text-gray-600');

        // Update subtitle
        const subtitle = document.getElementById('salesSubtitle');
        if (subtitle) {
            if (type === 'daily') {
                subtitle.textContent = 'Penjualan 30 hari terakhir';
            } else {
                subtitle.textContent = 'Penjualan mingguan (30 hari terakhir)';
            }
        }

        createSalesChart(type);
    }

    // Change product view
    function changeProductView(type) {
        document.querySelectorAll('.product-view-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-green-100', 'text-green-600');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        });

        event.target.classList.add('active', 'bg-green-100', 'text-green-600');
        event.target.classList.remove('bg-gray-100', 'text-gray-600');

        createProductsChart(type);
    }

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Export to Excel (CSV format)
    function exportToExcel() {
        const startDate = document.getElementById('startDate')?.value || '';
        const endDate = document.getElementById('endDate')?.value || '';
        
        // Prepare data
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "LAPORAN PENJUALAN\n";
        csvContent += "Periode: " + (startDate || 'Awal') + " - " + (endDate || 'Sekarang') + "\n\n";
        
        // Summary
        csvContent += "RINGKASAN\n";
        csvContent += "Total Penjualan,{{ $totalSales }}\n";
        csvContent += "Total Transaksi,{{ $totalTransactions }}\n";
        csvContent += "Rata-rata per Transaksi,{{ $avgPerTransaction }}\n";
        csvContent += "Total Produk Terjual,{{ $totalProductsSold }}\n\n";
        
        // Monthly Report
        csvContent += "LAPORAN BULANAN\n";
        csvContent += "Bulan,{{ $monthlyReport['monthLabel'] ?? '' }}\n";
        csvContent += "Total Penjualan,{{ $monthlyReport['total'] ?? 0 }}\n";
        csvContent += "Growth,{{ $monthlyReport['growthPercent'] ?? '-' }}%\n";
        csvContent += "Profit Margin,{{ $monthlyReport['profitMargin'] ?? '-' }}%\n\n";
        
        // Top Products
        csvContent += "PRODUK TERLARIS\n";
        csvContent += "No,Nama Produk,Jumlah Terjual,Total Penjualan,Estimasi Profit\n";
        @foreach($topProducts as $index => $prod)
        csvContent += "{{ $loop->iteration }},{{ $prod->nama }},{{ $prod->total_qty }},{{ $prod->revenue }},{{ $prod->revenue * 0.5 }}\n";
        @endforeach
        
        // Download
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "laporan_penjualan_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js tidak ter-load. Pastikan CDN Chart.js berfungsi.');
            return;
        }

        // Create charts with delay to ensure DOM is ready
        setTimeout(() => {
            try {
                // Update initial subtitle
                const subtitle = document.getElementById('salesSubtitle');
                if (subtitle) {
                    subtitle.textContent = 'Penjualan 30 hari terakhir';
                }
                
                createSalesChart('daily');
                createProductsChart('quantity');
            } catch (error) {
                console.error('Error creating charts:', error);
            }
        }, 300);
    });
</script>

<style>
    @media print {
        /* Hide buttons when printing */
        button, .no-print {
            display: none !important;
        }
        
        /* Adjust layout for print */
        .content {
            padding: 0 !important;
        }
        
        /* Ensure charts are visible */
        canvas {
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* Page breaks */
        .bg-white {
            page-break-inside: avoid;
        }
        
        /* Clean borders */
        .border-gray-200 {
            border-color: #000 !important;
        }
    }
</style>
