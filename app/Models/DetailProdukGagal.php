<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProdukGagal extends Model
{
    use HasFactory;

    protected $table = 'detail_produk_gagal';

    protected $fillable = [
        'produk_gagal_id',
        'bahan_baku_id',
        'jumlah_digunakan',
        'satuan',
    ];

    public function produkGagal()
    {
        return $this->belongsTo(ProdukGagal::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function getJumlahDalamSatuanKecilAttribute()
    {
        if ($this->satuan === 'besar' && $this->bahanBaku && $this->bahanBaku->konversi) {
            return $this->jumlah_digunakan * $this->bahanBaku->konversi->jumlah;
        }

        return $this->jumlah_digunakan;
    }
}
