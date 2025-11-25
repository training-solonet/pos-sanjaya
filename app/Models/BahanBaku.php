<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama',
        'stok',
        'kategori',
        'min_stok',
        'harga_satuan',
        'satuan', // Pastikan ini ada
        'tglupdate',
    ];

    protected $casts = [
        'tglupdate' => 'datetime',
    ];

    public function rincianResep()
    {
        return $this->hasMany(RincianResep::class, 'id_bahan');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_bahan_baku');
    }

    public function opnames()
    {
        return $this->hasMany(Opname::class, 'id_bahan');
    }
}