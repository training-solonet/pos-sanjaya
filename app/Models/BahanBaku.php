<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama',
        'stok',
        'kategori',
        'min_stok',
        'harga_satuan',
        'id_konversi',
        'tglupdate',
    ];

    protected $casts = [
        'tglupdate' => 'datetime',
        'stok' => 'integer',
        'min_stok' => 'integer',
        'harga_satuan' => 'integer',
    ];

    // Relasi ke Konversi
    public function konversi()
    {
        return $this->belongsTo(Konversi::class, 'id_konversi');
    }

    // Helper method untuk mendapatkan satuan besar
    public function getSatuanBesarAttribute()
    {
        return $this->konversi ? $this->konversi->satuan_besar : 'Satuan';
    }

    // Helper untuk mendapatkan satuan kecil
    public function getSatuanKecilAttribute()
    {
        return $this->konversi ? $this->konversi->satuan_kecil : 'Satuan';
    }

    // Helper untuk mendapatkan jumlah konversi
    public function getJumlahKonversiAttribute()
    {
        return $this->konversi ? $this->konversi->jumlah : 1;
    }

    // Stok dalam satuan kecil (yang disimpan di database)
    public function getStokDalamSatuanKecilAttribute()
    {
        return $this->stok;
    }

    // Stok dalam satuan besar (dihitung dari stok kecil)
    public function getStokDalamSatuanBesarAttribute()
    {
        if ($this->konversi && $this->konversi->jumlah > 0) {
            return $this->stok / $this->konversi->jumlah;
        }

        return $this->stok;
    }

    // Format stok untuk ditampilkan
    public function getStokDisplayAttribute()
    {
        if ($this->konversi && $this->konversi->jumlah > 0) {
            $stokBesar = floor($this->stok / $this->konversi->jumlah);
            $sisaStok = $this->stok % $this->konversi->jumlah;

            if ($sisaStok > 0) {
                return [
                    'besar' => number_format($stokBesar, 0).' '.$this->satuan_besar.' + '.$sisaStok.' '.$this->satuan_kecil,
                    'kecil' => number_format($this->stok, 0).' '.$this->satuan_kecil,
                ];
            } else {
                return [
                    'besar' => number_format($stokBesar, 0).' '.$this->satuan_besar,
                    'kecil' => number_format($this->stok, 0).' '.$this->satuan_kecil,
                ];
            }
        }

        return [
            'besar' => number_format($this->stok, 0).' '.$this->satuan_besar,
            'kecil' => number_format($this->stok, 0).' '.$this->satuan_kecil,
        ];
    }

    // Method untuk menambah stok dengan konversi
    public function tambahStok($jumlah, $satuan = 'kecil')
    {
        if ($satuan === 'besar' && $this->konversi) {
            $jumlah = $jumlah * $this->konversi->jumlah;
        }

        $this->stok += $jumlah;
        $this->tglupdate = now();
        $this->save();

        return $this;
    }

    // Method untuk mengurangi stok dengan konversi
    public function kurangiStok($jumlah, $satuan = 'kecil')
    {
        if ($satuan === 'besar' && $this->konversi) {
            $jumlah = $jumlah * $this->konversi->jumlah;
        }

        $this->stok -= $jumlah;
        $this->tglupdate = now();
        $this->save();

        return $this;
    }

    // Method untuk cek apakah stok mencukupi
    public function stokCukup($jumlah, $satuan = 'kecil')
    {
        if ($satuan === 'besar' && $this->konversi) {
            $jumlah = $jumlah * $this->konversi->jumlah;
        }

        return $this->stok >= $jumlah;
    }

    public function rincianResep()
    {
        return $this->hasMany(RincianResep::class, 'id_bahan');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_bahan_baku');
    }

    public function opnames()
    {
        return $this->hasMany(Opname::class, 'id_bahan');
    }

    public static function getDefaultBahanBaku()
    {
        // Coba ambil bahan baku pertama
        $bahanBaku = self::first();

        if (! $bahanBaku) {
            // Buat bahan baku default jika tidak ada
            $konversi = Konversi::first();

            if (! $konversi) {
                // Buat konversi default jika tidak ada
                $konversi = Konversi::create([
                    'id_satuan' => 1,
                    'satuan_besar' => 'Karung',
                    'nilai' => 1,
                    'satuan_kecil' => 'kg',
                    'jumlah' => 25,
                    'tgl' => now(),
                ]);
            }

            $bahanBaku = self::create([
                'nama' => 'Bahan Baku Umum',
                'stok' => 0,
                'min_stok' => 0,
                'kategori' => 'Bahan Utama',
                'harga_satuan' => 0,
                'id_konversi' => $konversi->id,
                'tglupdate' => now(),
            ]);
        }

        return $bahanBaku;
    }
}
