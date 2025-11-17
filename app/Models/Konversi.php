<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    //
    protected $table = 'konversi';
    protected $fillable = [
        'jumlah',
        'tgl',  
    ];
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }
}
