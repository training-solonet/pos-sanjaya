<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class custommer extends Model
{
    //
    protected $table = 'data_customer';

    protected $fillable = [
        'nama',
        'telepon',
        'email',
    ];

    public function DetailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class, 'id_customer');
    }
}
