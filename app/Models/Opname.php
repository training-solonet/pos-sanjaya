<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opname extends Model
{
    protected $table = 'opname';

    protected $fillable = [
        'id_bahan',
        'tgl',
        'stok',
        'catatan',
    ];

    protected $casts = [
        'tgl' => 'datetime',
        'stok' => 'decimal:2',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tgl', now()->toDateString());
    }
}
