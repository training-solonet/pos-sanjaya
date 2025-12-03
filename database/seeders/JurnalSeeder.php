<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurnalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // Data pemasukan
        DB::table('jurnal')->insert([
            [
                'tgl' => $today->setTime(10, 0, 0),
                'jenis' => 'pemasukan',
                'kategori' => 'Penjualan',
                'keterangan' => 'Penjualan roti coklat dan donat pagi',
                'nominal' => 350000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tgl' => $today->setTime(13, 30, 0),
                'jenis' => 'pemasukan',
                'kategori' => 'Penjualan',
                'keterangan' => 'Penjualan kue ulang tahun custom',
                'nominal' => 500000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tgl' => $today->setTime(16, 45, 0),
                'jenis' => 'pemasukan',
                'kategori' => 'Penjualan',
                'keterangan' => 'Penjualan pastry dan croissant sore',
                'nominal' => 250000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Data pengeluaran
        DB::table('jurnal')->insert([
            [
                'tgl' => $today->setTime(9, 0, 0),
                'jenis' => 'pengeluaran',
                'kategori' => 'Utilitas',
                'keterangan' => 'Bayar listrik dan air bulan ini',
                'nominal' => 450000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tgl' => $today->setTime(11, 0, 0),
                'jenis' => 'pengeluaran',
                'kategori' => 'Bahan Baku',
                'keterangan' => 'Pembelian tepung terigu 25kg dan mentega 10kg',
                'nominal' => 750000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tgl' => $today->setTime(14, 0, 0),
                'jenis' => 'pengeluaran',
                'kategori' => 'Operasional',
                'keterangan' => 'Service mesin mixer',
                'nominal' => 200000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Data kemarin untuk testing filter tanggal
        $yesterday = Carbon::yesterday();

        DB::table('jurnal')->insert([
            [
                'tgl' => $yesterday->setTime(10, 0, 0),
                'jenis' => 'pemasukan',
                'kategori' => 'Penjualan',
                'keterangan' => 'Penjualan roti kemarin',
                'nominal' => 400000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tgl' => $yesterday->setTime(15, 0, 0),
                'jenis' => 'pengeluaran',
                'kategori' => 'Transportasi',
                'keterangan' => 'Bensin untuk pengiriman',
                'nominal' => 150000,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
