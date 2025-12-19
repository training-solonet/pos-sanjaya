<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS Sanjaya - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        body { font-family: "Inter", sans-serif; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        
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
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-title {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-user-info {
            display: none;
        }
        
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    @include('layouts.manajemen.sidebar')
    
    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>
    
    <!-- Main Content -->
    <div class="content flex-1">
        <!-- Header -->
        @include('layouts.manajemen.header')
        
        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                @yield('content')
            </div>
        </main>
    </div>
    
    @yield('js')
    
    <!-- PERBAIKAN: Tambahkan script untuk update waktu di layout utama -->
    <script>
        let sidebarOpen = false;
        let sidebarCollapsed = false;
        
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileOverlay");
            const content = document.querySelector('.content');
            const toggleIcon = document.getElementById('desktopToggleIcon');
            
            console.log('Toggle sidebar clicked, width:', window.innerWidth);
            
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
                console.log('Mobile: sidebar open:', sidebarOpen);
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
                console.log('Desktop: sidebar collapsed:', sidebarCollapsed);
                
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
                sidebar.classList.add('collapsed');
                if (content) {
                    content.classList.add('sidebar-collapsed');
                }
                if (toggleIcon) {
                    toggleIcon.classList.add('rotated');
                }
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
            const seconds = now.getSeconds().toString().padStart(2, '0');
            
            const dateTimeElement = document.getElementById('currentDateTime');
            if (dateTimeElement) {
                dateTimeElement.textContent = `${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}`;
            }
        }
        
        // Handle window resize
        window.addEventListener("resize", function() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("mobileOverlay");
            const content = document.querySelector('.content');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove("-translate-x-full");
                overlay.classList.add("hidden");
                sidebarOpen = false;
                // Restore collapsed state on desktop
                restoreSidebarState();
            } else {
                sidebar.classList.add("-translate-x-full");
                sidebar.classList.remove('collapsed');
                content.classList.remove('sidebar-collapsed');
                overlay.classList.add("hidden");
                sidebarOpen = false;
                sidebarCollapsed = false;
            }
        });
        
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
                !sidebar.contains(e.target) &&
                !clickedMenuButton &&
                !sidebar.classList.contains("-translate-x-full")) {
                sidebar.classList.add("-translate-x-full");
                overlay.classList.add("hidden");
                sidebarOpen = false;
            }
        });
        
        // Initialize datetime on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
            updateDateTime();
            setInterval(updateDateTime, 1000); // Update setiap 1 detik
            restoreSidebarState(); // Restore sidebar state
            
            // Debug: check if sidebar element exists
            const sidebar = document.getElementById("sidebar");
            console.log('Sidebar element:', sidebar);
            console.log('Initial sidebar classes:', sidebar ? sidebar.className : 'not found');
        });
    </script>
</body>
</html>