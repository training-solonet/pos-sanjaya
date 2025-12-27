<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Kasir - {{ $tanggal }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .pemasukan {
            color: #059669;
        }
        .pengeluaran {
            color: #DC2626;
        }
        .summary {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .summary-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>JURNAL HARIAN KASIR</h2>
        <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal/Waktu</th>
                <th width="12%">Jenis</th>
                <th width="15%">Kategori</th>
                <th width="35%">Keterangan</th>
                <th width="18%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnals as $index => $jurnal)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($jurnal->tgl)->format('d/m/Y H:i') }}</td>
                <td class="{{ $jurnal->jenis == 'pemasukan' ? 'pemasukan' : 'pengeluaran' }}">
                    {{ ucfirst($jurnal->jenis) }}
                </td>
                <td>{{ $jurnal->kategori }}</td>
                <td>{{ $jurnal->keterangan }}</td>
                <td class="text-right">Rp {{ number_format($jurnal->nominal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data jurnal</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table style="width: 50%; margin-left: auto; border: 2px solid #000;">
            <tr>
                <td class="summary-label">Total Pemasukan:</td>
                <td class="text-right pemasukan" style="font-weight: bold;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Pengeluaran:</td>
                <td class="text-right pengeluaran" style="font-weight: bold;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid #000;">
                <td class="summary-label">Saldo Bersih:</td>
                <td class="text-right" style="font-weight: bold; font-size: 14px;">
                    Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: right;">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
