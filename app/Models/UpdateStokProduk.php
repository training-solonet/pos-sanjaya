<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateStokProduk extends Model
{
    protected $table = 'update_stok_produk';

    protected $fillable = [
        'id_produk',
        'stok_awal',
        'stok_baru',
        'total_stok',
        'kadaluarsa',
        'tanggal_update',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'kadaluarsa' => 'datetime',
    ];

    // Relasi ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
