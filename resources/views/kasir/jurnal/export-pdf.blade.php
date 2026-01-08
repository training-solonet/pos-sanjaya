<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Harian - {{ $tanggal }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #16a34a;
            font-size: 24px;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        
        .summary-item {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        
        .summary-item.pemasukan {
            border-top: 3px solid #22c55e;
        }
        
        .summary-item.pengeluaran {
            border-top: 3px solid #ef4444;
        }
        
        .summary-item.saldo {
            border-top: 3px solid #3b82f6;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .summary-value.pemasukan {
            color: #16a34a;
        }
        
        .summary-value.pengeluaran {
            color: #dc2626;
        }
        
        .summary-value.saldo {
            color: #2563eb;
        }
        
        .summary-count {
            font-size: 10px;
            color: #999;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #16a34a;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-pemasukan {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .badge-pengeluaran {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN JURNAL HARIAN</h1>
        <h2>Tanggal: {{ $tanggal }}</h2>
    </div>
    
    <div class="summary">
        <div class="summary-item pemasukan">
            <div class="summary-label">Total Pemasukan</div>
            <div class="summary-value pemasukan">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            <div class="summary-count">{{ $jumlahPemasukan }} transaksi</div>
        </div>
        <div class="summary-item pengeluaran">
            <div class="summary-label">Total Pengeluaran</div>
            <div class="summary-value pengeluaran">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            <div class="summary-count">{{ $jumlahPengeluaran }} transaksi</div>
        </div>
        <div class="summary-item saldo">
            <div class="summary-label">Saldo Bersih</div>
            <div class="summary-value saldo">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</div>
            <div class="summary-count">Pemasukan - Pengeluaran</div>
        </div>
    </div>
    
    @if($jurnals->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="8%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="12%">Jenis</th>
                    <th width="15%">Kategori</th>
                    <th width="33%">Keterangan</th>
                    <th width="20%" class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurnals as $index => $jurnal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($jurnal->tgl)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $jurnal->jenis }}">
                            {{ ucfirst($jurnal->jenis) }}
                        </span>
                    </td>
                    <td>{{ $jurnal->kategori }}</td>
                    <td>{{ $jurnal->keterangan }}</td>
                    <td class="text-right" style="color: {{ $jurnal->jenis == 'pemasukan' ? '#16a34a' : '#dc2626' }}; font-weight: bold;">
                        {{ $jurnal->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($jurnal->nominal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            Tidak ada data transaksi untuk tanggal {{ $tanggal }}
        </div>
    @endif
    
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i:s') }} WIB<br>
        Sistem POS Sanjaya - Jurnal Harian
    </div>
</body>
</html>
