<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    //
    protected $table = 'konversi';

    protected $fillable = [
        'id_satuan',
        'satuan_besar',
        'jumlah',
        'satuan_kecil',
        'nilai',
        'tgl',
    ];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

}
