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
  <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()">
  </div>

  @include('layouts.manajemen.sidebar')

  <!-- Sidebar Overlay for Mobile -->
  <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()">
  </div>

  <!-- Main Content -->
  <div class="content flex-1 lg:flex-1">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Mobile Menu Button & Page Title -->
          <div class="flex items-center space-x-4">
            <button onclick="toggleSidebar()"
              class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
              <i class="fas fa-bars text-gray-600"></i>
            </button>
          </div>

          <!-- Header Actions -->
          <div class="flex items-center space-x-4">
            <div class="hidden md:block text-right">
              <p class="text-sm font-medium text-gray-900">Manager: Admin</p>
              <p class="text-xs text-gray-500" id="currentDateTime"></p>
            </div>
            <button
              class="relative w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
              <i class="fas fa-bell text-gray-600"></i>
              <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
            </button>
          </div>
        </div>
      </div>
    </header>

    @yield('content')
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
    });
  </script>
</body>
</html>