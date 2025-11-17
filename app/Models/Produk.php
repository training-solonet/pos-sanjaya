<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = [
        'nama',
        'stok',
        'min_stok',
        'harga',
        'kadaluarsa',
    ];

    public function rekapProduk()
    {
        return $this->hasMany(RekapProduk::class, 'id_produk');
    }
     public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_produk');
    }
}
