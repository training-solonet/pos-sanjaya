<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Manajemen - {{ $periodLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
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
    </style>
</head>
<body>
    <div class="header">
        <h2>JURNAL KEUANGAN MANAJEMEN</h2>
        <p>Periode: {{ $periodLabel }} ({{ ucfirst($period) }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Tanggal/Waktu</th>
                <th width="10%">Jenis</th>
                <th width="12%">Kategori</th>
                <th width="8%">Role</th>
                <th width="38%">Keterangan</th>
                <th width="16%" class="text-right">Nominal</th>
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
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($tgl)->format('d/m/Y H:i') }}</td>
                <td class="{{ $jenis == 'pemasukan' ? 'pemasukan' : 'pengeluaran' }}">
                    {{ ucfirst($jenis) }}
                </td>
                <td>{{ $kategori }}</td>
                <td>{{ ucfirst($role) }}</td>
                <td>{{ $keterangan }}</td>
                <td class="text-right">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data jurnal</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table style="width: 50%; margin-left: auto; border: 2px solid #000;">
            <tr>
                <td style="font-weight: bold;">Total Pemasukan:</td>
                <td class="text-right pemasukan" style="font-weight: bold;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Pengeluaran:</td>
                <td class="text-right pengeluaran" style="font-weight: bold;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid #000;">
                <td style="font-weight: bold;">Saldo Bersih:</td>
                <td class="text-right" style="font-weight: bold; font-size: 13px;">
                    Rp {{ number_format($saldoBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
