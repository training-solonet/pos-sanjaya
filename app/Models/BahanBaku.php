<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama',
        'stok',
        'kategori',
        'min_stok',
        'harga_satuan',
        'tglupdate',

    ];

    public function rincianResep()
    {
        return $this->hasMany(RincianResep::class, 'id_bahan');
    }
}
