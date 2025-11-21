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
  </script>
</body>

</html>