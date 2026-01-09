<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';

    protected $fillable = [
        'kode_promo',
        'nama_promo',
        'jenis',
        'nilai',
        'min_transaksi',
        'maks_potongan',
        'is_stackable',
        'start_date',
        'end_date',
        'status',
        'stok',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_stackable' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function bundleProducts()
    {
        return $this->hasMany(BundleProduct::class);
    }
}
