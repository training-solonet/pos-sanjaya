<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
</head>
<body>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold; background-color: #2c3e50; color: white;">
                    LAPORAN DETAIL TRANSAKSI - POS SANJAYA
                </th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: left; background-color: #ecf0f1;">
                    Tanggal Laporan: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
                </th>
            </tr>
            @if($metode)
            <tr>
                <th colspan="7" style="text-align: left; background-color: #ecf0f1;">
                    Metode Pembayaran: {{ ucfirst($metode) }}
                </th>
            </tr>
            @endif
            @if($kasirName)
            <tr>
                <th colspan="7" style="text-align: left; background-color: #ecf0f1;">
                    Kasir: {{ $kasirName }}
                </th>
            </tr>
            @endif
            <tr>
                <th colspan="7" style="text-align: left; background-color: #ecf0f1;">
                    Dicetak: {{ now()->format('d F Y H:i:s') }}
                </th>
            </tr>
            <tr>
                <th colspan="7">&nbsp;</th>
            </tr>
            <tr style="background-color: #3498db; color: white; font-weight: bold;">
                <th>ID</th>
                <th>Waktu</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Pembayaran</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
                @php
                    $details = $t->detailTransaksi;
                @endphp
                @foreach($details as $detailIndex => $detail)
                <tr>
                    @if($detailIndex == 0)
                    <td rowspan="{{ $details->count() }}">{{ $t->id_transaksi }}</td>
                    <td rowspan="{{ $details->count() }}">{{ \Carbon\Carbon::parse($t->tgl)->format('d/m/Y H:i') }}</td>
                    @endif
                    <td>{{ optional($detail->produk)->nama ?? 'Produk Tidak Ditemukan' }}</td>
                    <td style="text-align: center;">{{ $detail->jumlah }}</td>
                    @if($detailIndex == 0)
                    <td rowspan="{{ $details->count() }}" style="text-align: right; font-weight: bold;">Rp {{ number_format($t->bayar, 0, ',', '.') }}</td>
                    <td rowspan="{{ $details->count() }}">{{ ucfirst($t->metode) }}</td>
                    <td rowspan="{{ $details->count() }}">{{ optional($t->user)->name ?? 'Unknown' }}</td>
                    @endif
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    Tidak ada data transaksi
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="3">RINGKASAN</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3">Total Transaksi</td>
                <td colspan="4">{{ $totalTransaksi }}</td>
            </tr>
            <tr>
                <td colspan="3">Total Item Terjual</td>
                <td colspan="4">{{ number_format($totalItem) }}</td>
            </tr>
            <tr>
                <td colspan="3">Total Penjualan</td>
                <td colspan="4">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
            @if($byPayment->count() > 0)
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="3">RINGKASAN PER METODE PEMBAYARAN</td>
                <td colspan="4"></td>
            </tr>
            @foreach($byPayment as $payment)
            <tr>
                <td colspan="3">{{ $payment['method'] }} ({{ $payment['count'] }} transaksi)</td>
                <td colspan="4">Rp {{ number_format($payment['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #2c3e50; color: white; font-weight: bold;">
                <td colspan="3">TOTAL KESELURUHAN</td>
                <td colspan="4">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tfoot>
    </table>
</body>
</html>
