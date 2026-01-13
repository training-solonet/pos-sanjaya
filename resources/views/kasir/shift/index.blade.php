@extends('layouts.kasir.index')

@section('page-title', 'Shift Kasir')
@section('page-description', 'Sistem kasir dan penjualan')

@section('content')
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .animate-slide-in { animation: slideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    </style>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
                    <button onclick="openStartShiftModal()" class="px-6 py-3 bg-gradient-to-r from-green-400 to-green-700 text-white font-semibold rounded-xl hover:from-green-500 hover:to-green-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
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
                            
                            <!-- Card untuk semua metode pembayaran -->
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm mb-4">
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Total Penjualan</p>
                                    <p class="font-semibold text-green-600 text-lg" id="activeShiftSales">
                                        @if($activeShift && isset($activeShift->total_penjualan_calculated))
                                            Rp {{ number_format($activeShift->total_penjualan_calculated, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan Tunai</p>
                                    <p class="font-semibold text-blue-600 text-lg" id="activeShiftCashSales">
                                        @if($activeShift && isset($activeShift->penjualan_tunai_calculated))
                                            Rp {{ number_format($activeShift->penjualan_tunai_calculated, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan QRIS</p>
                                    <p class="font-semibold text-green-600 text-lg" id="activeShiftQRISSales">
                                        @if($activeShift && isset($activeShift->penjualan_qris_calculated))
                                            Rp {{ number_format($activeShift->penjualan_qris_calculated, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan Kartu</p>
                                    <p class="font-semibold text-blue-600 text-lg" id="activeShiftCardSales">
                                        @if($activeShift && isset($activeShift->penjualan_kartu_calculated))
                                            Rp {{ number_format($activeShift->penjualan_kartu_calculated, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan Transfer</p>
                                    <p class="font-semibold text-purple-600 text-lg" id="activeShiftTransferSales">
                                        @if($activeShift && isset($activeShift->penjualan_transfer_calculated))
                                            Rp {{ number_format($activeShift->penjualan_transfer_calculated, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Total Transaksi</p>
                                    <p class="font-semibold text-purple-600 text-lg" id="activeShiftTransactions">
                                        @if($activeShift && isset($activeShift->total_transaksi_calculated))
                                            {{ $activeShift->total_transaksi_calculated }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Modal Awal</p>
                                    <p class="font-semibold text-green-600 text-lg" id="activeShiftModal">
                                        @if($activeShift)
                                            Rp {{ number_format($activeShift->modal, 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Uang Seharusnya</p>
                                    <p class="font-semibold text-green-600 text-lg" id="activeShiftExpectedCash">
                                        @if($activeShift)
                                            Rp {{ number_format($activeShift->modal + ($activeShift->total_penjualan_calculated ?? 0), 0, ',', '.') }}
                                        @else
                                            Rp 0
                                        @endif
                                    </p>
                                </div>
                                {{-- <div class="bg-white bg-opacity-50 p-3 rounded-lg shadow-sm">
                                    <p class="text-gray-600 text-xs mb-1">Rumus</p>
                                    <p class="font-semibold text-gray-600 text-xs">Modal + Total Penjualan</p>
                                </div> --}}
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
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
                                    <p class="text-gray-600">Shift ID: <span class="font-semibold text-gray-900">#{{ $activeShift ? str_pad($activeShift->id, 6, '0', STR_PAD_LEFT) : '-' }}</span></p>
                                    <p class="text-gray-600 text-xs mt-2">*Uang seharusnya = Modal awal + Total penjualan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button onclick="openEndShiftModal()" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 whitespace-nowrap">
                        <i class="fas fa-stop mr-2"></i>Tutup Shift
                    </button>
                </div>
            </div>
        </div>

        <!-- Shift History -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Shift</h3>
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
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
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
                                @if($shift->mulai && $shift->selesai)
                                    @php
                                        $start = \Carbon\Carbon::parse($shift->mulai);
                                        $end = \Carbon\Carbon::parse($shift->selesai);
                                        $durasi = round($start->diffInMinutes($end));
                                    @endphp
                                    @if($durasi >= 60)
                                    {{ floor($durasi / 60) }}j {{ $durasi % 60 }}m
                                    @else
                                    {{ $durasi }}m
                                    @endif
                                @else
                                    0m
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-green-600 font-semibold">
                                Rp {{ number_format($shift->modal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-green-600 font-semibold">
                                Rp {{ number_format($shift->total_penjualan ?? 0, 0, ',', '.') }}
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
                                <button onclick="viewShiftDetail({{ $shift->id }})" class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 text-xs transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @if($shifts->isEmpty())
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                <i class="fas fa-history text-2xl mb-2 text-gray-300"></i>
                                <p>Belum ada riwayat shift</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Start Shift Modal -->
    <div id="startShiftModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md modal-content-enter">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-play text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Mulai Shift Baru</h4>
                                <p class="text-sm text-gray-500 mt-1">Isi modal awal untuk memulai shift</p>
                            </div>
                        </div>
                        <button onclick="closeStartShiftModal()" 
                                class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors duration-200">
                            <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kasir</label>
                        <div class="flex items-center space-x-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-green-600 text-sm"></i>
                            </div>
                            <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Modal Awal (Cash) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                                <span class="text-gray-500">Rp</span>
                                <div class="w-6 h-px bg-gray-300"></div>
                            </div>
                            <input type="text" 
                                   id="startShiftModalAmount" 
                                   class="w-full pl-16 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200"
                                   placeholder="0" 
                                   oninput="formatCurrency(this)"
                                   autofocus>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-gray-200 flex space-x-3">
                    <button onclick="closeStartShiftModal()" 
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </button>
                    <button onclick="startShift()" 
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-play-circle mr-2"></i>Mulai Shift
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- End Shift Modal -->
    <div id="endShiftModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden modal-content-enter">
                <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-stop text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Tutup Shift</h4>
                                <p class="text-sm text-gray-500 mt-1">Verifikasi uang kasir sebelum menutup shift</p>
                            </div>
                        </div>
                        <button onclick="closeEndShiftModal()" 
                                class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors duration-200">
                            <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                        </button>
                    </div>
                </div>
                
                <div class="overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="p-6 space-y-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 animate-fade-in">
                            <h5 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                                Ringkasan Shift
                            </h5>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-blue-100">
                                    <p class="text-gray-600 text-xs mb-1">Kasir</p>
                                    <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                </div>
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-blue-100">
                                    <p class="text-gray-600 text-xs mb-1">Durasi</p>
                                    <p class="font-semibold text-gray-900" id="endShiftDuration">-</p>
                                </div>
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-blue-100">
                                    <p class="text-gray-600 text-xs mb-1">Modal Awal</p>
                                    <p class="font-semibold text-green-600" id="endShiftModalAwal">Rp 0</p>
                                </div>
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-blue-100">
                                    <p class="text-gray-600 text-xs mb-1">Total Transaksi</p>
                                    <p class="font-semibold text-blue-600" id="endShiftTotalTrx">0</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div class="bg-white bg-opacity-80 p-4 rounded-lg border border-green-100">
                                    <p class="text-gray-600 text-xs mb-1">Total Penjualan</p>
                                    <p class="font-semibold text-2xl text-green-600" id="endShiftTotalSales">Rp 0</p>
                                </div>
                                <div class="bg-white bg-opacity-80 p-4 rounded-lg border border-blue-100">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan Tunai</p>
                                    <p class="font-semibold text-2xl text-blue-600" id="endShiftCashSales">Rp 0</p>
                                </div>
                            </div>
                            <!-- Card untuk metode pembayaran lainnya -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-green-100">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan QRIS</p>
                                    <p class="font-semibold text-green-600" id="endShiftQRISSales">Rp 0</p>
                                </div>
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-blue-100">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan Kartu</p>
                                    <p class="font-semibold text-blue-600" id="endShiftCardSales">Rp 0</p>
                                </div>
                                <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-purple-100">
                                    <p class="text-gray-600 text-xs mb-1">Penjualan Transfer</p>
                                    <p class="font-semibold text-purple-600" id="endShiftTransferSales">Rp 0</p>
                                </div>
                            </div>
                        </div>

                        <!-- PERBAIKAN: Uang yang Seharusnya Ada menggunakan total penjualan -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5 animate-slide-in">
                            <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-calculator mr-2 text-green-600"></i>
                                Uang yang Seharusnya Ada
                            </h5>
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-3xl font-bold text-green-600" id="expectedCash">Rp 0</p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <i class="fas fa-formula mr-1"></i>
                                        <strong>Modal Awal + Total Penjualan</strong>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        (Semua metode: Tunai, QRIS, Kartu, Transfer)
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="animate-slide-in">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                                Uang Aktual di Laci <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 flex items-center space-x-3">
                                    <span class="text-gray-500 text-lg font-medium">Rp</span>
                                    <div class="w-8 h-px bg-gray-300"></div>
                                </div>
                                <input type="text" 
                                       id="endShiftActualCash" 
                                       class="w-full pl-20 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all duration-200 text-lg font-medium"
                                       placeholder="0" 
                                       oninput="formatCurrency(this); calculateDifference()"
                                       autofocus>
                            </div>
                        </div>

                        <div id="cashDifferenceAlert" class="space-y-3 hidden animate-slide-in">
                            <div id="cashSurplus" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-5 hidden">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-green-900 text-lg">Uang Lebih</p>
                                        <div class="mt-2 p-2 bg-green-100 rounded-lg inline-block">
                                            <span class="font-bold text-green-800">+</span>
                                            <span id="surplusAmount" class="font-bold text-green-800 text-lg">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="cashShortage" class="bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-300 rounded-xl p-5 hidden">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-minus-circle text-red-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-red-900 text-lg">Uang Kurang</p>
                                        <div class="mt-2 p-2 bg-red-100 rounded-lg inline-block">
                                            <span class="font-bold text-red-800">-</span>
                                            <span id="shortageAmount" class="font-bold text-red-800 text-lg">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="cashExact" class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-xl p-5 hidden">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-blue-900 text-lg">Uang Sesuai</p>
                                        <div class="mt-2 p-2 bg-blue-100 rounded-lg inline-block">
                                            <span class="font-bold text-blue-800">Tidak ada selisih</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-gray-200 sticky bottom-0 bg-white flex space-x-3">
                    <button onclick="closeEndShiftModal()" 
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button onclick="endShift()" 
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-stop-circle mr-2"></i>Tutup Shift
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Shift Detail Modal -->
    <div id="viewShiftModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden modal-content-enter">
                <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-eye text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Detail Shift</h4>
                                <p class="text-sm text-gray-500 mt-1">Informasi lengkap shift kasir</p>
                            </div>
                        </div>
                        <button onclick="closeViewShiftModal()" 
                                class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors duration-200">
                            <i class="fas fa-times text-gray-500 hover:text-gray-700"></i>
                        </button>
                    </div>
                </div>
                
                <div class="overflow-y-auto max-h-[calc(90vh-120px)] p-6" id="viewShiftContent">
                    <!-- Content akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let activeShift = null;
    let shiftDurationInterval = null;
    let statsUpdateInterval = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function formatCurrency(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('id-ID');
        }
    }

    function parseCurrency(value) {
        return parseInt(value.replace(/[^0-9]/g, '')) || 0;
    }

    function showNotification(icon, title, text, timer = 3000) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            timer: timer,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true,
            background: '#f9fafb'
        });
    }

    function showError(title, text) {
        return Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK'
        });
    }

    function showConfirmation(title, text, confirmText = 'Ya, Lanjutkan') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        });
    }

    function showSuccess(title, text) {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true
        });
    }

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
        
        if (document.getElementById('endShiftModal').classList.contains('flex')) {
            document.getElementById('endShiftDuration').textContent = durationText;
        }
    }

    function loadShiftStatistics() {
        if (!activeShift || !activeShift.id) return;
        
        fetch(`/kasir/shift/${activeShift.id}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const shift = data.data.shift;
                const stats = data.data.statistik;
                
                document.getElementById('activeShiftTransactions').textContent = stats.total_transaksi;
                document.getElementById('activeShiftSales').textContent = 
                    'Rp ' + parseInt(stats.total_penjualan).toLocaleString('id-ID');
                document.getElementById('activeShiftCashSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_tunai).toLocaleString('id-ID');
                document.getElementById('activeShiftQRISSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_qris).toLocaleString('id-ID');
                document.getElementById('activeShiftCardSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_kartu).toLocaleString('id-ID');
                document.getElementById('activeShiftTransferSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_transfer).toLocaleString('id-ID');
                
                // PERBAIKAN RUMUS: Expected cash = modal + total penjualan (semua metode)
                const expectedCash = parseInt(activeShift.modal) + parseInt(stats.total_penjualan);
                document.getElementById('activeShiftExpectedCash').textContent = 
                    'Rp ' + expectedCash.toLocaleString('id-ID');
                
                activeShift.total_penjualan = stats.total_penjualan;
                activeShift.penjualan_tunai = stats.penjualan_tunai;
                activeShift.penjualan_qris = stats.penjualan_qris;
                activeShift.penjualan_kartu = stats.penjualan_kartu;
                activeShift.penjualan_transfer = stats.penjualan_transfer;
                activeShift.total_transaksi = stats.total_transaksi;
                
                if (document.getElementById('endShiftModal').classList.contains('flex')) {
                    updateEndShiftModal(shift, stats);
                }
            }
        })
        .catch(error => {
            console.error('Error loading shift statistics:', error);
        });
    }

    function updateEndShiftModal(shift, stats) {
        if (!shift || !stats) return;
        
        document.getElementById('endShiftModalAwal').textContent = 
            'Rp ' + parseInt(shift.modal).toLocaleString('id-ID');
        document.getElementById('endShiftTotalTrx').textContent = stats.total_transaksi;
        document.getElementById('endShiftTotalSales').textContent = 
            'Rp ' + parseInt(stats.total_penjualan).toLocaleString('id-ID');
        document.getElementById('endShiftCashSales').textContent = 
            'Rp ' + parseInt(stats.penjualan_tunai).toLocaleString('id-ID');
        document.getElementById('endShiftQRISSales').textContent = 
            'Rp ' + parseInt(stats.penjualan_qris).toLocaleString('id-ID');
        document.getElementById('endShiftCardSales').textContent = 
            'Rp ' + parseInt(stats.penjualan_kartu).toLocaleString('id-ID');
        document.getElementById('endShiftTransferSales').textContent = 
            'Rp ' + parseInt(stats.penjualan_transfer).toLocaleString('id-ID');
        
        // PERBAIKAN RUMUS: expected cash = modal + total penjualan (semua metode)
        const expectedCash = parseInt(shift.modal) + parseInt(stats.total_penjualan);
        document.getElementById('expectedCash').textContent = 
            'Rp ' + expectedCash.toLocaleString('id-ID');
        
        const modalElement = document.getElementById('endShiftModal');
        modalElement.dataset.expected = expectedCash;
        modalElement.dataset.modal = shift.modal;
        modalElement.dataset.totalPenjualan = stats.total_penjualan; // Simpan total penjualan
      
        const actualInput = document.getElementById('endShiftActualCash').value;
        if (actualInput) {
            calculateDifference();
        }
    }

    function openStartShiftModal() {
        const modal = document.getElementById('startShiftModal');
        modal.classList.remove('hidden');
        document.getElementById('startShiftModalAmount').value = '';
        setTimeout(() => {
            document.getElementById('startShiftModalAmount').focus();
        }, 300);
    }

    function closeStartShiftModal() {
        const modal = document.getElementById('startShiftModal');
        modal.classList.add('hidden');
    }

    async function startShift() {
        const modalInput = document.getElementById('startShiftModalAmount').value;
        const modal = parseCurrency(modalInput);
        
        if (modal <= 0) {
            await showError('Validasi Gagal', 'Modal awal harus lebih dari 0!');
            return;
        }
        
        try {
            const response = await fetch('{{ route("kasir.shift.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    modal: modal
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                closeStartShiftModal();
                await showSuccess('Berhasil!', 'Shift berhasil dimulai');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                await showError('Gagal Memulai Shift', data.message || 'Terjadi kesalahan saat memulai shift');
            }
        } catch (error) {
            console.error('Error:', error);
            await showError('Kesalahan Sistem', 'Terjadi kesalahan saat memulai shift!');
        }
    }

    function openEndShiftModal() {
        if (!activeShift || !activeShift.id) return;
        
        const modal = document.getElementById('endShiftModal');
        modal.classList.remove('hidden');
        document.getElementById('endShiftActualCash').value = '';
        document.getElementById('cashDifferenceAlert').classList.add('hidden');
        
        fetch(`/kasir/shift/${activeShift.id}`, {
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
                document.getElementById('endShiftCashSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_tunai).toLocaleString('id-ID');
                document.getElementById('endShiftQRISSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_qris).toLocaleString('id-ID');
                document.getElementById('endShiftCardSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_kartu).toLocaleString('id-ID');
                document.getElementById('endShiftTransferSales').textContent = 
                    'Rp ' + parseInt(stats.penjualan_transfer).toLocaleString('id-ID');
                
                // PERBAIKAN RUMUS: expected cash = modal + total penjualan (semua metode)
                const expectedCash = parseInt(shift.modal) + parseInt(stats.total_penjualan);
                document.getElementById('expectedCash').textContent = 
                    'Rp ' + expectedCash.toLocaleString('id-ID');
                
                const modalElement = document.getElementById('endShiftModal');
                modalElement.dataset.expected = expectedCash;
                modalElement.dataset.modal = shift.modal;
                modalElement.dataset.totalPenjualan = stats.total_penjualan;
                
                setTimeout(() => {
                    document.getElementById('endShiftActualCash').focus();
                }, 300);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Gagal Memuat Data', 'Gagal memuat data shift!');
        });
    }

    function closeEndShiftModal() {
        const modal = document.getElementById('endShiftModal');
        modal.classList.add('hidden');
    }

    function calculateDifference() {
        const actualInput = document.getElementById('endShiftActualCash').value;
        if (!actualInput) {
            document.getElementById('cashDifferenceAlert').classList.add('hidden');
            return;
        }
        
        const actualCash = parseCurrency(actualInput);
        const modal = parseInt(document.getElementById('endShiftModal').dataset.modal || 0);
        const totalPenjualan = parseInt(document.getElementById('endShiftModal').dataset.totalPenjualan || 0);
        // PERBAIKAN RUMUS: expected cash = modal + total penjualan (semua metode)
        const expectedCash = modal + totalPenjualan;
        const difference = actualCash - expectedCash;
        
        document.getElementById('cashDifferenceAlert').classList.remove('hidden');
        document.getElementById('cashSurplus').classList.add('hidden');
        document.getElementById('cashShortage').classList.add('hidden');
        document.getElementById('cashExact').classList.add('hidden');
        
        if (difference > 0) {
            document.getElementById('cashSurplus').classList.remove('hidden');
            document.getElementById('surplusAmount').textContent = 
                'Rp ' + Math.abs(difference).toLocaleString('id-ID');
        } else if (difference < 0) {
            document.getElementById('cashShortage').classList.remove('hidden');
            document.getElementById('shortageAmount').textContent = 
                'Rp ' + Math.abs(difference).toLocaleString('id-ID');
        } else {
            document.getElementById('cashExact').classList.remove('hidden');
        }
    }

    async function endShift() {
        const actualInput = document.getElementById('endShiftActualCash').value;
        const actualCash = parseCurrency(actualInput);
        
        if (actualCash <= 0) {
            await showError('Validasi Gagal', 'Uang aktual di laci wajib diisi dan harus lebih dari 0!');
            return;
        }
        
        const confirmation = await showConfirmation(
            'Tutup Shift?',
            'Yakin ingin menutup shift? Tindakan ini tidak dapat dibatalkan.',
            'Ya, Tutup Shift'
        );
        
        if (!confirmation.isConfirmed) {
            return;
        }
        
        try {
            const response = await fetch(`/kasir/shift/${activeShift.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    uang_aktual: actualCash
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                closeEndShiftModal();
                await showSuccess('Berhasil!', 'Shift berhasil ditutup');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                await showError('Gagal Menutup Shift', data.message || 'Terjadi kesalahan saat menutup shift');
            }
        } catch (error) {
            console.error('Error:', error);
            await showError('Kesalahan Sistem', 'Terjadi kesalahan saat menutup shift!');
        }
    }

    async function viewShiftDetail(shiftId) {
        try {
            document.getElementById('viewShiftContent').innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-gray-600">Memuat data shift...</span>
                </div>
            `;
            
            const modal = document.getElementById('viewShiftModal');
            modal.classList.remove('hidden');
            
            const response = await fetch(`/kasir/shift/${shiftId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const shift = data.data.shift;
                const stats = data.data.statistik;
                const transaksis = data.data.transaksis || [];
                
                const start = new Date(shift.mulai);
                const end = shift.selesai ? new Date(shift.selesai) : null;
                const duration = shift.durasi || 0;
                const hours = Math.floor(duration / 60);
                const minutes = duration % 60;
                
                // PERBAIKAN RUMUS: Uang Seharusnya = Modal Awal + Total Penjualan (semua metode)
                const uangSeharusnya = parseInt(shift.modal) + parseInt(stats.total_penjualan || 0);
                
                let transaksiRows = '';
                if (transaksis.length > 0) {
                    transaksiRows = `
                        <div class="bg-gray-50 rounded-xl p-4 mt-6">
                            <h5 class="font-semibold text-gray-900 mb-3">10 Transaksi Terbaru</h5>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Invoice</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Waktu</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Metode</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Customer</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${transaksis.map((transaksi, index) => {
                                            let metodeClass = 'bg-gray-100 text-gray-700 border border-gray-200';
                                            let metodeText = transaksi.metode || 'unknown';
                                            
                                            if (transaksi.metode === 'tunai') {
                                                metodeClass = 'bg-green-100 text-green-700 border border-green-200';
                                                metodeText = 'Tunai';
                                            } else if (transaksi.metode === 'kartu') {
                                                metodeClass = 'bg-blue-100 text-blue-700 border border-blue-200';
                                                metodeText = 'Kartu';
                                            } else if (transaksi.metode === 'transfer') {
                                                metodeClass = 'bg-purple-100 text-purple-700 border border-purple-200';
                                                metodeText = 'Transfer';
                                            } else if (transaksi.metode === 'qris') {
                                                metodeClass = 'bg-orange-100 text-orange-700 border border-orange-200';
                                                metodeText = 'QRIS';
                                            }
                                            
                                            const invoice = transaksi.id_transaksi || '-';
                                            const customer = transaksi.customer || 'Non-Member';
                                            
                                            return `
                                                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                                    <td class="px-3 py-3 text-sm text-gray-700">${index + 1}</td>
                                                    <td class="px-3 py-3 text-sm text-gray-700 font-mono font-semibold">${invoice}</td>
                                                    <td class="px-3 py-3 text-sm text-gray-700">
                                                        ${new Date(transaksi.tgl).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                                    </td>
                                                    <td class="px-3 py-3 text-sm">
                                                        <span class="px-2 py-1 ${metodeClass} rounded text-xs font-medium">
                                                            ${metodeText}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-3 text-sm text-gray-700">${customer}</td>
                                                    <td class="px-3 py-3 text-sm text-green-600 font-semibold">
                                                        Rp ${parseInt(transaksi.total).toLocaleString('id-ID')}
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                                <h5 class="font-semibold text-gray-900 mb-4">Informasi Shift</h5>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">ID Shift:</span>
                                        <span class="font-semibold text-gray-900">#${shift.id.toString().padStart(6, '0')}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Kasir:</span>
                                        <span class="font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Mulai:</span>
                                        <span class="font-semibold text-gray-900">${formatDateTime(start)}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Selesai:</span>
                                        <span class="font-semibold text-gray-900">${end ? formatDateTime(end) : 'Masih aktif'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Durasi:</span>
                                        <span class="font-semibold text-gray-900">${hours} jam ${minutes} menit</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 rounded-xl p-5 border border-green-200">
                                <h5 class="font-semibold text-gray-900 mb-4">Ringkasan Keuangan</h5>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Modal Awal:</span>
                                        <span class="font-semibold text-gray-900">Rp ${parseInt(shift.modal).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Total Penjualan:</span>
                                        <span class="font-semibold text-green-600 text-lg">Rp ${parseInt(stats.total_penjualan).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="border-t border-green-200 pt-3"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Penjualan Tunai:</span>
                                        <span class="font-semibold text-green-600">Rp ${parseInt(stats.penjualan_tunai || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Penjualan QRIS:</span>
                                        <span class="font-semibold text-green-600">Rp ${parseInt(stats.penjualan_qris || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Penjualan Kartu:</span>
                                        <span class="font-semibold text-green-600">Rp ${parseInt(stats.penjualan_kartu || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Penjualan Transfer:</span>
                                        <span class="font-semibold text-green-600">Rp ${parseInt(stats.penjualan_transfer || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                    <div class="border-t border-green-200 pt-3"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Total Transaksi:</span>
                                        <span class="font-semibold text-blue-600">${stats.total_transaksi}</span>
                                    </div>
                                    ${shift.selesai ? `
                                    <div class="border-t border-green-200 pt-3 flex justify-between items-center">
                                        <span class="text-gray-700 font-medium">Uang Seharusnya:</span>
                                        <span class="font-semibold text-gray-900">Rp ${uangSeharusnya.toLocaleString('id-ID')}</span>
                                        <small class="text-gray-500 text-xs">(Modal + Total Penjualan)</small>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700 font-medium">Uang Setelah Dihitung:</span>
                                        <span class="font-semibold text-gray-900">Rp ${parseInt(shift.uang_aktual || 0).toLocaleString('id-ID')}</span>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        ${shift.selesai ? `
                        <div class="${getSelisihClass(shift.selisih)} rounded-xl p-5 border ${getSelisihBorderClass(shift.selisih)}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="font-semibold text-gray-900 mb-1">Rekapitulasi Kas</h5>
                                    <p class="text-sm text-gray-600">Perbandingan uang seharusnya vs uang setelah dihitung</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Rumus: Uang Seharusnya = Modal Awal + Total Penjualan</strong>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold ${getSelisihTextClass(shift.selisih)}">
                                        ${shift.selisih === 0 ? 'PAS' : (shift.selisih > 0 ? '+' : '-') + 'Rp ' + Math.abs(parseInt(shift.selisih)).toLocaleString('id-ID')}
                                    </div>
                                    <div class="text-sm ${getSelisihTextClass(shift.selisih)} mt-1">
                                        ${shift.selisih === 0 ? '✓ Uang sesuai' : shift.selisih > 0 ? '↑ Uang lebih' : '↓ Uang kurang'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${transaksiRows}
                    </div>
                `;
                
                document.getElementById('viewShiftContent').innerHTML = content;
            } else {
                document.getElementById('viewShiftContent').innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Gagal memuat data shift</h4>
                        <p class="text-gray-500 text-sm">${data.message || 'Terjadi kesalahan saat memuat data'}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading shift detail:', error);
            document.getElementById('viewShiftContent').innerHTML = `
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Error</h4>
                    <p class="text-gray-500 text-sm">Gagal memuat detail shift. Silakan coba lagi.</p>
                </div>
            `;
        }
    }

    function closeViewShiftModal() {
        const modal = document.getElementById('viewShiftModal');
        modal.classList.add('hidden');
    }

    function formatDateTime(date) {
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getSelisihClass(selisih) {
        if (selisih === 0) return 'bg-blue-50';
        return selisih > 0 ? 'bg-green-50' : 'bg-red-50';
    }

    function getSelisihBorderClass(selisih) {
        if (selisih === 0) return 'border-blue-200';
        return selisih > 0 ? 'border-green-200' : 'border-red-200';
    }

    function getSelisihTextClass(selisih) {
        if (selisih === 0) return 'text-blue-600';
        return selisih > 0 ? 'text-green-600' : 'text-red-600';
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($activeShift)
            activeShift = {
                id: {{ $activeShift->id }},
                mulai: '{{ $activeShift->mulai }}',
                modal: {{ $activeShift->modal }},
                total_penjualan: {{ $activeShift->total_penjualan_calculated ?? 0 }},
                penjualan_tunai: {{ $activeShift->penjualan_tunai_calculated ?? 0 }},
                penjualan_qris: {{ $activeShift->penjualan_qris_calculated ?? 0 }},
                penjualan_kartu: {{ $activeShift->penjualan_kartu_calculated ?? 0 }},
                penjualan_transfer: {{ $activeShift->penjualan_transfer_calculated ?? 0 }},
                total_transaksi: {{ $activeShift->total_transaksi_calculated ?? 0 }}
            };
            
            updateShiftDuration();
            shiftDurationInterval = setInterval(updateShiftDuration, 1000);
            loadShiftStatistics();
            statsUpdateInterval = setInterval(loadShiftStatistics, 3000);
        @endif

        const modals = ['startShiftModal', 'endShiftModal', 'viewShiftModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'startShiftModal') closeStartShiftModal();
                        if (modalId === 'endShiftModal') closeEndShiftModal();
                        if (modalId === 'viewShiftModal') closeViewShiftModal();
                    }
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('startShiftModal').classList.contains('hidden')) {
                    closeStartShiftModal();
                }
                if (!document.getElementById('endShiftModal').classList.contains('hidden')) {
                    closeEndShiftModal();
                }
                if (!document.getElementById('viewShiftModal').classList.contains('hidden')) {
                    closeViewShiftModal();
                }
            }
        });
    });

    window.addEventListener('beforeunload', function() {
        if (shiftDurationInterval) clearInterval(shiftDurationInterval);
        if (statsUpdateInterval) clearInterval(statsUpdateInterval);
    });
</script>
@endsection