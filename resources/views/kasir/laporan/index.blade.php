@extends('layouts.kasir.index')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>

<!-- Mobile Overlay -->
<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Main Content -->
<main class="flex-1 w-full lg:w-auto overflow-x-hidden">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Date Info -->
                    <div class="flex items-center space-x-4 px-4 py-2 bg-gray-50 rounded-xl">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Laporan Penjualan</p>
                            <p class="text-xs text-gray-500" id="currentDate"></p>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-2">
                        <button onclick="printReport()" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-xl flex items-center gap-2 hover:bg-blue-200 transition-colors" title="Cetak Laporan">
                            <i class="fas fa-print text-sm"></i>
                            <span class="hidden sm:inline text-sm font-medium">Print</span>
                        </button>
                        <button onclick="exportToExcel()" class="px-4 py-2 bg-green-100 text-green-600 rounded-xl flex items-center gap-2 hover:bg-green-200 transition-colors" title="Export Excel">
                            <i class="fas fa-file-excel text-sm"></i>
                            <span class="hidden sm:inline text-sm font-medium">Excel</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 lg:p-8">

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="px-4 lg:px-6 py-4">
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
                                <option value="Tunai">Tunai</option>
                                <option value="EDC">EDC</option>
                                <option value="QRIS">QRIS</option>
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
                                <option value="Kasir 1">Kasir 1</option>
                                <option value="Kasir 2">Kasir 2</option>
                                <option value="Admin">Admin</option>
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
                            <p class="text-sm text-gray-500">Daftar semua transaksi hari ini</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="text" id="searchTransaction" placeholder="Cari transaksi..." 
                                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent w-full lg:w-auto">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Invoice</th>
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

    <!-- Toggle Sidebar Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

    <script>
        // Sample data - in production, this would come from a database
        const salesData = {
            transactions: [
                { invoice: 'INV-001', time: '08:15', products: 'Roti Tawar x2, Teh Botol x1', quantity: 3, total: 22000, payment: 'Tunai', cashier: 'Kasir 1' },
                { invoice: 'INV-002', time: '08:45', products: 'Croissant x3', quantity: 3, total: 45000, payment: 'QRIS', cashier: 'Kasir 1' },
                { invoice: 'INV-003', time: '09:20', products: 'Roti Coklat x2, Air Mineral x2', quantity: 4, total: 30000, payment: 'Tunai', cashier: 'Kasir 1' },
                { invoice: 'INV-004', time: '10:05', products: 'Donat Coklat x5', quantity: 5, total: 35000, payment: 'EDC', cashier: 'Kasir 1' },
                { invoice: 'INV-005', time: '10:30', products: 'Pain au Chocolat x2, Teh Botol x2', quantity: 4, total: 44000, payment: 'Tunai', cashier: 'Kasir 1' },
                { invoice: 'INV-006', time: '11:15', products: 'Roti Tawar x3, Jus Botol x1', quantity: 4, total: 35000, payment: 'QRIS', cashier: 'Kasir 1' },
                { invoice: 'INV-007', time: '11:45', products: 'Bagel x2, Soda Kaleng x2', quantity: 4, total: 36000, payment: 'Tunai', cashier: 'Kasir 1' },
                { invoice: 'INV-008', time: '12:30', products: 'Brioche x1, Croissant x2, Teh Botol x3', quantity: 6, total: 66000, payment: 'EDC', cashier: 'Kasir 1' },
                { invoice: 'INV-009', time: '13:10', products: 'Donat Isi x4, Air Mineral x2', quantity: 6, total: 42000, payment: 'Tunai', cashier: 'Kasir 1' },
                { invoice: 'INV-010', time: '14:00', products: 'Roti Coklat x3, Teh Botol x3', quantity: 6, total: 54000, payment: 'QRIS', cashier: 'Kasir 1' },
                { invoice: 'INV-011', time: '14:45', products: 'Croissant x2, Jus Botol x1', quantity: 3, total: 41000, payment: 'Tunai', cashier: 'Kasir 1' },
                { invoice: 'INV-012', time: '15:20', products: 'Roti Tawar x4, Air Mineral x4', quantity: 8, total: 44000, payment: 'EDC', cashier: 'Kasir 1' },
            ]
        };

        // Filter state
        let currentFilters = {
            search: '',
            date: '',
            payment: '',
            cashier: ''
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            displayCurrentDate();
            setTodayDate();
            renderTransactions();
            setupSearch();
        });

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
            const today = new Date();
            const dateOptions = { day: 'numeric', month: 'short', year: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit' };
            const dateStr = today.toLocaleDateString('id-ID', dateOptions);
            const timeStr = today.toLocaleTimeString('id-ID', timeOptions);
            document.getElementById('currentDate').textContent = `${dateStr}, ${timeStr}`;
        }
        
        // Update date every minute
        setInterval(displayCurrentDate, 60000);

        // Apply filters
        function applyFilters() {
            currentFilters.date = document.getElementById('filterDate').value;
            currentFilters.payment = document.getElementById('filterPayment').value;
            currentFilters.cashier = document.getElementById('filterCashier').value;
            renderTransactions();
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('filterDate').value = '';
            document.getElementById('filterPayment').value = '';
            document.getElementById('filterCashier').value = '';
            currentFilters = {
                search: currentFilters.search,
                date: '',
                payment: '',
                cashier: ''
            };
            setTodayDate();
            renderTransactions();
        }

        // Render transactions table
        function renderTransactions() {
            const tbody = document.getElementById('transactionTableBody');
            let transactions = salesData.transactions;

            // Apply search filter
            if (currentFilters.search) {
                transactions = transactions.filter(t => 
                    t.invoice.toLowerCase().includes(currentFilters.search.toLowerCase()) ||
                    t.products.toLowerCase().includes(currentFilters.search.toLowerCase())
                );
            }

            // Apply payment filter
            if (currentFilters.payment) {
                transactions = transactions.filter(t => t.payment === currentFilters.payment);
            }

            // Apply cashier filter
            if (currentFilters.cashier) {
                transactions = transactions.filter(t => t.cashier === currentFilters.cashier);
            }

            tbody.innerHTML = transactions.map(t => `
                <tr class="hover:bg-gray-50">
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

            document.getElementById('showingCount').textContent = transactions.length;
            document.getElementById('totalCount').textContent = salesData.transactions.length;
        }

        // Setup search
        function setupSearch() {
            const searchInput = document.getElementById('searchTransaction');
            searchInput.addEventListener('input', (e) => {
                currentFilters.search = e.target.value;
                renderTransactions();
            });
        }

        // Utility functions
        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function getPaymentBadgeClass(payment) {
            const classes = {
                'Tunai': 'bg-green-100 text-green-800',
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
            let csv = 'No Invoice,Waktu,Produk,Jumlah,Total,Pembayaran,Kasir\n';
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