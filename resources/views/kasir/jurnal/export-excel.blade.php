<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 16px; font-weight: bold;">JURNAL HARIAN KASIR</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center;">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</th>
        </tr>
        <tr><th colspan="6"></th></tr>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th>No</th>
            <th>Tanggal/Waktu</th>
            <th>Jenis</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jurnals as $index => $jurnal)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($jurnal->tgl)->format('d/m/Y H:i') }}</td>
            <td>{{ ucfirst($jurnal->jenis) }}</td>
            <td>{{ $jurnal->kategori }}</td>
            <td>{{ $jurnal->keterangan }}</td>
            <td style="text-align: right;">{{ number_format($jurnal->nominal, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center;">Tidak ada data jurnal</td>
        </tr>
        @endforelse
        <tr><td colspan="6"></td></tr>
        <tr style="font-weight: bold; background-color: #e8f5e9;">
            <td colspan="5">Total Pemasukan:</td>
            <td style="text-align: right;">{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #ffebee;">
            <td colspan="5">Total Pengeluaran:</td>
            <td style="text-align: right;">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #fff9c4; font-size: 14px;">
            <td colspan="5">Saldo Bersih:</td>
            <td style="text-align: right;">{{ number_format($saldoBersih, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
