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