@extends('layouts.manajemen.index')

@section('content')
    <div class="content flex-1 lg:flex-1">
        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header Actions -->
                @php
                    $totalResep = isset($resep) ? $resep->count() : 0;
                    $activeResep = isset($resep) ? $resep->where('status','Aktif')->count() : 0;
                    $activePercent = $totalResep ? round($activeResep / $totalResep * 100) : 0;
                @endphp
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Resep</h2>
                        <p class="text-gray-600">Manajemen resep dan analisis biaya produksi</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="exportRecipes()"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 shadow-sm">
                            <i class="fas fa-download mr-2"></i>
                            Export Data
                        </button>
                        <button onclick="openAddRecipeModal()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 shadow-sm">
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
                                <p class="text-2xl font-bold text-gray-900">{{ $totalResep }}</p>
                                <p class="text-xs text-green-600">&nbsp;</p>
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
                                <p class="text-2xl font-bold text-gray-900">{{ $activeResep }}</p>
                                <p class="text-xs text-blue-600">{{ $activePercent }}% dari total</p>
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
                                <i
                                    class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="searchInput"
                                    placeholder="Cari nama resep, bahan, atau kategori..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    onkeyup="filterRecipes()">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <select id="categoryFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                onchange="filterRecipes()">
                                <option value="">Semua Kategori</option>
                                <option value="Roti & Pastry">Roti & Pastry</option>
                                <option value="Kue & Dessert">Kue & Dessert</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Makanan Utama">Makanan Utama</option>
                                <option value="Snack">Snack</option>
                            </select>
                            <select id="statusFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                onchange="filterRecipes()">
                                <option value="">Semua Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Draft">Draft</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                            <select id="costFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                onchange="filterRecipes()">
                                <option value="">Filter Biaya</option>
                                <option value="low">
                                    < Rp 10.000</option>
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
                                <button onclick="toggleView('table')" id="tableViewBtn"
                                    class="p-2 text-gray-500 hover:text-gray-700 bg-gray-100 rounded-lg">
                                    <i class="fas fa-table"></i>
                                </button>
                                <button onclick="toggleView('grid')" id="gridViewBtn"
                                    class="p-2 text-gray-500 hover:text-gray-700 rounded-lg">
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
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Resep</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Porsi</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Food Cost</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga Jual</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Margin</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
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
@endsection
<!-- Add Recipe Modal -->
<div id="addRecipeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Buat Resep Baru</h3>
            <button onclick="closeAddRecipeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="recipeForm" class="px-6 py-6 overflow-auto" style="max-height:calc(90vh - 120px);">
            <!-- Informasi Dasar -->
            <div class="bg-white rounded-lg p-4 mb-4 border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-lg font-semibold text-gray-800">Informasi Dasar</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Resep</label>
                        <input id="recipeName" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: Croissant Coklat Premium">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select id="recipeCategory" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Pilih Kategori</option>
                            <option value="Roti & Pastry">Roti & Pastry</option>
                            <option value="Kue & Dessert">Kue & Dessert</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Makanan Utama">Makanan Utama</option>
                            <option value="Snack">Snack</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Porsi/Yield</label>
                        <input id="recipeYield" type="number" min="1" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg" oninput="calculateTotalCost()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Pembuatan</label>
                        <input id="recipeDuration" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="90 menit">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="recipeStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="Aktif">Aktif</option>
                            <option value="Draft">Draft (Belum Final)</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <!-- target price is displayed in totals section to match design -->
                </div>
            </div>

            <!-- Bahan & Kalkulasi Biaya -->
            <div class="bg-blue-50 rounded-lg p-4 mb-4 border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-semibold text-gray-800">Bahan & Kalkulasi Biaya</h4>
                    <button type="button" onclick="addIngredient()" class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">+ Tambah Bahan</button>
                </div>
                <div id="ingredientsList" class="bg-white rounded-lg p-3 border border-gray-100">
                    <!-- Ingredient rows inserted here -->
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="text-xs text-green-700">Total Food Cost</div>
                        <div id="totalFoodCost" class="font-bold text-green-900">Rp 0</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="text-xs text-green-700">Cost per Porsi</div>
                        <div id="costPerPortion" class="font-bold text-green-900">Rp 0</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-green-700">Harga Jual Target</div>
                            <input id="targetPrice" type="number" min="0" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" value="35000" oninput="calculateMargin()">
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-green-700">Margin Profit</div>
                            <div id="profitMargin" class="font-bold text-green-900">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instruksi & Catatan -->
            <div class="bg-yellow-50 rounded-lg p-4 mb-4 border border-yellow-100">
                <h4 class="text-lg font-semibold text-gray-800 mb-3">Instruksi & Catatan</h4>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Langkah Pembuatan</label>
                    <textarea id="recipeInstructions" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Chef</label>
                        <textarea id="recipeNotes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Tips & trik khusus..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="recipeStatusDuplicate" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="Aktif">Aktif</option>
                            <option value="Draft">Draft (Belum Final)</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-3 justify-end border-t pt-4 sticky bottom-0 bg-white z-10">
                <button type="button" onclick="closeAddRecipeModal()" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                <button type="button" onclick="saveRecipe()" class="px-4 py-2 bg-green-600 text-white rounded-lg">Simpan Resep</button>
            </div>
        </form>
    </div>
</div>
<script>
    // Recipes loaded from database (prepared in controller)
    let recipes = @json($recipes ?? []);

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
            tbody.innerHTML =
                '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada resep yang ditemukan</td></tr>';
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
            grid.innerHTML =
                '<div class="col-span-full text-center py-8 text-gray-500">Tidak ada resep yang ditemukan</div>';
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
        switch (status) {
            case 'Aktif':
                return 'bg-green-100 text-green-800';
            case 'Draft':
                return 'bg-yellow-100 text-yellow-800';
            case 'Nonaktif':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
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
                switch (costFilter) {
                    case 'low':
                        matchesCost = recipe.foodCost < 10000;
                        break;
                    case 'medium':
                        matchesCost = recipe.foodCost >= 10000 && recipe.foodCost <= 25000;
                        break;
                    case 'high':
                        matchesCost = recipe.foodCost > 25000;
                        break;
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
        // reset form and ingredient rows before showing
        const form = document.getElementById('recipeForm');
        if (form) form.reset();
        resetIngredientsForm();
        resetCostCalculation();
        // dim sidebar and show modal
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.add('opacity-40', 'pointer-events-none');
        }
        const modal = document.getElementById('addRecipeModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeAddRecipeModal() {
        const modal = document.getElementById('addRecipeModal');
        if (modal) modal.classList.add('hidden');
        // restore sidebar
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.remove('opacity-40', 'pointer-events-none');
        }
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
        newIngredient.className =
        'ingredient-item grid grid-cols-1 md:grid-cols-6 gap-3 p-3 bg-white rounded-lg border';
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
                ingredients.push({
                    name,
                    quantity,
                    unit,
                    price,
                    subtotal
                });
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
