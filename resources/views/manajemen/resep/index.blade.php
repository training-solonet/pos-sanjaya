@extends('layouts.manajemen.header')
@section('title', 'Resep')

@section('content')

@endsection


{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Resep - POS Sanjaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
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
    
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:relative lg:flex-shrink-0">
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cash-register text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Sanjaya Bakery</h1>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route("manajemen") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-home text-gray-400 group-hover:text-green-600 mr-3"></i>
                Dashboard Manajemen
            </a>
            <a href="{{ route("manajemen_jurnal") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-book text-gray-400 group-hover:text-green-600 mr-3"></i>
                Jurnal Harian
            </a>
            <a href="{{ route("manajemen_bahanbaku") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-boxes text-gray-400 group-hover:text-green-600 mr-3"></i>
                Stok Bahan Baku
            </a>
            <a href="{{ route("manajemen_produk") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-cookie-bite text-gray-400 group-hover:text-green-600 mr-3"></i>
                Stok Produk
            </a>
            <a href="{{ route("manajemen_konversi") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-exchange-alt text-gray-400 group-hover:text-green-600 mr-3"></i>
                Konversi Satuan
            </a>
            <a href="{{ route("manajemen_produk") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-700 rounded-lg">
                <i class="fas fa-utensils text-white mr-3"></i>
                Resep & Produksi
            </a>
            <a href="{{ route("manajemen_laporan") }}" class="nav-item group flex items-center px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="fas fa-chart-line text-gray-400 group-hover:text-green-600 mr-3"></i>
                Laporan
            </a>
        </nav>

        <!-- User Profile -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">Admin</p>
                    <p class="text-xs text-gray-500">Manager</p>
                </div>
                <a href="../index.html" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200">
                    <i class="fas fa-sign-out-alt text-gray-600 text-sm"></i>
                </a>
            </div>
        </div>
    </div>

  <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>


    <!-- Main Content -->
    <div class="content flex-1 lg:flex-1">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Mobile Menu Button & Page Title -->
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-medium text-gray-900">Manager: Admin</p>
                            <p class="text-xs text-gray-500" id="currentDateTime"></p>
                        </div>
                        <button class="relative w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                            <i class="fas fa-bell text-gray-600"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Resep</h2>
                        <p class="text-gray-600">Manajemen resep dan analisis biaya produksi</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="exportRecipes()" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 shadow-sm">
                            <i class="fas fa-download mr-2"></i>
                            Export Data
                        </button>
                        <button onclick="openAddRecipeModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-green-600 hover:to-green-700 shadow-sm">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Resep Baru
                        </button>
                    </div>
                </div>

                <!-- Analytics Dashboard -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Resep</p>
                                <p class="text-2xl font-bold text-gray-900">24</p>
                                <p class="text-xs text-green-600">+3 bulan ini</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-utensils text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Resep Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">18</p>
                                <p class="text-xs text-blue-600">75% dari total</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Panel -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="searchInput" placeholder="Cari nama resep, bahan, atau kategori..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onkeyup="filterRecipes()">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="filterRecipes()">
                                <option value="">Semua Kategori</option>
                                <option value="Roti & Pastry">Roti & Pastry</option>
                                <option value="Kue & Dessert">Kue & Dessert</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Makanan Utama">Makanan Utama</option>
                                <option value="Snack">Snack</option>
                            </select>
                            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="filterRecipes()">
                                <option value="">Semua Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Draft">Draft</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                            <select id="costFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="filterRecipes()">
                                <option value="">Filter Biaya</option>
                                <option value="low">< Rp 10.000</option>
                                <option value="medium">Rp 10.000 - 25.000</option>
                                <option value="high">> Rp 25.000</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Recipe Table -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Resep</h3>
                            <div class="flex items-center space-x-3">
                                <button onclick="toggleView('table')" id="tableViewBtn" class="p-2 text-gray-500 hover:text-gray-700 bg-gray-100 rounded-lg">
                                    <i class="fas fa-table"></i>
                                </button>
                                <button onclick="toggleView('grid')" id="gridViewBtn" class="p-2 text-gray-500 hover:text-gray-700 rounded-lg">
                                    <i class="fas fa-th-large"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resep</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Food Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="recipeTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Table rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Grid View -->
                    <div id="gridView" class="hidden p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="recipeGrid">
                            <!-- Grid cards will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Recipe Modal -->
    <div id="addRecipeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Buat Resep Baru</h3>
                    <button onclick="closeAddRecipeModal()" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form id="recipeForm" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Resep *</label>
                                    <input type="text" id="recipeName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Contoh: Croissant Coklat Premium">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                                    <select id="recipeCategory" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                        <option value="">Pilih Kategori</option>
                                        <option value="Roti & Pastry">Roti & Pastry</option>
                                        <option value="Kue & Dessert">Kue & Dessert</option>
                                        <option value="Minuman">Minuman</option>
                                        <option value="Makanan Utama">Makanan Utama</option>
                                        <option value="Snack">Snack</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Porsi/Yield *</label>
                                    <input type="number" id="recipeYield" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="12">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Pembuatan</label>
                                    <input type="text" id="recipeDuration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="90 menit">
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients & Costing -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">Bahan & Kalkulasi Biaya</h4>
                                <button type="button" onclick="addIngredient()" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                    <i class="fas fa-plus mr-1"></i>Tambah Bahan
                                </button>
                            </div>
                            <div id="ingredientsList" class="space-y-3 mb-4">
                                <div class="ingredient-item grid grid-cols-1 md:grid-cols-6 gap-3 p-3 bg-white rounded-lg border">
                                    <input type="text" placeholder="Nama bahan" class="ingredient-name px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                                    <select class="ingredient-unit px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                        <option value="gram">gram</option>
                                        <option value="kg">kg</option>
                                        <option value="ml">ml</option>
                                        <option value="liter">liter</option>
                                        <option value="pcs">pcs</option>
                                        <option value="sdm">sdm</option>
                                        <option value="sdt">sdt</option>
                                    </select>
                                    <input type="number" step="0.01" placeholder="Harga/unit" class="ingredient-price px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                                    <input type="number" step="0.01" placeholder="Subtotal" class="ingredient-subtotal px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                                    <button type="button" onclick="removeIngredient(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Cost Summary -->
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                                    <div>
                                        <div class="text-sm text-gray-600">Total Food Cost</div>
                                        <div class="text-lg font-bold text-gray-900" id="totalFoodCost">Rp 0</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Cost per Porsi</div>
                                        <div class="text-lg font-bold text-gray-900" id="costPerPortion">Rp 0</div>
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600 block">Harga Jual Target</label>
                                        <input type="number" id="targetPrice" class="text-lg font-bold text-center border border-gray-300 rounded-lg px-2 py-1" oninput="calculateMargin()" placeholder="35000">
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Margin Profit</div>
                                        <div class="text-lg font-bold" id="profitMargin">0%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions & Notes -->
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Instruksi & Catatan</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Langkah Pembuatan</label>
                                    <textarea id="recipeInstructions" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="1. Persiapkan semua bahan...&#10;2. Campurkan tepung dengan...&#10;3. Uleni adonan hingga..."></textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Chef</label>
                                        <textarea id="recipeNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Tips & trik khusus..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select id="recipeStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="Draft">Draft (Belum Final)</option>
                                            <option value="Aktif">Aktif (Siap Produksi)</option>
                                            <option value="Nonaktif">Nonaktif (Tidak Digunakan)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-between p-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pastikan semua kalkulasi biaya sudah benar sebelum menyimpan
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="closeAddRecipeModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button onclick="saveRecipe()" class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700">
                            <i class="fas fa-save mr-2"></i>Simpan Resep
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipe Detail Modal -->
    <div id="recipeDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto">
                <!-- Modal content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        let recipes = [
            {
                id: 1,
                name: "Croissant Coklat Premium",
                category: "Roti & Pastry",
                yield: 12,
                duration: "180 menit",
                foodCost: 18500,
                sellingPrice: 65000,
                margin: 71.5,
                status: "Aktif",
                ingredients: [
                    { name: "Tepung Terigu Protein Tinggi", quantity: 500, unit: "gram", price: 15, subtotal: 7500 },
                    { name: "Mentega Tawar Premium", quantity: 300, unit: "gram", price: 35, subtotal: 10500 },
                    { name: "Dark Chocolate Chips", quantity: 150, unit: "gram", price: 65, subtotal: 9750 },
                    { name: "Telur Segar Grade A", quantity: 2, unit: "pcs", price: 2500, subtotal: 5000 },
                    { name: "Susu Murni", quantity: 250, unit: "ml", price: 18, subtotal: 4500 }
                ],
                instructions: "1. Siapkan adonan dasar dengan tepung, telur, dan susu\n2. Laminasi dengan mentega cold method\n3. Roll dan lipat 3 kali dengan jeda 30 menit\n4. Bentuk croissant dan isi coklat\n5. Proofing 2 jam suhu ruang\n6. Panggang 175°C selama 25 menit",
                notes: "Mentega harus benar-benar dingin saat laminasi. Jangan overproof agar tetap flaky."
            },
            {
                id: 2,
                name: "Brownies Fudgy Deluxe",
                category: "Kue & Dessert",
                yield: 16,
                duration: "90 menit",
                foodCost: 22000,
                sellingPrice: 75000,
                margin: 70.7,
                status: "Aktif",
                ingredients: [
                    { name: "Dark Chocolate 70%", quantity: 400, unit: "gram", price: 85, subtotal: 34000 },
                    { name: "Mentega Unsalted", quantity: 200, unit: "gram", price: 35, subtotal: 7000 },
                    { name: "Gula Castor", quantity: 250, unit: "gram", price: 12, subtotal: 3000 },
                    { name: "Telur Grade A", quantity: 4, unit: "pcs", price: 2500, subtotal: 10000 },
                    { name: "Tepung Terigu", quantity: 100, unit: "gram", price: 15, subtotal: 1500 }
                ],
                instructions: "1. Lelehkan coklat dan mentega dengan double boiler\n2. Kocok telur dan gula hingga fluffy\n3. Campurkan coklat leleh ke adonan telur\n4. Masukkan tepung, aduk rata\n5. Tuang ke loyang 20x20 cm\n6. Panggang 160°C selama 35-40 menit",
                notes: "Jangan overbake agar tetap fudgy. Test dengan tusuk gigi, masih sedikit basah OK."
            },
            {
                id: 3,
                name: "Signature Coffee Latte",
                category: "Minuman",
                yield: 1,
                duration: "5 menit",
                foodCost: 8500,
                sellingPrice: 28000,
                margin: 69.6,
                status: "Aktif",
                ingredients: [
                    { name: "Espresso Bean Premium", quantity: 18, unit: "gram", price: 150, subtotal: 2700 },
                    { name: "Susu Full Cream", quantity: 200, unit: "ml", price: 18, subtotal: 3600 },
                    { name: "Vanilla Syrup", quantity: 15, unit: "ml", price: 80, subtotal: 1200 },
                    { name: "Cup Paper 12oz", quantity: 1, unit: "pcs", price: 1000, subtotal: 1000 }
                ],
                instructions: "1. Extract espresso double shot (36ml dalam 25-30 detik)\n2. Steam susu hingga 65°C dengan microfoam halus\n3. Tuang vanilla syrup ke cup\n4. Tuang espresso\n5. Pour latte art dengan susu",
                notes: "Susu jangan terlalu panas. Microfoam harus halus untuk latte art yang bagus."
            }
        ];

        let currentView = 'table';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            if (window.innerWidth < 1024) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }

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

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000); // Update every minute
            renderTableView(); // Show all recipes by default
        });

        function toggleView(view) {
            currentView = view;
            const tableView = document.getElementById('tableView');
            const gridView = document.getElementById('gridView');
            const tableBtn = document.getElementById('tableViewBtn');
            const gridBtn = document.getElementById('gridViewBtn');

            if (view === 'table') {
                tableView.classList.remove('hidden');
                gridView.classList.add('hidden');
                tableBtn.classList.add('bg-gray-100');
                gridBtn.classList.remove('bg-gray-100');
                renderTableView();
            } else {
                tableView.classList.add('hidden');
                gridView.classList.remove('hidden');
                gridBtn.classList.add('bg-gray-100');
                tableBtn.classList.remove('bg-gray-100');
                renderGridView();
            }
        }

        function renderTableView(recipesToRender = recipes) {
            const tbody = document.getElementById('recipeTableBody');
            
            if (recipesToRender.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada resep yang ditemukan</td></tr>';
                return;
            }

            tbody.innerHTML = recipesToRender.map(recipe => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r ${getCategoryGradient(recipe.category)} rounded-lg flex items-center justify-center mr-3">
                                <i class="fas ${getCategoryIcon(recipe.category)} text-white text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">${recipe.name}</div>
                                <div class="text-xs text-gray-500">${recipe.yield} porsi • ${recipe.duration}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">${recipe.category}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${recipe.yield}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp ${recipe.foodCost.toLocaleString('id-ID')}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp ${recipe.sellingPrice.toLocaleString('id-ID')}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 text-xs font-medium ${getMarginColor(recipe.margin)} rounded-full">${recipe.margin}%</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 text-xs font-medium ${getStatusColor(recipe.status)} rounded-full">${recipe.status}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <button onclick="viewRecipe(${recipe.id})" class="text-blue-600 hover:text-blue-700" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editRecipe(${recipe.id})" class="text-green-600 hover:text-green-700" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="duplicateRecipe(${recipe.id})" class="text-purple-600 hover:text-purple-700" title="Duplikat">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button onclick="deleteRecipe(${recipe.id})" class="text-red-600 hover:text-red-700" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function renderGridView(recipesToRender = recipes) {
            const grid = document.getElementById('recipeGrid');
            
            if (recipesToRender.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">Tidak ada resep yang ditemukan</div>';
                return;
            }

            grid.innerHTML = recipesToRender.map(recipe => `
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r ${getCategoryGradient(recipe.category)} rounded-lg flex items-center justify-center">
                                    <i class="fas ${getCategoryIcon(recipe.category)} text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">${recipe.name}</h3>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">${recipe.category}</span>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium ${getStatusColor(recipe.status)} rounded-full">${recipe.status}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-gray-600 text-xs">Food Cost</div>
                                <div class="font-bold text-gray-900">Rp ${recipe.foodCost.toLocaleString('id-ID')}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-gray-600 text-xs">Harga Jual</div>
                                <div class="font-bold text-gray-900">Rp ${recipe.sellingPrice.toLocaleString('id-ID')}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-gray-600 text-xs">Porsi</div>
                                <div class="font-bold text-gray-900">${recipe.yield}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-gray-600 text-xs">Margin</div>
                                <div class="font-bold ${recipe.margin >= 70 ? 'text-green-600' : recipe.margin >= 50 ? 'text-yellow-600' : 'text-red-600'}">${recipe.margin}%</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <button onclick="viewRecipe(${recipe.id})" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail Resep
                            </button>
                            <div class="flex items-center space-x-2">
                                <button onclick="editRecipe(${recipe.id})" class="text-blue-600 hover:text-blue-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="duplicateRecipe(${recipe.id})" class="text-purple-600 hover:text-purple-700" title="Duplikat">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button onclick="deleteRecipe(${recipe.id})" class="text-red-600 hover:text-red-700" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getCategoryGradient(category) {
            const gradients = {
                'Roti & Pastry': 'from-orange-400 to-orange-600',
                'Kue & Dessert': 'from-pink-400 to-pink-600',
                'Minuman': 'from-blue-400 to-blue-600',
                'Makanan Utama': 'from-green-400 to-green-600',
                'Snack': 'from-purple-400 to-purple-600'
            };
            return gradients[category] || 'from-gray-400 to-gray-600';
        }

        function getCategoryIcon(category) {
            const icons = {
                'Roti & Pastry': 'fa-bread-slice',
                'Kue & Dessert': 'fa-birthday-cake',
                'Minuman': 'fa-mug-hot',
                'Makanan Utama': 'fa-utensils',
                'Snack': 'fa-cookie-bite'
            };
            return icons[category] || 'fa-utensils';
        }

        function getStatusColor(status) {
            switch(status) {
                case 'Aktif': return 'bg-green-100 text-green-800';
                case 'Draft': return 'bg-yellow-100 text-yellow-800';
                case 'Nonaktif': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function getMarginColor(margin) {
            if (margin >= 70) return 'bg-green-100 text-green-800';
            if (margin >= 50) return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        }

        function filterRecipes() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const costFilter = document.getElementById('costFilter').value;

            const filtered = recipes.filter(recipe => {
                const matchesSearch = recipe.name.toLowerCase().includes(searchTerm) || 
                                    recipe.ingredients.some(ing => ing.name.toLowerCase().includes(searchTerm));
                const matchesCategory = !categoryFilter || recipe.category === categoryFilter;
                const matchesStatus = !statusFilter || recipe.status === statusFilter;
                
                let matchesCost = true;
                if (costFilter) {
                    switch(costFilter) {
                        case 'low': matchesCost = recipe.foodCost < 10000; break;
                        case 'medium': matchesCost = recipe.foodCost >= 10000 && recipe.foodCost <= 25000; break;
                        case 'high': matchesCost = recipe.foodCost > 25000; break;
                    }
                }
                
                return matchesSearch && matchesCategory && matchesStatus && matchesCost;
            });

            if (currentView === 'table') {
                renderTableView(filtered);
            } else {
                renderGridView(filtered);
            }
        }

        function openAddRecipeModal() {
            document.getElementById('addRecipeModal').classList.remove('hidden');
        }

        function closeAddRecipeModal() {
            document.getElementById('addRecipeModal').classList.add('hidden');
            document.getElementById('recipeForm').reset();
            resetIngredientsForm();
            resetCostCalculation();
        }

        function resetIngredientsForm() {
            document.getElementById('ingredientsList').innerHTML = `
                <div class="ingredient-item grid grid-cols-1 md:grid-cols-6 gap-3 p-3 bg-white rounded-lg border">
                    <input type="text" placeholder="Nama bahan" class="ingredient-name px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                    <select class="ingredient-unit px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="gram">gram</option>
                        <option value="kg">kg</option>
                        <option value="ml">ml</option>
                        <option value="liter">liter</option>
                        <option value="pcs">pcs</option>
                        <option value="sdm">sdm</option>
                        <option value="sdt">sdt</option>
                    </select>
                    <input type="number" step="0.01" placeholder="Harga/unit" class="ingredient-price px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                    <input type="number" step="0.01" placeholder="Subtotal" class="ingredient-subtotal px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                    <button type="button" onclick="removeIngredient(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        }

        function resetCostCalculation() {
            document.getElementById('totalFoodCost').textContent = 'Rp 0';
            document.getElementById('costPerPortion').textContent = 'Rp 0';
            document.getElementById('targetPrice').value = '';
            document.getElementById('profitMargin').textContent = '0%';
            document.getElementById('profitMargin').className = 'text-lg font-bold text-gray-900';
        }

        function addIngredient() {
            const ingredientsList = document.getElementById('ingredientsList');
            const newIngredient = document.createElement('div');
            newIngredient.className = 'ingredient-item grid grid-cols-1 md:grid-cols-6 gap-3 p-3 bg-white rounded-lg border';
            newIngredient.innerHTML = `
                <input type="text" placeholder="Nama bahan" class="ingredient-name px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                <select class="ingredient-unit px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="ml">ml</option>
                    <option value="liter">liter</option>
                    <option value="pcs">pcs</option>
                    <option value="sdm">sdm</option>
                    <option value="sdt">sdt</option>
                </select>
                <input type="number" step="0.01" placeholder="Harga/unit" class="ingredient-price px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                <input type="number" step="0.01" placeholder="Subtotal" class="ingredient-subtotal px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                <button type="button" onclick="removeIngredient(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            ingredientsList.appendChild(newIngredient);
        }

        function removeIngredient(button) {
            button.parentElement.remove();
            calculateTotalCost();
        }

        function calculateIngredientCost(input) {
            const row = input.closest('.ingredient-item');
            const quantity = parseFloat(row.querySelector('.ingredient-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.ingredient-price').value) || 0;
            const subtotal = quantity * price;
            
            row.querySelector('.ingredient-subtotal').value = subtotal.toFixed(0);
            calculateTotalCost();
        }

        function calculateTotalCost() {
            const subtotals = document.querySelectorAll('.ingredient-subtotal');
            let total = 0;
            
            subtotals.forEach(subtotal => {
                total += parseFloat(subtotal.value) || 0;
            });

            document.getElementById('totalFoodCost').textContent = `Rp ${total.toLocaleString('id-ID')}`;
            
            const yield = parseInt(document.getElementById('recipeYield').value) || 1;
            const costPerPortion = total / yield;
            document.getElementById('costPerPortion').textContent = `Rp ${costPerPortion.toLocaleString('id-ID')}`;
            
            calculateMargin();
        }

        function calculateMargin() {
            const totalCost = parseFloat(document.getElementById('totalFoodCost').textContent.replace(/[^0-9]/g, '')) || 0;
            const targetPrice = parseFloat(document.getElementById('targetPrice').value) || 0;
            
            if (targetPrice > 0 && totalCost > 0) {
                const margin = ((targetPrice - totalCost) / targetPrice * 100).toFixed(1);
                const marginElement = document.getElementById('profitMargin');
                marginElement.textContent = `${margin}%`;
                
                if (margin >= 70) {
                    marginElement.className = 'text-lg font-bold text-green-600';
                } else if (margin >= 50) {
                    marginElement.className = 'text-lg font-bold text-yellow-600';
                } else {
                    marginElement.className = 'text-lg font-bold text-red-600';
                }
            } else {
                document.getElementById('profitMargin').textContent = '0%';
                document.getElementById('profitMargin').className = 'text-lg font-bold text-gray-900';
            }
        }

        function saveRecipe() {
            const form = document.getElementById('recipeForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Collect ingredients
            const ingredients = [];
            document.querySelectorAll('.ingredient-item').forEach(item => {
                const name = item.querySelector('.ingredient-name').value;
                const quantity = parseFloat(item.querySelector('.ingredient-quantity').value);
                const unit = item.querySelector('.ingredient-unit').value;
                const price = parseFloat(item.querySelector('.ingredient-price').value);
                const subtotal = parseFloat(item.querySelector('.ingredient-subtotal').value);
                
                if (name && quantity && price) {
                    ingredients.push({ name, quantity, unit, price, subtotal });
                }
            });

            if (ingredients.length === 0) {
                alert('Minimal harus ada 1 bahan baku dengan harga!');
                return;
            }

            const totalCost = parseFloat(document.getElementById('totalFoodCost').textContent.replace(/[^0-9]/g, '')) || 0;
            const targetPrice = parseFloat(document.getElementById('targetPrice').value) || 0;
            const margin = targetPrice > 0 ? ((targetPrice - totalCost) / targetPrice * 100) : 0;

            const newRecipe = {
                id: recipes.length + 1,
                name: document.getElementById('recipeName').value,
                category: document.getElementById('recipeCategory').value,
                yield: parseInt(document.getElementById('recipeYield').value),
                duration: document.getElementById('recipeDuration').value,
                foodCost: totalCost,
                sellingPrice: targetPrice,
                margin: parseFloat(margin.toFixed(1)),
                status: document.getElementById('recipeStatus').value,
                ingredients: ingredients,
                instructions: document.getElementById('recipeInstructions').value,
                notes: document.getElementById('recipeNotes').value
            };

            recipes.push(newRecipe);
            
            // Re-render current view
            if (currentView === 'table') {
                renderTableView();
            } else {
                renderGridView();
            }
            
            closeAddRecipeModal();
            
            // Show success message
            alert('Resep berhasil disimpan dengan kalkulasi biaya!');
        }
    </script>
</body>
</html> --}}