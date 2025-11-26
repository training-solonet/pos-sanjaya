<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>POS Sanjaya - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#3B82F6",
            secondary: "#1E40AF",
            accent: "#F59E0B",
            success: "#10B981",
            danger: "#EF4444",
            dark: "#1F2937",
          },
        },
      },
    };
  </script>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

    body {
      font-family: "Inter", sans-serif;
    }

    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

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
  {{-- <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()">
  </div> --}}

  <!-- Sidebar -->
   @include('layouts.manajemen.sidebar')

  <!-- Sidebar Overlay for Mobile -->
  <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()">
  </div>

  <!-- Main Content -->
  <div class="content flex-1 lg:ml-64">
    <!-- Header -->
     @include('layouts.manajemen.header')
     <!-- Page Content -->
    <main class="p-4 sm:p-6 lg:p-8">
      <div class="space-y-6">
        @yield('content')
      </div>
    </main>
  </div>

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
</body>
</html>