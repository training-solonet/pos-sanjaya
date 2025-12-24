@extends('layouts.manajemen.index')

@section('content')
    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[9999] space-y-2" style="max-width: 400px;">
        <!-- Toast notifications will be inserted here -->
    </div>

    {{-- <div class="content flex-1 lg:flex-1"> --}}
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
                        <div class="relative inline-block text-left">
                            <button onclick="toggleExportDropdown()" id="exportButton"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 shadow-sm">
                                <i class="fas fa-download mr-2"></i>
                                Export Data
                                <i class="fas fa-chevron-down ml-2"></i>
                            </button>
                            <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('management.resep.index', ['export' => 'excel']) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-file-excel text-green-600 mr-2"></i>
                                        Export ke Excel
                                    </a>
                                    <a href="{{ route('management.resep.index', ['export' => 'pdf']) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                                        Export ke PDF
                                    </a>
                                </div>
                            </div>
                        </div>
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
    {{-- </div> --}}
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
                        <div class="flex gap-2">
                            <select id="productSelect" onchange="onProductSelected(this)" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                                <option value="">Pilih Produk </option>
                            </select>
                            <input type="hidden" id="recipeNameHidden">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Pilih produk untuk mengisi nama & harga.</p>
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

<!-- Confirmation Modal for Delete -->
<div id="confirmDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-trash text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Hapus Resep?</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Resep yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin menghapus resep ini?
            </p>
            <div class="flex space-x-3">
                <button onclick="closeConfirmDelete()" 
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </button>
                <button onclick="confirmDelete()" 
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Toast Notification System
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        
        const toastId = 'toast-' + Date.now();
        const icons = {
            success: '<i class="fas fa-check-circle text-green-500"></i>',
            error: '<i class="fas fa-exclamation-circle text-red-500"></i>',
            warning: '<i class="fas fa-exclamation-triangle text-yellow-500"></i>',
            info: '<i class="fas fa-info-circle text-blue-500"></i>'
        };
        
        const colors = {
            success: 'bg-green-50 border-green-200',
            error: 'bg-red-50 border-red-200',
            warning: 'bg-yellow-50 border-yellow-200',
            info: 'bg-blue-50 border-blue-200'
        };
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `${colors[type]} border-l-4 rounded-lg shadow-lg p-4 mb-2 transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 text-xl">
                    ${icons[type]}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <button onclick="removeToast('${toastId}')" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 10);
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                removeToast(toastId);
            }, duration);
        }
    }
    
    function removeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (!toast) return;
        
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }

    // Daftar produk (digunakan untuk mengisi otomatis nama resep & harga).
    // Variabel ini di-inject dari controller sebagai JSON.
    let produkList = @json($produks ?? []);

    // Isi `select` produk pada modal.
    // - Membaca `produkList` yang disediakan controller
    // - Menampilkan hanya nama produk sebagai teks opsi
    // - Menyimpan `harga` produk di atribut `data-harga` agar saat dipilih bisa mengisi `targetPrice`
    function populateProductSelect() {
        try {
            const sel = document.getElementById('productSelect');
            if (!sel) return;
            // clear existing but keep first empty option; show only product name in list
            sel.innerHTML = '<option value="">Pilih Produk (opsional)</option>' + (produkList || []).map(p => `<option value="${p.id}" data-harga="${p.harga}">${p.nama}</option>`).join('');
        } catch (e) {
            console.error('populateProductSelect error', e);
        }
    }

    // Handler saat produk dipilih dari `productSelect`.
    // - Menyimpan nama produk terpilih ke field tersembunyi `recipeNameHidden`
    // - Mengisi `targetPrice` dengan harga produk jika tersedia
    // - Memanggil `calculateMargin()` agar tampilan margin terupdate
    function onProductSelected(sel) {
        try {
            const id = sel.value;
            const hiddenName = document.getElementById('recipeNameHidden');
            const priceEl = document.getElementById('targetPrice');
            if (!id) {
                // if cleared, do not overwrite hidden name (use existing for edit flows)
                calculateMargin();
                return;
            }
            const found = (produkList || []).find(p => String(p.id) === String(id));
            if (!found) return;
            if (hiddenName) hiddenName.value = found.nama || hiddenName.value;
            if (priceEl) priceEl.value = found.harga ?? priceEl.value;
            calculateMargin();
        } catch (e) {
            console.error('onProductSelected error', e);
        }
    }
    // --- Data yang di-inject dari server ---
    // `recipes`: array objek resep yang disiapkan controller
    // `bahans`: daftar bahan baku (id, nama, stok) untuk dipakai di select bahan
    let recipes = @json($recipes ?? []);
    // Bahan baku list (id, nama, stok)
    let bahanList = @json($bahans ?? []);

    // Render sebuah `<datalist>` tersembunyi berisi semua nama bahan
    // - Berguna sebagai fallback untuk input yang perlu lookup nama bahan
    // - Datalist disembunyikan dari layout tapi tersedia untuk input bila diperlukan
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

    // Buat string HTML `<option>` untuk setiap bahan
    // - Mengembalikan gabungan option yang akan disisipkan ke <select>
    // - Dipakai saat membuat baris bahan secara dinamis
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

    // Show Laravel session flash messages as toasts
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        
        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
        
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('{{ $error }}', 'error');
            @endforeach
        @endif
    });

    // Toggle export dropdown
    function toggleExportDropdown() {
        const dropdown = document.getElementById('exportDropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // Toggle individual export menu for each recipe
    function toggleExportMenu(recipeId) {
        const menu = document.getElementById('exportMenu' + recipeId);
        if (menu) {
            // Close all other export menus first
            document.querySelectorAll('[id^="exportMenu"]').forEach(m => {
                if (m.id !== 'exportMenu' + recipeId) {
                    m.classList.add('hidden');
                }
            });
            menu.classList.toggle('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('exportDropdown');
        const button = document.getElementById('exportButton');
        
        if (dropdown && button && !button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }

        // Close export menus when clicking outside
        const isExportButton = event.target.closest('[onclick*="toggleExportMenu"]');
        const isExportMenu = event.target.closest('[id^="exportMenu"]');
        
        if (!isExportButton && !isExportMenu) {
            document.querySelectorAll('[id^="exportMenu"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

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

    // Inisialisasi ketika halaman selesai dimuat
    window.addEventListener('DOMContentLoaded', function() {
        // populate product select if produk provided
        populateProductSelect();
        updateDateTime();
        setInterval(updateDateTime, 60000); // Perbarui setiap menit
        renderTableView(); 
        updateStats();
        
        // Check if there's an 'edit' parameter in URL to auto-open edit modal
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('edit');
        if (editId) {
            // Wait a bit for data to be loaded, then open edit modal
            setTimeout(() => {
                editRecipe(editId);
                // Clean URL without reload
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 500);
        }
        
        // sinkronkan dengan DB setiap 60 detik
        setInterval(refreshRecipes, 60000);
    });

    // Toggle antara tampilan 'table' dan 'grid'
    // - Mengubah kelas DOM untuk menampilkan view yang sesuai
    // - Merender ulang konten berdasarkan array `recipes`
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

    // Render daftar resep sebagai tabel HTML
    // - Menerima parameter opsional `recipesToRender` untuk hasil yang sudah difilter
    // - Menggunakan helper kecil untuk warna, ikon, dan format angka
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
                                <div class="text-sm font-medium text-gray-900 cursor-pointer text-blue-600" onclick="viewRecipe(${recipe.id})">${recipe.name}</div>
                                <div class="text-xs text-gray-500">${recipe.yield} porsi • ${formatDuration(recipe.duration)}</div>
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
                            <button onclick="deleteRecipe(${recipe.id})" class="text-red-600 hover:text-red-700" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
    }

    // Render resep dalam bentuk kartu responsif (grid view)
    // - Isi sama seperti tabel tetapi ditampilkan dalam layout kartu untuk browsing visual
    function renderGridView(recipesToRender = recipes) {
        const grid = document.getElementById('recipeGrid');

        if (recipesToRender.length === 0) {
            grid.innerHTML =
                '<div class="col-span-full text-center py-8 text-gray-500">Tidak ada resep yang ditemukan</div>';
            return;
        }

        grid.innerHTML = recipesToRender.map(recipe => `
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300 recipe-card cursor-pointer" onclick="viewRecipe(${recipe.id})">
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
                            <!-- Name click navigates to detail page; removed separate detail button -->
                            <div class="flex items-center space-x-2">
                                <button onclick="event.stopPropagation(); editRecipe(${recipe.id})" class="text-blue-600 hover:text-blue-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <div class="relative inline-block">
                                    <button onclick="event.stopPropagation(); toggleExportMenu(${recipe.id})" class="text-purple-600 hover:text-purple-700" title="Export">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <div id="exportMenu${recipe.id}" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                        <a href="/management/resep/${recipe.id}?export=excel" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-file-excel text-green-600 mr-1"></i>Excel
                                        </a>
                                        <a href="/management/resep/${recipe.id}?export=pdf" class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-file-pdf text-red-600 mr-1"></i>PDF
                                        </a>
                                    </div>
                                </div>
                                <button onclick="event.stopPropagation(); deleteRecipe(${recipe.id})" class="text-red-600 hover:text-red-700" title="Hapus">
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

    // Format teks durasi (menit)
    function formatDuration(d) {
        if (!d && d !== 0) return '';
        const s = String(d).trim();
        if (s === '') return '';
        if (/\bmenit\b/i.test(s)) return s;
        return s + ' menit';
    }

    // Filter resep berdasarkan kata kunci, kategori, status, dan rentang biaya
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

    // Buka modal 'Buat / Edit Resep'
    // - Mereset form, membersihkan state sementara, lalu menampilkan modal
    // - Menggunakan `editingRecipeId` untuk membedakan create vs update saat menyimpan
    function openAddRecipeModal() {
        // reset form and ingredient rows before showing
        const form = document.getElementById('recipeForm');
        if (form) form.reset();
        resetIngredientsForm();
        resetCostCalculation();
        editingRecipeId = null;
        const hiddenName = document.getElementById('recipeNameHidden');
        if (hiddenName) hiddenName.value = '';
        // dim sidebar and show modal
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList) {
            sidebar.classList.add('opacity-40', 'pointer-events-none');
        }
        const modal = document.getElementById('addRecipeModal');
        if (modal && modal.classList) modal.classList.remove('hidden');
    }

    // Tutup modal resep dan bersihkan state sementara
    // - Menyembunyikan modal, mereset form, dan mengosongkan nama tersembunyi
    function closeAddRecipeModal() {
        const modal = document.getElementById('addRecipeModal');
        if (modal && modal.classList) modal.classList.add('hidden');
        // restore sidebar
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList) {
            sidebar.classList.remove('opacity-40', 'pointer-events-none');
        }
        document.getElementById('recipeForm').reset();
        const hiddenName2 = document.getElementById('recipeNameHidden');
        if (hiddenName2) hiddenName2.value = '';
        resetIngredientsForm();
        resetCostCalculation();
        editingRecipeId = null;
    }

    // Reset daftar bahan menjadi satu baris kosong
    // - Membangun markup baris bahan awal menggunakan `createBahanOptions()`
    // - Menjamin state disabled pada opsi bahan dikembalikan semula
    function resetIngredientsForm() {
        document.getElementById('ingredientsList').innerHTML = `
                <div class="ingredient-item grid grid-cols-1 md:grid-cols-12 gap-4 p-4 bg-white rounded-lg border">
                    <div class="relative md:col-span-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bahan</label>
                        <select class="ingredient-name w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="onBahanSelected(this)">
                            <option value="">Pilih bahan...</option>
                            ${createBahanOptions()}
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                        <input type="number" step="0.01" placeholder="0" class="ingredient-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Satuan</label>
                        <select class="ingredient-unit w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="gram">gram</option>
                            <option value="kg">kg</option>
                            <option value="ml">ml</option>
                            <option value="liter">liter</option>
                            <option value="pcs">pcs</option>
                            <option value="slice">slice</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Harga/unit</label>
                        <input type="number" step="0.01" placeholder="0" class="ingredient-price w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                        <input type="number" step="0.01" placeholder="0" class="ingredient-subtotal w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="button" onclick="removeIngredient(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 flex items-center justify-center">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            // ensure options disabled state is reset
            refreshBahanOptionsDisable();
    }

    // --- Helper CRUD ---
    // Isi baris bahan berdasarkan data bahan yang tersimpan pada resep
    // - Membuat node DOM untuk setiap bahan dan mengisi nilai-nilainya
    // - Memanggil `onBahanSelected()` agar stok dan batas diterapkan pada baris
    function populateIngredientsFromRecipe(recipe) {
        const list = document.getElementById('ingredientsList');
        list.innerHTML = '';
        (recipe.ingredients || []).forEach(ing => {
            const newIngredient = document.createElement('div');
            newIngredient.className = 'ingredient-item grid grid-cols-1 md:grid-cols-12 gap-4 p-4 bg-white rounded-lg border items-center';
            newIngredient.innerHTML = `
                <div class="relative md:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bahan</label>
                    <select class="ingredient-name w-full px-3 py-2 border border-gray-300 rounded-lg" onchange="onBahanSelected(this)">
                        <option value="">Pilih bahan...</option>
                        ${createBahanOptions()}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                    <input type="number" step="0.01" placeholder="0" class="ingredient-quantity w-full px-3 py-2 border border-gray-300 rounded-lg" oninput="calculateIngredientCost(this)" value="${ing.quantity}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Satuan</label>
                    <select class="ingredient-unit w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="gram">gram</option>
                        <option value="kg">kg</option>
                        <option value="ml">ml</option>
                        <option value="liter">liter</option>
                        <option value="pcs">pcs</option>
                        <option value="slice">slice</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga/unit</label>
                    <input type="number" step="0.01" placeholder="0" class="ingredient-price w-full px-3 py-2 border border-gray-300 rounded-lg" oninput="calculateIngredientCost(this)" value="${ing.price}" required>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                    <input type="number" step="0.01" placeholder="0" class="ingredient-subtotal w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly value="${ing.subtotal}">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeIngredient(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 flex items-center justify-center">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            // set bahan selection and apply auto-fill satuan & harga (match by name -> id)
            const nameSelect = newIngredient.querySelector('.ingredient-name');
            if (nameSelect) {
                const found = (bahanList || []).find(b => b.nama === (ing.name || ''));
                if (found) {
                    nameSelect.value = found.id;
                    onBahanSelected(nameSelect); // ini akan auto-set satuan & harga
                } else {
                    nameSelect.value = '';
                    // jika bahan tidak ditemukan, set manual dari data resep
                    const sel = newIngredient.querySelector('.ingredient-unit');
                    if (sel) sel.value = ing.unit || 'gram';
                }
            }
            list.appendChild(newIngredient);
        });
        calculateTotalCost();
        // disable selected bahan in other selects
        refreshBahanOptionsDisable();
    }

    // Buka modal dalam mode Edit dan isi field sesuai resep yang dipilih
    // - Menetapkan `editingRecipeId` ke id resep
    // - Mencocokkan nama resep dengan `Produk` untuk pre-select; bila tidak cocok, simpan nama ke field tersembunyi
    function editRecipe(id) {
        const recipe = recipes.find(r => parseInt(r.id) === parseInt(id));
        if (!recipe) {
            showToast('Resep tidak ditemukan', 'error');
            return;
        }
        editingRecipeId = id;
        // set product select if product exists, otherwise store name in hidden input
        const prodSelEdit = document.getElementById('productSelect');
        const hiddenEdit = document.getElementById('recipeNameHidden');
        if (prodSelEdit) {
            const foundProd = (produkList || []).find(p => p.nama === (recipe.name || ''));
            if (foundProd) {
                prodSelEdit.value = foundProd.id;
                onProductSelected(prodSelEdit);
                // keep hidden name in sync
                if (hiddenEdit) hiddenEdit.value = foundProd.nama || '';
            } else {
                prodSelEdit.value = '';
                if (hiddenEdit) hiddenEdit.value = recipe.name || '';
            }
        } else {
            if (hiddenEdit) hiddenEdit.value = recipe.name || '';
        }
        document.getElementById('recipeCategory').value = recipe.category || '';
        document.getElementById('recipeYield').value = recipe.yield || 1;
        document.getElementById('recipeDuration').value = recipe.duration || '';
        // the top status was removed; use the bottom status select
        const statusEl = document.getElementById('recipeStatusDuplicate') || document.getElementById('recipeStatus');
        if (statusEl) statusEl.value = recipe.status || 'Draft';
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

    // Duplikat resep ke form baru (copy)
    // - Tidak langsung menyimpan; membuka modal dengan nilai disalin dan `editingRecipeId = null`
    function duplicateRecipe(id) {
        const recipe = recipes.find(r => parseInt(r.id) === parseInt(id));
        if (!recipe) {
            showToast('Resep tidak ditemukan', 'error');
            return;
        }
        editingRecipeId = null;
        // set product select if product exists, otherwise set hidden name
        const prodSelDup = document.getElementById('productSelect');
        const hiddenDup = document.getElementById('recipeNameHidden');
        if (prodSelDup) {
            const foundProd = (produkList || []).find(p => p.nama === (recipe.name || ''));
            if (foundProd) {
                prodSelDup.value = foundProd.id;
                onProductSelected(prodSelDup);
                if (hiddenDup) hiddenDup.value = (foundProd.nama || '') + ' (Copy)';
            } else {
                prodSelDup.value = '';
                if (hiddenDup) hiddenDup.value = (recipe.name || '') + ' (Copy)';
            }
        } else {
            if (hiddenDup) hiddenDup.value = (recipe.name || '') + ' (Copy)';
        }
        document.getElementById('recipeCategory').value = recipe.category || '';
        document.getElementById('recipeYield').value = recipe.yield || 1;
        document.getElementById('recipeDuration').value = recipe.duration || '';
        const statusEl2 = document.getElementById('recipeStatusDuplicate') || document.getElementById('recipeStatus');
        if (statusEl2) statusEl2.value = recipe.status || 'Draft';
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

    // Navigasi ke halaman detail resep yang disajikan server
    // - Menggunakan `window.location.href` ke route `/management/resep/{id}`
    function viewRecipe(id) {
        // redirect to the server-side show page for the recipe
        // route: /management/resep/{id}
        if (!id) return;
        const base = "{{ url('management/resep') }}"; // '/management/resep'
        window.location.href = base + '/' + encodeURIComponent(id);
    }

    // Hapus resep melalui AJAX dengan konfirmasi modal
    let deleteRecipeId = null;
    
    function deleteRecipe(id) {
        deleteRecipeId = id;
        const modal = document.getElementById('confirmDeleteModal');
        if (modal) modal.classList.remove('hidden');
    }
    
    function closeConfirmDelete() {
        deleteRecipeId = null;
        const modal = document.getElementById('confirmDeleteModal');
        if (modal) modal.classList.add('hidden');
    }
    
    function confirmDelete() {
        if (!deleteRecipeId) return;
        
        const id = deleteRecipeId;
        closeConfirmDelete();
        
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/management/resep/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    // Refresh list to reflect deletion
                    refreshRecipes();
                    showToast('Resep berhasil dihapus', 'success');
                } else {
                    throw new Error((data && data.message) || 'Gagal menghapus resep');
                }
            }).catch(err => { 
                console.error(err); 
                showToast(err.message || 'Terjadi kesalahan saat menghapus resep', 'error'); 
            });
    }

    // Ketika bahan dipilih pada baris bahan:
    // - Menyimpan id bahan pada atribut baris untuk referensi saat submit
    // - Mengatur satuan otomatis sesuai satuan_kecil bahan
    // - Mengisi harga_satuan otomatis dari database
    function onBahanSelected(input) {
        const row = input.closest('.ingredient-item');
        if (!row) return;
        
        const unitSelect = row.querySelector('.ingredient-unit');
        const priceInput = row.querySelector('.ingredient-price');
        
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
            row.setAttribute('data-bahan-id', found.id);
            
            // Set satuan otomatis berdasarkan satuan_kecil
            if (unitSelect && found.satuan_kecil) {
                const satuan = String(found.satuan_kecil).toLowerCase().trim();
                
                // Update options berdasarkan satuan_kecil
                if (satuan === 'kg') {
                    // kg bisa diganti gram
                    unitSelect.innerHTML = `
                        <option value="kg" selected>kg</option>
                        <option value="gram">gram</option>
                    `;
                } else if (satuan === 'gram' || satuan === 'g') {
                    // gram hanya gram
                    unitSelect.innerHTML = `<option value="gram" selected>gram</option>`;
                } else if (satuan === 'liter' || satuan === 'l') {
                    // liter bisa diganti ml
                    unitSelect.innerHTML = `
                        <option value="liter" selected>liter</option>
                        <option value="ml">ml</option>
                    `;
                } else if (satuan === 'ml') {
                    // ml hanya ml
                    unitSelect.innerHTML = `<option value="ml" selected>ml</option>`;
                } else if (satuan === 'pcs') {
                    // pcs hanya pcs
                    unitSelect.innerHTML = `<option value="pcs" selected>pcs</option>`;
                } else if (satuan === 'slice') {
                    // slice hanya slice
                    unitSelect.innerHTML = `<option value="slice" selected>slice</option>`;
                } else {
                    // default ke satuan dari database
                    unitSelect.innerHTML = `<option value="${satuan}" selected>${satuan}</option>`;
                }
            }
            
            // Set harga otomatis dari harga_satuan
            if (priceInput && found.harga_satuan) {
                priceInput.value = found.harga_satuan;
                // Trigger kalkulasi subtotal
                calculateIngredientCost(priceInput);
            }
        } else {
            row.removeAttribute('data-bahan-id');
            
            // Reset ke pilihan default jika tidak ada bahan
            if (unitSelect) {
                unitSelect.innerHTML = `
                    <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="ml">ml</option>
                    <option value="liter">liter</option>
                    <option value="pcs">pcs</option>
                    <option value="slice">slice</option>
                `;
            }
            if (priceInput) {
                priceInput.value = '';
            }
        }
        // perbarui status disabled opsi bahan di semua select
        refreshBahanOptionsDisable();
    }

    // Nonaktifkan opsi bahan yang sudah dipilih di select lain untuk mencegah duplikasi
    // - Melindungi agar user tidak memilih bahan yang sama lebih dari sekali
    function refreshBahanOptionsDisable() {
        try {
            const selects = Array.from(document.querySelectorAll('.ingredient-name'));
            // collect selected values (non-empty)
            const selected = selects.map(s => (s.value || '').toString()).filter(v => v !== '');

            selects.forEach(s => {
                const myVal = (s.value || '').toString();
                Array.from(s.options).forEach(opt => {
                    const val = (opt.value || '').toString();
                    if (!val) {
                        opt.disabled = false;
                        return;
                    }
                    // disable if selected in other select
                    if (val !== myVal && selected.includes(val)) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            });
        } catch (e) {
            console.error('refreshBahanOptionsDisable error', e);
        }
    }

    // Tampilkan dropdown saran bahan saat pengguna mengetik
    // - Memfilter `bahanList` dan menampilkan daftar saran yang dapat diklik
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

    // Reset semua tampilan terkait perhitungan biaya ke nilai default
    function resetCostCalculation() {
        document.getElementById('totalFoodCost').textContent = 'Rp 0';
        document.getElementById('costPerPortion').textContent = 'Rp 0';
        document.getElementById('targetPrice').value = '';
        document.getElementById('profitMargin').textContent = '0%';
        document.getElementById('profitMargin').className = 'text-lg font-bold text-gray-900';
    }

    // Tambah satu baris bahan kosong ke form
    // - Menggunakan markup yang sama dengan `resetIngredientsForm` agar konsisten
    function addIngredient() {
        const ingredientsList = document.getElementById('ingredientsList');
        const newIngredient = document.createElement('div');
        newIngredient.className = 'ingredient-item grid grid-cols-1 md:grid-cols-12 gap-4 p-4 bg-white rounded-lg border items-center';
        newIngredient.innerHTML = `
                <div class="relative md:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bahan</label>
                    <select class="ingredient-name w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="onBahanSelected(this)">
                        <option value="">Pilih bahan...</option>
                        ${createBahanOptions()}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                    <input type="number" step="0.01" placeholder="0" class="ingredient-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Satuan</label>
                    <select class="ingredient-unit w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="gram">gram</option>
                        <option value="kg">kg</option>
                        <option value="ml">ml</option>
                        <option value="liter">liter</option>
                        <option value="pcs">pcs</option>
                        <option value="slice">slice</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Harga/unit</label>
                    <input type="number" step="0.01" placeholder="0" class="ingredient-price w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" oninput="calculateIngredientCost(this)" required>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                    <input type="number" step="0.01" placeholder="0" class="ingredient-subtotal w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeIngredient(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 flex items-center justify-center">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        ingredientsList.appendChild(newIngredient);
        // after adding new select, refresh disabled options so existing selections are respected
        refreshBahanOptionsDisable();
    }

    // Hapus baris bahan dan hitung ulang total biaya
    function removeIngredient(button) {
        button.parentElement.remove();
        calculateTotalCost();
        // when a row is removed, re-enable that bahan in other selects
        refreshBahanOptionsDisable();
    }

    // Hitung subtotal untuk satu baris bahan berdasarkan qty * harga
    function calculateIngredientCost(input) {
        const row = input.closest('.ingredient-item');
        const quantity = parseFloat(row.querySelector('.ingredient-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.ingredient-price').value) || 0;
        const subtotal = quantity * price;

        row.querySelector('.ingredient-subtotal').value = subtotal.toFixed(0);
        calculateTotalCost();
    }

    // Hitung total biaya semua bahan dan cost per porsi
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

    // Hitung margin profit berdasarkan target price dan total food cost
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

    // Tampilkan margin profit berdasarkan nilai dari database (menimpa perhitungan client)
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

    // Perbarui statistik header berdasarkan array `recipes` saat ini
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

    // Ambil daftar resep terbaru dari server (JSON) dan refresh UI
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

    // Simpan resep (create/update)
    // - Validasi form dan bahan
    // - Mengumpulkan payload sesuai format server
    // - Mengirim request POST/PUT dan menampilkan hasil
    function saveRecipe() {
        const form = document.getElementById('recipeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Kumpulkan bahan (hanya izinkan pilihan dari daftar bahan/bahan baku)
        const ingredients = [];
        let invalidSelection = false;
        let missingPrice = false;
        document.querySelectorAll('.ingredient-item').forEach(item => {
            const bahanEl = item.querySelector('.ingredient-name');
            const bahanId = bahanEl ? bahanEl.value : '';
            const quantity = parseFloat(item.querySelector('.ingredient-quantity').value) || 0;
            const unit = item.querySelector('.ingredient-unit').value;
            const priceRaw = item.querySelector('.ingredient-price').value;
            const price = priceRaw ? parseFloat(priceRaw) : 0;
            const subtotal = parseFloat(item.querySelector('.ingredient-subtotal').value) || 0;

            const found = (bahanList || []).find(b => String(b.id) === String(bahanId));
            if (!found) {
                // If user entered qty or price but didn't pick bahan from list => invalid
                if (quantity > 0 || price > 0) invalidSelection = true;
                return; // skip empty/unused rows
            }

            // If bahan is selected and quantity specified, price must be provided (>0)
            if (found && quantity > 0) {
                if (!price || price <= 0) {
                    missingPrice = true;
                }
                ingredients.push({ bahan_id: found.id, name: found.nama, quantity, unit, price, subtotal });
            }
            // If bahan selected but no quantity and price provided, ignore row
        });

        if (invalidSelection) {
            showToast('Beberapa bahan tidak dipilih dari daftar. Pilih bahan dari daftar stok.', 'warning');
            return;
        }

        if (missingPrice) {
            showToast('Harga/unit wajib diisi untuk semua bahan yang memiliki jumlah. Isi harga sebelum menyimpan.', 'warning');
            return;
        }

        if (ingredients.length === 0) {
            showToast('Minimal harus ada 1 bahan baku dengan harga!', 'warning');
            return;
        }

        const payload = {
            name: document.getElementById('recipeNameHidden').value,
            category: document.getElementById('recipeCategory').value,
            yield: parseInt(document.getElementById('recipeYield').value) || 1,
            duration: document.getElementById('recipeDuration').value,
            status: (document.getElementById('recipeStatusDuplicate') || document.getElementById('recipeStatus')).value,
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
        }).then(res => {
            // Check if response is not ok (status 422, 500, etc)
            if (!res.ok) {
                return res.json().then(errData => {
                    throw { status: res.status, data: errData };
                });
            }
            return res.json();
        })
        .then(data => {
            if (data && data.success) {
                closeAddRecipeModal();
                // refresh from server to show canonical data
                refreshRecipes();
                editingRecipeId = null;
                showToast('Resep berhasil disimpan', 'success');
            } else {
                throw new Error((data && data.message) || 'Gagal menyimpan resep');
            }
        }).catch(err => {
            console.error(err);
            let errorMessage = 'Terjadi kesalahan saat menyimpan resep';
            
            // Handle validation errors (422)
            if (err.status === 422 && err.data) {
                if (err.data.message) {
                    errorMessage = err.data.message;
                } else if (err.data.errors) {
                    errorMessage = Array.isArray(err.data.errors) 
                        ? err.data.errors.join('\n') 
                        : Object.values(err.data.errors).flat().join('\n');
                }
            } else if (err.message) {
                errorMessage = err.message;
            }
            
            showToast(errorMessage, 'error');
        });
    }
</script>
