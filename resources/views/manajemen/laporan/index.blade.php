@extends('layouts.manajemen.index')

@section('content')
    <div class="content flex-1 lg:flex-1">

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>Export Excel
                        </button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option>Semua Kategori</option>
                                    <option>Makanan</option>
                                    <option>Minuman</option>
                                    <option>Snack</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                                Filter
                            </button>
                        </div>
                    </div>
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
                                <p class="text-sm text-gray-500">Penjualan 30 hari terakhir</p>
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
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terjual
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($topProducts as $prod)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400"></i>
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $prod->nama }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($prod->total_qty,0,',','.') }} pcs</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">Rp {{ number_format($prod->revenue,0,',','.') }}</td>
                                        <td class="px-6 py-4 text-sm text-success">Rp {{ number_format(($prod->revenue * 0.5) ?? 0,0,',','.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data produk terlaris</td>
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
                                <p class="text-2xl font-bold text-accent">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

<script>
    let sidebarOpen = false;

    // Toggle sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebarOpen = !sidebarOpen;

        if (sidebarOpen) {
            sidebar.classList.add('show');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.remove('show');
            overlay.classList.add('hidden');
        }
    }

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

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('show');
            overlay.classList.add('hidden');
            sidebarOpen = false;
        }
    });

    // Chart variables
    let salesChart, productsChart;
    // Server-provided datasets (if available)
    let serverSales = @json($salesChart ?? null);
    let serverProducts = @json($productsChart ?? null);
    let serverMonthly = @json($monthlyReport ?? null);

    // Generate realistic sales data based on October 2025
    function generateSalesData(type = 'daily') {
        const currentDate = new Date(2025, 9, 1); // October 1, 2025
        const labels = [];
        const data = [];

        if (type === 'daily') {
            // Generate 30 days of data
            for (let i = 0; i < 30; i++) {
                const date = new Date(currentDate);
                date.setDate(date.getDate() + i);
                labels.push(date.getDate());

                // Generate realistic sales with patterns
                let baseAmount = 1800000; // Base 1.8M

                // Weekend boost (Sabtu-Minggu)
                const dayOfWeek = date.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    baseAmount *= 1.4; // 40% increase on weekends
                }

                // Peak days (mid-month bonus)
                if (date.getDate() >= 15 && date.getDate() <= 20) {
                    baseAmount *= 1.2; // 20% increase mid-month
                }

                // Add random variation ±300k
                const variation = (Math.random() - 0.5) * 600000;
                data.push(Math.round(baseAmount + variation));
            }
        } else {
            // Weekly data (4 weeks)
            const weekLabels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
            labels.push(...weekLabels);

            // Weekly totals (sum of 7 days each)
            const weeklyTotals = [12500000, 13200000, 14100000, 13800000];
            data.push(...weeklyTotals);
        }

        return {
            labels,
            data
        };
    }

    // Generate product sales data (fallback) - kept for demo if server data not present
    function generateProductData(type = 'quantity') {
        return {
            labels: ['Demo A', 'Demo B', 'Demo C'],
            data: [120, 90, 60],
            colors: ['#EF4444', '#F97316', '#F59E0B']
        };
    }

    // Create sales chart (uses server data when available)
    function createSalesChart(type = 'daily') {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        let labels = [];
        let data = [];

        if (serverSales && serverSales.labels && serverSales.data) {
            labels = serverSales.labels;
            data = serverSales.data;
        } else {
            const generated = generateSalesData(type);
            labels = generated.labels;
            data = generated.data;
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
                    x: { grid: { display: false }, title: { display: true, text: type === 'daily' ? 'Tanggal' : 'Periode', font: { size: 12, weight: 'bold' } } },
                    y: { grid: { color: 'rgba(0, 0, 0, 0.05)' }, title: { display: true, text: 'Penjualan (Rp)', font: { size: 12, weight: 'bold' } }, ticks: { callback: function(value) { return 'Rp ' + (value / 1000000).toFixed(1) + 'M'; } } }
                },
                animation: { duration: 800, easing: 'easeInOutQuart' }
            }
        });

        // Update statistics
        const max = data.length ? Math.max(...data) : 0;
        const min = data.length ? Math.min(...data) : 0;
        const avg = data.length ? Math.round(data.reduce((a, b) => a + b, 0) / data.length) : 0;

        document.getElementById('maxSales').textContent = formatCurrency(max);
        document.getElementById('minSales').textContent = formatCurrency(min);
        document.getElementById('avgSales').textContent = formatCurrency(avg);
    }

    // Create products chart (uses server data when available)
    function createProductsChart(type = 'quantity') {
        const ctx = document.getElementById('productsChart');
        if (!ctx) return;

        let labels = [];
        let data = [];
        let colors = [];

        if (serverProducts && serverProducts.labels) {
            labels = serverProducts.labels;
            data = (type === 'quantity') ? serverProducts.dataQty : serverProducts.dataRevenue;
            colors = serverProducts.colors || [];
        } else {
            const generated = generateProductData(type);
            labels = generated.labels;
            data = generated.data;
            colors = generated.colors;
        }

        if (productsChart) {
            productsChart.destroy();
        }

        productsChart = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderWidth: 0, hoverBorderWidth: 3, hoverBorderColor: '#FFFFFF', hoverOffset: 10 }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 15, font: { size: 12 }, padding: 15, generateLabels: function(chart) { const data = chart.data; return data.labels.map((label, i) => ({ text: label, fillStyle: data.datasets[0].backgroundColor[i], hidden: false, index: i })); } } },
                    tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', titleColor: '#FFF', bodyColor: '#FFF', callbacks: { label: function(context) { const value = context.parsed; const total = context.dataset.data.reduce((a,b)=>a+b,0); const percentage = ((value/total)*100).toFixed(1); if (type === 'quantity') return context.label + ': ' + value + ' porsi (' + percentage + '%)'; return context.label + ': ' + formatCurrency(value) + ' (' + percentage + '%)'; } } }
                },
                cutout: '65%',
                animation: { animateRotate: true, duration: 800 }
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

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Create charts with delay to ensure DOM is ready
        setTimeout(() => {
            createSalesChart('daily');
            createProductsChart('quantity');
        }, 300);
    });
