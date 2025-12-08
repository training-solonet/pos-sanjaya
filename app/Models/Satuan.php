<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    //
    use HasFactory;

    protected $table = 'satuan';

    protected $fillable = [
        'nama',

    ];

    public function bahanBaku()
    {
        return $this->hasMany(BahanBaku::class, 'id_satuan');
    }

    public function konversiBesar()
    {
        return $this->hasMany(Konversi::class, 'satuan_besar');
    }

    public function konversiKecil()
    {
        return $this->hasMany(Konversi::class, 'satuan_kecil');
    }
}
