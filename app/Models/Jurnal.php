<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    //
    protected $table = 'jurnal';

    protected $fillable = [
        'jenis',
        'keterangan',
        'nomimal',
        'kategori',
        'role',
        'tgl',
    ];
}
