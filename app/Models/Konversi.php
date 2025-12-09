<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    protected $table = 'konversi';

    protected $fillable = [
        'id_satuan',
        'satuan_besar',
        'jumlah',
        'satuan_kecil',
        'nilai',
        'tgl',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function bahanBaku()
    {
        return $this->hasMany(BahanBaku::class, 'id_konversi');
    }

    // Helper untuk mendapatkan deskripsi konversi
    public function getDeskripsiAttribute()
    {
        return "1 {$this->satuan_besar} = {$this->nilai} {$this->satuan_kecil}";
    }
}