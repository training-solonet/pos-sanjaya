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
        'nama_bahan',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
}
