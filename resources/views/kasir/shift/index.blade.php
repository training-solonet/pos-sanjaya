@extends('layouts.kasir.index')

@section('page-title', 'Shift Kasir')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')

    <!-- Main Content -->

        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Current Shift Status -->
            <div class="mb-6">
                <div id="noShiftCard" class="bg-gradient-to-r from-orange-50 to-orange-100 border-2 border-orange-200 rounded-2xl p-6">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="flex items-center space-x-4 mb-4 md:mb-0">
                            <div class="w-16 h-16 bg-orange-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-orange-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Belum Ada Shift Aktif</h3>
                                <p class="text-sm text-gray-600">Mulai shift untuk melakukan transaksi</p>
                            </div>
                        </div>
                        <button onclick="openStartShiftModal()" class="px-6 py-3 bg-gradient-to-r from-green-400 to-green-700 text-white font-semibold rounded-xl hover:from-green-500 hover:to-green-800 transition-all shadow-lg">
                            <i class="fas fa-play mr-2"></i>Mulai Shift
                        </button>
                    </div>
                </div>

                <div id="activeShiftCard" class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-200 rounded-2xl p-6 hidden">
                    <div class="flex flex-col md:flex-row items-start justify-between">
                        <div class="flex items-start space-x-4 mb-4 md:mb-0 flex-1">
                            <div class="w-16 h-16 bg-green-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-clock text-green-600 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900">Shift Aktif</h3>
                                    <span class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-full animate-pulse">AKTIF</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-gray-600">Kasir: <span class="font-semibold text-gray-900" id="activeShiftCashier">-</span></p>
                                        <p class="text-gray-600">Mulai: <span class="font-semibold text-gray-900" id="activeShiftStart">-</span></p>
                                        <p class="text-gray-600">Durasi: <span class="font-semibold text-gray-900" id="activeShiftDuration">-</span></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Modal Awal: <span class="font-semibold text-green-600" id="activeShiftModal">Rp 0</span></p>
                                        <p class="text-gray-600">Transaksi: <span class="font-semibold text-blue-600" id="activeShiftTransactions">0</span></p>
                                        <p class="text-gray-600">Total Penjualan: <span class="font-semibold text-green-600" id="activeShiftSales">Rp 0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button onclick="openEndShiftModal()" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-800 transition-all shadow-lg whitespace-nowrap">
                            <i class="fas fa-stop mr-2"></i>Tutup Shift
                        </button>
                    </div>
                </div>
            </div>

            <!-- Shift History -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Shift</h3>
                    <div class="flex items-center space-x-2">
                        <select id="filterPeriod" onchange="filterShifts()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penjualan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="shiftHistoryTable" class="bg-white divide-y divide-gray-200">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Start Shift Modal -->
    <div id="startShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h4 class="text-xl font-bold text-gray-900">Mulai Shift Baru</h4>
                    <button onclick="closeStartShiftModal()" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Nama Kasir <span class="text-red-500">*</span></label>
                    <input type="text" id="startShiftCashier" value="Admin" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400" placeholder="Nama kasir">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Modal Awal (Cash) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" id="startShiftModalAmount" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400" placeholder="0" oninput="formatCurrency(this)">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan jumlah uang tunai awal di laci kasir</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Catatan (Opsional)</label>
                    <textarea id="startShiftNotes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex space-x-3">
                <button onclick="closeStartShiftModal()" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Batal
                </button>
                <button onclick="startShift()" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-400 to-green-700 hover:from-green-500 hover:to-green-800 text-white font-medium rounded-xl transition-all">
                    Mulai Shift
                </button>
            </div>
        </div>
    </div>

    <!-- End Shift Modal -->
    <div id="endShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h4 class="text-xl font-bold text-gray-900">Tutup Shift</h4>
                    <button onclick="closeEndShiftModal()" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Shift Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h5 class="font-semibold text-gray-900 mb-3">Ringkasan Shift</h5>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-600">Kasir:</p>
                            <p class="font-semibold text-gray-900" id="endShiftCashierName">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Durasi:</p>
                            <p class="font-semibold text-gray-900" id="endShiftDuration">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Modal Awal:</p>
                            <p class="font-semibold text-green-600" id="endShiftModalAwal">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Total Transaksi:</p>
                            <p class="font-semibold text-blue-600" id="endShiftTotalTrx">0</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-600">Total Penjualan:</p>
                            <p class="font-semibold text-lg text-green-600" id="endShiftTotalSales">Rp 0</p>
                        </div>
                    </div>
                </div>

                <!-- Expected Cash -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <h5 class="font-semibold text-gray-900 mb-2">Uang yang Seharusnya Ada</h5>
                    <p class="text-2xl font-bold text-green-600" id="expectedCash">Rp 0</p>
                    <p class="text-xs text-gray-600 mt-1">Modal Awal + Penjualan Tunai</p>
                </div>

                <!-- Actual Cash Count -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Uang Aktual di Laci <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" id="endShiftActualCash" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400" placeholder="0" oninput="formatCurrency(this); calculateDifference()">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Hitung semua uang tunai yang ada di laci kasir</p>
                </div>

                <!-- Difference Alert -->
                <div id="cashDifferenceAlert" class="hidden">
                    <div id="cashSurplus" class="bg-green-50 border border-green-200 rounded-xl p-4 hidden">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-600 text-xl mt-1"></i>
                            <div>
                                <p class="font-semibold text-green-900">Uang Lebih</p>
                                <p class="text-sm text-green-700">Selisih: <span id="surplusAmount" class="font-bold">Rp 0</span></p>
                            </div>
                        </div>
                    </div>
                    <div id="cashShortage" class="bg-red-50 border border-red-200 rounded-xl p-4 hidden">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl mt-1"></i>
                            <div>
                                <p class="font-semibold text-red-900">Uang Kurang</p>
                                <p class="text-sm text-red-700">Selisih: <span id="shortageAmount" class="font-bold">Rp 0</span></p>
                            </div>
                        </div>
                    </div>
                    <div id="cashExact" class="bg-blue-50 border border-blue-200 rounded-xl p-4 hidden">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-blue-600 text-xl mt-1"></i>
                            <div>
                                <p class="font-semibold text-blue-900">Pas! Uang Sesuai</p>
                                <p class="text-sm text-blue-700">Tidak ada selisih</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Catatan Penutupan</label>
                    <textarea id="endShiftNotes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400" placeholder="Catatan tambahan (opsional)..."></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex space-x-3 sticky bottom-0 bg-white">
                <button onclick="closeEndShiftModal()" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Batal
                </button>
                <button onclick="endShift()" class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 text-white font-medium rounded-xl transition-all">
                    Tutup Shift
                </button>
            </div>
        </div>
    </div>

    <!-- View Shift Detail Modal -->
    <div id="viewShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h4 class="text-xl font-bold text-gray-900">Detail Shift</h4>
                    <button onclick="closeViewShiftModal()" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6" id="viewShiftContent">
                <!-- Content injected by JS -->
            </div>
        </div>
    </div>

    <script>
        const SHIFTS_KEY = 'pos_shifts';
        const ACTIVE_SHIFT_KEY = 'pos_active_shift';
        let activeShift = null;
        let shiftDurationInterval = null;

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Date/time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const dateTimeElement = document.getElementById('currentDateTime');
            if (dateTimeElement) dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }

        // Format currency input
        function formatCurrency(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) {
                input.value = parseInt(value).toLocaleString('id-ID');
            }
        }

        // Get shifts from localStorage
        function getShifts() {
            try {
                return JSON.parse(localStorage.getItem(SHIFTS_KEY) || '[]');
            } catch (e) {
                return [];
            }
        }

        // Save shifts to localStorage
        function saveShifts(shifts) {
            localStorage.setItem(SHIFTS_KEY, JSON.stringify(shifts));
        }

        // Get active shift
        function getActiveShift() {
            try {
                return JSON.parse(localStorage.getItem(ACTIVE_SHIFT_KEY));
            } catch (e) {
                return null;
            }
        }

        // Save active shift
        function saveActiveShift(shift) {
            if (shift) {
                localStorage.setItem(ACTIVE_SHIFT_KEY, JSON.stringify(shift));
            } else {
                localStorage.removeItem(ACTIVE_SHIFT_KEY);
            }
        }

        // Load and display shift status
        function loadShiftStatus() {
            activeShift = getActiveShift();
            
            if (activeShift) {
                document.getElementById('noShiftCard').classList.add('hidden');
                document.getElementById('activeShiftCard').classList.remove('hidden');
                
                document.getElementById('activeShiftCashier').textContent = activeShift.cashier;
                document.getElementById('activeShiftStart').textContent = new Date(activeShift.startTime).toLocaleString('id-ID', { 
                    day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' 
                });
                document.getElementById('activeShiftModal').textContent = 'Rp ' + activeShift.initialCash.toLocaleString('id-ID');
                
                // Start duration timer
                updateShiftDuration();
                if (shiftDurationInterval) clearInterval(shiftDurationInterval);
                shiftDurationInterval = setInterval(updateShiftDuration, 1000);
                
                // Update stats (mock data for now)
                document.getElementById('activeShiftTransactions').textContent = activeShift.transactions || 0;
                document.getElementById('activeShiftSales').textContent = 'Rp ' + (activeShift.totalSales || 0).toLocaleString('id-ID');
            } else {
                document.getElementById('noShiftCard').classList.remove('hidden');
                document.getElementById('activeShiftCard').classList.add('hidden');
                if (shiftDurationInterval) {
                    clearInterval(shiftDurationInterval);
                }
            }
        }

        // Update shift duration
        function updateShiftDuration() {
            if (!activeShift) return;
            
            const start = new Date(activeShift.startTime);
            const now = new Date();
            const diff = now - start;
            
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const durationText = `${hours}j ${minutes}m ${seconds}d`;
            document.getElementById('activeShiftDuration').textContent = durationText;
        }

        // Start Shift Modal
        function openStartShiftModal() {
            document.getElementById('startShiftModal').classList.remove('hidden');
            document.getElementById('startShiftModal').classList.add('flex');
        }

        function closeStartShiftModal() {
            document.getElementById('startShiftModal').classList.add('hidden');
            document.getElementById('startShiftModal').classList.remove('flex');
        }

        // Start shift
        function startShift() {
            const cashier = document.getElementById('startShiftCashier').value.trim();
            const modalInput = document.getElementById('startShiftModalAmount').value.replace(/[^0-9]/g, '');
            const notes = document.getElementById('startShiftNotes').value.trim();
            
            if (!cashier) {
                alert('Nama kasir wajib diisi!');
                return;
            }
            
            if (!modalInput) {
                alert('Modal awal wajib diisi!');
                return;
            }
            
            const newShift = {
                id: Date.now(),
                cashier: cashier,
                startTime: new Date().toISOString(),
                initialCash: parseInt(modalInput),
                notes: notes,
                transactions: 0,
                totalSales: 0,
                status: 'active'
            };
            
            saveActiveShift(newShift);
            loadShiftStatus();
            closeStartShiftModal();
            
            // Clear form
            document.getElementById('startShiftModalAmount').value = '';
            document.getElementById('startShiftNotes').value = '';
            
            showNotification('Shift berhasil dimulai!', 'success');
        }

        // End Shift Modal
        function openEndShiftModal() {
            if (!activeShift) return;
            
            const start = new Date(activeShift.startTime);
            const now = new Date();
            const diff = now - start;
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            
            document.getElementById('endShiftCashierName').textContent = activeShift.cashier;
            document.getElementById('endShiftDuration').textContent = `${hours}j ${minutes}m`;
            document.getElementById('endShiftModalAwal').textContent = 'Rp ' + activeShift.initialCash.toLocaleString('id-ID');
            document.getElementById('endShiftTotalTrx').textContent = activeShift.transactions || 0;
            document.getElementById('endShiftTotalSales').textContent = 'Rp ' + (activeShift.totalSales || 0).toLocaleString('id-ID');
            
            // Calculate expected cash (initial cash + cash sales)
            const expectedCash = activeShift.initialCash + (activeShift.totalSales || 0);
            document.getElementById('expectedCash').textContent = 'Rp ' + expectedCash.toLocaleString('id-ID');
            
            document.getElementById('endShiftModal').classList.remove('hidden');
            document.getElementById('endShiftModal').classList.add('flex');
        }

        function closeEndShiftModal() {
            document.getElementById('endShiftModal').classList.add('hidden');
            document.getElementById('endShiftModal').classList.remove('flex');
            document.getElementById('endShiftActualCash').value = '';
            document.getElementById('endShiftNotes').value = '';
            document.getElementById('cashDifferenceAlert').classList.add('hidden');
        }

        // Calculate cash difference
        function calculateDifference() {
            const actualInput = document.getElementById('endShiftActualCash').value.replace(/[^0-9]/g, '');
            if (!actualInput) {
                document.getElementById('cashDifferenceAlert').classList.add('hidden');
                return;
            }
            
            const actualCash = parseInt(actualInput);
            const expectedCash = activeShift.initialCash + (activeShift.totalSales || 0);
            const difference = actualCash - expectedCash;
            
            document.getElementById('cashDifferenceAlert').classList.remove('hidden');
            document.getElementById('cashSurplus').classList.add('hidden');
            document.getElementById('cashShortage').classList.add('hidden');
            document.getElementById('cashExact').classList.add('hidden');
            
            if (difference > 0) {
                document.getElementById('cashSurplus').classList.remove('hidden');
                document.getElementById('surplusAmount').textContent = 'Rp ' + difference.toLocaleString('id-ID');
            } else if (difference < 0) {
                document.getElementById('cashShortage').classList.remove('hidden');
                document.getElementById('shortageAmount').textContent = 'Rp ' + Math.abs(difference).toLocaleString('id-ID');
            } else {
                document.getElementById('cashExact').classList.remove('hidden');
            }
        }

        // End shift
        function endShift() {
            const actualInput = document.getElementById('endShiftActualCash').value.replace(/[^0-9]/g, '');
            const notes = document.getElementById('endShiftNotes').value.trim();
            
            if (!actualInput) {
                alert('Uang aktual di laci wajib diisi!');
                return;
            }
            
            if (!confirm('Yakin ingin menutup shift? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            
            const actualCash = parseInt(actualInput);
            const expectedCash = activeShift.initialCash + (activeShift.totalSales || 0);
            const difference = actualCash - expectedCash;
            
            // Save completed shift
            const completedShift = {
                ...activeShift,
                endTime: new Date().toISOString(),
                actualCash: actualCash,
                expectedCash: expectedCash,
                difference: difference,
                endNotes: notes,
                status: 'completed'
            };
            
            const shifts = getShifts();
            shifts.unshift(completedShift);
            saveShifts(shifts);
            
            // Clear active shift
            saveActiveShift(null);
            activeShift = null;
            
            loadShiftStatus();
            loadShiftHistory();
            closeEndShiftModal();
            
            showNotification('Shift berhasil ditutup!', 'success');
        }

        // Load shift history
        function loadShiftHistory() {
            const shifts = getShifts();
            const tbody = document.getElementById('shiftHistoryTable');
            
            if (shifts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada riwayat shift</td></tr>';
                return;
            }
            
            tbody.innerHTML = '';
            shifts.forEach((shift, idx) => {
                const start = new Date(shift.startTime);
                const end = new Date(shift.endTime);
                const duration = Math.floor((end - start) / 60000);
                const hours = Math.floor(duration / 60);
                const minutes = duration % 60;
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm">
                        <span class="font-semibold text-gray-900">#${shift.id.toString().slice(-6)}</span>
                        <p class="text-xs text-gray-500">${start.toLocaleDateString('id-ID')}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">${shift.cashier}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        ${start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} - 
                        ${end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">${hours}j ${minutes}m</td>
                    <td class="px-4 py-3 text-sm text-green-600 font-semibold">Rp ${shift.initialCash.toLocaleString('id-ID')}</td>
                    <td class="px-4 py-3 text-sm text-green-600 font-semibold">Rp ${shift.totalSales.toLocaleString('id-ID')}</td>
                    <td class="px-4 py-3 text-sm">
                        ${shift.difference === 0 
                            ? '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Pas</span>'
                            : shift.difference > 0 
                                ? `<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">+Rp ${shift.difference.toLocaleString('id-ID')}</span>`
                                : `<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">-Rp ${Math.abs(shift.difference).toLocaleString('id-ID')}</span>`
                        }
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="viewShiftDetail(${idx})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // View shift detail
        function viewShiftDetail(index) {
            const shifts = getShifts();
            const shift = shifts[index];
            
            const start = new Date(shift.startTime);
            const end = new Date(shift.endTime);
            const duration = Math.floor((end - start) / 60000);
            const hours = Math.floor(duration / 60);
            const minutes = duration % 60;
            
            const content = `
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h5 class="font-semibold text-gray-900 mb-3">Informasi Shift</h5>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600">ID Shift:</p>
                                <p class="font-semibold text-gray-900">#${shift.id.toString().slice(-6)}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Kasir:</p>
                                <p class="font-semibold text-gray-900">${shift.cashier}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Mulai:</p>
                                <p class="font-semibold text-gray-900">${start.toLocaleString('id-ID')}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Selesai:</p>
                                <p class="font-semibold text-gray-900">${end.toLocaleString('id-ID')}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Durasi:</p>
                                <p class="font-semibold text-gray-900">${hours} jam ${minutes} menit</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Total Transaksi:</p>
                                <p class="font-semibold text-blue-600">${shift.transactions || 0}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 rounded-xl p-4">
                        <h5 class="font-semibold text-gray-900 mb-3">Keuangan</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Modal Awal:</span>
                                <span class="font-semibold text-gray-900">Rp ${shift.initialCash.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Penjualan:</span>
                                <span class="font-semibold text-green-600">Rp ${shift.totalSales.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="border-t border-green-200 pt-2 flex justify-between">
                                <span class="text-gray-600">Uang Seharusnya:</span>
                                <span class="font-semibold text-gray-900">Rp ${shift.expectedCash.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Uang Aktual:</span>
                                <span class="font-semibold text-gray-900">Rp ${shift.actualCash.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="border-t border-green-200 pt-2 flex justify-between">
                                <span class="font-semibold text-gray-900">Selisih:</span>
                                <span class="font-bold text-lg ${shift.difference === 0 ? 'text-blue-600' : shift.difference > 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${shift.difference === 0 ? 'Pas' : (shift.difference > 0 ? '+' : '') + 'Rp ' + shift.difference.toLocaleString('id-ID')}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    ${shift.notes || shift.endNotes ? `
                    <div class="bg-blue-50 rounded-xl p-4">
                        <h5 class="font-semibold text-gray-900 mb-2">Catatan</h5>
                        ${shift.notes ? `<p class="text-sm text-gray-700 mb-2"><strong>Pembukaan:</strong> ${shift.notes}</p>` : ''}
                        ${shift.endNotes ? `<p class="text-sm text-gray-700"><strong>Penutupan:</strong> ${shift.endNotes}</p>` : ''}
                    </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('viewShiftContent').innerHTML = content;
            document.getElementById('viewShiftModal').classList.remove('hidden');
            document.getElementById('viewShiftModal').classList.add('flex');
        }

        function closeViewShiftModal() {
            document.getElementById('viewShiftModal').classList.add('hidden');
            document.getElementById('viewShiftModal').classList.remove('flex');
        }

        // Filter shifts
        function filterShifts() {
            const period = document.getElementById('filterPeriod').value;
            // Implement filtering logic here
            loadShiftHistory();
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-[70] px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            } text-white`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-xl"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 60000);
            loadShiftStatus();
            loadShiftHistory();
        });
    </script>
@endsection


