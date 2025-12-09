<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    //
    protected $table = 'konversi';

    protected $fillable = [
        'satuan_besar',
        'satuan_kecil',
        'jumlah',
        'nilai',
        'tgl',
    ];

    public function satuanBesar()
    {
        return $this->belongsTo(Satuan::class, 'satuan_besar');
    }

    public function satuanKecil()
    {
        return $this->belongsTo(Satuan::class, 'satuan_kecil');
    }
}
