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
        // Data from backend
        const salesData = {
            transactions: @json($transactions)
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
            restoreFilters();
        });

        // Restore filter values from URL
        function restoreFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            
            const tanggal = urlParams.get('tanggal');
            const metode = urlParams.get('metode');
            const kasir = urlParams.get('kasir');
            
            if (tanggal) document.getElementById('filterDate').value = tanggal;
            if (metode) document.getElementById('filterPayment').value = metode;
            if (kasir) document.getElementById('filterCashier').value = kasir;
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
            const tanggal = document.getElementById('filterDate').value;
            const metode = document.getElementById('filterPayment').value;
            const kasir = document.getElementById('filterCashier').value;
            
            // Build URL with query parameters
            const params = new URLSearchParams();
            if (tanggal) params.append('tanggal', tanggal);
            if (metode) params.append('metode', metode);
            if (kasir) params.append('kasir', kasir);
            
            // Reload page with filters
            window.location.href = '{{ route("kasir.laporan.index") }}?' + params.toString();
        }

        // Reset filters
        function resetFilters() {
            window.location.href = '{{ route("kasir.laporan.index") }}';
        }

        // Render transactions table
        function renderTransactions() {
            const tbody = document.getElementById('transactionTableBody');
            let transactions = salesData.transactions;

            // Apply search filter (client-side)
            if (currentFilters.search) {
                transactions = transactions.filter(t => 
                    t.invoice.toLowerCase().includes(currentFilters.search.toLowerCase()) ||
                    t.products.toLowerCase().includes(currentFilters.search.toLowerCase())
                );
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