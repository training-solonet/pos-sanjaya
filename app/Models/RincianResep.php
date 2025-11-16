<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianResep extends Model
{
    protected $table = 'rincian_resep';
    protected $fillable = [
        'qty',
        'hitungan',
        'harga',
    ];
}
