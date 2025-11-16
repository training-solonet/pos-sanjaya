<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    //
    protected $table = 'detail_transaksi';
    protected $fillable = [
        'qty',
        'harga',
    ];
}
