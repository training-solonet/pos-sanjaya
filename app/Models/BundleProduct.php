<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleProduct extends Model
{
    use HasFactory;

    protected $table = 'bundle_products';

    protected $fillable = [
        'promo_id',
        'produk_id',
        'quantity',
    ];

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
