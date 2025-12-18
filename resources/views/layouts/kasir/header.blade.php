<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Mobile Menu Button -->
            <div class="flex items-center">
                <button onclick="toggleSidebar()" title="Menu"
                    class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center space-x-4">
                <!-- User Info -->
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium text-gray-900">Kasir: {{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500" id="currentDateTime"></p>
                </div>
                
                <!-- Notification Button -->
                <button class="relative w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bell text-gray-600"></i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center font-semibold">1</span>
                </button>
            </div>
        </div>
    </div>
</header>
