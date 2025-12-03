<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';

    protected $fillable = [
        'tgl',
        'jenis',
        'keterangan',
        'nominal',
        'kategori',
        'role',
    ];

    protected $casts = [
        'tgl' => 'datetime',
    ];
}
