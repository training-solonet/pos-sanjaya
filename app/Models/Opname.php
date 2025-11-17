<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opname extends Model
{
    //
    protected $table = 'opname';
    protected $fillable = [
        'stok',
        'catatan',
        'tgl',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
}