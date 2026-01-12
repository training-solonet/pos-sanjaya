<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdukGagal extends Model
{
    use HasFactory;

    protected $table = 'produk_gagal';

    protected $fillable = [
        'produk_id',
        'nama_produk',
        'jumlah_gagal',
        'keterangan',
        'tanggal_gagal',
        'created_by',
    ];

    protected $casts = [
        'tanggal_gagal' => 'date',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailProdukGagal::class, 'produk_gagal_id');
    }

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'detail_produk_gagal', 'produk_gagal_id', 'bahan_baku_id')
            ->withPivot('jumlah_digunakan', 'satuan')
            ->withTimestamps();
    }

    public function getTotalBahanBakuAttribute()
    {
        return $this->detail->count();
    }

    public function getFormattedTanggalAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_gagal)->translatedFormat('d F Y');
    }

    public function getTotalBiayaBahanAttribute()
    {
        $total = 0;
        foreach ($this->detail as $detail) {
            $bahan = $detail->bahanBaku;
            if ($bahan) {
                if ($detail->satuan === 'besar') {
                    $total += ($detail->jumlah_digunakan * $bahan->harga_satuan);
                } else {
                    $jumlahBesar = $detail->jumlah_digunakan / ($bahan->konversi ? $bahan->konversi->jumlah : 1);
                    $total += ($jumlahBesar * $bahan->harga_satuan);
                }
            }
        }

        return $total;
    }
}
