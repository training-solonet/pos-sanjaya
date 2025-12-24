@extends('layouts.kasir.index')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    
    /* Real-time animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

<!-- Mobile Overlay -->
<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="px-4 lg:px-6 py-4">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-xs text-gray-500 italic">
                            <i class="fas fa-info-circle mr-1"></i>Filter otomatis diterapkan saat mengubah pilihan
                        </span>
                    </div>
                    <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                        <!-- Filter by Date -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Tanggal
                            </label>
                            <input type="date" id="filterDate" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>

                        <!-- Filter by Payment Method -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-credit-card mr-2 text-gray-400"></i>Metode Pembayaran
                            </label>
                            <select id="filterPayment" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Semua Metode</option>
                                <option value="tunai">Tunai</option>
                                <option value="kartu">Kartu</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <!-- Filter by Cashier -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-gray-400"></i>Kasir
                            </label>
                            <select id="filterCashier" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Semua Kasir</option>
                                @foreach($kasirList as $kasir)
                                <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <button onclick="applyFilters()" 
                                    class="px-6 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:opacity-90 flex items-center space-x-2 transition-all">
                                <i class="fas fa-filter text-sm"></i>
                                <span>Terapkan</span>
                            </button>
                            <button onclick="resetFilters()" 
                                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center space-x-2 transition-all">
                                <i class="fas fa-redo text-sm"></i>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Transactions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 lg:px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
                            <p class="text-sm text-gray-500">
                                Daftar semua transaksi hari ini
                                <span id="realTimeIndicator" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span id="lastUpdateTime" class="ml-2 text-xs text-gray-400"></span>
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="text" id="searchTransaction" placeholder="Cari ID Transaksi..." 
                                   onkeyup="handleSearch(this.value)"
                                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent w-full lg:w-auto">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="px-4 lg:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Menampilkan <span id="showingCount">0</span> dari <span id="totalCount">0</span> transaksi
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Data from backend
        const salesData = {
            transactions: @json($transactions)
        };
        
        // Debug: Log initial data
        console.log('Initial transactions:', salesData.transactions);

        // Filter state
        let currentFilters = {
            search: '',
            date: '',
            payment: '',
            cashier: ''
        };

        // Real-time update settings
        let autoRefreshEnabled = true;
        let refreshInterval = null;
        const REFRESH_INTERVAL_MS = 5000; // 5 seconds
        let isInitialLoad = true; // Flag untuk initial load

        // Fungsi handle search yang dipanggil langsung dari input
        window.handleSearch = function(value) {
            console.log('=== HANDLE SEARCH CALLED ===');
            console.log('Search value:', value);
            console.log('Before - currentFilters.search:', currentFilters.search);
            
            currentFilters.search = value;
            
            console.log('After - currentFilters.search:', currentFilters.search);
            console.log('Total transactions:', salesData.transactions.length);
            
            // Debug: tampilkan sample data
            if (salesData.transactions.length > 0) {
                console.log('Sample transaction:', salesData.transactions[0]);
            }
            
            renderTransactions();
            console.log('=== RENDER COMPLETE ===');
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            displayCurrentDate();
            setTodayDate();
            currentFilters.date = document.getElementById('filterDate').value;
            renderTransactions();
            setupFilterListeners();
            restoreFilters();
            updateLastUpdateTime();
            updateRefreshButton();
            startAutoRefresh();
        });

        // Restore filter values from URL
        function restoreFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            
            const tanggal = urlParams.get('tanggal');
            const metode = urlParams.get('metode');
            const kasir = urlParams.get('kasir');
            
            if (tanggal) {
                document.getElementById('filterDate').value = tanggal;
                currentFilters.date = tanggal;
            }
            if (metode) {
                document.getElementById('filterPayment').value = metode;
                currentFilters.payment = metode;
            }
            if (kasir) {
                document.getElementById('filterCashier').value = kasir;
                currentFilters.cashier = kasir;
            }
            
            // Fetch data with restored filters
            if (tanggal || metode || kasir) {
                fetchTransactions();
            }
        }

        // Set today's date in filter
        function setTodayDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            document.getElementById('filterDate').value = `${year}-${month}-${day}`;
        }

        // Display current date
        function displayCurrentDate() {
            const currentDateEl = document.getElementById('currentDate');
            if (currentDateEl) {
                const today = new Date();
                const dateOptions = { day: 'numeric', month: 'short', year: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit' };
                const dateStr = today.toLocaleDateString('id-ID', dateOptions);
                const timeStr = today.toLocaleTimeString('id-ID', timeOptions);
                currentDateEl.textContent = `${dateStr}, ${timeStr}`;
            }
        }
        
        // Update date every minute
        setInterval(displayCurrentDate, 60000);

        // Apply filters
        function applyFilters() {
            const tanggal = document.getElementById('filterDate').value;
            const metode = document.getElementById('filterPayment').value;
            const kasir = document.getElementById('filterCashier').value;
            
            // Update current filters
            currentFilters.date = tanggal;
            currentFilters.payment = metode;
            currentFilters.cashier = kasir;
            
            // Fetch new data with filters
            fetchTransactions();
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('filterDate').value = '';
            document.getElementById('filterPayment').value = '';
            document.getElementById('filterCashier').value = '';
            document.getElementById('searchTransaction').value = '';
            
            currentFilters = {
                search: '',
                date: '',
                payment: '',
                cashier: ''
            };
            
            setTodayDate();
            currentFilters.date = document.getElementById('filterDate').value;
            fetchTransactions();
        }

        // Fetch transactions from API
        async function fetchTransactions() {
            try {
                // Show loading state   
                showLoadingState();
                
                const params = new URLSearchParams();
                if (currentFilters.date) params.append('tanggal', currentFilters.date);
                if (currentFilters.payment) params.append('metode', currentFilters.payment);
                if (currentFilters.cashier) params.append('kasir', currentFilters.cashier);
                if (currentFilters.search) params.append('search', currentFilters.search);
                
                console.log('Fetching from API with filters:', currentFilters);
                const url = `{{ route('kasir.laporan.api.transactions') }}?${params.toString()}`;
                console.log('API URL:', url);
                
                const response = await fetch(url);
                const data = await response.json();
                console.log('API Response:', data);
                
                if (data.success) {
                    const oldCount = salesData.transactions.length;
                    const newCount = data.transactions.length;
                    
                    salesData.transactions = data.transactions;
                    renderTransactions();
                    updateLastUpdateTime();
                    
                    // Hide loading state
                    hideLoadingState();
                    
                    // Flash indicator
                    flashUpdateIndicator();
                    
                    // Show notification if new transactions (only for auto-refresh, not manual filter)
                    if (newCount > oldCount && oldCount > 0 && autoRefreshEnabled) {
                        showNewTransactionNotification(newCount - oldCount);
                    }
                }
            } catch (error) {
                console.error('Error fetching transactions:', error);
                hideLoadingState();
                showErrorNotification();
            }
        }
        
        // Show new transaction notification
        function showNewTransactionNotification(count) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 animate-fade-in';
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${count} transaksi baru ditambahkan</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 500);
            }, 3000);
        }
        
        // Show error notification
        function showErrorNotification() {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 animate-fade-in';
            notification.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>Gagal memuat data transaksi</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 500);
            }, 3000);
        }

        // Show loading state
        function showLoadingState() {
            const tbody = document.getElementById('transactionTableBody');
            const showingCount = document.getElementById('showingCount');
            const totalCount = document.getElementById('totalCount');
            
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <i class="fas fa-spinner fa-spin text-3xl text-green-500"></i>
                            <p class="text-sm text-gray-500">Memuat data transaksi...</p>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Hide loading state
        function hideLoadingState() {
            // Loading state will be replaced by actual data in renderTransactions()
        }

        // Start auto refresh
        function startAutoRefresh() {
            if (autoRefreshEnabled && !refreshInterval) {
                refreshInterval = setInterval(() => {
                    fetchTransactions();
                }, REFRESH_INTERVAL_MS);
                updateRefreshButton();
            }
        }

        // Stop auto refresh
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
                updateRefreshButton();
            }
        }

        // Toggle auto refresh
        function toggleAutoRefresh() {
            autoRefreshEnabled = !autoRefreshEnabled;
            
            if (autoRefreshEnabled) {
                startAutoRefresh();
                fetchTransactions(); // Immediate refresh
            } else {
                stopAutoRefresh();
            }
        }

        // Update refresh button state
        function updateRefreshButton() {
            const btn = document.getElementById('autoRefreshBtn');
            const icon = document.getElementById('refreshIcon');
            const text = document.getElementById('refreshText');
            
            if (autoRefreshEnabled && refreshInterval) {
                btn.className = 'px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 flex items-center space-x-2 transition-all';
                icon.className = 'fas fa-sync-alt text-sm animate-spin';
                text.textContent = 'Auto';
            } else {
                btn.className = 'px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center space-x-2 transition-all';
                icon.className = 'fas fa-sync-alt text-sm';
                text.textContent = 'Manual';
            }
        }

        // Update last update time
        function updateLastUpdateTime() {
            const lastUpdateEl = document.getElementById('lastUpdateTime');
            if (lastUpdateEl) {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                lastUpdateEl.textContent = `Update terakhir: ${timeStr}`;
            }
        }

        // Flash update indicator
        function flashUpdateIndicator() {
            const indicator = document.getElementById('realTimeIndicator');
            if (indicator) {
                indicator.classList.add('ring-2', 'ring-green-300');
                setTimeout(() => {
                    indicator.classList.remove('ring-2', 'ring-green-300');
                }, 300);
            }
        }

        // Render transactions table
        function renderTransactions() {
            console.log('=== RENDER TRANSACTIONS CALLED ===');
            console.log('Total transactions in salesData:', salesData.transactions.length);
            console.log('Current search filter:', currentFilters.search);
            
            const tbody = document.getElementById('transactionTableBody');
            
            if (!tbody) {
                console.error('Table body not found!');
                return;
            }
            
            let transactions = [...salesData.transactions]; // Copy array
            console.log('Copied transactions:', transactions.length);

            // Apply search filter (client-side)
            if (currentFilters.search && currentFilters.search.trim() !== '') {
                const searchTerm = currentFilters.search.trim();
                console.log('Applying search filter with term:', searchTerm);
                
                transactions = transactions.filter(t => {
                    // Convert invoice to string untuk perbandingan
                    const invoiceStr = String(t.invoice);
                    const invoiceMatch = invoiceStr.includes(searchTerm);
                    
                    if (invoiceMatch) {
                        console.log('Match found - Invoice:', t.invoice);
                    }
                    
                    return invoiceMatch;
                });
                
                console.log('After filter - transactions count:', transactions.length);
            }

            // Add fade-in animation for new rows (hanya jika bukan initial load)
            const currentCount = tbody.querySelectorAll('tr').length;
            const newCount = transactions.length;
            const hasNewData = !isInitialLoad && newCount > currentCount;

            if (transactions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                <p class="text-sm text-gray-500">Tidak ada transaksi ditemukan</p>
                                <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian</p>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = transactions.map((t, index) => `
                    <tr class="hover:bg-gray-50 transition-all ${hasNewData && index === 0 ? 'animate-fade-in bg-green-50' : ''}">
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">${t.invoice}</span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">${t.time}</span>
                        </td>
                        <td class="px-4 lg:px-6 py-4">
                            <span class="text-sm text-gray-900">${t.products}</span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">${t.quantity} item</span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">${formatCurrency(t.total)}</span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ${getPaymentBadgeClass(t.payment)}">
                                ${t.payment}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">${t.cashier}</span>
                        </td>
                    </tr>
                `).join('');
            }

            document.getElementById('showingCount').textContent = transactions.length;
            document.getElementById('totalCount').textContent = salesData.transactions.length;

            // Remove highlight after animation
            if (hasNewData) {
                setTimeout(() => {
                    const firstRow = tbody.querySelector('tr:first-child');
                    if (firstRow) {
                        firstRow.classList.remove('bg-green-50');
                    }
                }, 2000);
            }

            // Set initial load flag to false after first render
            if (isInitialLoad) {
                isInitialLoad = false;
            }
        }

        // Setup filter listeners for real-time changes
        function setupFilterListeners() {
            // Date filter change
            const dateInput = document.getElementById('filterDate');
            dateInput.addEventListener('change', (e) => {
                console.log('Date changed to:', e.target.value);
                currentFilters.date = e.target.value;
                fetchTransactions();
            });
            
            // Payment method filter change
            const paymentSelect = document.getElementById('filterPayment');
            paymentSelect.addEventListener('change', (e) => {
                console.log('Payment method changed to:', e.target.value);
                currentFilters.payment = e.target.value;
                fetchTransactions();
            });
            
            // Cashier filter change
            const cashierSelect = document.getElementById('filterCashier');
            cashierSelect.addEventListener('change', (e) => {
                console.log('Cashier changed to:', e.target.value);
                currentFilters.cashier = e.target.value;
                fetchTransactions();
            });
        }

        // Utility functions
        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function getPaymentBadgeClass(payment) {
            const classes = {
                'Tunai': 'bg-green-100 text-green-800',
                'Kartu': 'bg-blue-100 text-blue-800',
                'Transfer': 'bg-purple-100 text-purple-800',
                'Qris': 'bg-orange-100 text-orange-800',
                'EDC': 'bg-blue-100 text-blue-800',
                'QRIS': 'bg-purple-100 text-purple-800'
            };
            return classes[payment] || 'bg-gray-100 text-gray-800';
        }

        // Print report
        function printReport() {
            window.print();
        }

        // Export to Excel (simplified version - in production use a library like SheetJS)
        function exportToExcel() {
            let csv = 'ID,Waktu,Produk,Jumlah,Total,Pembayaran,Kasir\n';
            salesData.transactions.forEach(t => {
                csv += `${t.invoice},${t.time},"${t.products}",${t.quantity},${t.total},${t.payment},${t.cashier}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `laporan-penjualan-${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                aside, button { display: none !important; }
                main { margin: 0 !important; padding: 20px !important; }
                .no-print { display: none !important; }
            }
        `;
        document.head.appendChild(style);
    </script>
</main>
@endsection