<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18px;
            color: #34495e;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            flex: 1;
            color: #333;
        }
        
        .summary-section {
            margin-bottom: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        
        .summary-card h3 {
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .summary-card p {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }
        
        table thead {
            background-color: #2c3e50;
            color: white;
        }
        
        table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table tbody tr:hover {
            background-color: #e8f4f8;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .payment-summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .payment-summary h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .payment-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            padding-top: 12px;
            margin-top: 5px;
            border-top: 2px solid #2c3e50;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-tunai {
            background-color: #27ae60;
            color: white;
        }
        
        .badge-kartu {
            background-color: #3498db;
            color: white;
        }
        
        .badge-transfer {
            background-color: #9b59b6;
            color: white;
        }
        
        .badge-qris {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DETAIL TRANSAKSI</h1>
        <h2>POS Sanjaya</h2>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Tanggal Laporan:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</div>
        </div>
        @if($metode)
        <div class="info-row">
            <div class="info-label">Metode Pembayaran:</div>
            <div class="info-value">{{ ucfirst($metode) }}</div>
        </div>
        @endif
        @if($kasirName)
        <div class="info-row">
            <div class="info-label">Kasir:</div>
            <div class="info-value">{{ $kasirName }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Dicetak:</div>
            <div class="info-value">{{ now()->format('d F Y H:i:s') }}</div>
        </div>
    </div>
    
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Transaksi</h3>
                <p>{{ $totalTransaksi }}</p>
            </div>
            <div class="summary-card" style="border-left-color: #27ae60;">
                <h3>Total Item Terjual</h3>
                <p>{{ number_format($totalItem) }}</p>
            </div>
            <div class="summary-card" style="border-left-color: #e74c3c;">
                <h3>Total Penjualan</h3>
                <p>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">ID</th>
                <th style="width: 12%;">Waktu</th>
                <th style="width: 30%;">Produk</th>
                <th style="width: 8%;" class="text-center">Jumlah</th>
                <th style="width: 15%;" class="text-right">Total</th>
                <th style="width: 10%;" class="text-center">Pembayaran</th>
                <th style="width: 10%;">Kasir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $t)
            @php
                $details = $t->detailTransaksi;
            @endphp
            @foreach($details as $detailIndex => $detail)
            <tr>
                @if($detailIndex == 0)
                <td class="text-center" rowspan="{{ $details->count() }}">{{ $index + 1 }}</td>
                <td rowspan="{{ $details->count() }}">{{ $t->id_transaksi }}</td>
                <td rowspan="{{ $details->count() }}">{{ \Carbon\Carbon::parse($t->tgl)->format('d/m/Y H:i') }}</td>
                @endif
                <td>{{ optional($detail->produk)->nama ?? 'Produk Tidak Ditemukan' }}</td>
                <td class="text-center">{{ $detail->jumlah }}</td>
                @if($detailIndex == 0)
                <td class="text-right" rowspan="{{ $details->count() }}" style="font-weight: bold;">Rp {{ number_format($t->bayar, 0, ',', '.') }}</td>
                <td class="text-center" rowspan="{{ $details->count() }}">
                    <span class="badge badge-{{ strtolower($t->metode) }}">{{ ucfirst($t->metode) }}</span>
                </td>
                <td rowspan="{{ $details->count() }}">{{ optional($t->user)->name ?? 'Unknown' }}</td>
                @endif
            </tr>
            @endforeach
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #7f8c8d;">
                    Tidak ada data transaksi
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($byPayment->count() > 0)
    <div class="payment-summary">
        <h3>Ringkasan Per Metode Pembayaran</h3>
        @foreach($byPayment as $payment)
        <div class="payment-row">
            <span>{{ $payment['method'] }} ({{ $payment['count'] }} transaksi)</span>
            <span>Rp {{ number_format($payment['total'], 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="payment-row">
            <span>TOTAL KESELURUHAN</span>
            <span>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
        </div>
    </div>
    @endif
    
    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem POS Sanjaya</p>
        <p>Dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
