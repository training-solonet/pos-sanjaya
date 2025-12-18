@extends('layouts.kasir.index')

@section('content')
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="content flex-1 lg:flex-1">                    

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Jurnal Harian</h2>
                    <div class="flex space-x-2">
                        <form action="{{ route('kasir.jurnal.index') }}" method="GET" id="filterDateForm">
                            <input type="date" name="tanggal" id="filterDate" class="px-3 py-2 border border-gray-300 rounded-lg" value="{{ $tanggal }}" onchange="this.form.submit()">
                        </form>
                        <button class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
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
                            <p id="summaryTotalRevenue" class="text-3xl font-bold text-green-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                            <p id="revenueCount" class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-receipt mr-2"></i>
                                {{ $jumlahPemasukan }} transaksi hari ini
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
                            <p id="summaryTotalExpense" class="text-3xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                            <p id="expenseCount" class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-calendar-day mr-2"></i>
                                {{ $jumlahPengeluaran }} transaksi hari ini
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
                            <p id="summaryNetBalance" class="text-3xl font-bold {{ ($totalPemasukan - $totalPengeluaran) >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</p>
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
                                <button onclick="openTransactionModal()" 
                                    class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 transition-colors flex items-center gap-2">
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
                                <select id="filterType" onchange="filterTransactions()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                    <option value="semua">Semua Transaksi</option>
                                    <option value="pemasukan">Pemasukan</option>
                                    <option value="pengeluaran">Pengeluaran</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                                <select id="filterCategory" onchange="filterTransactions()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                    <option value="semua">Semua Kategori</option>
                                    <option value="penjualan">Penjualan</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="transportasi">Transportasi</option>
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
                            <tbody class="divide-y divide-gray-200">
                                @forelse($jurnals as $jurnal)
                                <tr data-type="{{ $jurnal->jenis }}" data-category="{{ strtolower($jurnal->kategori) }}" data-id="{{ $jurnal->id }}">
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($jurnal->tgl)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jurnal->jenis == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas fa-arrow-{{ $jurnal->jenis == 'pemasukan' ? 'up' : 'down' }} mr-1"></i>
                                            {{ ucfirst($jurnal->jenis) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $jurnal->kategori }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $jurnal->keterangan }}</td>
                                    <td class="px-6 py-4 text-sm font-medium {{ $jurnal->jenis == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $jurnal->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($jurnal->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <button onclick="editTransaction({{ $jurnal->id }})" class="text-green-600 hover:text-green-800 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteTransaction({{ $jurnal->id }})" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>Belum ada transaksi untuk tanggal ini</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer Summary -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Total Pemasukan:</span>
                                <span id="footerTotalRevenue" class="text-lg font-bold text-green-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Total Pengeluaran:</span>
                                <span id="footerTotalExpense" class="text-lg font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Saldo Bersih:</span>
                                <span id="footerNetBalance" class="text-lg font-bold {{ ($totalPemasukan - $totalPengeluaran) >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
                    <input type="hidden" id="transactionType" value="">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="transactionDate" 
                               class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                        <select id="transactionTypeSelect" onchange="updateTransactionType()"
                                class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select id="transactionCategory"
                                class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400">
                            <!-- Options will be dynamically populated -->
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="transactionDescription" rows="3" placeholder="Contoh: Penjualan roti coklat dan donat" 
                               class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                        <input type="number" id="transactionAmount" placeholder="0" 
                               class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-400">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeTransactionModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" id="submitButton"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token setup for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        // Categories configuration
        const categories = {
            pemasukan: [
                { value: 'Penjualan', label: 'Penjualan' },
                { value: 'lainnya', label: 'Lainnya' }
            ],
            pengeluaran: [
                { value: 'Operasional', label: 'Operasional' },
                { value: 'Utilitas', label: 'Utilitas (Listrik, Air, dll)' },
                { value: 'Bahan Baku', label: 'Bahan Baku' },
                { value: 'Transportasi', label: 'Transportasi' },
                { value: 'lainnya', label: 'Lainnya' }
            ]
        };

        // Category labels for display
        const categoryLabels = {
            'Penjualan': 'Penjualan',
            'Operasional': 'Operasional',
            'Utilitas': 'Utilitas',
            'Bahan Baku': 'Bahan Baku',
            'Transportasi': 'Transportasi',
            'lainnya': 'Lainnya'
        };

        let editingId = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);
            
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('transactionDate').value = today;
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

        // Open transaction modal
        function openTransactionModal(type = 'pemasukan') {
            const modal = document.getElementById('transactionModal');
            const modalTitle = document.getElementById('modalTitle');
            const typeSelect = document.getElementById('transactionTypeSelect');
            const typeInput = document.getElementById('transactionType');
            
            editingId = null;
            
            // Set type
            typeInput.value = type;
            typeSelect.value = type;
            
            // Update modal title
            modalTitle.textContent = 'Tambah Transaksi';
            
            // Populate categories
            populateCategories(type);
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Update transaction type and refresh categories
        function updateTransactionType() {
            const type = document.getElementById('transactionTypeSelect').value;
            document.getElementById('transactionType').value = type;
            populateCategories(type);
        }

        // Close transaction modal
        function closeTransactionModal() {
            document.getElementById('transactionModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('transactionForm').reset();
            editingId = null;
        }

        // Populate categories based on transaction type
        function populateCategories(type) {
            const categorySelect = document.getElementById('transactionCategory');
            categorySelect.innerHTML = '';
            
            categories[type].forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.value;
                option.textContent = cat.label;
                categorySelect.appendChild(option);
            });
        }

        // Handle form submission
        document.getElementById('transactionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const type = document.getElementById('transactionType').value;
            const date = document.getElementById('transactionDate').value;
            const category = document.getElementById('transactionCategory').value;
            const description = document.getElementById('transactionDescription').value;
            const amount = document.getElementById('transactionAmount').value;

            if (!date || !category || !description || !amount) {
                alert('Semua field harus diisi!');
                return;
            }

            if (amount <= 0) {
                alert('Nominal harus lebih dari 0!');
                return;
            }

            const formData = {
                tgl: date,
                jenis: type,
                kategori: category,
                keterangan: description,
                nominal: amount
            };

            try {
                let url = '{{ route("kasir.jurnal.store") }}';
                let method = 'POST';
                
                if (editingId) {
                    url = `/kasir/jurnal/${editingId}`;
                    method = 'PUT';
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    closeTransactionModal();
                    showSuccessMessage(result.message);
                    
                    // Reload page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Gagal menyimpan transaksi!');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan transaksi!');
            }
        });

        // Edit transaction
        async function editTransaction(id) {
            try {
                const response = await fetch(`/kasir/jurnal/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const jurnal = await response.json();
                
                // Fill form with data
                editingId = id;
                document.getElementById('transactionType').value = jurnal.jenis;
                document.getElementById('transactionTypeSelect').value = jurnal.jenis;
                document.getElementById('transactionDate').value = jurnal.tgl.split(' ')[0];
                document.getElementById('transactionDescription').value = jurnal.keterangan;
                document.getElementById('transactionAmount').value = jurnal.nominal;
                
                // Open modal
                const modal = document.getElementById('transactionModal');
                const modalTitle = document.getElementById('modalTitle');
                const submitButton = document.getElementById('submitButton');
                
                if (jurnal.jenis === 'pemasukan') {
                    modalTitle.textContent = 'Edit Pemasukan';
                    submitButton.className = 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
                } else {
                    modalTitle.textContent = 'Edit Pengeluaran';
                    submitButton.className = 'flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
                }
                
                populateCategories(jurnal.jenis);
                document.getElementById('transactionCategory').value = jurnal.kategori;
                document.getElementById('transactionAmount').value = jurnal.nominal;
                
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengambil data transaksi!');
            }
        }

        // Delete transaction
        async function deleteTransaction(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
                return;
            }

            try {
                const response = await fetch(`/kasir/jurnal/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showSuccessMessage(result.message);
                    
                    // Reload page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Gagal menghapus transaksi!');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus transaksi!');
            }
        }

        // Filter transactions
        function filterTransactions() {
            const filterType = document.getElementById('filterType').value;
            const filterCategory = document.getElementById('filterCategory').value;
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            
            const rows = document.querySelectorAll('#transactionTable tbody tr');
            
            rows.forEach(row => {
                const type = row.getAttribute('data-type');
                const category = row.getAttribute('data-category');
                const cells = row.querySelectorAll('td');
                
                if (cells.length < 4) return; // Skip empty row
                
                const description = cells[3].textContent.toLowerCase();
                
                let showRow = true;
                
                // Filter by type
                if (filterType !== 'semua' && type !== filterType) {
                    showRow = false;
                }
                
                // Filter by category
                if (filterCategory !== 'semua' && category !== filterCategory.toLowerCase()) {
                    showRow = false;
                }
                
                // Filter by search
                if (searchInput && !description.includes(searchInput)) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        // Show success message
        function showSuccessMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Close modal when clicking outside
        document.getElementById('transactionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTransactionModal();
            }
        });
    </script>
</body>
</html>
@endsection