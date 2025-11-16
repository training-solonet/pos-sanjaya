<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    //
    protected $table = 'transaksi';
    protected $fillable = [
        'tanggal',
        'metode',
        'ppn',
        'diskon',
        'bayar',
        'kembalian',
    ];
}
