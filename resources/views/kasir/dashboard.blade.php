@extends('layouts.kasir.index')

@section('content')
    <!-- Page Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-green-400 to-green-700 rounded-xl shadow-lg p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold mb-2">Selamat Datang di POS Sanjaya</h2>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-cash-register text-7xl text-white/20"></i>
                    </div> 
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Penjualan Hari Ini -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Penjualan Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900 mb-2">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</p>
                            @if($persenPenjualan >= 0)
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>{{ abs($persenPenjualan) }}% dari kemarin</p>
                            @else
                                <p class="text-sm text-danger"><i class="fas fa-arrow-down mr-1"></i>{{ abs($persenPenjualan) }}% dari kemarin</p>
                            @endif
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-cash-register text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Transaksi</p>
                            <p class="text-2xl font-bold text-gray-900 mb-2">{{ $totalTransaksiHariIni }}</p>
                            @if($persenTransaksi >= 0)
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>{{ abs($persenTransaksi) }}% dari kemarin</p>
                            @else
                                <p class="text-sm text-danger"><i class="fas fa-arrow-down mr-1"></i>{{ abs($persenTransaksi) }}% dari kemarin</p>
                            @endif
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-receipt text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Produk Terjual -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Produk Terjual</p>
                            <p class="text-2xl font-bold text-gray-900 mb-2">{{ $produkTerjualHariIni }}</p>
                            @if($persenProdukTerjual >= 0)
                                <p class="text-sm text-success"><i class="fas fa-arrow-up mr-1"></i>{{ abs($persenProdukTerjual) }}% dari kemarin</p>
                            @else
                                <p class="text-sm text-danger"><i class="fas fa-arrow-down mr-1"></i>{{ abs($persenProdukTerjual) }}% dari kemarin</p>
                            @endif
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Stok Rendah -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Stok Rendah</p>
                            <p class="text-2xl font-bold text-gray-900 mb-2">{{ $stokRendah }}</p>
                            <p class="text-sm text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Perlu restok</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>Aksi Cepat
                    </h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Transaksi Penjualan -->
                    <a href="{{ route('kasir.transaksi.index') }}" class="group relative bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6 hover:from-green-100 hover:to-green-200 hover:border-green-400 transition-all duration-300 hover:shadow-xl hover:scale-105">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-cash-register text-white text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Transaksi Penjualan</h4>
                            <p class="text-sm text-gray-600 mb-4 leading-relaxed">Proses penjualan produk dan cetak struk pembayaran</p>
                            <div class="flex items-center space-x-2 text-green-600 font-semibold">
                                <span class="text-sm">Mulai Transaksi</span>
                                <i class="fas fa-arrow-right text-sm group-hover:translate-x-2 transition-transform"></i>
                            </div>
                        </div>
                    </a>

                    <!-- Jurnal Harian -->
                    <a href="{{ route('kasir.jurnal.index') }}" class="group relative bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 hover:from-blue-100 hover:to-blue-200 hover:border-blue-400 transition-all duration-300 hover:shadow-xl hover:scale-105">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-book text-white text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Jurnal Harian</h4>
                            <p class="text-sm text-gray-600 mb-4 leading-relaxed">Riwayat transaksi dan laporan penjualan harian</p>
                            <div class="flex items-center space-x-2 text-blue-600 font-semibold">
                                <span class="text-sm">Buka Jurnal</span>
                                <i class="fas fa-arrow-right text-sm group-hover:translate-x-2 transition-transform"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Transactions & Top Products -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-history text-green-600 mr-2"></i>Transaksi Terakhir
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Daftar transaksi terbaru hari ini</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($transaksiTerakhir as $index => $transaksi)
                                @php
                                    $bgColors = [
                                        'bg-gradient-to-br from-green-100 to-green-50',
                                        'bg-gradient-to-br from-blue-100 to-blue-50',
                                        'bg-gradient-to-br from-purple-100 to-purple-50'
                                    ];
                                    $iconColors = ['text-green-600', 'text-blue-600', 'text-purple-600'];
                                    $bgColor = $bgColors[$index % 3];
                                    $iconColor = $iconColors[$index % 3];
                                    $totalItem = $transaksi->detailTransaksi->sum('jumlah');
                                @endphp
                                <div class="flex items-center justify-between p-4 {{ $bgColor }} rounded-xl hover:shadow-md transition-shadow">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                            <i class="fas fa-receipt {{ $iconColor }} text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">#TRX-{{ str_pad($transaksi->id, 3, '0', STR_PAD_LEFT) }}</p>
                                            <p class="text-xs text-gray-600">{{ $totalItem }} item • {{ $transaksi->created_at->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <p class="text-base font-bold text-gray-900">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</p>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">Belum ada transaksi hari ini</p>
                                </div>
                            @endforelse
                        </div>
                        @if($transaksiTerakhir->count() > 0)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('kasir.jurnal.index') }}" class="flex items-center justify-center text-sm text-green-600 hover:text-green-800 font-semibold group">
                                <span>Lihat Semua Transaksi</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>Produk Terlaris
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Top produk dengan penjualan tertinggi</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($produkTerlaris as $index => $item)
                                @php
                                    $badgeColors = [
                                        'bg-yellow-100 text-yellow-800',
                                        'bg-gray-100 text-gray-800',
                                        'bg-orange-100 text-orange-800'
                                    ];
                                    $badgeIcons = ['fa-crown', 'fa-medal', 'fa-award'];
                                    $badgeColor = $badgeColors[$index] ?? 'bg-gray-100 text-gray-800';
                                    $badgeIcon = $badgeIcons[$index] ?? 'fa-box';
                                @endphp
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:shadow-md transition-shadow border border-gray-100">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="flex items-center justify-center">
                                            <span class="w-8 h-8 {{ $badgeColor }} rounded-lg flex items-center justify-center font-bold text-sm">
                                                <i class="fas {{ $badgeIcon }}"></i>
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-900">{{ $item->produk->nama }}</p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-md font-medium">
                                                    {{ $item->total_terjual }} terjual
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-base font-bold text-gray-900">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500">per unit</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 font-medium">Belum ada produk terjual hari ini</p>
                                </div>
                            @endforelse
                        </div>
                        @if($produkTerlaris->count() > 0)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('kasir.jurnal.index') }}" class="flex items-center justify-center text-sm text-blue-600 hover:text-blue-800 font-semibold group">
                                <span>Lihat Laporan Lengkap</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                <i class="fas fa-chart-area text-purple-600 mr-2"></i>Grafik Penjualan
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Ringkasan penjualan 7 hari terakhir</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="changeChartPeriod('7days')" class="chart-period-btn active px-4 py-2 text-sm font-semibold bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">7 Hari</button>
                            <button onclick="changeChartPeriod('30days')" class="chart-period-btn px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">30 Hari</button>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="h-80 relative">
                        <canvas id="salesChart"></canvas>
                    </div>
                    <!-- Chart Summary -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 border-t border-gray-200">
                        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                            <div class="flex items-center justify-center space-x-2 mb-2">
                                <div class="w-4 h-4 bg-green-500 rounded-full shadow-sm"></div>
                                <span class="text-sm font-semibold text-gray-700">Total Penjualan</span>
                            </div>
                            <p class="text-xl font-bold text-gray-900" id="totalSales">Rp 0</p>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                            <div class="flex items-center justify-center space-x-2 mb-2">
                                <div class="w-4 h-4 bg-blue-500 rounded-full shadow-sm"></div>
                                <span class="text-sm font-semibold text-gray-700">Rata-rata Harian</span>
                            </div>
                            <p class="text-xl font-bold text-gray-900" id="avgDaily">Rp 0</p>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl">
                            <div class="flex items-center justify-center space-x-2 mb-2">
                                <div class="w-4 h-4 bg-yellow-500 rounded-full shadow-sm"></div>
                                <span class="text-sm font-semibold text-gray-700">Total Transaksi</span>
                            </div>
                            <p class="text-xl font-bold text-gray-900" id="totalTransactions">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Analytics Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Products Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-trophy text-amber-500 mr-2"></i>Produk Terlaris
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Berdasarkan jumlah penjualan</p>
                    </div>
                    <div class="p-6">
                        <div class="h-64 relative">
                            <canvas id="productsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Sales by Hour -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-clock text-indigo-600 mr-2"></i>Penjualan per Jam
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Distribusi penjualan hari ini</p>
                    </div>
                    <div class="p-6">
                        <div class="h-64 relative">
                            <canvas id="hourlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Sales Chart Data
    const salesData = {
            '7days': {
                labels: @json($labels7Hari),
                sales: @json($penjualan7Hari),
                transactions: @json($transaksi7Hari),
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
            
            // Calculate totals - ensure we're working with numbers
            const totalSales = data.sales.reduce((sum, value) => {
                const numValue = parseFloat(value) || 0;
                return sum + numValue;
            }, 0);
            
            const totalTransactions = data.transactions.reduce((sum, value) => {
                const numValue = parseInt(value) || 0;
                return sum + numValue;
            }, 0);
            
            // Calculate average
            const daysCount = period === '7days' ? 7 : 30;
            const avgDaily = totalSales / daysCount;
            
            // Format with proper Indonesian number format
            const formattedTotalSales = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(totalSales);
            
            const formattedAvgDaily = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(Math.round(avgDaily));
            
            // Update DOM
            document.getElementById('totalSales').textContent = formattedTotalSales;
            document.getElementById('avgDaily').textContent = formattedAvgDaily;
            document.getElementById('totalTransactions').textContent = totalTransactions.toLocaleString('id-ID');
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
            
            // Data produk terlaris dari controller
            const produkData = @json($produkTerlarisChart);
            const labels = produkData.map(item => item.produk ? item.produk.nama : 'Produk');
            const values = produkData.map(item => parseInt(item.total_terjual));
            
            // Warna yang bervariasi
            const colors = [
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(156, 163, 175, 0.8)'
            ];
            
            const borderColors = [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(156, 163, 175)'
            ];
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels.length > 0 ? labels : ['Tidak ada data'],
                    datasets: [{
                        data: values.length > 0 ? values : [1],
                        backgroundColor: colors.slice(0, labels.length > 0 ? labels.length : 1),
                        borderColor: borderColors.slice(0, labels.length > 0 ? labels.length : 1),
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
            
            // Data penjualan per jam dari controller
            const hourlyLabels = @json($labelJam);
            const hourlyData = @json($penjualanPerJam);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: hourlyLabels,
                    datasets: [{
                        label: 'Transaksi',
                        data: hourlyData,
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
@endsection