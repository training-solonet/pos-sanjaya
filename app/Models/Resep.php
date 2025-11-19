<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'nama',
        'porsi',
        'kategori',
        'waktu_pembuatan',
        'langkah',
        'catatan',
        'status',
    ];

    public function rincianResep()
    {
        return $this->hasMany(RincianResep::class, 'id_resep');
    }
}
