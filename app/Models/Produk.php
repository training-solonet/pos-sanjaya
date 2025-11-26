<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'id_bahan_baku',
        'nama',
        'stok',
        'min_stok',
        'harga',
        'kadaluarsa',
    ];

    // Tambahkan relasi ke bahan baku
    public function bahan_baku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku');
    }

    public function rekapProduk()
    {
        return $this->hasMany(RekapProduk::class, 'id_produk');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_produk');
    }
}
