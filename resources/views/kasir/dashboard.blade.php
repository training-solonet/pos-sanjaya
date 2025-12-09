@extends('layouts.kasir.index')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sanjaya - Dashboard</title>
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
        
        /* Responsive sidebar styles */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar:not(.-translate-x-full) {
                transform: translateX(0);
            }
        }
        
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen lg:flex">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="content flex-1 lg:flex-1">

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="px-4 sm:px-6 lg:px-8 pb-4">
            <div class="space-y-4">
                <!-- Welcome Section -->
                <div class="bg-gradient-to-r from-green-400 to-green-700 rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Selamat Datang di POS Sanjaya</h2>
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
                                <p class="text-sm font-medium text-gray-600">Penjualan Hari Ini</p>
                                <p class="text-lg font-bold text-gray-900">Rp 2.450.000</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>12% dari kemarin</p>
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
                                <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                                <p class="text-lg font-bold text-gray-900">87</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>5% dari kemarin</p>
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
                                <p class="text-sm font-medium text-gray-600">Produk Terjual</p>
                                <p class="text-lg font-bold text-gray-900">234</p>
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>8% dari kemarin</p>
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
                                <p class="text-lg font-bold text-gray-900">3</p>
                                <p class="text-sm text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Perlu restok</p>
                            </div>
                            <div class="w-12 h-12 bg-danger/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-danger text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Transaksi Penjualan -->
                        <a href="{{ route('kasir.transaksi.index') }}" class="group relative bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4 hover:from-green-100 hover:to-green-200 transition-all duration-300 hover:shadow-md hover:scale-102">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300 shadow-md">
                                    <i class="fas fa-cash-register text-white text-lg"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-900 mb-2">Transaksi Penjualan</h4>
                                <p class="text-xs text-gray-600 mb-3 leading-relaxed">Proses penjualan produk dan cetak struk pembayaran</p>
                                <div class="flex items-center space-x-2 text-green-600 font-medium">
                                    <span class="text-xs">Mulai Transaksi</span>
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                </div>
                            </div>
                        </a>

                        <!-- Jurnal Harian -->
                        <a href="{{ route('kasir.jurnal.index') }}" class="group relative bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 hover:shadow-md hover:scale-102">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300 shadow-md">
                                    <i class="fas fa-book text-white text-lg"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-900 mb-2">Jurnal Harian</h4>
                                <p class="text-xs text-gray-600 mb-3 leading-relaxed">Riwayat transaksi dan laporan penjualan harian</p>
                                <div class="flex items-center space-x-2 text-blue-600 font-medium">
                                    <span class="text-xs">Buka Jurnal</span>
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Transactions & Top Products -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Transaksi Terakhir</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-700 bg-opacity-10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-receipt text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">#TRX-001</p>
                                            <p class="text-xs text-gray-500">2 item • 09:30</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp 35.000</p>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-receipt text-success"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">#TRX-002</p>
                                            <p class="text-xs text-gray-500">1 item • 09:25</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp 15.000</p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-receipt text-accent"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">#TRX-003</p>
                                            <p class="text-xs text-gray-500">3 item • 09:20</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp 45.000</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="jurnal.html" class="text-sm text-green-600 hover:text-green-800 font-medium">Lihat Semua Transaksi →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-bread-slice text-gray-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Roti Tawar</p>
                                            <p class="text-xs text-gray-500">65 terjual</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp 8.000</p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-cookie text-gray-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Croissant</p>
                                            <p class="text-xs text-gray-500">48 terjual</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp 15.000</p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-water text-gray-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Teh Botol</p>
                                            <p class="text-xs text-gray-500">42 terjual</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">Rp 6.000</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="laporan.html" class="text-sm text-green-600 hover:text-green-800 font-medium">Lihat Laporan Lengkap →</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Grafik Penjualan</h3>
                            <p class="text-sm text-gray-500">Ringkasan penjualan 7 hari terakhir</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="changeChartPeriod('7days')" class="chart-period-btn active px-3 py-1 text-xs bg-green-100 text-green-600 rounded-lg">7 Hari</button>
                            <button onclick="changeChartPeriod('30days')" class="chart-period-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">30 Hari</button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="h-80 relative">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <!-- Chart Summary -->
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <div class="flex items-center justify-center space-x-2 mb-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Total Penjualan</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900" id="totalSales">Rp 15.850.000</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center space-x-2 mb-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Rata-rata Harian</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900" id="avgDaily">Rp 2.264.000</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center space-x-2 mb-2">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Transaksi</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900" id="totalTransactions">487</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Analytics Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Products Chart -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                            <p class="text-sm text-gray-500">Berdasarkan jumlah penjualan</p>
                        </div>
                        <div class="p-6">
                            <div class="h-64 relative">
                                <canvas id="productsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Sales by Hour -->
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Penjualan per Jam</h3>
                            <p class="text-sm text-gray-500">Distribusi penjualan hari ini</p>
                        </div>
                        <div class="p-6">
                            <div class="h-64 relative">
                                <canvas id="hourlyChart"></canvas>
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
            const overlay = document.getElementById('mobileOverlay');
            
            // Only toggle on mobile/tablet
            if (window.innerWidth < 1024) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                sidebarOpen = !sidebar.classList.contains('-translate-x-full');
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
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                sidebarOpen = false;
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                sidebarOpen = false;
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            const menuButtons = document.querySelectorAll('[onclick*="toggleSidebar"]');
            
            let clickedMenuButton = false;
            menuButtons.forEach(button => {
                if (button.contains(e.target)) {
                    clickedMenuButton = true;
                }
            });
            
            if (window.innerWidth < 1024 && 
                !sidebar.contains(e.target) && 
                !clickedMenuButton && 
                !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                sidebarOpen = false;
            }
        });

        // Sales Chart Data
        const salesData = {
            '7days': {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                sales: [1850000, 2100000, 2400000, 1950000, 2650000, 2900000, 2000000],
                transactions: [45, 52, 68, 47, 78, 89, 60],
                colors: {
                    primary: 'rgba(34, 197, 94, 0.8)',
                    secondary: 'rgba(59, 130, 246, 0.6)'
                }
            },
            '30days': {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                sales: [15200000, 18500000, 17800000, 19200000],
                transactions: [385, 461, 433, 478],
                colors: {
                    primary: 'rgba(34, 197, 94, 0.8)',
                    secondary: 'rgba(59, 130, 246, 0.6)'
                }
            }
        };

        let currentChart = null;
        let currentPeriod = '7days';

        // Create Sales Chart
        function createSalesChart(period = '7days') {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const data = salesData[period];
            
            // Destroy existing chart if it exists
            if (currentChart) {
                currentChart.destroy();
            }

            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: data.sales,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: data.colors.primary,
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
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
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
        }

        // Change Chart Period
        function changeChartPeriod(period) {
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
            updateSalesStats(period);
        }

        // Update Sales Statistics
        function updateSalesStats(period) {
            const data = salesData[period];
            const totalSales = data.sales.reduce((sum, value) => sum + value, 0);
            const totalTransactions = data.transactions.reduce((sum, value) => sum + value, 0);
            const avgDaily = period === '7days' ? totalSales / 7 : totalSales / 30;
            
            document.getElementById('totalSales').textContent = 'Rp ' + totalSales.toLocaleString('id-ID');
            document.getElementById('avgDaily').textContent = 'Rp ' + Math.round(avgDaily).toLocaleString('id-ID');
            document.getElementById('totalTransactions').textContent = totalTransactions.toString();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);
            
            // Initialize mobile state
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
            
            // Initialize charts
            setTimeout(() => {
                createSalesChart('7days');
                updateSalesStats('7days');
                createProductsChart();
                createHourlyChart();
            }, 100);
        });

        // Create Top Products Chart (Doughnut)
        function createProductsChart() {
            const ctx = document.getElementById('productsChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Roti Tawar', 'Croissant', 'Teh Botol', 'Donat Coklat', 'Lainnya'],
                    datasets: [{
                        data: [65, 48, 42, 35, 25],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(156, 163, 175, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(156, 163, 175)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Create Hourly Sales Chart (Bar)
        function createHourlyChart() {
            const ctx = document.getElementById('hourlyChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
                    datasets: [{
                        label: 'Transaksi',
                        data: [2, 5, 8, 12, 18, 25, 15, 12, 8, 5],
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
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
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Transaksi: ' + context.parsed.y;
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
                                stepSize: 5,
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
                    }
                }
            });
        }
    </script>
</body>
</html>  
@endsection