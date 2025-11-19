<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    //
    protected $table = 'satuan';

    protected $fillable = [
        'nama',

    ];

    public function konversi()
    {
        return $this->hasMany(Konversi::class, 'id_satuan');
    }
}
