<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS Sanjaya - Management Dashboard</title>
    
    <!-- PERBAIKAN: Ganti CDN tailwind dengan yang tepat -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <!-- PERBAIKAN: Pastikan Chart.js dimuat SEBELUM script kita -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
            background-color: #f9fafb;
        }
        
        .scrollbar-hide { 
            -ms-overflow-style: none; 
            scrollbar-width: none; 
        }
        
        .scrollbar-hide::-webkit-scrollbar { 
            display: none; 
        }
        
        /* Sidebar base styles */
        .sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 16rem !important; /* 256px */
        }
        
        /* Sidebar collapsed state */
        .sidebar.collapsed {
            width: 4.5rem !important; /* 72px */
        }
        
        /* Hide text when collapsed */
        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .sidebar-user-info,
        .sidebar.collapsed .sidebar-logout-btn {
            display: none;
        }
        
        /* Center icons when collapsed */
        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        .sidebar.collapsed .sidebar-icon {
            margin-right: 0 !important;
        }
        
        /* Main content margin adjustment */
        .content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Desktop mode */
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            
            .content {
                margin-left: 16rem;
            }
            
            .content.sidebar-collapsed {
                margin-left: 4.5rem;
            }
        }
        
        /* Mobile mode */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                width: 16rem !important;
            }
            
            .sidebar.-translate-x-full {
                transform: translateX(-100%);
            }
            
            .sidebar:not(.-translate-x-full) {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                width: 16rem !important;
            }
            
            .content {
                margin-left: 0 !important;
            }
        }
        
        /* Icon rotation animation */
        .rotate-icon {
            transition: transform 0.3s ease;
        }
        
        .rotate-icon.rotated {
            transform: rotate(180deg);
        }
        
        /* PERBAIKAN: Custom styles untuk chart */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* PERBAIKAN: Styling untuk tombol chart period */
        .chart-period-btn.active {
            background-color: #10b981 !important;
            color: white !important;
        }
        
        .chart-period-btn {
            transition: all 0.2s ease;
        }
        
        .chart-period-btn:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    @include('layouts.manajemen.sidebar')
    
    <!-- Main Content -->
    <div class="content flex-1">
        <!-- Header -->
        @include('layouts.manajemen.header')
        
        <!-- Page Content -->
        <div class="min-h-screen bg-gray-50">
            @yield('content')
        </div>
    </div>
    
    <!-- PERBAIKAN: Pastikan script dashboard di-load di sini -->
    @yield('js')
    
    <!-- PERBAIKAN: Script umum untuk semua halaman management -->
    <script>
        let sidebarOpen = false;
        let sidebarCollapsed = false;
        
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileOverlay");
            const content = document.querySelector('.content');
            const toggleIcon = document.getElementById('desktopToggleIcon');
            
            if (!sidebar) {
                console.error('Sidebar element not found!');
                return;
            }
            
            if (window.innerWidth < 1024) {
                // Mobile behavior - slide in/out
                sidebar.classList.toggle("-translate-x-full");
                if (overlay) {
                    overlay.classList.toggle("hidden");
                }
                sidebarOpen = !sidebar.classList.contains("-translate-x-full");
            } else {
                // Desktop behavior - collapse/expand
                sidebar.classList.toggle('collapsed');
                if (content) {
                    content.classList.toggle('sidebar-collapsed');
                }
                if (toggleIcon) {
                    toggleIcon.classList.toggle('rotated');
                }
                sidebarCollapsed = sidebar.classList.contains('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            }
        }
        
        // Restore sidebar state from localStorage
        function restoreSidebarState() {
            const sidebar = document.getElementById("sidebar");
            const content = document.querySelector('.content');
            const toggleIcon = document.getElementById('desktopToggleIcon');
            const savedState = localStorage.getItem('sidebarCollapsed');
            
            if (savedState === 'true' && window.innerWidth >= 1024) {
                if (sidebar) sidebar.classList.add('collapsed');
                if (content) content.classList.add('sidebar-collapsed');
                if (toggleIcon) toggleIcon.classList.add('rotated');
                sidebarCollapsed = true;
            }
        }
        
        // Update current date and time
        function updateDateTime() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            
            const dateTimeElement = document.getElementById('currentDateTime');
            if (dateTimeElement) {
                dateTimeElement.textContent = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}`;
            }
        }
        
        // Handle window resize
        function handleWindowResize() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileOverlay");
            const content = document.querySelector('.content');
            
            if (window.innerWidth >= 1024) {
                if (sidebar) sidebar.classList.remove("-translate-x-full");
                if (overlay) overlay.classList.add("hidden");
                sidebarOpen = false;
                // Restore collapsed state on desktop
                restoreSidebarState();
            } else {
                if (sidebar) {
                    sidebar.classList.add("-translate-x-full");
                    sidebar.classList.remove('collapsed');
                }
                if (content) content.classList.remove('sidebar-collapsed');
                if (overlay) overlay.classList.add("hidden");
                sidebarOpen = false;
                sidebarCollapsed = false;
            }
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener("click", function(e) {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileOverlay");
            const menuButtons = document.querySelectorAll('[onclick*="toggleSidebar"]');
            
            let clickedMenuButton = false;
            menuButtons.forEach((button) => {
                if (button.contains(e.target)) {
                    clickedMenuButton = true;
                }
            });
            
            if (window.innerWidth < 1024 &&
                sidebar &&
                !sidebar.contains(e.target) &&
                !clickedMenuButton &&
                !sidebar.classList.contains("-translate-x-full")) {
                sidebar.classList.add("-translate-x-full");
                if (overlay) overlay.classList.add("hidden");
                sidebarOpen = false;
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            // Update waktu setiap menit
            setInterval(updateDateTime, 60000);
            restoreSidebarState();
            
            // Add window resize listener
            window.addEventListener('resize', handleWindowResize);
            
            // Initialize handle on load
            handleWindowResize();
        });
    </script>
</body>
</html>