</script>

{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="U                    Dashboard Manajemen
                </a>

                <!-- Jurnal Harian -->
                <a href="jurnal.html" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sanjaya - Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#F59E0B',
                        success: '#10B981',
                        danger: '#EF4444',
                        dark: '#1F2937',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen lg:flex">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:relative lg:flex-shrink-0">
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cash-register text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Sanjaya Bakery</h1>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route("manajemen") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-home text-gray-400 group-hover:text-green-600 mr-3"></i>
                Dashboard Manajemen
            </a>
            <a href="{{ route("manajemen_jurnal") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-book text-gray-400 group-hover:text-green-600 mr-3"></i>
                Jurnal Harian
            </a>
            <a href="{{ route("manajemen_bahanbaku") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-boxes text-gray-400 group-hover:text-green-600 mr-3"></i>
                Stok Bahan Baku
            </a>
            <a href="{{ route("manajemen_produk") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-cookie-bite text-gray-400 group-hover:text-green-600 mr-3"></i>
                Stok Produk
            </a>
            <a href="{{ route("manajemen_konversi") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-exchange-alt text-gray-400 group-hover:text-green-600 mr-3"></i>
                Konversi Satuan
            </a>
            <a href="{{ route("manajemen_resep") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-utensils text-gray-400 group-hover:text-green-600 mr-3"></i>
                Resep & Produksi
            </a>
            <a href="{{ route("manajemen_laporan") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg">
                <i class="fas fa-chart-line text-white mr-3"></i>
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
                    <p class="text-sm font-medium text-gray-900 truncate">Admin</p>
                    <p class="text-xs text-gray-500">Kasir</p>
                </div>
                <button class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200">
                    <i class="fas fa-sign-out-alt text-gray-600 text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="content flex-1 lg:flex-1">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Mobile Menu Button & Page Title -->
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-medium text-gray-900">Kasir: Admin</p>
                            <p class="text-xs text-gray-500" id="currentDateTime"></p>
                        </div>
                        <button class="relative w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                            <i class="fas fa-bell text-gray-600"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>Export Excel
                        </button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option>Semua Kategori</option>
                                    <option>Makanan</option>
                                    <option>Minuman</option>
                                    <option>Snack</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                                <p class="text-2xl font-bold text-gray-900">Rp 2.450.000</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>12% dari minggu lalu</p>
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
                                <p class="text-2xl font-bold text-gray-900">127</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>8% dari minggu lalu</p>
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
                                <p class="text-2xl font-bold text-gray-900">Rp 19.291</p>
                                <p class="text-sm text-danger"><i class="fas fa-arrow-down mr-1"></i>3% dari minggu lalu</p>
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
                                <p class="text-2xl font-bold text-gray-900">234</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>15% dari minggu lalu</p>
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
                                <p class="text-sm text-gray-500">Penjualan 30 hari terakhir</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="changeSalesView('daily')" class="sales-view-btn active px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg">Harian</button>
                                <button onclick="changeSalesView('weekly')" class="sales-view-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Mingguan</button>
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
                                <button onclick="changeProductView('quantity')" class="product-view-btn active px-3 py-1 text-xs bg-green-100 text-green-600 rounded-lg">Qty</button>
                                <button onclick="changeProductView('revenue')" class="product-view-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Revenue</button>
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
                                        <span class="text-sm font-medium">Roti Tawar</span>
                                        <span class="ml-auto text-sm text-gray-600">325 pcs</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-2">Highest Revenue</p>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                        <span class="text-sm font-medium">Croissant</span>
                                        <span class="ml-auto text-sm text-gray-600">Rp 4.5M</span>
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
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terjual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-bread-slice text-gray-400"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">Roti Tawar</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">65 pcs</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp 520.000</td>
                                    <td class="px-6 py-4 text-sm text-success">Rp 260.000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-cookie text-gray-400"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">Croissant</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">48 pcs</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp 720.000</td>
                                    <td class="px-6 py-4 text-sm text-success">Rp 360.000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-water text-gray-400"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">Teh Botol</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">42 botol</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp 252.000</td>
                                    <td class="px-6 py-4 text-sm text-success">Rp 126.000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-cookie-bite text-gray-400"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">Donat Coklat</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">35 pcs</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp 245.000</td>
                                    <td class="px-6 py-4 text-sm text-success">Rp 122.500</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-bread-slice text-gray-400"></i>
                                            </div>
                                            <span class="font-medium text-gray-900">Roti Coklat</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">32 pcs</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp 384.000</td>
                                    <td class="px-6 py-4 text-sm text-success">Rp 192.000</td>
                                </tr>
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
                                <h4 class="text-xl font-bold text-gray-900">Januari 2024</h4>
                                <p class="text-sm text-gray-500 mb-2">Total Penjualan</p>
                                <p class="text-2xl font-bold text-primary">Rp 9.850.000</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-success/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-trending-up text-success text-2xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">Growth</h4>
                                <p class="text-sm text-gray-500 mb-2">Vs Bulan Lalu</p>
                                <p class="text-2xl font-bold text-success">+18%</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-accent/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-star text-accent text-2xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">Profit Margin</h4>
                                <p class="text-sm text-gray-500 mb-2">Rata-rata</p>
                                <p class="text-2xl font-bold text-accent">45%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let sidebarOpen = false;

        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebarOpen = !sidebarOpen;
            
            if (sidebarOpen) {
                sidebar.classList.add('show');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.remove('show');
                overlay.classList.add('hidden');
            }
        }

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

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.add('hidden');
                sidebarOpen = false;
            }
        });

        // Chart variables
        let salesChart, productsChart;

        // Generate realistic sales data based on October 2025
        function generateSalesData(type = 'daily') {
            const currentDate = new Date(2025, 9, 1); // October 1, 2025
            const labels = [];
            const data = [];
            
            if (type === 'daily') {
                // Generate 30 days of data
                for (let i = 0; i < 30; i++) {
                    const date = new Date(currentDate);
                    date.setDate(date.getDate() + i);
                    labels.push(date.getDate());
                    
                    // Generate realistic sales with patterns
                    let baseAmount = 1800000; // Base 1.8M
                    
                    // Weekend boost (Sabtu-Minggu)
                    const dayOfWeek = date.getDay();
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        baseAmount *= 1.4; // 40% increase on weekends
                    }
                    
                    // Peak days (mid-month bonus)
                    if (date.getDate() >= 15 && date.getDate() <= 20) {
                        baseAmount *= 1.2; // 20% increase mid-month
                    }
                    
                    // Add random variation ±300k
                    const variation = (Math.random() - 0.5) * 600000;
                    data.push(Math.round(baseAmount + variation));
                }
            } else {
                // Weekly data (4 weeks)
                const weekLabels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
                labels.push(...weekLabels);
                
                // Weekly totals (sum of 7 days each)
                const weeklyTotals = [12500000, 13200000, 14100000, 13800000];
                data.push(...weeklyTotals);
            }
            
            return { labels, data };
        }

        // Generate product sales data with real Indonesian food names
        function generateProductData(type = 'quantity') {
            const products = [
                { name: 'Roti Tawar', qty: 325, revenue: 2600000, color: '#EF4444' },
                { name: 'Croissant', qty: 240, revenue: 3600000, color: '#F97316' },
                { name: 'Teh Botol', qty: 210, revenue: 1260000, color: '#F59E0B' },
                { name: 'Roti Coklat', qty: 185, revenue: 2220000, color: '#84CC16' },
                { name: 'Donat Coklat', qty: 175, revenue: 1225000, color: '#10B981' },
                { name: 'Pain au Chocolat', qty: 150, revenue: 2400000, color: '#06B6D4' },
                { name: 'Brioche', qty: 128, revenue: 2304000, color: '#3B82F6' },
                { name: 'Bagel', qty: 120, revenue: 1200000, color: '#8B5CF6' },
                { name: 'Air Mineral', qty: 180, revenue: 540000, color: '#EC4899' }
            ];

            // Sort by the selected metric and take top 6
            const sortedProducts = products.sort((a, b) => {
                return type === 'quantity' ? b.qty - a.qty : b.revenue - a.revenue;
            }).slice(0, 6);

            return {
                labels: sortedProducts.map(p => p.name),
                data: sortedProducts.map(p => type === 'quantity' ? p.qty : p.revenue),
                colors: sortedProducts.map(p => p.color)
            };
        }

        // Create sales chart
        function createSalesChart(type = 'daily') {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const { labels, data } = generateSalesData(type);
            
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
                        legend: {
                            display: false
                        },
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
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: type === 'daily' ? 'Tanggal Oktober 2025' : 'Periode',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            title: {
                                display: true,
                                text: 'Penjualan (Rp)',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            // Update statistics
            const max = Math.max(...data);
            const min = Math.min(...data);
            const avg = Math.round(data.reduce((a, b) => a + b, 0) / data.length);

            document.getElementById('maxSales').textContent = formatCurrency(max);
            document.getElementById('minSales').textContent = formatCurrency(min);
            document.getElementById('avgSales').textContent = formatCurrency(avg);
        }

        // Create products chart
        function createProductsChart(type = 'quantity') {
            const ctx = document.getElementById('productsChart');
            if (!ctx) return;

            const { labels, data, colors } = generateProductData(type);
            
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
                        borderWidth: 0,
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
                                font: {
                                    size: 12
                                },
                                padding: 15,
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
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    
                                    if (type === 'quantity') {
                                        return context.label + ': ' + value + ' porsi (' + percentage + '%)';
                                    } else {
                                        return context.label + ': ' + formatCurrency(value) + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    },
                    cutout: '65%',
                    animation: {
                        animateRotate: true,
                        duration: 1200
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

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);
            
            // Create charts with delay to ensure DOM is ready
            setTimeout(() => {
                createSalesChart('daily');
                createProductsChart('quantity');
            }, 300);
        });
    </script>
</body>
</html> --}}
