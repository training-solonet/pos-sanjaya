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
                                <p id="totalRecipesCount" class="text-2xl font-bold text-gray-900">{{ $totalResep }}</p>
                                <p id="totalRecipesNote" class="text-xs text-green-600">&nbsp;</p>
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
                                <p id="activeRecipesCount" class="text-2xl font-bold text-gray-900">{{ $activeResep }}</p>
                                <p class="text-xs text-blue-600"><span id="activeRecipesPercent">{{ $activePercent }}%</span> dari total</p>
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
                                <option value="Roti dan Pastry">Roti dan Pastry</option>
                                <option value="Kue dan Dessert">Kue dan Dessert</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Makanan">Makanan</option>
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
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-lg max-h-[90vh] overflow-hidden">
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
                            <option value="Roti dan Pastry">Roti dan Pastry</option>
                            <option value="Kue dan Dessert">Kue dan Dessert</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Makanan">Makanan</option>
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
                </div>
            </div>
                <style>
                    /* Tweak ingredient stock text and subtotal appearance inside modal */
                    #addRecipeModal .ingredient-stock { font-size: 0.75rem; color: #6B7280; }
                    #addRecipeModal .ingredient-subtotal { background-color: #F3F4F6; }
                    /* Suggestion dropdown for bahan name */
                    .bahan-suggestions { font-size: 0.9rem; }
                    .bahan-suggestions .item { padding: 0.5rem 0.75rem; cursor: pointer; }
                    .bahan-suggestions .item:hover { background-color: #F3F4F6; }
                    /* Make ingredient input look more like select (white, border) */
                    .ingredient-name { background-color: #ffffff; }
                    .ingredient-name:focus { box-shadow: 0 0 0 3px rgba(34,197,94,0.12); }
                    /* ensure suggestion list text is dark on white */
                    .bahan-suggestions { background: #fff; color: #111827; }
                    /* increase gap and vertically center fields */
                    .ingredient-item { align-items: center; }
                    /* Force unit select to be compact */
                    .ingredient-unit { max-width: 84px; }
                    /* Make grid cards a consistent minimum height for a tidier layout */
                    #recipeGrid .recipe-card { min-height: 220px; }
                    /* Slightly increase modal responsiveness on very wide screens */
                    @media (min-width: 1400px) {
                        #addRecipeModal > div { max-width: 80rem; }
                    }
                </style>

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
                            <input id="targetPrice" type="number" min="0" class="mt-1 w-28 px-2 py-1 border border-gray-300 rounded-lg bg-white" value="35000" oninput="calculateMargin()">
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
    // Bahan baku list (id, nama, stok)
    let bahanList = @json($bahans ?? []);

    // Render datalist for bahan names
    (function renderBahanDatalist() {
        try {
            const container = document.createElement('div');
            container.style.display = 'none';
            const dl = document.createElement('datalist');
            dl.id = 'bahanList';
            (bahanList || []).forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.nama;
                opt.setAttribute('data-stok', b.stok);
                container.appendChild(opt);
                dl.appendChild(opt);
            });
            container.appendChild(dl);
            document.body.appendChild(container);
        } catch (e) {
            console.error('Failed to render bahan datalist', e);
        }
    })();

    // Create option tags for bahan select (used in ingredient rows)
    function createBahanOptions() {
        try {
            return (bahanList || []).map(b => `<option value="${b.id}" data-stok="${b.stok}">${b.nama}</option>`).join('');
        } catch (e) {
            console.error('createBahanOptions error', e);
            return '';
        }
    }

    let currentView = 'table';
    let editingRecipeId = null;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');

        if (window.innerWidth < 1024) {
            if (sidebar && sidebar.classList) sidebar.classList.toggle('-translate-x-full');
            if (overlay && overlay.classList) overlay.classList.toggle('hidden');
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
        updateStats();
        // keep in sync with DB every 60s
        setInterval(refreshRecipes, 60000);
    });

    function toggleView(view) {
        currentView = view;
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const tableBtn = document.getElementById('tableViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');

        if (view === 'table') {
            if (tableView && tableView.classList) tableView.classList.remove('hidden');
            if (gridView && gridView.classList) gridView.classList.add('hidden');
            if (tableBtn && tableBtn.classList) tableBtn.classList.add('bg-gray-100');
            if (gridBtn && gridBtn.classList) gridBtn.classList.remove('bg-gray-100');
            renderTableView();
        } else {
            if (tableView && tableView.classList) tableView.classList.add('hidden');
            if (gridView && gridView.classList) gridView.classList.remove('hidden');
            if (gridBtn && gridBtn.classList) gridBtn.classList.add('bg-gray-100');
            if (tableBtn && tableBtn.classList) tableBtn.classList.remove('bg-gray-100');
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
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp ${((Number(recipe.foodCost) || 0)).toLocaleString('id-ID')}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp ${((Number(recipe.sellingPrice) || 0)).toLocaleString('id-ID')}</td>
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
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300 recipe-card">
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
                                <div class="font-bold text-gray-900">Rp ${((Number(recipe.foodCost) || 0)).toLocaleString('id-ID')}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-gray-600 text-xs">Harga Jual</div>
                                <div class="font-bold text-gray-900">Rp ${((Number(recipe.sellingPrice) || 0)).toLocaleString('id-ID')}</div>
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
            'Roti dan Pastry': 'from-orange-400 to-orange-600',
            'Kue dan Dessert': 'from-pink-400 to-pink-600',
            'Minuman': 'from-blue-400 to-blue-600',
            'Makanan': 'from-green-400 to-green-600',
            'Snack': 'from-purple-400 to-purple-600'
        };
        return gradients[category] || 'from-gray-400 to-gray-600';
    }

    function getCategoryIcon(category) {
        const icons = {
            'Roti dan Pastry': 'fa-bread-slice',
            'Kue dan Dessert': 'fa-birthday-cake',
            'Minuman': 'fa-mug-hot',
            'Makanan': 'fa-utensils',
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
        editingRecipeId = null;
        // dim sidebar and show modal
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList) {
            sidebar.classList.add('opacity-40', 'pointer-events-none');
        }
        const modal = document.getElementById('addRecipeModal');
        if (modal && modal.classList) modal.classList.remove('hidden');
    }

    function closeAddRecipeModal() {
        const modal = document.getElementById('addRecipeModal');
        if (modal && modal.classList) modal.classList.add('hidden');
        // restore sidebar
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList) {
            sidebar.classList.remove('opacity-40', 'pointer-events-none');
        }
        document.getElementById('recipeForm').reset();
        resetIngredientsForm();
        resetCostCalculation();
        editingRecipeId = null;
    }

    function resetIngredientsForm() {
        document.getElementById('ingredientsList').innerHTML = `
                <div class="ingredient-item grid grid-cols-1 md:grid-cols-12 gap-6 p-3 bg-white rounded-lg border">
                    <div class="relative md:col-span-5">
                        <select class="ingredient-name w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="onBahanSelected(this)">
                            <option value="">Pilih bahan...</option>
                            ${createBahanOptions()}
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                        <div class="ingredient-stock text-xs text-gray-500 mt-1">Stok: -</div>
                    </div>
                    <select class="ingredient-unit md:col-span-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="gram">gram</option>
                        <option value="kg">kg</option>
                        <option value="ml">ml</option>
                        <option value="liter">liter</option>
                        <option value="pcs">pcs</option>
                        <option value="sdm">sdm</option>
                        <option value="sdt">sdt</option>
                    </select>
                    <input type="number" step="0.01" placeholder="Harga/unit" class="ingredient-price md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                    <input type="number" step="0.01" placeholder="Subtotal" class="ingredient-subtotal md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                    <button type="button" onclick="removeIngredient(this)" class="md:col-span-1 flex items-center justify-center px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
    }

    // --- CRUD helpers ---
    function populateIngredientsFromRecipe(recipe) {
        const list = document.getElementById('ingredientsList');
        list.innerHTML = '';
        (recipe.ingredients || []).forEach(ing => {
            const newIngredient = document.createElement('div');
            newIngredient.className = 'ingredient-item grid grid-cols-1 md:grid-cols-12 gap-6 p-3 bg-white rounded-lg border items-center';
            newIngredient.innerHTML = `
                <div class="relative md:col-span-5">
                    <select class="ingredient-name w-full px-3 py-2 border border-gray-300 rounded-lg" onchange="onBahanSelected(this)">
                        <option value="">Pilih bahan...</option>
                        ${createBahanOptions()}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" oninput="calculateIngredientCost(this)" value="${ing.quantity}">
                    <div class="ingredient-stock text-xs text-gray-500 mt-1">Stok: -</div>
                </div>
                <select class="ingredient-unit md:col-span-1 w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="ml">ml</option>
                    <option value="liter">liter</option>
                    <option value="pcs">pcs</option>
                    <option value="sdm">sdm</option>
                    <option value="sdt">sdt</option>
                </select>
                <input type="number" step="0.01" placeholder="Harga/unit" class="ingredient-price md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg" oninput="calculateIngredientCost(this)" value="${ing.price}">
                <input type="number" step="0.01" placeholder="Subtotal" class="ingredient-subtotal md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly value="${ing.subtotal}">
                <button type="button" onclick="removeIngredient(this)" class="md:col-span-1 flex items-center justify-center px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            const sel = newIngredient.querySelector('.ingredient-unit');
            if (sel) sel.value = ing.unit || 'gram';
            // set bahan selection and apply stock limits (match by name -> id)
            const nameSelect = newIngredient.querySelector('.ingredient-name');
            if (nameSelect) {
                const found = (bahanList || []).find(b => b.nama === (ing.name || ''));
                if (found) {
                    nameSelect.value = found.id;
                    onBahanSelected(nameSelect);
                } else {
                    nameSelect.value = '';
                }
            }
            list.appendChild(newIngredient);
        });
        calculateTotalCost();
    }

    function editRecipe(id) {
        const recipe = recipes.find(r => parseInt(r.id) === parseInt(id));
        if (!recipe) return alert('Resep tidak ditemukan');
        editingRecipeId = id;
        document.getElementById('recipeName').value = recipe.name || '';
        document.getElementById('recipeCategory').value = recipe.category || '';
        document.getElementById('recipeYield').value = recipe.yield || 1;
        document.getElementById('recipeDuration').value = recipe.duration || '';
        document.getElementById('recipeStatus').value = recipe.status || 'Draft';
        document.getElementById('recipeInstructions').value = recipe.instructions || '';
        document.getElementById('recipeNotes').value = recipe.notes || '';
        // set target price from DB so calculations use the same base
        const targetEl = document.getElementById('targetPrice');
        if (targetEl) targetEl.value = recipe.sellingPrice || recipe.harga_jual || '';
        populateIngredientsFromRecipe(recipe);
        // ensure margin shown in modal matches DB value (override any client calc)
        if (typeof recipe.margin !== 'undefined' && recipe.margin !== null) {
            setProfitMarginDisplay(recipe.margin);
        }
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList) sidebar.classList.add('opacity-40', 'pointer-events-none');
        const modal = document.getElementById('addRecipeModal');
        if (modal && modal.classList) modal.classList.remove('hidden');
    }

    function duplicateRecipe(id) {
        const recipe = recipes.find(r => parseInt(r.id) === parseInt(id));
        if (!recipe) return alert('Resep tidak ditemukan');
        editingRecipeId = null;
        document.getElementById('recipeName').value = recipe.name + ' (Copy)';
        document.getElementById('recipeCategory').value = recipe.category || '';
        document.getElementById('recipeYield').value = recipe.yield || 1;
        document.getElementById('recipeDuration').value = recipe.duration || '';
        document.getElementById('recipeStatus').value = recipe.status || 'Draft';
        document.getElementById('recipeInstructions').value = recipe.instructions || '';
        document.getElementById('recipeNotes').value = recipe.notes || '';
        // prefill target price and ingredients, then display DB margin
        const targetElCopy = document.getElementById('targetPrice');
        if (targetElCopy) targetElCopy.value = recipe.sellingPrice || recipe.harga_jual || '';
        populateIngredientsFromRecipe(recipe);
        if (typeof recipe.margin !== 'undefined' && recipe.margin !== null) {
            setProfitMarginDisplay(recipe.margin);
        }
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList) sidebar.classList.add('opacity-40', 'pointer-events-none');
        const modal = document.getElementById('addRecipeModal');
        if (modal && modal.classList) modal.classList.remove('hidden');
    }

    function viewRecipe(id) {
        const recipe = recipes.find(r => parseInt(r.id) === parseInt(id));
        if (!recipe) return alert('Resep tidak ditemukan');
        let txt = `Resep: ${recipe.name}\nKategori: ${recipe.category}\nPorsi: ${recipe.yield}\nStatus: ${recipe.status}\n\nBahan:\n`;
        (recipe.ingredients || []).forEach(i => { txt += `- ${i.name}: ${i.quantity} ${i.unit} @ ${i.price}\n`; });
        txt += `\nInstruksi:\n${recipe.instructions || ''}`;
        alert(txt);
    }

    function deleteRecipe(id) {
        if (!confirm('Yakin ingin menghapus resep ini?')) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/management/resep/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    refreshRecipes();
                    alert('Resep berhasil dihapus.');
                } else {
                    throw new Error((data && data.message) || 'Gagal menghapus resep');
                }
            }).catch(err => { console.error(err); alert(err.message || 'Terjadi kesalahan saat menghapus resep'); });
    }

    // When a bahan is selected in the name input, set qty max and show stock
    function onBahanSelected(input) {
        const row = input.closest('.ingredient-item');
        if (!row) return;
        const qtyInput = row.querySelector('.ingredient-quantity');
        let found = null;
        try {
            if (input.tagName === 'SELECT') {
                const id = input.value;
                if (id) found = (bahanList || []).find(b => String(b.id) === String(id));
            } else {
                const name = (input.value || '').trim();
                found = (bahanList || []).find(b => b.nama === name);
            }
        } catch (e) {
            console.error('onBahanSelected error', e);
        }

        if (found) {
            if (qtyInput) {
                qtyInput.max = found.stok;
                qtyInput.placeholder = `Max: ${found.stok}`;
                if (parseFloat(qtyInput.value) > Number(found.stok)) {
                    qtyInput.value = found.stok;
                    calculateIngredientCost(qtyInput);
                }
            }
            const stockEl = row.querySelector('.ingredient-stock');
            if (stockEl) stockEl.textContent = `Stok: ${found.stok} • Sisa: ${Math.max(0, found.stok - (parseFloat(qtyInput.value) || 0))}`;
            row.setAttribute('data-bahan-id', found.id);
            row.setAttribute('data-bahan-stok', found.stok);
        } else {
            if (qtyInput) {
                qtyInput.removeAttribute('max');
                qtyInput.placeholder = '';
            }
            row.removeAttribute('data-bahan-id');
            row.removeAttribute('data-bahan-stok');
        }
    }

    // Show suggestions dropdown for bahan as user types
    function onBahanInput(input) {
        const val = (input.value || '').toLowerCase();
        const row = input.closest('.ingredient-item');
        if (!row) return;
        const box = row.querySelector('.bahan-suggestions');
        if (!box) return;
        if (!val) {
            box.innerHTML = '';
            if (box && box.classList) box.classList.add('hidden');
            return;
        }
        const matches = (bahanList || []).filter(b => (b.nama || '').toLowerCase().includes(val)).slice(0, 30);
        if (matches.length === 0) {
            box.innerHTML = '';
            if (box && box.classList) box.classList.add('hidden');
            return;
        }
        box.innerHTML = matches.map(m => `<div class="item" data-id="${m.id}" data-nama="${m.nama}" data-stok="${m.stok}">${m.nama} <span class="text-xs text-gray-400">• stok ${m.stok}</span></div>`).join('');
        if (box && box.classList) box.classList.remove('hidden');

        // attach click handlers
        box.querySelectorAll('.item').forEach(el => {
            el.onclick = function () {
                const name = this.getAttribute('data-nama');
                input.value = name;
                // call existing handler to set stok etc
                onBahanSelected(input);
                if (box && box.classList) box.classList.add('hidden');
            };
        });

        // hide suggestions on blur after short delay to allow click
        input.addEventListener('blur', function () { setTimeout(() => { if (box && box.classList) box.classList.add('hidden'); }, 150); }, { once: true });
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
        'ingredient-item grid grid-cols-1 md:grid-cols-12 gap-6 p-3 bg-white rounded-lg border items-center';
        newIngredient.innerHTML = `
                <div class="relative md:col-span-5">
                    <select class="ingredient-name md:col-span-4 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="onBahanSelected(this)">
                        <option value="">Pilih bahan...</option>
                        ${createBahanOptions()}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="number" step="0.01" placeholder="Qty" class="ingredient-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                    <div class="ingredient-stock text-xs text-gray-500 mt-1">Stok: -</div>
                </div>
                <select class="ingredient-unit md:col-span-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="ml">ml</option>
                    <option value="liter">liter</option>
                    <option value="pcs">pcs</option>
                    <option value="sdm">sdm</option>
                    <option value="sdt">sdt</option>
                </select>
                <input type="number" step="0.01" placeholder="Harga/unit" class="ingredient-price md:col-span-2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                <input type="number" step="0.01" placeholder="Subtotal" class="ingredient-subtotal md:col-span-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                <button type="button" onclick="removeIngredient(this)" class="md:col-span-1 flex items-center justify-center px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
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

        // enforce max if bahan has stok
        const stokAttr = row.getAttribute('data-bahan-stok');
        const stok = stokAttr !== null ? Number(stokAttr) : null;
        if (stok !== null && !isNaN(stok)) {
            if (quantity > stok) {
                row.querySelector('.ingredient-quantity').value = stok;
            }
            const remaining = stok - (parseFloat(row.querySelector('.ingredient-quantity').value) || 0);
            const stockEl = row.querySelector('.ingredient-stock');
            if (stockEl) stockEl.textContent = `Stok: ${stok} • Sisa: ${remaining}`;
        }

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

    // Set profit margin display using value from database (overrides calculated value)
    function setProfitMarginDisplay(margin) {
        const el = document.getElementById('profitMargin');
        if (!el) return;
        const m = (margin === null || margin === undefined || isNaN(Number(margin))) ? 0 : Number(margin);
        el.textContent = `${m}%`;
        if (m >= 70) {
            el.className = 'text-lg font-bold text-green-600';
        } else if (m >= 50) {
            el.className = 'text-lg font-bold text-yellow-600';
        } else {
            el.className = 'text-lg font-bold text-red-600';
        }
    }

    // update header stats from current recipes array
    function updateStats() {
        const total = recipes.length;
        const active = recipes.filter(r => (r.status || '').toLowerCase() === 'aktif').length;
        const percent = total ? Math.round((active / total) * 100) : 0;
        const totalEl = document.getElementById('totalRecipesCount');
        const activeEl = document.getElementById('activeRecipesCount');
        const percentEl = document.getElementById('activeRecipesPercent');
        if (totalEl) totalEl.textContent = total;
        if (activeEl) activeEl.textContent = active;
        if (percentEl) percentEl.textContent = percent + '%';
    }

    // fetch latest recipes from server and refresh UI
    function refreshRecipes() {
        fetch('/management/resep', { headers: { 'Accept': 'application/json' } })
            .then(res => res.ok ? res.json() : null)
            .then(data => {
                if (!data || !data.success) return;
                // map server representation to frontend shape
                recipes = (data.recipes || []).map(r => ({
                    id: r.id,
                    name: r.name || r.nama || '',
                    category: r.category || r.kategori || '',
                    yield: r.yield || r.porsi || 1,
                    duration: r.duration || r.waktu_pembuatan || '',
                    foodCost: r.foodCost || 0,
                    sellingPrice: r.sellingPrice || r.harga_jual || 0,
                    margin: r.margin || 0,
                    status: r.status || 'Draft',
                    ingredients: r.ingredients || r.rincian_resep || r.rincianResep || [],
                    instructions: r.instructions || r.langkah || '',
                    notes: r.notes || r.catatan || ''
                }));
                if (currentView === 'table') renderTableView(); else renderGridView();
                updateStats();
            }).catch(err => console.error('refreshRecipes error', err));
    }

    function saveRecipe() {
        const form = document.getElementById('recipeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Collect ingredients (only allow selection from bahanList)
        const ingredients = [];
        let invalidSelection = false;
        document.querySelectorAll('.ingredient-item').forEach(item => {
            const bahanId = item.querySelector('.ingredient-name').value;
            const quantity = parseFloat(item.querySelector('.ingredient-quantity').value) || 0;
            const unit = item.querySelector('.ingredient-unit').value;
            const price = parseFloat(item.querySelector('.ingredient-price').value) || 0;
            const subtotal = parseFloat(item.querySelector('.ingredient-subtotal').value) || 0;

            const found = (bahanList || []).find(b => String(b.id) === String(bahanId));
            if (!found) {
                if (quantity > 0 || price > 0) invalidSelection = true;
                return; // skip empty rows
            }

            if (found && quantity > 0) {
                ingredients.push({ bahan_id: found.id, name: found.nama, quantity, unit, price, subtotal });
            }
        });

        if (invalidSelection) {
            alert('Beberapa bahan tidak dipilih dari daftar. Pilih bahan dari daftar stok.');
            return;
        }

        if (ingredients.length === 0) {
            alert('Minimal harus ada 1 bahan baku dengan harga!');
            return;
        }

        const payload = {
            name: document.getElementById('recipeName').value,
            category: document.getElementById('recipeCategory').value,
            yield: parseInt(document.getElementById('recipeYield').value) || 1,
            duration: document.getElementById('recipeDuration').value,
            status: document.getElementById('recipeStatus').value,
            instructions: document.getElementById('recipeInstructions').value,
            notes: document.getElementById('recipeNotes').value,
            sellingPrice: parseFloat(document.getElementById('targetPrice').value) || 0,
            ingredients: ingredients
        };

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const isEdit = editingRecipeId !== null && editingRecipeId !== undefined;
        const url = isEdit ? `/management/resep/${editingRecipeId}` : `{{ route('management.resep.store') }}`;
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload)
        }).then(res => res.json())
        .then(data => {
            if (data && data.success) {
                closeAddRecipeModal();
                // refresh from server to show canonical data
                refreshRecipes();
                editingRecipeId = null;
                alert('Resep berhasil disimpan.');
            } else {
                throw new Error((data && data.message) || 'Gagal menyimpan resep');
            }
        }).catch(err => {
            console.error(err);
            alert(err.message || 'Terjadi kesalahan saat menyimpan resep');
        });
    }
</script>
