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

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
