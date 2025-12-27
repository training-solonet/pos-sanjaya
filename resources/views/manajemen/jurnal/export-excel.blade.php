<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">JURNAL KEUANGAN MANAJEMEN</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Periode: {{ $periodLabel }} ({{ ucfirst($period) }})</th>
        </tr>
        <tr><th colspan="7"></th></tr>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th>No</th>
            <th>Tanggal/Waktu</th>
            <th>Jenis</th>
            <th>Kategori</th>
            <th>Role</th>
            <th>Keterangan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jurnals as $index => $jurnal)
        @php
            $isArray = is_array($jurnal);
            $jenis = $isArray ? $jurnal['jenis'] : $jurnal->jenis;
            $tgl = $isArray ? $jurnal['tgl'] : $jurnal->tgl;
            $kategori = $isArray ? $jurnal['kategori'] : $jurnal->kategori;
            $keterangan = $isArray ? $jurnal['keterangan'] : $jurnal->keterangan;
            $nominal = $isArray ? $jurnal['nominal'] : $jurnal->nominal;
            $role = $isArray ? ($jurnal['role'] ?? 'admin') : $jurnal->role;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($tgl)->format('d/m/Y H:i') }}</td>
            <td>{{ ucfirst($jenis) }}</td>
            <td>{{ $kategori }}</td>
            <td>{{ ucfirst($role) }}</td>
            <td>{{ $keterangan }}</td>
            <td style="text-align: right;">{{ number_format($nominal, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center;">Tidak ada data jurnal</td>
        </tr>
        @endforelse
        <tr><td colspan="7"></td></tr>
        <tr style="font-weight: bold; background-color: #e8f5e9;">
            <td colspan="6">Total Pemasukan:</td>
            <td style="text-align: right;">{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #ffebee;">
            <td colspan="6">Total Pengeluaran:</td>
            <td style="text-align: right;">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #fff9c4; font-size: 14px;">
            <td colspan="6">Saldo Bersih:</td>
            <td style="text-align: right;">{{ number_format($saldoBersih, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
