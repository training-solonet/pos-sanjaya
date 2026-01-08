<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    use HasFactory;

    protected $table = 'pajak';

    protected $fillable = [
        'nama_pajak',
        'persen',
        'status',
        'start_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'date',
    ];
}
