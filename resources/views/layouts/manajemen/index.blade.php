@extends('layouts.manajemen.header')

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
            <p class="text-2xl font-bold text-gray-900">Rp 2.450.000</p>
            <p class="text-sm text-success">
              <i class="fas fa-arrow-up mr-1"></i>12% dari kemarin
            </p>
          </div>
          <div
            class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-700 bg-opacity-10 rounded-lg flex items-center justify-center">
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
            <p class="text-2xl font-bold text-gray-900">87</p>
            <p class="text-sm text-success">
              <i class="fas fa-arrow-up mr-1"></i>5% dari kemarin
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
            <p class="text-2xl font-bold text-gray-900">234</p>
            <p class="text-sm text-success">
              <i class="fas fa-arrow-up mr-1"></i>8% dari kemarin
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
            <p class="text-2xl font-bold text-gray-900">3</p>
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
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <a href="jurnal.html"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
          <div
            class="w-12 h-12 bg-gradient-to-r from-purple-400 to-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
            <i class="fas fa-book text-white text-xl"></i>
          </div>
          <span class="text-sm font-medium text-gray-900 text-center">Jurnal Harian</span>
          <span class="text-xs text-gray-500 mt-1">Kelola keuangan</span>
        </a>

        <a href="laporan.html"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
          <div
            class="w-12 h-12 bg-gradient-to-r from-amber-400 to-amber-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
            <i class="fas fa-chart-line text-white text-xl"></i>
          </div>
          <span class="text-sm font-medium text-gray-900 text-center">Lihat Laporan</span>
          <span class="text-xs text-gray-500 mt-1">Analisis bisnis</span>
        </a>

        <a href="stok-bahan.html"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
          <div
            class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
            <i class="fas fa-boxes text-white text-xl"></i>
          </div>
          <span class="text-sm font-medium text-gray-900 text-center">Stok Bahan</span>
          <span class="text-xs text-gray-500 mt-1">Monitor stok</span>
        </a>

        <a href="stok-opname.html"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
          <div
            class="w-12 h-12 bg-gradient-to-r from-red-400 to-red-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
            <i class="fas fa-clipboard-check text-white text-xl"></i>
          </div>
          <span class="text-sm font-medium text-gray-900 text-center">Stok Opname</span>
          <span class="text-xs text-gray-500 mt-1">Audit stok</span>
        </a>

        <a href="konversi-satuan.html"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 group">
          <div
            class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
            <i class="fas fa-balance-scale text-white text-xl"></i>
          </div>
          <span class="text-sm font-medium text-gray-900 text-center">Konversi Satuan</span>
          <span class="text-xs text-gray-500 mt-1">Kelola satuan</span>
        </a>
      </div>
    </div>

    <!-- Recent Transactions & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Low Stock Alert -->
      <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Stok Bahan Baku Menipis
            </h3>
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
              <i class="fas fa-exclamation-triangle mr-1"></i>Perlu Restok
            </span>
          </div>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <!-- Ragi Instan -->
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-seedling text-red-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Ragi Instan
                  </p>
                  <p class="text-xs text-red-600">
                    Sisa 2 kg • Min: 5 kg
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-red-600">Kritis</p>
                <p class="text-xs text-gray-500">40%</p>
              </div>
            </div>

            <!-- Mentega/Margarin -->
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-butter text-yellow-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Mentega/Margarin
                  </p>
                  <p class="text-xs text-yellow-600">
                    Sisa 8 kg • Min: 10 kg
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-yellow-600">Rendah</p>
                <p class="text-xs text-gray-500">80%</p>
              </div>
            </div>

            <!-- Bread Improver -->
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-flask text-orange-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Bread Improver
                  </p>
                  <p class="text-xs text-orange-600">
                    Sisa 3 kg • Min: 5 kg
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-orange-600">Rendah</p>
                <p class="text-xs text-gray-500">60%</p>
              </div>
            </div>

            <!-- Keju Parut -->
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-cheese text-red-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Keju Parut
                  </p>
                  <p class="text-xs text-red-600">
                    Sisa 2 kg • Min: 5 kg
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-red-600">Kritis</p>
                <p class="text-xs text-gray-500">40%</p>
              </div>
            </div>

            <!-- Minyak Sayur -->
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-oil-can text-red-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Minyak Sayur
                  </p>
                  <p class="text-xs text-red-600">
                    Sisa 0 liter • Min: 10 liter
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-red-600">Habis</p>
                <p class="text-xs text-gray-500">0%</p>
              </div>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="stok-bahan.html" class="text-sm text-green-600 hover:text-green-800 font-medium">Kelola Stok
              Bahan Baku →</a>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            Produk Roti Terlaris
          </h3>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-bread-slice text-amber-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Roti Tawar
                  </p>
                  <p class="text-xs text-gray-500">58 terjual</p>
                </div>
              </div>
              <p class="text-sm font-bold text-gray-900">Rp 12.000</p>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-cookie-bite text-orange-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Roti Cokelat
                  </p>
                  <p class="text-xs text-gray-500">45 terjual</p>
                </div>
              </div>
              <p class="text-sm font-bold text-gray-900">Rp 8.000</p>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-birthday-cake text-red-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Roti Keju
                  </p>
                  <p class="text-xs text-gray-500">42 terjual</p>
                </div>
              </div>
              <p class="text-sm font-bold text-gray-900">Rp 10.000</p>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-ice-cream text-pink-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Roti Strawberry
                  </p>
                  <p class="text-xs text-gray-500">38 terjual</p>
                </div>
              </div>
              <p class="text-sm font-bold text-gray-900">Rp 9.000</p>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <i class="fas fa-candy-cane text-purple-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    Roti Kismis
                  </p>
                  <p class="text-xs text-gray-500">32 terjual</p>
                </div>
              </div>
              <p class="text-sm font-bold text-gray-900">Rp 11.000</p>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="laporan.html" class="text-sm text-green-600 hover:text-green-800 font-medium">Lihat Laporan
              Lengkap →</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Sales Chart -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">
            Grafik Penjualan
          </h3>
          <p class="text-sm text-gray-500">
            Ringkasan penjualan 7 hari terakhir
          </p>
        </div>
        <div class="flex items-center space-x-2">
          <button onclick="changeChartPeriod('7days')"
            class="chart-period-btn active px-3 py-1 text-xs bg-green-100 text-green-600 rounded-lg">
            7 Hari
          </button>
          <button onclick="changeChartPeriod('30days')"
            class="chart-period-btn px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
            30 Hari
          </button>
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
            <p class="text-lg font-bold text-gray-900" id="totalSales">
              Rp 15.850.000
            </p>
          </div>
          <div class="text-center">
            <div class="flex items-center justify-center space-x-2 mb-2">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span class="text-sm text-gray-600">Rata-rata Harian</span>
            </div>
            <p class="text-lg font-bold text-gray-900" id="avgDaily">
              Rp 2.264.000
            </p>
          </div>
          <div class="text-center">
            <div class="flex items-center justify-center space-x-2 mb-2">
              <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
              <span class="text-sm text-gray-600">Transaksi</span>
            </div>
            <p class="text-lg font-bold text-gray-900" id="totalTransactions">
              487
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  let sidebarOpen = false;

  // Toggle sidebar
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobileOverlay");

    // Only toggle on mobile/tablet
    if (window.innerWidth < 1024) {
      sidebar.classList.toggle("-translate-x-full");
      overlay.classList.toggle("hidden");
      sidebarOpen = !sidebar.classList.contains("-translate-x-full");
    }
  }

  // Update current date and time
  function updateDateTime() {
    const now = new Date();
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };
    const dateTimeElement = document.getElementById("currentDateTime");
    if (dateTimeElement) {
      dateTimeElement.textContent = now.toLocaleDateString(
        "id-ID",
        options
      );
    }
  }

  // Handle window resize
  window.addEventListener("resize", function () {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobileOverlay");

    if (window.innerWidth >= 1024) {
      sidebar.classList.remove("-translate-x-full");
      overlay.classList.add("hidden");
      sidebarOpen = false;
    } else {
      sidebar.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
      sidebarOpen = false;
    }
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (e) {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobileOverlay");
    const menuButtons = document.querySelectorAll(
      '[onclick*="toggleSidebar"]'
    );

    let clickedMenuButton = false;
    menuButtons.forEach((button) => {
      if (button.contains(e.target)) {
        clickedMenuButton = true;
      }
    });

    if (
      window.innerWidth < 1024 &&
      !sidebar.contains(e.target) &&
      !clickedMenuButton &&
      !sidebar.classList.contains("-translate-x-full")
    ) {
      sidebar.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
      sidebarOpen = false;
    }
  });

  // Sales Chart Data
  const salesData = {
    "7days": {
      labels: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
      sales: [
        1850000, 2100000, 2400000, 1950000, 2650000, 2900000, 2000000,
      ],
      transactions: [45, 52, 68, 47, 78, 89, 60],
      colors: {
        primary: "rgba(34, 197, 94, 0.8)",
        secondary: "rgba(59, 130, 246, 0.6)",
      },
    },
    "30days": {
      labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
      sales: [15200000, 18500000, 17800000, 19200000],
      transactions: [385, 461, 433, 478],
      colors: {
        primary: "rgba(34, 197, 94, 0.8)",
        secondary: "rgba(59, 130, 246, 0.6)",
      },
    },
  };

  let currentChart = null;
  let currentPeriod = "7days";

  // Create Sales Chart
  function createSalesChart(period = "7days") {
    const ctx = document.getElementById("salesChart").getContext("2d");
    const data = salesData[period];

    // Destroy existing chart if it exists
    if (currentChart) {
      currentChart.destroy();
    }

    currentChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: data.labels,
        datasets: [
          {
            label: "Penjualan (Rp)",
            data: data.sales,
            borderColor: "rgb(34, 197, 94)",
            backgroundColor: data.colors.primary,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: "rgb(34, 197, 94)",
            pointBorderColor: "#ffffff",
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            backgroundColor: "rgba(0, 0, 0, 0.8)",
            titleColor: "white",
            bodyColor: "white",
            borderColor: "rgb(34, 197, 94)",
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: function (context) {
                return (
                  "Penjualan: Rp " +
                  context.parsed.y.toLocaleString("id-ID")
                );
              },
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "rgba(0, 0, 0, 0.05)",
            },
            ticks: {
              callback: function (value) {
                return "Rp " + (value / 1000000).toFixed(1) + "M";
              },
              color: "rgb(107, 114, 128)",
            },
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "rgb(107, 114, 128)",
            },
          },
        },
        interaction: {
          intersect: false,
          mode: "index",
        },
      },
    });
  }

  // Change Chart Period
  function changeChartPeriod(period) {
    currentPeriod = period;

    // Update button states
    document.querySelectorAll(".chart-period-btn").forEach((btn) => {
      btn.classList.remove("active", "bg-green-100", "text-green-600");
      btn.classList.add("bg-gray-100", "text-gray-600");
    });

    event.target.classList.remove("bg-gray-100", "text-gray-600");
    event.target.classList.add("active", "bg-green-100", "text-green-600");

    // Update chart
    createSalesChart(period);
    updateSalesStats(period);
  }

  // Update Sales Statistics
  function updateSalesStats(period) {
    const data = salesData[period];
    const totalSales = data.sales.reduce((sum, value) => sum + value, 0);
    const totalTransactions = data.transactions.reduce(
      (sum, value) => sum + value,
      0
    );
    const avgDaily = period === "7days" ? totalSales / 7 : totalSales / 30;

    document.getElementById("totalSales").textContent =
      "Rp " + totalSales.toLocaleString("id-ID");
    document.getElementById("avgDaily").textContent =
      "Rp " + Math.round(avgDaily).toLocaleString("id-ID");
    document.getElementById("totalTransactions").textContent =
      totalTransactions.toString();
  }

  // Initialize
  document.addEventListener("DOMContentLoaded", function () {
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Initialize mobile state
    if (window.innerWidth < 1024) {
      const sidebar = document.getElementById("sidebar");
      const overlay = document.getElementById("mobileOverlay");
      sidebar.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
    }

    // Initialize charts
    setTimeout(() => {
      createSalesChart("7days");
      updateSalesStats("7days");
      createProductsChart();
      createHourlyChart();
    }, 100);
  });

  // Create Top Products Chart (Doughnut)
  function createProductsChart() {
    const ctx = document.getElementById("productsChart").getContext("2d");

    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: [
          "Nasi Gudeg",
          "Ayam Bakar",
          "Kopi Hitam",
          "Es Teh Manis",
          "Lainnya",
        ],
        datasets: [
          {
            data: [45, 28, 32, 25, 15],
            backgroundColor: [
              "rgba(34, 197, 94, 0.8)",
              "rgba(59, 130, 246, 0.8)",
              "rgba(245, 158, 11, 0.8)",
              "rgba(239, 68, 68, 0.8)",
              "rgba(156, 163, 175, 0.8)",
            ],
            borderColor: [
              "rgb(34, 197, 94)",
              "rgb(59, 130, 246)",
              "rgb(245, 158, 11)",
              "rgb(239, 68, 68)",
              "rgb(156, 163, 175)",
            ],
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              padding: 20,
              usePointStyle: true,
              font: {
                size: 12,
              },
            },
          },
          tooltip: {
            backgroundColor: "rgba(0, 0, 0, 0.8)",
            titleColor: "white",
            bodyColor: "white",
            borderColor: "rgb(34, 197, 94)",
            borderWidth: 1,
            cornerRadius: 8,
            callbacks: {
              label: function (context) {
                const total = context.dataset.data.reduce(
                  (sum, value) => sum + value,
                  0
                );
                const percentage = ((context.parsed / total) * 100).toFixed(
                  1
                );
                return (
                  context.label +
                  ": " +
                  context.parsed +
                  " (" +
                  percentage +
                  "%)"
                );
              },
            },
          },
        },
        cutout: "60%",
      },
    });
  }

  // Create Hourly Sales Chart (Bar)
  function createHourlyChart() {
    const ctx = document.getElementById("hourlyChart").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: [
          "08:00",
          "09:00",
          "10:00",
          "11:00",
          "12:00",
          "13:00",
          "14:00",
          "15:00",
          "16:00",
          "17:00",
        ],
        datasets: [
          {
            label: "Transaksi",
            data: [2, 5, 8, 12, 18, 25, 15, 12, 8, 5],
            backgroundColor: "rgba(59, 130, 246, 0.8)",
            borderColor: "rgb(59, 130, 246)",
            borderWidth: 1,
            borderRadius: 4,
            borderSkipped: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            backgroundColor: "rgba(0, 0, 0, 0.8)",
            titleColor: "white",
            bodyColor: "white",
            borderColor: "rgb(59, 130, 246)",
            borderWidth: 1,
            cornerRadius: 8,
            callbacks: {
              label: function (context) {
                return "Transaksi: " + context.parsed.y;
              },
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "rgba(0, 0, 0, 0.05)",
            },
            ticks: {
              stepSize: 5,
              color: "rgb(107, 114, 128)",
            },
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "rgb(107, 114, 128)",
            },
          },
        },
      },
    });
  }
</script>
@endpush