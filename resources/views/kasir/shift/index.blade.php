@extends('layouts.kasir.index')

@section('page-title', 'Shift Kasir')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')
    <!-- Main Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <!-- Current Shift Status -->
        <div class="mb-6">
            <div id="noShiftCard" class="bg-gradient-to-r from-orange-50 to-orange-100 border-2 border-orange-200 rounded-2xl p-6 @if($activeShift) hidden @endif">
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

            <div id="activeShiftCard" class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-200 rounded-2xl p-6 @if(!$activeShift) hidden @endif">
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
                                    <p class="text-gray-600">Kasir: <span class="font-semibold text-gray-900">{{ Auth::user()->name }}</span></p>
                                    <p class="text-gray-600">Mulai: <span class="font-semibold text-gray-900" id="activeShiftStart">
                                        @if($activeShift)
                                            {{ \Carbon\Carbon::parse($activeShift->mulai)->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </span></p>
                                    <p class="text-gray-600">Durasi: <span class="font-semibold text-gray-900" id="activeShiftDuration">-</span></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Modal Awal: <span class="font-semibold text-green-600" id="activeShiftModal">
                                        @if($activeShift)
                                            Rp {{ number_format($activeShift->modal, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </span></p>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penjualan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="shiftHistoryTable" class="bg-white divide-y divide-gray-200">
                        @foreach($shifts as $shift)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-semibold text-gray-900">#{{ str_pad($shift->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($shift->mulai)->format('d/m H:i') }}
                                @if($shift->selesai)
                                - {{ \Carbon\Carbon::parse($shift->selesai)->format('H:i') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($shift->durasi >= 60)
                                {{ floor($shift->durasi / 60) }}j {{ $shift->durasi % 60 }}m
                                @else
                                {{ $shift->durasi }}m
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-green-600 font-semibold">
                                Rp {{ number_format($shift->modal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-green-600 font-semibold">
                                Rp {{ number_format($shift->total_penjualan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($shift->selisih == 0)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">Pas</span>
                                @elseif($shift->selisih > 0)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">
                                    +Rp {{ number_format($shift->selisih, 0, ',', '.') }}
                                </span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">
                                    -Rp {{ number_format(abs($shift->selisih), 0, ',', '.') }}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="viewShiftDetail({{ $shift->id }})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @if($shifts->isEmpty())
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                Belum ada riwayat shift
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Kasir</label>
                    <input type="text" value="{{ Auth::user()->name }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50" readonly>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Modal Awal (Cash) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" id="startShiftModalAmount" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400" placeholder="0" oninput="formatCurrency(this)">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan jumlah uang tunai awal di laci kasir</p>
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
                            <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
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
    let activeShift = null;
    let shiftDurationInterval = null;
    let statsUpdateInterval = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Format currency input
    function formatCurrency(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('id-ID');
        }
    }

    // Parse currency to number
    function parseCurrency(value) {
        return parseInt(value.replace(/[^0-9]/g, '')) || 0;
    }

    // Update shift duration
    function updateShiftDuration() {
        if (!activeShift || !activeShift.mulai) return;
        
        const start = new Date(activeShift.mulai);
        const now = new Date();
        const diff = now - start;
        
        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        
        const durationText = `${hours}j ${minutes}m ${seconds}d`;
        document.getElementById('activeShiftDuration').textContent = durationText;
    }

    // Load real-time statistics for active shift
    function loadShiftStatistics() {
        if (!activeShift || !activeShift.id) return;
        
        fetch(`{{ url('kasir/shift') }}/${activeShift.id}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data.statistik;
                
                // Update display
                document.getElementById('activeShiftTransactions').textContent = stats.total_transaksi;
                document.getElementById('activeShiftSales').textContent = 
                    'Rp ' + parseInt(stats.total_penjualan).toLocaleString('id-ID');
                document.getElementById('activeShiftCashSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_tunai).toLocaleString('id-ID');
                
                // Update data attributes for end shift modal
                document.getElementById('activeShiftCard').dataset.totalPenjualan = stats.total_penjualan;
                document.getElementById('activeShiftCard').dataset.penjualanTunai = stats.penjualan_tunai;
                document.getElementById('activeShiftCard').dataset.totalTransaksi = stats.total_transaksi;
            }
        })
        .catch(error => {
            console.error('Error loading shift statistics:', error);
        });
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
        const modalInput = document.getElementById('startShiftModalAmount').value;
        const modal = parseCurrency(modalInput);
        
        if (modal <= 0) {
            alert('Modal awal harus lebih dari 0!');
            return;
        }
        
        fetch('{{ route("kasir.shift.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                modal: modal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal memulai shift!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memulai shift!');
        });
    }

    // End Shift Modal
    function openEndShiftModal() {
        if (!activeShift || !activeShift.id) return;
        
        // Load shift details for summary
        fetch(`{{ url('kasir/shift') }}/${activeShift.id}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const shift = data.data.shift;
                const stats = data.data.statistik;
                
                const start = new Date(shift.mulai);
                const now = new Date();
                const diff = now - start;
                const hours = Math.floor(diff / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                
                document.getElementById('endShiftDuration').textContent = `${hours}j ${minutes}m`;
                document.getElementById('endShiftModalAwal').textContent = 
                    'Rp ' + parseInt(shift.modal).toLocaleString('id-ID');
                document.getElementById('endShiftTotalTrx').textContent = stats.total_transaksi;
                document.getElementById('endShiftTotalSales').textContent = 
                    'Rp ' + parseInt(stats.total_penjualan).toLocaleString('id-ID');
                
                // Calculate expected cash (initial cash + cash sales)
                const expectedCash = shift.modal + stats.penjualan_tunai;
                document.getElementById('expectedCash').textContent = 
                    'Rp ' + expectedCash.toLocaleString('id-ID');
                
                // Store data for calculation
                const modalElement = document.getElementById('endShiftModal');
                modalElement.dataset.expected = expectedCash;
                modalElement.dataset.modal = shift.modal;
                modalElement.dataset.penjualanTunai = stats.penjualan_tunai;
                
                document.getElementById('endShiftModal').classList.remove('hidden');
                document.getElementById('endShiftModal').classList.add('flex');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data shift!');
        });
    }

    function closeEndShiftModal() {
        document.getElementById('endShiftModal').classList.add('hidden');
        document.getElementById('endShiftModal').classList.remove('flex');
        document.getElementById('endShiftActualCash').value = '';
        document.getElementById('cashDifferenceAlert').classList.add('hidden');
    }

    // Calculate cash difference
    function calculateDifference() {
        const actualInput = document.getElementById('endShiftActualCash').value;
        if (!actualInput) {
            document.getElementById('cashDifferenceAlert').classList.add('hidden');
            return;
        }
        
        const actualCash = parseCurrency(actualInput);
        const modal = parseInt(document.getElementById('endShiftModal').dataset.modal || 0);
        const penjualanTunai = parseInt(document.getElementById('endShiftModal').dataset.penjualanTunai || 0);
        const expectedCash = modal + penjualanTunai;
        const difference = actualCash - expectedCash;
        
        document.getElementById('cashDifferenceAlert').classList.remove('hidden');
        document.getElementById('cashSurplus').classList.add('hidden');
        document.getElementById('cashShortage').classList.add('hidden');
        document.getElementById('cashExact').classList.add('hidden');
        
        if (difference > 0) {
            document.getElementById('cashSurplus').classList.remove('hidden');
            document.getElementById('surplusAmount').textContent = 
                'Rp ' + difference.toLocaleString('id-ID');
        } else if (difference < 0) {
            document.getElementById('cashShortage').classList.remove('hidden');
            document.getElementById('shortageAmount').textContent = 
                'Rp ' + Math.abs(difference).toLocaleString('id-ID');
        } else {
            document.getElementById('cashExact').classList.remove('hidden');
        }
    }

    // End shift
    function endShift() {
        const actualInput = document.getElementById('endShiftActualCash').value;
        const actualCash = parseCurrency(actualInput);
        
        if (actualCash <= 0) {
            alert('Uang aktual di laci wajib diisi dan harus lebih dari 0!');
            return;
        }
        
        if (!confirm('Yakin ingin menutup shift? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }
        
        fetch(`{{ url('kasir/shift') }}/${activeShift.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                uang_aktual: actualCash
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal menutup shift!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menutup shift!');
        });
    }

    // View shift detail
    function viewShiftDetail(shiftId) {
        fetch(`{{ url('kasir/shift') }}/${shiftId}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const shift = data.data.shift;
                const stats = data.data.statistik;
                const transaksis = data.data.transaksis || [];
                
                const start = new Date(shift.mulai);
                const end = shift.selesai ? new Date(shift.selesai) : null;
                const duration = shift.durasi || 0;
                const hours = Math.floor(duration / 60);
                const minutes = duration % 60;
                
                let transaksiRows = '';
                if (transaksis.length > 0) {
                    transaksiRows = `
                        <div class="bg-gray-50 rounded-xl p-4 mt-4">
                            <h5 class="font-semibold text-gray-900 mb-3">10 Transaksi Terbaru</h5>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Waktu</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Metode</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${transaksis.map((transaksi, index) => {
                                            let total = 0;
                                            transaksi.detailTransaksis.forEach(detail => {
                                                total += detail.jumlah * detail.harga;
                                            });
                                            
                                            return `
                                                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                                    <td class="px-3 py-2 text-sm text-gray-700">${index + 1}</td>
                                                    <td class="px-3 py-2 text-sm text-gray-700">
                                                        ${new Date(transaksi.tgl).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm">
                                                        <span class="px-2 py-1 ${transaksi.metode === 'tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'} rounded text-xs">
                                                            ${transaksi.metode}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-green-600 font-semibold">
                                                        Rp ${total.toLocaleString('id-ID')}
                                                    </td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }
                
                const content = `
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h5 class="font-semibold text-gray-900 mb-3">Informasi Shift</h5>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-600">ID Shift:</p>
                                    <p class="font-semibold text-gray-900">#${shift.id.toString().padStart(6, '0')}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Kasir:</p>
                                    <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Mulai:</p>
                                    <p class="font-semibold text-gray-900">${start.toLocaleString('id-ID')}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Selesai:</p>
                                    <p class="font-semibold text-gray-900">${end ? end.toLocaleString('id-ID') : 'Masih aktif'}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Durasi:</p>
                                    <p class="font-semibold text-gray-900">${hours} jam ${minutes} menit</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-xl p-4">
                            <h5 class="font-semibold text-gray-900 mb-3">Keuangan</h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Modal Awal:</span>
                                    <span class="font-semibold text-gray-900">Rp ${parseInt(shift.modal).toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Penjualan:</span>
                                    <span class="font-semibold text-green-600">Rp ${parseInt(stats.total_penjualan).toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Penjualan Tunai:</span>
                                    <span class="font-semibold text-green-600">Rp ${parseInt(stats.penjualan_tunai).toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Transaksi:</span>
                                    <span class="font-semibold text-blue-600">${stats.total_transaksi}</span>
                                </div>
                                ${shift.selesai ? `
                                <div class="border-t border-green-200 pt-2 flex justify-between">
                                    <span class="text-gray-600">Uang Seharusnya:</span>
                                    <span class="font-semibold text-gray-900">Rp ${parseInt(shift.modal + stats.penjualan_tunai).toLocaleString('id-ID')}</span>
                                </div>
                                <div class="border-t border-green-200 pt-2 flex justify-between">
                                    <span class="text-gray-600">Uang Aktual:</span>
                                    <span class="font-semibold text-gray-900">Rp ${parseInt(shift.uang_aktual).toLocaleString('id-ID')}</span>
                                </div>
                                <div class="border-t border-green-200 pt-2 flex justify-between">
                                    <span class="font-semibold text-gray-900">Selisih:</span>
                                    <span class="font-bold text-lg ${shift.selisih === 0 ? 'text-blue-600' : shift.selisih > 0 ? 'text-green-600' : 'text-red-600'}">
                                        ${shift.selisih === 0 ? 'Pas' : (shift.selisih > 0 ? '+' : '') + 'Rp ' + parseInt(shift.selisih).toLocaleString('id-ID')}
                                    </span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        
                        ${transaksiRows}
                    </div>
                `;
                
                document.getElementById('viewShiftContent').innerHTML = content;
                document.getElementById('viewShiftModal').classList.remove('hidden');
                document.getElementById('viewShiftModal').classList.add('flex');
            }
        })
        .catch(error => {
            console.error('Error loading shift detail:', error);
            alert('Gagal memuat detail shift!');
        });
    }

    function closeViewShiftModal() {
        document.getElementById('viewShiftModal').classList.add('hidden');
        document.getElementById('viewShiftModal').classList.remove('flex');
    }

    // Filter shifts
    function filterShifts() {
        const period = document.getElementById('filterPeriod').value;
        window.location.href = '{{ route("kasir.shift.index") }}?period=' + period;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial active shift data from server-side
        @if($activeShift)
            activeShift = {
                id: {{ $activeShift->id }},
                mulai: '{{ $activeShift->mulai }}',
                modal: {{ $activeShift->modal }},
                total_penjualan: {{ $activeShift->total_penjualan ?? 0 }},
                penjualan_tunai: {{ $activeShift->penjualan_tunai ?? 0 }},
                total_transaksi: {{ $activeShift->total_transaksi ?? 0 }}
            };
            
            // Update initial statistics display
            document.getElementById('activeShiftTransactions').textContent = {{ $activeShift->total_transaksi ?? 0 }};
            document.getElementById('activeShiftSales').textContent = 'Rp ' + parseInt({{ $activeShift->total_penjualan ?? 0 }}).toLocaleString('id-ID');
            document.getElementById('activeShiftCashSales').textContent = 'Rp ' + parseInt({{ $activeShift->penjualan_tunai ?? 0 }}).toLocaleString('id-ID');
            
            // Start intervals
            updateShiftDuration();
            shiftDurationInterval = setInterval(updateShiftDuration, 1000);
            
            // Load real-time statistics every 5 seconds
            loadShiftStatistics();
            statsUpdateInterval = setInterval(loadShiftStatistics, 5000);
        @endif
    });

    // Clean up intervals when leaving page
    window.addEventListener('beforeunload', function() {
        if (shiftDurationInterval) clearInterval(shiftDurationInterval);
        if (statsUpdateInterval) clearInterval(statsUpdateInterval);
    });
</script>
@endsection