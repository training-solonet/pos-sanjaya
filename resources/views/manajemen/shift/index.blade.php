@extends('layouts.manajemen.index')

@section('page-title', 'Monitoring Shift Management')
@section('page-description', 'Sistem monitoring shift untuk manajemen')

@section('content')
    <!-- Main Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 rounded-2xl p-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <div class="w-16 h-16 bg-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Monitoring Shift</h3>
                            <p class="text-sm text-gray-600">Data real-time semua shift yang telah selesai</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Export All Dropdown -->
                        <div class="relative group">
                            <button class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-800 transition-all shadow-lg flex items-center">
                                <i class="fas fa-download mr-2"></i>Export Semua
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                <a href="{{ route('management.shiftman.index', ['period' => request('period', 'all'), 'export' => 'excel']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i> Excel
                                </a>
                                <a href="{{ route('management.shiftman.index', ['period' => request('period', 'all'), 'export' => 'pdf']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i> PDF
                                </a>
                                <a href="{{ route('management.shiftman.index', ['period' => request('period', 'all'), 'export' => 'csv']) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-csv text-blue-600 mr-2"></i> CSV
                                </a>
                            </div>
                        </div>
                        
                        <select id="filterPeriod" onchange="filterShifts()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                            <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift History -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Shift</h3>
                <div class="text-sm text-gray-600">
                    Total: {{ $shifts->total() }} shift
                </div>
            </div>

            @if($shifts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjualan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="shiftHistoryTable" class="bg-white divide-y divide-gray-200">
                        @foreach($shifts as $shift)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <span class="font-semibold text-gray-900">#{{ str_pad($shift->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $shift->user->name ?? 'Unknown' }}
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
                            <td class="px-4 py-3 text-sm text-blue-600 font-semibold">
                                {{ $shift->total_transaksi }}
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
                                <button onclick="viewShiftDetail({{ $shift->id }})" 
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs flex items-center">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($shifts->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex flex-col sm:flex-row items-center justify-between">
                    <div class="mb-2 sm:mb-0">
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium">{{ $shifts->firstItem() }}</span>
                            -
                            <span class="font-medium">{{ $shifts->lastItem() }}</span>
                            dari
                            <span class="font-medium">{{ $shifts->total() }}</span>
                            shift
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            {{-- Tombol Previous --}}
                            @if ($shifts->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <span class="sr-only">Sebelumnya</span>
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $shifts->previousPageUrl() }}&period={{ request('period', 'all') }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Sebelumnya</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            {{-- Tombol Halaman --}}
                            @php
                                $current = $shifts->currentPage();
                                $last = $shifts->lastPage();
                                $start = max(1, $current - 2);
                                $end = min($last, $current + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $shifts->url(1) }}&period={{ request('period', 'all') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    1
                                </a>
                                @if($start > 2)
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                        ...
                                    </span>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $current)
                                    <span class="relative inline-flex items-center px-4 py-2 border border-green-500 bg-green-50 text-sm font-medium text-green-600">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $shifts->url($page) }}&period={{ request('period', 'all') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                        ...
                                    </span>
                                @endif
                                <a href="{{ $shifts->url($last) }}&period={{ request('period', 'all') }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    {{ $last }}
                                </a>
                            @endif

                            {{-- Tombol Next --}}
                            @if ($shifts->hasMorePages())
                                <a href="{{ $shifts->nextPageUrl() }}&period={{ request('period', 'all') }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Berikutnya</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <span class="sr-only">Berikutnya</span>
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
            @endif
            
            @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Belum ada data shift</h4>
                <p class="text-gray-500 text-sm">Tidak ada shift yang ditemukan untuk periode ini</p>
            </div>
            @endif
        </div>
    </main>

    <!-- View Shift Detail Modal -->
    <div id="viewShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl max-h-[90vh] overflow-y-auto">
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
            
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button onclick="closeViewShiftModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    // View shift detail - PERBAIKAN INI
    function viewShiftDetail(shiftId) {
        fetch(`/management/shiftman/${shiftId}`, {
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
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-semibold text-gray-900">10 Transaksi Terbaru</h5>
                                <span class="text-sm text-gray-600">Total: Rp ${formatRupiah(stats.total_penjualan)}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">#</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Invoice</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Waktu</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Metode</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Customer</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${transaksis.map((transaksi, index) => {
                                            const metodeClass = getMetodeClass(transaksi.metode);
                                            const metodeLabel = getMetodeLabel(transaksi.metode);
                                            const invoice = transaksi.id_transaksi || transaksi.invoice || '-';
                                            const customer = transaksi.customer || 'Non-Member';
                                            
                                            return `
                                                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'} hover:bg-gray-100">
                                                    <td class="px-3 py-2 text-sm text-gray-700">${index + 1}</td>
                                                    <td class="px-3 py-2 text-sm text-gray-700 font-mono">${invoice}</td>
                                                    <td class="px-3 py-2 text-sm text-gray-700">
                                                        ${new Date(transaksi.tgl).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm">
                                                        <span class="px-2 py-1 ${metodeClass} rounded text-xs font-medium">
                                                            ${metodeLabel}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-gray-700">${customer}</td>
                                                    <td class="px-3 py-2 text-sm text-green-600 font-semibold">
                                                        Rp ${formatRupiah(transaksi.total)}
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
                    <div class="space-y-6">
                        <!-- Shift Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-xl p-5">
                                <h5 class="font-semibold text-gray-900 mb-4">Informasi Shift</h5>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">ID Shift:</span>
                                        <span class="font-semibold text-gray-900">#${shift.id.toString().padStart(6, '0')}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Kasir:</span>
                                        <span class="font-semibold text-gray-900">${shift.user}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Mulai:</span>
                                        <span class="font-semibold text-gray-900">${start.toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Selesai:</span>
                                        <span class="font-semibold text-gray-900">${end ? end.toLocaleString('id-ID') : 'Masih aktif'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Durasi:</span>
                                        <span class="font-semibold text-gray-900">${hours} jam ${minutes} menit</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 rounded-xl p-5">
                                <h5 class="font-semibold text-gray-900 mb-4">Ringkasan Keuangan</h5>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Modal Awal:</span>
                                        <span class="font-semibold text-gray-900">Rp ${formatRupiah(shift.modal)}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Total Penjualan:</span>
                                        <span class="font-semibold text-green-600 text-lg">Rp ${formatRupiah(stats.total_penjualan)}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Penjualan Tunai:</span>
                                        <span class="font-semibold text-green-600">Rp ${formatRupiah(stats.penjualan_tunai)}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Total Transaksi:</span>
                                        <span class="font-semibold text-blue-600">${stats.total_transaksi}</span>
                                    </div>
                                    ${shift.selesai ? `
                                    <div class="border-t border-green-200 pt-3 flex justify-between items-center">
                                        <span class="text-gray-700 font-medium">Uang Seharusnya:</span>
                                        <span class="font-semibold text-gray-900">Rp ${formatRupiah(shift.modal + stats.penjualan_tunai)}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700 font-medium">Uang Aktual:</span>
                                        <span class="font-semibold text-gray-900">Rp ${formatRupiah(shift.uang_aktual)}</span>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Difference Summary -->
                        ${shift.selesai ? `
                        <div class="${getSelisihClass(shift.selisih)} rounded-xl p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="font-semibold text-gray-900 mb-1">Rekapitulasi Kas</h5>
                                    <p class="text-sm text-gray-600">Perbandingan uang seharusnya vs aktual</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold ${getSelisihTextClass(shift.selisih)}">
                                        ${shift.selisih === 0 ? 'PAS' : (shift.selisih > 0 ? '+' : '') + 'Rp ' + formatRupiah(Math.abs(shift.selisih))}
                                    </div>
                                    <div class="text-xs ${getSelisihTextClass(shift.selisih)}">
                                        ${shift.selisih === 0 ? 'Uang sesuai' : shift.selisih > 0 ? 'Uang lebih' : 'Uang kurang'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        <!-- Transactions -->
                        ${transaksiRows}
                    </div>
                `;
                
                document.getElementById('viewShiftContent').innerHTML = content;
                document.getElementById('viewShiftModal').classList.remove('hidden');
                document.getElementById('viewShiftModal').classList.add('flex');
            } else {
                alert('Gagal memuat detail shift!');
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
        window.location.href = '{{ route("management.shiftman.index") }}?period=' + period;
    }

    // Helper functions
    function formatRupiah(value) {
        return parseInt(value || 0).toLocaleString('id-ID');
    }

    function getMetodeClass(metode) {
        switch(metode) {
            case 'tunai': return 'bg-green-100 text-green-700';
            case 'kartu': return 'bg-blue-100 text-blue-700';
            case 'transfer': return 'bg-purple-100 text-purple-700';
            case 'qris': return 'bg-orange-100 text-orange-700';
            default: return 'bg-gray-100 text-gray-700';
        }
    }

    function getMetodeLabel(metode) {
        switch(metode) {
            case 'tunai': return 'Tunai';
            case 'kartu': return 'Kartu';
            case 'transfer': return 'Transfer';
            case 'qris': return 'QRIS';
            default: return metode;
        }
    }

    function getSelisihClass(selisih) {
        if (selisih === 0) return 'bg-blue-50';
        return selisih > 0 ? 'bg-green-50' : 'bg-red-50';
    }

    function getSelisihTextClass(selisih) {
        if (selisih === 0) return 'text-blue-600';
        return selisih > 0 ? 'text-green-600' : 'text-red-600';
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Shift Management Dashboard loaded');
    });
</script>
@endsection