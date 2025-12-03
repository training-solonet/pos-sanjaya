@extends('layouts.manajemen.index')

@php
    // Set locale ke Indonesia untuk Carbon
    \Carbon\Carbon::setLocale('id');
@endphp

@section('content')
    <!-- Page Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Action Buttons -->
            <div class="no-print flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('management.produk.index') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button onclick="openAddStockModal()" 
                            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-all flex items-center">
                        <i class="fas fa-plus mr-2"></i>Input Stok Baru
                    </button>
                </div>
                <div class="flex gap-2">
                    <button onclick="printPage()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <div class="relative">
                        <button onclick="toggleExportDropdown()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors flex items-center">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                        <div id="exportDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-10">
                            <button onclick="exportToCSV()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-file-csv text-green-500 mr-2"></i>Export ke CSV
                            </button>
                            <button onclick="exportToExcel()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-file-excel text-green-600 mr-2"></i>Export ke Excel
                            </button>
                            <button onclick="exportToPDF()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-file-pdf text-red-500 mr-2"></i>Export ke PDF
                            </button>
                        </div>
                    </div>
                    <button onclick="confirmClearAll()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center">
                        <i class="fas fa-trash mr-2"></i>Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Total Entri</span>
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list text-blue-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEntries }}</p>
                    <p class="text-xs text-gray-500 mt-1">Riwayat Input</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Hari Ini</span>
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-day text-green-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayEntries }}</p>
                    <p class="text-xs text-gray-500 mt-1">Input Hari Ini</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Minggu Ini</span>
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-week text-purple-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $weekEntries }}</p>
                    <p class="text-xs text-gray-500 mt-1">Input Minggu Ini</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Bulan Ini</span>
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-orange-600"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $monthEntries }}</p>
                    <p class="text-xs text-gray-500 mt-1">Input Bulan Ini</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="no-print bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Berdasarkan Tanggal</label>
                        <input type="date" id="filterDate" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Periode</label>
                        <select id="filterPeriod" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400">
                            <option value="all">Semua Periode</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button onclick="applyFilter()" 
                                class="px-6 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <button onclick="resetFilter()" 
                                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- History List -->
            <div id="historyContainer" class="space-y-4">
                @if($history->count() > 0)
                    @foreach($history as $index => $entry)
                        @php
                            // Format tanggal dengan Carbon untuk bahasa Indonesia
                            $date = \Carbon\Carbon::parse($entry->tanggal_update);
                            // Format: Hari, Tanggal Bulan Tahun Jam:Menit
                            $dateStr = $date->translatedFormat('l, d F Y H:i');
                            $kadaluarsaDate = \Carbon\Carbon::parse($entry->kadaluarsa);
                            $kadaluarsaStr = $kadaluarsaDate->translatedFormat('d F Y');
                            
                            // Hitung selisih hari untuk kadaluarsa
                            $now = \Carbon\Carbon::now();
                            $diffDays = $now->diffInDays($kadaluarsaDate, false);
                        @endphp
                        
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow" 
                             data-entry-id="{{ $entry->id }}"
                             data-tanggal="{{ $date->format('Y-m-d') }}"
                             data-bulan="{{ $date->format('Y-m') }}">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-clipboard-list text-green-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">Penambahan Stok #{{ $history->count() - $index }}</h4>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i>{{ $dateStr }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                                            <div>
                                                <p class="text-xs text-gray-500">Produk</p>
                                                <p class="text-lg font-bold text-gray-900 nama-produk" data-id="{{ $entry->id_produk }}">{{ $entry->produk->nama }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Stok Awal</p>
                                                <p class="text-lg font-bold text-gray-900 stok-awal" data-value="{{ $entry->stok_awal }}">{{ $entry->stok_awal }} pcs</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Stok Tambahan</p>
                                                <p class="text-lg font-bold text-green-600 stok-tambahan" data-value="{{ $entry->stok_baru }}">+{{ $entry->stok_baru }} pcs</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Total Stok</p>
                                                <p class="text-lg font-bold text-blue-600 total-stok" data-value="{{ $entry->total_stok }}">{{ $entry->total_stok }} pcs</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <p class="text-xs text-gray-500">Keterangan</p>
                                            <p class="text-sm text-gray-700 keterangan-text">{{ $entry->keterangan ?: '-' }}</p>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <p class="text-xs text-gray-500">Kadaluarsa</p>
                                            <p class="text-sm font-medium {{ $diffDays < 0 ? 'text-red-600' : ($diffDays <= 7 ? 'text-orange-600' : 'text-gray-700') }} kadaluarsa-text" data-value="{{ $entry->kadaluarsa }}">
                                                {{ $kadaluarsaStr }}
                                                @if($diffDays < 0)
                                                    <span class="ml-2 text-xs text-red-500">(Sudah Kadaluarsa)</span>
                                                @elseif($diffDays <= 7)
                                                    <span class="ml-2 text-xs text-orange-500">({{ $diffDays }} hari lagi)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2">
                                        <button onclick="openEditModal({{ $entry->id }})" 
                                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-sm flex items-center">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </button>
                                        <button onclick="deleteEntry({{ $entry->id }})" 
                                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm flex items-center">
                                            <i class="fas fa-trash mr-2"></i>Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div id="emptyState" class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Riwayat</h3>
                        <p class="text-gray-500 mb-4">Belum ada data penambahan stok produk</p>
                        <button onclick="openAddStockModal()" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            <i class="fas fa-plus mr-2"></i>Tambah Stok Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Tambah Stok Baru -->
    <div id="addStockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Tambah Stok Produk</h3>
                    <button onclick="closeAddStockModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="addStockForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                        <select name="id_produk" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                                id="select_produk">
                            <option value="">Pilih Produk</option>
                            @foreach ($produk as $item)
                                <option value="{{ $item->id }}" data-stok="{{ $item->stok }}">
                                    {{ $item->nama }} (Stok: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal</label>
                        <input type="number" id="current_stok" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok Tambahan</label>
                        <input type="number" name="stok_baru" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               oninput="calculateTotalStock()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Stok</label>
                        <input type="number" id="total_stok" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa Baru</label>
                        <input type="date" name="kadaluarsa" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ date('Y-m-d', strtotime('+14 days')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                  placeholder="Contoh: Tambahan stok produksi hari ini"></textarea>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeAddStockModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Lengkap -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-semibold">Edit Data Stok</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editForm" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" id="edit_nama_produk" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal</label>
                        <input type="number" id="edit_stok_awal" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok Tambahan</label>
                        <input type="number" name="stok_baru" id="edit_stok_baru" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               oninput="calculateEditTotalStock()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Stok</label>
                        <input type="number" id="edit_total_stok" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa</label>
                        <input type="date" name="kadaluarsa" id="edit_kadaluarsa" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let exportDropdownVisible = false;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Set today as default filter date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('filterDate').value = today;
        
        // Initialize product stock calculation
        document.querySelector('#select_produk').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const currentStock = selectedOption.getAttribute('data-stok') || 0;
            document.getElementById('current_stok').value = currentStock;
            calculateTotalStock();
        });

        // Close export dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const exportDropdown = document.getElementById('exportDropdown');
            const exportBtn = e.target.closest('button');
            
            if (exportDropdown && !exportDropdown.contains(e.target) && 
                exportBtn && !exportBtn.textContent.includes('Export')) {
                exportDropdown.classList.add('hidden');
                exportDropdownVisible = false;
            }
        });
    });

    // ========== MODAL FUNCTIONS ==========
    
    // Open Add Stock Modal
    function openAddStockModal() {
        // Reset form
        document.getElementById('addStockForm').reset();
        document.getElementById('current_stok').value = '';
        document.getElementById('total_stok').value = '';
        
        // Set default kadaluarsa (2 weeks from now)
        const twoWeeksLater = new Date();
        twoWeeksLater.setDate(twoWeeksLater.getDate() + 14);
        document.querySelector('input[name="kadaluarsa"]').value = twoWeeksLater.toISOString().split('T')[0];
        
        // Show modal
        document.getElementById('addStockModal').classList.remove('hidden');
    }

    // Close Add Stock Modal
    function closeAddStockModal() {
        document.getElementById('addStockModal').classList.add('hidden');
    }

    // Calculate Total Stock for Add Modal
    function calculateTotalStock() {
        const currentStock = parseInt(document.getElementById('current_stok').value) || 0;
        const additionalStock = parseInt(document.querySelector('input[name="stok_baru"]').value) || 0;
        document.getElementById('total_stok').value = currentStock + additionalStock;
    }

    // Open Edit Modal
    function openEditModal(id) {
        const entryElement = document.querySelector(`[data-entry-id="${id}"]`);
        if (entryElement) {
            const namaProduk = entryElement.querySelector('.nama-produk').textContent;
            const stokAwal = entryElement.querySelector('.stok-awal').getAttribute('data-value');
            const stokTambahan = entryElement.querySelector('.stok-tambahan').getAttribute('data-value');
            const totalStok = entryElement.querySelector('.total-stok').getAttribute('data-value');
            const kadaluarsa = entryElement.querySelector('.kadaluarsa-text').getAttribute('data-value');
            const keterangan = entryElement.querySelector('.keterangan-text').textContent;
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama_produk').value = namaProduk;
            document.getElementById('edit_stok_awal').value = stokAwal;
            document.getElementById('edit_stok_baru').value = stokTambahan;
            document.getElementById('edit_total_stok').value = totalStok;
            document.getElementById('edit_kadaluarsa').value = kadaluarsa;
            document.getElementById('edit_keterangan').value = keterangan !== '-' ? keterangan : '';
            
            document.getElementById('editModal').classList.remove('hidden');
        }
    }

    // Calculate Total Stock for Edit Modal
    function calculateEditTotalStock() {
        const stokAwal = parseInt(document.getElementById('edit_stok_awal').value) || 0;
        const stokBaru = parseInt(document.getElementById('edit_stok_baru').value) || 0;
        document.getElementById('edit_total_stok').value = stokAwal + stokBaru;
    }

    // Close Edit Modal
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Toggle Export Dropdown
    function toggleExportDropdown() {
        const dropdown = document.getElementById('exportDropdown');
        exportDropdownVisible = !exportDropdownVisible;
        
        if (exportDropdownVisible) {
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    // ========== FORM SUBMISSIONS ==========
    
    // Add Stock Form Submission
    document.getElementById('addStockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Validate kadaluarsa
        const kadaluarsa = new Date(data.kadaluarsa);
        const today = new Date();
        if (kadaluarsa < today) {
            Swal.fire('Error', 'Tanggal kadaluarsa tidak boleh kurang dari hari ini!', 'error');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menambahkan stok...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route("management.updateproduk.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({
                    title: 'Sukses!',
                    text: data.message || 'Stok berhasil ditambahkan',
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                }).then(() => {
                    closeAddStockModal();
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Gagal menambahkan stok', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
        });
    });

    // Edit Form Submission
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('edit_id').value;
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Validate kadaluarsa
        const kadaluarsa = new Date(data.kadaluarsa);
        const today = new Date();
        if (kadaluarsa < today) {
            Swal.fire('Error', 'Tanggal kadaluarsa tidak boleh kurang dari hari ini!', 'error');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Mengupdate...',
            text: 'Sedang mengupdate data stok...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`{{ route("management.updateproduk.index") }}/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-HTTP-Method-Override': 'PUT',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                stok_baru: data.stok_baru,
                kadaluarsa: data.kadaluarsa,
                keterangan: data.keterangan
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({
                    title: 'Sukses!',
                    text: data.message || 'Data stok berhasil diupdate',
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                }).then(() => {
                    closeEditModal();
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Gagal mengupdate data stok', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat mengupdate', 'error');
        });
    });

    // ========== CRUD OPERATIONS ==========
    
    // Delete Entry
    function deleteEntry(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "History stok ini akan dihapus dan stok produk akan dikurangi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang menghapus history...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`{{ route("management.updateproduk.index") }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: data.message || 'History berhasil dihapus.',
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Gagal menghapus', 'error');
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                });
            }
        });
    }

    // Confirm Clear All History
    function confirmClearAll() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Semua history stok akan dihapus PERMANEN! Tindakan ini akan mengurangi stok produk sesuai history yang dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Get all entry IDs
                const entries = document.querySelectorAll('[data-entry-id]');
                if (entries.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk dihapus', 'info');
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: `Sedang menghapus ${entries.length} history...`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Delete all entries in batch
                const entryIds = Array.from(entries).map(element => element.getAttribute('data-entry-id'));
                
                fetch(`{{ route("management.updateproduk.index") }}/batch-delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: entryIds })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: `Semua history (${entries.length}) berhasil dihapus.`,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Gagal menghapus', 'error');
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus', 'error');
                });
            }
        });
    }

    // ========== FILTER FUNCTIONS ==========
    
    function applyFilter() {
        const filterDate = document.getElementById('filterDate').value;
        const filterPeriod = document.getElementById('filterPeriod').value;
        const entries = document.querySelectorAll('[data-entry-id]');
        
        let visibleCount = 0;
        const now = new Date();
        
        entries.forEach(entry => {
            const entryDate = new Date(entry.getAttribute('data-tanggal'));
            let show = true;
            
            // Filter by specific date
            if (filterDate) {
                const filterDateObj = new Date(filterDate);
                if (entryDate.toDateString() !== filterDateObj.toDateString()) {
                    show = false;
                }
            }
            
            // Filter by period
            if (show && filterPeriod !== 'all') {
                const startOfDay = new Date(now);
                startOfDay.setHours(0, 0, 0, 0);
                
                const startOfWeek = new Date(now);
                startOfWeek.setDate(now.getDate() - now.getDay());
                startOfWeek.setHours(0, 0, 0, 0);
                
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                endOfWeek.setHours(23, 59, 59, 999);
                
                const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                endOfMonth.setHours(23, 59, 59, 999);
                
                switch(filterPeriod) {
                    case 'today':
                        if (entryDate < startOfDay) show = false;
                        break;
                    case 'week':
                        if (entryDate < startOfWeek || entryDate > endOfWeek) show = false;
                        break;
                    case 'month':
                        if (entryDate < startOfMonth || entryDate > endOfMonth) show = false;
                        break;
                }
            }
            
            if (show) {
                entry.style.display = 'block';
                visibleCount++;
            } else {
                entry.style.display = 'none';
            }
        });
        
        // Show empty state if no entries visible
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            if (visibleCount === 0 && entries.length > 0) {
                // Create temporary empty state
                const tempEmptyState = document.createElement('div');
                tempEmptyState.className = 'bg-white rounded-lg border border-gray-200 p-12 text-center';
                tempEmptyState.innerHTML = `
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-500 mb-4">Tidak ditemukan data dengan filter yang dipilih</p>
                    <button onclick="resetFilter()" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-400 to-blue-700 text-white rounded-lg hover:from-blue-500 hover:to-blue-800">
                        <i class="fas fa-redo mr-2"></i>Reset Filter
                    </button>
                `;
                
                // Remove existing temporary empty state if exists
                const existingTemp = document.querySelector('.temp-empty-state');
                if (existingTemp) existingTemp.remove();
                
                tempEmptyState.classList.add('temp-empty-state');
                document.getElementById('historyContainer').appendChild(tempEmptyState);
            } else {
                const existingTemp = document.querySelector('.temp-empty-state');
                if (existingTemp) existingTemp.remove();
            }
        }
    }

    function resetFilter() {
        document.getElementById('filterDate').value = '';
        document.getElementById('filterPeriod').value = 'all';
        
        const entries = document.querySelectorAll('[data-entry-id]');
        entries.forEach(entry => {
            entry.style.display = 'block';
        });
        
        const emptyState = document.getElementById('emptyState');
        const tempEmptyState = document.querySelector('.temp-empty-state');
        if (tempEmptyState) tempEmptyState.remove();
        
        if (emptyState && entries.length === 0) {
            emptyState.classList.remove('hidden');
        }
    }

    // ========== EXPORT FUNCTIONS ==========
    
    function exportToCSV() {
        const entries = document.querySelectorAll('[data-entry-id]');
        
        if (entries.length === 0) {
            Swal.fire('Info', 'Tidak ada data untuk diexport!', 'info');
            return;
        }
        
        let csvContent = "Tanggal,Waktu,Nama Produk,Stok Awal,Stok Tambahan,Total Stok,Kadaluarsa,Sisa Hari,Keterangan\n";
        
        entries.forEach(entry => {
            if (entry.style.display !== 'none') {
                const dateTime = entry.querySelector('.text-sm.text-gray-500').textContent.split(', ');
                const date = dateTime[1] ? dateTime[1] : '';
                const namaProduk = entry.querySelector('.nama-produk').textContent || '';
                const stokAwal = entry.querySelector('.stok-awal').textContent.replace(' pcs', '') || '0';
                const stokTambahan = entry.querySelector('.stok-tambahan').textContent.replace('+', '').replace(' pcs', '') || '0';
                const totalStok = entry.querySelector('.total-stok').textContent.replace(' pcs', '') || '0';
                const kadaluarsa = entry.querySelector('.kadaluarsa-text').textContent.split('(')[0].trim() || '';
                
                // Get remaining days info
                const remainingDaysSpan = entry.querySelector('.kadaluarsa-text span');
                const remainingDays = remainingDaysSpan ? remainingDaysSpan.textContent.replace(/[()]/g, '').trim() : '';
                
                const keterangan = entry.querySelector('.keterangan-text').textContent !== '-' ? 
                    entry.querySelector('.keterangan-text').textContent : '';
                
                csvContent += `"${date}","${namaProduk}","${stokAwal}","${stokTambahan}","${totalStok}","${kadaluarsa}","${remainingDays}","${keterangan}"\n`;
            }
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        const now = new Date();
        const dateStr = now.toISOString().split('T')[0];
        const timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '-');
        
        link.setAttribute('href', url);
        link.setAttribute('download', `riwayat_stok_${dateStr}_${timeStr}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Hide dropdown
        document.getElementById('exportDropdown').classList.add('hidden');
        exportDropdownVisible = false;
        
        Swal.fire('Sukses', 'Data berhasil diexport ke CSV!', 'success');
    }

    function exportToExcel() {
        const entries = document.querySelectorAll('[data-entry-id]');
        
        if (entries.length === 0) {
            Swal.fire('Info', 'Tidak ada data untuk diexport!', 'info');
            return;
        }
        
        // Prepare data array
        const data = [];
        const headers = ['Tanggal', 'Nama Produk', 'Stok Awal', 'Stok Tambahan', 'Total Stok', 'Kadaluarsa', 'Sisa Hari', 'Keterangan'];
        data.push(headers);
        
        entries.forEach(entry => {
            if (entry.style.display !== 'none') {
                const dateTime = entry.querySelector('.text-sm.text-gray-500').textContent.split(', ');
                const date = dateTime[1] ? dateTime[1] : '';
                const namaProduk = entry.querySelector('.nama-produk').textContent || '';
                const stokAwal = entry.querySelector('.stok-awal').textContent.replace(' pcs', '') || '0';
                const stokTambahan = entry.querySelector('.stok-tambahan').textContent.replace('+', '').replace(' pcs', '') || '0';
                const totalStok = entry.querySelector('.total-stok').textContent.replace(' pcs', '') || '0';
                const kadaluarsa = entry.querySelector('.kadaluarsa-text').textContent.split('(')[0].trim() || '';
                
                // Get remaining days info
                const remainingDaysSpan = entry.querySelector('.kadaluarsa-text span');
                const remainingDays = remainingDaysSpan ? remainingDaysSpan.textContent.replace(/[()]/g, '').trim() : '';
                
                const keterangan = entry.querySelector('.keterangan-text').textContent !== '-' ? 
                    entry.querySelector('.keterangan-text').textContent : '';
                
                data.push([date, namaProduk, stokAwal, stokTambahan, totalStok, kadaluarsa, remainingDays, keterangan]);
            }
        });
        
        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Create workbook
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Riwayat Stok");
        
        // Generate Excel file
        const now = new Date();
        const dateStr = now.toISOString().split('T')[0];
        const timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '-');
        
        XLSX.writeFile(wb, `riwayat_stok_${dateStr}_${timeStr}.xlsx`);
        
        // Hide dropdown
        document.getElementById('exportDropdown').classList.add('hidden');
        exportDropdownVisible = false;
        
        Swal.fire('Sukses', 'Data berhasil diexport ke Excel!', 'success');
    }

    function exportToPDF() {
        const entries = document.querySelectorAll('[data-entry-id]');
        
        if (entries.length === 0) {
            Swal.fire('Info', 'Tidak ada data untuk diexport!', 'info');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Membuat PDF...',
            text: 'Sedang membuat dokumen PDF...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            const pageWidth = doc.internal.pageSize.getWidth();
            
            // Add title
            doc.setFontSize(18);
            doc.setFont('helvetica', 'bold');
            doc.text('RIWAYAT STOK PRODUK', pageWidth / 2, 15, { align: 'center' });
            
            // Add date
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.text(`Dicetak pada: ${dateStr}`, pageWidth / 2, 22, { align: 'center' });
            
            // Prepare table data
            const tableData = [];
            
            entries.forEach(entry => {
                if (entry.style.display !== 'none') {
                    const dateTime = entry.querySelector('.text-sm.text-gray-500').textContent;
                    const namaProduk = entry.querySelector('.nama-produk').textContent || '';
                    const stokAwal = entry.querySelector('.stok-awal').textContent || '0';
                    const stokTambahan = entry.querySelector('.stok-tambahan').textContent || '0';
                    const totalStok = entry.querySelector('.total-stok').textContent || '0';
                    const kadaluarsa = entry.querySelector('.kadaluarsa-text').textContent.split('(')[0].trim() || '';
                    
                    tableData.push([
                        dateTime,
                        namaProduk,
                        stokAwal,
                        stokTambahan,
                        totalStok,
                        kadaluarsa
                    ]);
                }
            });
            
            // Add table
            doc.autoTable({
                head: [['Tanggal', 'Produk', 'Stok Awal', 'Stok Tambahan', 'Total Stok', 'Kadaluarsa']],
                body: tableData,
                startY: 30,
                theme: 'grid',
                headStyles: { fillColor: [59, 130, 246], textColor: 255, fontStyle: 'bold' },
                styles: { fontSize: 8, cellPadding: 2 },
                columnStyles: {
                    0: { cellWidth: 40 },
                    1: { cellWidth: 40 },
                    2: { cellWidth: 25 },
                    3: { cellWidth: 30 },
                    4: { cellWidth: 25 },
                    5: { cellWidth: 30 }
                }
            });
            
            // Add summary
            const summaryY = doc.lastAutoTable.finalY + 10;
            doc.setFontSize(10);
            doc.setFont('helvetica', 'bold');
            doc.text('Ringkasan:', 14, summaryY);
            
            doc.setFont('helvetica', 'normal');
            doc.text(`Total Entri: ${tableData.length}`, 14, summaryY + 7);
            
            // Save PDF
            const dateStrFile = now.toISOString().split('T')[0];
            const timeStrFile = now.toTimeString().split(' ')[0].replace(/:/g, '-');
            
            doc.save(`riwayat_stok_${dateStrFile}_${timeStrFile}.pdf`);
            
            Swal.close();
            
            // Hide dropdown
            document.getElementById('exportDropdown').classList.add('hidden');
            exportDropdownVisible = false;
            
            Swal.fire('Sukses', 'Data berhasil diexport ke PDF!', 'success');
        } catch (error) {
            Swal.close();
            console.error('Error generating PDF:', error);
            Swal.fire('Error', 'Gagal membuat PDF. Pastikan library tersedia.', 'error');
        }
    }

    // ========== PRINT FUNCTION ==========
    
    function printPage() {
        // Hide action buttons and filter during print
        const noPrintElements = document.querySelectorAll('.no-print');
        noPrintElements.forEach(el => {
            el.style.display = 'none';
        });
        
        // Show all entries for printing
        const entries = document.querySelectorAll('[data-entry-id]');
        entries.forEach(entry => {
            entry.style.display = 'block';
        });
        
        // Add print header
        const printHeader = document.createElement('div');
        printHeader.style.cssText = 'padding: 20px; text-align: center; background: #f8fafc; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;';
        printHeader.innerHTML = `
            <h1 style="font-size: 24px; font-weight: bold; color: #1e293b; margin-bottom: 5px;">RIWAYAT STOK PRODUK</h1>
            <p style="color: #64748b; font-size: 14px;">${new Date().toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}</p>
        `;
        
        document.body.insertBefore(printHeader, document.body.firstChild);
        
        // Print
        window.print();
        
        // Remove print header
        printHeader.remove();
        
        // Restore elements
        noPrintElements.forEach(el => {
            el.style.display = '';
        });
        
        // Restore filter state
        applyFilter();
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const addModal = document.getElementById('addStockModal');
        const editModal = document.getElementById('editModal');
        
        if (e.target === addModal) closeAddStockModal();
        if (e.target === editModal) closeEditModal();
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddStockModal();
            closeEditModal();
        }
    });
</script>
@endsection