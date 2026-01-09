@extends('layouts.manajemen.index')

@section('content')
    <!-- Page Content -->
    <main class="p-4 sm:p-6 lg:p-8">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Jurnal Transaksi</h2>
                    <p class="text-sm text-gray-500 mt-1" id="currentDateTime"></p>
                    <div class="flex items-center mt-2 space-x-2" id="periodInfo">
                        @php
                            $periodLabel = '';
                            $periodIcon = '';
                            $periodColor = '';
                            
                            switch($summary['period'] ?? 'daily') {
                                case 'daily':
                                    $periodLabel = 'Harian';
                                    $periodIcon = 'calendar-day';
                                    $periodColor = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'weekly':
                                    $periodLabel = 'Mingguan';
                                    $periodIcon = 'calendar-week';
                                    $periodColor = 'bg-purple-100 text-purple-800';
                                    break;
                                case 'monthly':
                                    $periodLabel = 'Bulanan';
                                    $periodIcon = 'calendar-month';
                                    $periodColor = 'bg-green-100 text-green-800';
                                    break;
                            }
                        @endphp
                        <span class="px-3 py-1 {{ $periodColor }} text-sm font-medium rounded-full">
                            <i class="fas fa-{{ $periodIcon }} mr-1"></i>{{ $periodLabel }}
                        </span>
                        <span class="text-sm text-gray-600" id="periodDateRange">
                            @if($summary['period'] == 'daily')
                                {{ \Carbon\Carbon::parse($summary['date'])->format('d M Y') }}
                            @elseif($summary['period'] == 'weekly')
                                {{ \Carbon\Carbon::parse($summary['date'])->startOfWeek()->format('d M Y') }} - {{ \Carbon\Carbon::parse($summary['date'])->endOfWeek()->format('d M Y') }}
                            @elseif($summary['period'] == 'monthly')
                                {{ \Carbon\Carbon::parse($summary['date'])->translatedFormat('F Y') }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <!-- Filter Waktu -->
                    <form id="periodForm" method="GET" action="{{ route('management.jurnal.index') }}" class="flex flex-wrap gap-2">
                        <select name="period" id="filterPeriod" onchange="this.form.submit()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                            <option value="daily" {{ request('period', 'daily') == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        
                        <!-- Tanggal -->
                        <input type="date" name="date" id="filterDate" onchange="this.form.submit()" 
                            class="px-3 py-2 border border-gray-300 rounded-lg"
                            value="{{ request('date', date('Y-m-d')) }}">
                        
                        <!-- Hidden fields for other filters -->
                        @if(request('jenis'))
                            <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                        @endif
                        @if(request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        
                        <!-- Tombol Export -->
                        <div class="relative">
                            <button type="button" onclick="toggleExportDropdown()"
                                class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-700 text-white rounded-lg hover:from-green-500 hover:to-green-800 flex items-center">
                                <i class="fas fa-download mr-2"></i>Export
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            <div id="exportDropdown" class="absolute hidden mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                <a href="#" onclick="exportData('excel')" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-excel text-green-500 mr-2"></i>Export Excel
                                </a>
                                <a href="#" onclick="exportData('pdf')" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>Export PDF
                                </a>
                                <a href="#" onclick="exportData('csv')" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-file-csv text-blue-500 mr-2"></i>Export CSV
                                </a>
                            </div>
                        </div>
                    </form>
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
                        <p class="text-3xl font-bold text-green-600">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-receipt mr-2"></i>
                            <span>{{ $summary['revenue_count'] ?? 0 }} transaksi</span>
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
                        <p class="text-3xl font-bold text-red-600">Rp {{ number_format($summary['total_expense'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 flex items-center">
                            <i class="fas fa-calendar-day mr-2"></i>
                            <span>{{ $summary['expense_count'] ?? 0 }} transaksi</span>
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
                        @php
                            $net_balance = $summary['net_balance'] ?? 0;
                            $balance_class = $net_balance > 0 ? 'text-green-600' : ($net_balance < 0 ? 'text-red-600' : 'text-blue-600');
                        @endphp
                        <p class="text-3xl font-bold {{ $balance_class }}">Rp {{ number_format($net_balance, 0, ',', '.') }}</p>
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
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
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
                    <form method="GET" action="{{ route('management.jurnal.index') }}" id="filterForm">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Jenis</label>
                                <select name="jenis" id="filterType" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                    <option value="">Semua Transaksi</option>
                                    <option value="pemasukan" {{ request('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                                <select name="kategori" id="filterCategory" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                    <option value="">Semua Kategori</option>
                                    <option value="Penjualan" {{ request('kategori') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                                    <option value="Bahan Baku" {{ request('kategori') == 'Bahan Baku' ? 'selected' : '' }}>Bahan Baku</option>
                                    <option value="Operasional" {{ request('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="Utilitas" {{ request('kategori') == 'Utilitas' ? 'selected' : '' }}>Utilitas</option>
                                    <option value="Transportasi" {{ request('kategori') == 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                                    <option value="lainnya" {{ request('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                                <div class="flex gap-2">
                                    <input type="text" name="search" id="searchInput" placeholder="Cari keterangan..."
                                        value="{{ request('search') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200">
                                    <button type="submit" class="px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('management.jurnal.index') }}" class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Hidden fields untuk periode -->
                        <input type="hidden" name="period" value="{{ request('period', 'daily') }}">
                        <input type="hidden" name="date" value="{{ request('date', date('Y-m-d')) }}">
                    </form>
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
                            @forelse ($transactions as $transaction)
                                @php
                                    $badgeClass = $transaction->jenis === 'pemasukan' ? 
                                        'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    $iconClass = $transaction->jenis === 'pemasukan' ? 'fa-arrow-up' : 'fa-arrow-down';
                                    $amountClass = $transaction->jenis === 'pemasukan' ? 'text-green-600' : 'text-red-600';
                                    $amountPrefix = $transaction->jenis === 'pemasukan' ? '+' : '-';
                                @endphp
                                
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($transaction->tgl)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                            <i class="fas {{ $iconClass }} mr-1"></i>
                                            {{ $transaction->jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->kategori }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->keterangan }}</td>
                                    <td class="px-6 py-4 text-sm font-medium {{ $amountClass }}">
                                        {{ $amountPrefix }} Rp {{ number_format($transaction->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button onclick="editTransaction({{ $transaction->id }})" 
                                                class="text-green-600 hover:text-green-800" title="Edit Transaksi">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteTransaction({{ $transaction->id }})" 
                                                class="text-red-500 hover:text-red-700" title="Hapus Transaksi">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada data transaksi untuk periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="mb-2 sm:mb-0">
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ $transactions->firstItem() }}</span>
                                -
                                <span class="font-medium">{{ $transactions->lastItem() }}</span>
                                dari
                                <span class="font-medium">{{ $transactions->total() }}</span>
                                transaksi
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Tombol Previous --}}
                                @if ($transactions->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <span class="sr-only">Sebelumnya</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $transactions->previousPageUrl() }}{{ request('jenis') ? '&jenis=' . request('jenis') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('period') ? '&period=' . request('period') : '' }}{{ request('date') ? '&date=' . request('date') : '' }}" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Sebelumnya</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Tombol Halaman --}}
                                @php
                                    $current = $transactions->currentPage();
                                    $last = $transactions->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <a href="{{ $transactions->url(1) }}{{ request('jenis') ? '&jenis=' . request('jenis') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('period') ? '&period=' . request('period') : '' }}{{ request('date') ? '&date=' . request('date') : '' }}" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                                        <a href="{{ $transactions->url($page) }}{{ request('jenis') ? '&jenis=' . request('jenis') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('period') ? '&period=' . request('period') : '' }}{{ request('date') ? '&date=' . request('date') : '' }}" 
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                                    <a href="{{ $transactions->url($last) }}{{ request('jenis') ? '&jenis=' . request('jenis') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('period') ? '&period=' . request('period') : '' }}{{ request('date') ? '&date=' . request('date') : '' }}" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        {{ $last }}
                                    </a>
                                @endif

                                {{-- Tombol Next --}}
                                @if ($transactions->hasMorePages())
                                    <a href="{{ $transactions->nextPageUrl() }}{{ request('jenis') ? '&jenis=' . request('jenis') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('period') ? '&period=' . request('period') : '' }}{{ request('date') ? '&date=' . request('date') : '' }}" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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

                <!-- Table Footer Summary -->
                <div class="p-6 border-t bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total Pemasukan:</span>
                            <span class="text-lg font-bold text-green-600">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total Pengeluaran:</span>
                            <span class="text-lg font-bold text-red-600">Rp {{ number_format($summary['total_expense'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Saldo Bersih:</span>
                            <span class="text-lg font-bold {{ $balance_class }}">Rp {{ number_format($net_balance, 0, ',', '.') }}</span>
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
                    @csrf
                    <input type="hidden" id="transactionId" value="">
                    
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

    let currentEditId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 60000);
      
      // Set today's date as default for modal
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('transactionDate').value = today;
      
      // Add event listener for transaction type change
      document.getElementById('transactionTypeSelect').addEventListener('change', function() {
        populateCategories(this.value);
      });
      
      // Close export dropdown when clicking outside
      document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('exportDropdown');
        const button = document.querySelector('button[onclick="toggleExportDropdown()"]');
        
        if (dropdown && button && !dropdown.contains(event.target) && !button.contains(event.target)) {
          dropdown.classList.add('hidden');
        }
      });
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

    // Edit transaction
    async function editTransaction(id) {
      try {
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

    // Delete transaction
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
          // Reload halaman untuk memperbarui data
          location.reload();
        } else {
          showErrorMessage(data.message);
        }
      } catch (error) {
        console.error('Error deleting transaction:', error);
        showErrorMessage('Gagal menghapus transaksi');
      }
    }

    // Handle form submission
    document.getElementById('transactionForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = {
        tgl: document.getElementById('transactionDate').value,
        jenis: document.getElementById('transactionTypeSelect').value,
        kategori: document.getElementById('transactionCategory').value,
        keterangan: document.getElementById('transactionDescription').value,
        nominal: document.getElementById('transactionAmount').value,
        _token: csrfToken
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
          // Reload halaman untuk memperbarui data
          location.reload();
        } else {
          showErrorMessage(data.message);
        }
      } catch (error) {
        console.error('Error saving transaction:', error);
        showErrorMessage('Gagal menyimpan transaksi');
      }
    });

    // Toggle export dropdown
    function toggleExportDropdown() {
      const dropdown = document.getElementById('exportDropdown');
      if (dropdown) {
        dropdown.classList.toggle('hidden');
      }
    }

    // Export function dengan format
    function exportData(format) {
      const filterPeriod = document.getElementById('filterPeriod').value;
      const filterDate = document.getElementById('filterDate').value;
      const filterJenis = document.getElementById('filterType').value;
      const filterKategori = document.getElementById('filterCategory').value;
      const search = document.getElementById('searchInput').value;
      
      // Buat URL dengan parameter saat ini
      let currentUrl = window.location.href;
      
      // Buat URL baru tanpa parameter page
      const url = new URL(currentUrl);
      url.searchParams.delete('page');
      
      // Tambahkan semua filter parameters
      url.searchParams.set('export', format);
      url.searchParams.set('period', filterPeriod);
      url.searchParams.set('date', filterDate);
      
      if (filterJenis) {
        url.searchParams.set('jenis', filterJenis);
      } else {
        url.searchParams.delete('jenis');
      }
      
      if (filterKategori) {
        url.searchParams.set('kategori', filterKategori);
      } else {
        url.searchParams.delete('kategori');
      }
      
      if (search) {
        url.searchParams.set('search', search);
      } else {
        url.searchParams.delete('search');
      }
      
      // Tutup dropdown
      const dropdown = document.getElementById('exportDropdown');
      if (dropdown) {
        dropdown.classList.add('hidden');
      }
      
      // Tampilkan loading indicator
      showExportLoading();
      
      // Buka URL export di tab baru
      window.open(url.toString(), '_blank');
      
      // Sembunyikan loading setelah 2 detik
      setTimeout(() => {
        hideExportLoading();
      }, 2000);
    }

    // Show loading indicator for export
    function showExportLoading() {
      let loadingDiv = document.getElementById('exportLoading');
      
      if (!loadingDiv) {
        loadingDiv = document.createElement('div');
        loadingDiv.id = 'exportLoading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        loadingDiv.innerHTML = `
          <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"></div>
            <p class="text-gray-700 font-medium">Menyiapkan file export...</p>
            <p class="text-sm text-gray-500 mt-2">Mohon tunggu sebentar</p>
          </div>
        `;
        document.body.appendChild(loadingDiv);
      } else {
        loadingDiv.classList.remove('hidden');
      }
    }

    // Hide loading indicator
    function hideExportLoading() {
      const loadingDiv = document.getElementById('exportLoading');
      if (loadingDiv) {
        loadingDiv.classList.add('hidden');
      }
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
      // Cek apakah sudah ada notifikasi sebelumnya
      const existingNotification = document.querySelector('.fixed-notification');
      if (existingNotification) {
        existingNotification.remove();
      }
      
      const notification = document.createElement('div');
      notification.className = `fixed-notification fixed top-4 right-4 ${color === 'green' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
      notification.innerHTML = `
        <div class="flex items-center">
          <i class="fas ${color === 'green' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
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

    // Close modal when clicking outside
    document.getElementById('transactionModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeTransactionModal();
      }
    });
</script>
@endsection