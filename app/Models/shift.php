<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $table = 'shift';

    protected $fillable = [
        'id_user',
        'mulai',
        'selesai',
        'durasi',
        'modal',
        'total_penjualan',
        'penjualan_tunai',
        'total_transaksi',
        'uang_aktual',
        'selisih',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
