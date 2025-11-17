<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapProduk extends Model
{
    //
    protected $table = 'rekap_produk';
    protected $fillable = [
        'nama',
        'masuk',
        'keluar',
        'tgl',
        'stok',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
