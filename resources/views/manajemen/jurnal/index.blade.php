@extends('layouts.manajemen.index')

@section('content')
    <!-- Page Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-2xl font-bold text-gray-900">Jurnal Harian</h2>
                <div class="flex space-x-2">
                    <input type="date" id="filterDate" class="px-3 py-2 border border-gray-300 rounded-lg"
                        value="{{ date('Y-m-d') }}">
                    <button onclick="exportData()"
                        class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card Total Pemasukan -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Total Pemasukan</h3>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p id="summaryTotalRevenue" class="text-3xl font-bold text-green-600">Rp 0</p>
                        <p id="revenueCount" class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-receipt mr-2"></i>
                            <span id="revenueCountText">0 transaksi hari ini</span>
                        </p>
                    </div>
                </div>

                <!-- Card Total Pengeluaran -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Total Pengeluaran</h3>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p id="summaryTotalExpense" class="text-3xl font-bold text-red-600">Rp 0</p>
                        <p id="expenseCount" class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-calendar-day mr-2"></i>
                            <span id="expenseCountText">0 transaksi hari ini</span>
                        </p>
                    </div>
                </div>

                <!-- Card Saldo Bersih -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wallet text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Saldo Bersih</h3>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p id="summaryNetBalance" class="text-3xl font-bold text-blue-600">Rp 0</p>
                        <p class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-chart-line mr-2"></i>
                            Pemasukan - Pengeluaran
                        </p>
                    </div>
                </div>
            </div>

            <!-- Jurnal Transaksi Section -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6 border-b">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Jurnal Transaksi</h3>
                            <p class="text-sm text-gray-500 mt-1">Catatan pemasukan dan pengeluaran</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <!-- TOMBOL TAMBAH TRANSAKSI YANG DISATUKAN -->
                            <button onclick="openTransactionModal()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                Tambah Transaksi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="p-6 border-b bg-gray-50">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Jenis</label>
                            <select id="filterType" onchange="filterTransactions()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                <option value="semua">Semua Transaksi</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                            <select id="filterCategory" onchange="filterTransactions()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                <option value="semua">Semua Kategori</option>
                                <option value="Penjualan">Penjualan</option>
                                <option value="Bahan Baku">Bahan Baku</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Utilitas">Utilitas</option>
                                <option value="Transportasi">Transportasi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                            <input type="text" id="searchInput" onkeyup="filterTransactions()"
                                placeholder="Cari keterangan..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="transactionTable" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="transactionTableBody">
                            <!-- Data akan di-load via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer Summary -->
                <div class="p-6 border-t bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total Pemasukan:</span>
                            <span id="footerTotalRevenue" class="text-lg font-bold text-green-600">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total Pengeluaran:</span>
                            <span id="footerTotalExpense" class="text-lg font-bold text-red-600">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Saldo Bersih:</span>
                            <span id="footerNetBalance" class="text-lg font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tambah/Edit Transaksi -->
    <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tambah Transaksi</h3>
                        <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <form id="transactionForm" class="p-6 space-y-4">
                    <input type="hidden" id="transactionId" value="">
                    <input type="hidden" id="transactionType" value="">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="transactionDate"
                            class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                        <select name='jenis' id="transactionTypeSelect"
                            class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"
                            required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="transactionCategory"
                            class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"
                            required>
                            <!-- Options will be dynamically populated -->
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="transactionDescription" rows="3" placeholder="Contoh: Penjualan roti coklat dan donat"
                            class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"
                            required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                        <input type="number" id="transactionAmount" placeholder="0" min="1"
                            class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"
                            required>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeTransactionModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" id="submitButton"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    // Categories configuration
    const categories = {
      pemasukan: [
        { value: 'Penjualan', label: 'Penjualan' },
        { value: 'lainnya', label: 'Lainnya' }
      ],
      pengeluaran: [
        { value: 'Bahan Baku', label: 'Bahan Baku' },
        { value: 'Operasional', label: 'Operasional' },
        { value: 'Utilitas', label: 'Utilitas (Listrik, Air, dll)' },
        { value: 'Transportasi', label: 'Transportasi' },
        { value: 'lainnya', label: 'Lainnya' }
      ]
    };

    // Fungsi untuk format mata uang Rupiah dengan titik dan dua desimal
    function formatCurrency(amount) {
        // Pastikan amount adalah number
        const num = parseFloat(amount) || 0;
        
        // Format dengan Intl.NumberFormat untuk konsistensi
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            useGrouping: true
        }).format(num);
    }

    // Fungsi untuk menampilkan Rupiah dengan format yang benar
    function formatRupiah(amount) {
        return `Rp ${formatCurrency(amount)}`;
    }

    let currentEditId = null;
    let allTransactions = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 60000);
      
      // Set today's date as default
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('transactionDate').value = today;
      
      // Load initial data
      loadTransactions();
      updateSummary();

      // Add event listener for transaction type change
      document.getElementById('transactionTypeSelect').addEventListener('change', function() {
        populateCategories(this.value);
      });

      // Initialize mobile state
      if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        if (sidebar) {
          sidebar.classList.add('-translate-x-full');
        }
        if (overlay) {
          overlay.classList.add('hidden');
        }
      }
    });

    // Update date time
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
      const dateTimeString = now.toLocaleDateString('id-ID', options);
      const dateTimeElement = document.getElementById('currentDateTime');
      if (dateTimeElement) {
        dateTimeElement.textContent = dateTimeString;
      }
    }

    // Load transactions from server - OPTIMIZED VERSION
    async function loadTransactions() {
      try {
        const filterDate = document.getElementById('filterDate').value;
        const filterType = document.getElementById('filterType').value;
        const filterCategory = document.getElementById('filterCategory').value;
        const searchInput = document.getElementById('searchInput').value;

        // Build query parameters
        const params = new URLSearchParams({
          data: '1',
          date: filterDate
        });

        if (filterType && filterType !== 'semua') {
          params.append('jenis', filterType);
        }

        if (filterCategory && filterCategory !== 'semua') {
          params.append('kategori', filterCategory);
        }

        if (searchInput) {
          params.append('search', searchInput);
        }

        const response = await fetch(`/management/jurnal?${params}`);
        
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        allTransactions = data;
        renderTransactions(allTransactions);
        updateSummary();
      } catch (error) {
        console.error('Error loading transactions:', error);
        showErrorMessage('Gagal memuat data transaksi');
      }
    }

    // Render transactions to table
    function renderTransactions(transactions) {
      const tbody = document.getElementById('transactionTableBody');
      if (!tbody) return;

      tbody.innerHTML = '';

      if (transactions.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
              Tidak ada data transaksi
            </td>
          </tr>
        `;
        return;
      }

      transactions.forEach(transaction => {
        const badgeClass = transaction.jenis === 'pemasukan' ? 
          'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const iconClass = transaction.jenis === 'pemasukan' ? 'fa-arrow-up' : 'fa-arrow-down';
        const amountClass = transaction.jenis === 'pemasukan' ? 'text-green-600' : 'text-red-600';
        const amountPrefix = transaction.jenis === 'pemasukan' ? '+' : '-';

        const row = document.createElement('tr');
        row.setAttribute('data-type', transaction.jenis);
        row.setAttribute('data-category', transaction.kategori);
        row.setAttribute('data-id', transaction.id);
        
        row.innerHTML = `
          <td class="px-6 py-4 text-sm text-gray-500">${formatDate(transaction.tgl)}</td>
          <td class="px-6 py-4 text-sm">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
              <i class="fas ${iconClass} mr-1"></i>
              ${transaction.jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran'}
            </span>
          </td>
          <td class="px-6 py-4 text-sm text-gray-900">${transaction.kategori}</td>
          <td class="px-6 py-4 text-sm text-gray-900">${transaction.keterangan}</td>
          <td class="px-6 py-4 text-sm font-medium ${amountClass}">
            ${amountPrefix} ${formatRupiah(transaction.nominal)}
          </td>
          <td class="px-6 py-4">
            <button onclick="editTransaction(${transaction.id})" class="text-green-600 hover:text-green-800 mr-2">
              <i class="fas fa-edit"></i>
            </button>
            <button onclick="deleteTransaction(${transaction.id})" class="text-red-500 hover:text-red-700">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;
      
        tbody.appendChild(row);
      });
    }

    // Update summary from server - OPTIMIZED VERSION
    async function updateSummary() {
      try {
        const filterDate = document.getElementById('filterDate').value;
        
        const response = await fetch(`/management/jurnal?summary=1&date=${filterDate}`);
        
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        // Update summary cards dengan format baru
        document.getElementById('summaryTotalRevenue').textContent = 
          formatRupiah(data.total_revenue);
        document.getElementById('summaryTotalExpense').textContent = 
          formatRupiah(data.total_expense);
        document.getElementById('summaryNetBalance').textContent = 
          formatRupiah(data.net_balance);
        
        document.getElementById('revenueCountText').textContent = 
          `${data.revenue_count} transaksi hari ini`;
        document.getElementById('expenseCountText').textContent = 
          `${data.expense_count} transaksi hari ini`;
        
        // Update footer dengan format baru
        document.getElementById('footerTotalRevenue').textContent = 
          formatRupiah(data.total_revenue);
        document.getElementById('footerTotalExpense').textContent = 
          formatRupiah(data.total_expense);
        document.getElementById('footerNetBalance').textContent = 
          formatRupiah(data.net_balance);
        
        // Update net balance color
        const netBalanceElements = [
          document.getElementById('summaryNetBalance'),
          document.getElementById('footerNetBalance')
        ];
        
        netBalanceElements.forEach(el => {
          if (el) {
            el.classList.remove('text-blue-600', 'text-green-600', 'text-red-600');
            if (data.net_balance > 0) {
              el.classList.add('text-green-600');
            } else if (data.net_balance < 0) {
              el.classList.add('text-red-600');
            } else {
              el.classList.add('text-blue-600');
            }
          }
        });
      } catch (error) {
        console.error('Error loading summary:', error);
      }
    }

    // Open transaction modal - DIUBAH: tanpa parameter type
    function openTransactionModal() {
      currentEditId = null;
      const modal = document.getElementById('transactionModal');
      const modalTitle = document.getElementById('modalTitle');
      const typeSelect = document.getElementById('transactionTypeSelect');
      const submitButton = document.getElementById('submitButton');
      
      if (!modal || !modalTitle || !typeSelect || !submitButton) return;
      
      // Set default type to pemasukan
      typeSelect.value = 'pemasukan';
      
      // Update modal title and button
      modalTitle.textContent = 'Tambah Transaksi';
      submitButton.className = 'flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700';
      submitButton.textContent = 'Simpan Transaksi';
      
      // Populate categories based on default type
      populateCategories('pemasukan');
      
      // Reset form and set today's date
      document.getElementById('transactionForm').reset();
      document.getElementById('transactionDate').value = new Date().toISOString().split('T')[0];
      document.getElementById('transactionId').value = '';
      
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    // Edit transaction - OPTIMIZED VERSION
    async function editTransaction(id) {
      try {
        // Gunakan route resource show
        const response = await fetch(`/management/jurnal/${id}`);
        
        if (!response.ok) {
          throw new Error('Transaksi tidak ditemukan');
        }
        
        const transaction = await response.json();
        
        if (transaction.error) {
          throw new Error(transaction.error);
        }

        currentEditId = id;
        const modal = document.getElementById('transactionModal');
        const modalTitle = document.getElementById('modalTitle');
        const typeSelect = document.getElementById('transactionTypeSelect');
        const submitButton = document.getElementById('submitButton');
        
        if (!modal || !modalTitle || !typeSelect || !submitButton) return;
        
        // Update modal title and button
        modalTitle.textContent = 'Edit Transaksi';
        submitButton.className = 'flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700';
        submitButton.textContent = 'Update Transaksi';
        
        // Populate categories
        populateCategories(transaction.jenis);
        
        // Fill form with existing data
        document.getElementById('transactionId').value = transaction.id;
        document.getElementById('transactionDate').value = transaction.tgl.split('T')[0];
        document.getElementById('transactionTypeSelect').value = transaction.jenis;
        document.getElementById('transactionCategory').value = transaction.kategori;
        document.getElementById('transactionDescription').value = transaction.keterangan;
        document.getElementById('transactionAmount').value = transaction.nominal;
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
      } catch (error) {
        console.error('Error loading transaction:', error);
        showErrorMessage('Gagal memuat data transaksi');
      }
    }

    // Delete transaction - OPTIMIZED VERSION
    async function deleteTransaction(id) {
      if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        return;
      }

      try {
        const response = await fetch(`/management/jurnal/${id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();
        
        if (data.success) {
          showSuccessMessage(data.message);
          await loadTransactions();
        } else {
          showErrorMessage(data.message);
        }
      } catch (error) {
        console.error('Error deleting transaction:', error);
        showErrorMessage('Gagal menghapus transaksi');
      }
    }

    // Handle form submission - OPTIMIZED VERSION
    document.getElementById('transactionForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = {
        tgl: document.getElementById('transactionDate').value,
        jenis: document.getElementById('transactionTypeSelect').value,
        kategori: document.getElementById('transactionCategory').value,
        keterangan: document.getElementById('transactionDescription').value,
        nominal: document.getElementById('transactionAmount').value
      };

      // Validation
      if (!formData.tgl || !formData.kategori || !formData.keterangan || !formData.nominal) {
        alert('Semua field harus diisi!');
        return;
      }

      if (formData.nominal <= 0) {
        alert('Nominal harus lebih dari 0!');
        return;
      }

      const transactionId = document.getElementById('transactionId').value;
      const url = transactionId ? 
        `/management/jurnal/${transactionId}` : 
        '/management/jurnal';
      
      const method = transactionId ? 'PUT' : 'POST';

      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            ...(transactionId && { 'X-HTTP-Method-Override': 'PUT' })
          },
          body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
          closeTransactionModal();
          showSuccessMessage(data.message);
          await loadTransactions();
        } else {
          showErrorMessage(data.message);
        }
      } catch (error) {
        console.error('Error saving transaction:', error);
        showErrorMessage('Gagal menyimpan transaksi');
      }
    });

    // Filter transactions
    function filterTransactions() {
      loadTransactions(); // Sekarang semua filter dilakukan di server
    }

    // Helper functions
    function populateCategories(type) {
      const categorySelect = document.getElementById('transactionCategory');
      if (!categorySelect) return;
      
      categorySelect.innerHTML = '';
      
      categories[type].forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.value;
        option.textContent = cat.label;
        categorySelect.appendChild(option);
      });
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric' 
      });
    }

    function closeTransactionModal() {
      const modal = document.getElementById('transactionModal');
      if (modal) {
        modal.classList.add('hidden');
      }
      document.body.style.overflow = '';
      document.getElementById('transactionForm').reset();
      currentEditId = null;
    }

    function showSuccessMessage(message) {
      showNotification(message, 'green');
    }

    function showErrorMessage(message) {
      showNotification(message, 'red');
    }

    function showNotification(message, color) {
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 bg-${color}-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
      notification.innerHTML = `
        <div class="flex items-center">
          <i class="fas fa-${color === 'green' ? 'check' : 'exclamation'}-circle mr-2"></i>
          ${message}
        </div>
      `;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
          }
        }, 300);
      }, 3000);
    }

    // Event listener for date filter
    document.getElementById('filterDate').addEventListener('change', function() {
      loadTransactions();
    });

    // Export function
    function exportData() {
      const filterDate = document.getElementById('filterDate').value;
      // Anda bisa menambahkan fungsi export di controller jika diperlukan
      alert('Fitur export akan segera tersedia');
    }

    // Close modal when clicking outside
    document.getElementById('transactionModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeTransactionModal();
      }
    });
  </script>
@endsection