<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'sku', // Tambahkan ini
        'nama',
        'kategori',
        'stok',
        'min_stok',
        'harga',
        'kadaluarsa',
        'id_bahan_baku',
    ];

    protected $casts = [
        'kadaluarsa' => 'datetime',
    ];

    /**
     * Boot method untuk generate SKU otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produk) {
            // Generate SKU hanya jika belum ada
            if (empty($produk->sku)) {
                $produk->sku = self::generateSKU();
            }
        });

        static::created(function ($produk) {
            // Jika masih belum ada SKU (misalnya dari seeder), generate setelah create
            if (empty($produk->sku)) {
                $produk->sku = 'PROD-' . str_pad($produk->id, 6, '0', STR_PAD_LEFT);
                $produk->saveQuietly(); // save tanpa memanggil event
            }
        });
    }

    /**
     * Generate SKU unik
     */
    public static function generateSKU()
    {
        $latestProduct = self::latest('id')->first();
        $nextId = $latestProduct ? $latestProduct->id + 1 : 1;
        
        return 'PROD-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    // Relasi ke update stok produk
    public function updateStokHistory()
    {
        return $this->hasMany(UpdateStokProduk::class, 'id_produk')
            ->orderBy('tanggal_update', 'desc');
    }

    // Relasi ke bahan baku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku');
    }

    // Method untuk update stok
    public function updateStok($stok_baru, $kadaluarsa_baru = null, $keterangan = null, $sumber = null)
    {
        try {
            $stok_awal = $this->stok;
            $total_stok = $stok_awal + $stok_baru;

            // Update produk
            $this->update([
                'stok' => $total_stok,
                'kadaluarsa' => $kadaluarsa_baru ?? $this->kadaluarsa,
            ]);

            // Simpan history
            UpdateStokProduk::create([
                'id_produk' => $this->id,
                'stok_awal' => $stok_awal,
                'stok_baru' => $stok_baru,
                'total_stok' => $total_stok,
                'kadaluarsa' => $kadaluarsa_baru ?? $this->kadaluarsa,
                'tanggal_update' => now(),
                'keterangan' => $keterangan,
                'sumber' => $sumber,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Get latest kadaluarsa from updates
    public function getLatestKadaluarsa()
    {
        $latestUpdate = $this->updateStokHistory()->first();

        return $latestUpdate ? $latestUpdate->kadaluarsa : $this->kadaluarsa;
    }

    // Hitung sisa hari kadaluarsa dengan benar
    public function getDaysUntilExpired()
    {
        $now = Carbon::now();
        $kadaluarsa = Carbon::parse($this->kadaluarsa);

        // Hitung selisih hari dengan presisi
        $diff = $now->diffInDays($kadaluarsa, false);

        return $diff; // Positif jika masih berlaku, negatif jika sudah expired
    }

    // Hitung sisa hari untuk tampilan (untuk menampilkan "3 hari lagi")
    public function getRemainingDaysForDisplay()
    {
        $days = $this->getDaysUntilExpired();

        if ($days > 0) {
            return $days; // Masih berlaku, sisa X hari
        } elseif ($days == 0) {
            return 0; // Hari ini expired
        } else {
            return $days; // Sudah expired (negatif)
        }
    }

    // Cek status stok
    public function getStockStatus()
    {
        if ($this->stok == 0) {
            return 'habis';
        } elseif ($this->stok <= $this->min_stok) {
            return 'rendah';
        } else {
            return 'aman';
        }
    }

    // Cek status kadaluarsa yang diperbaiki
    public function getExpiryStatus()
    {
        $days = $this->getDaysUntilExpired();

        if ($days < 0) {
            return 'expired';
        } elseif ($days == 0) {
            return 'hari_ini';
        } elseif ($days < 3) { // Kurang dari 3 hari
            return 'kritis';
        } elseif ($days <= 7) {
            return 'mendekati';
        } else {
            return 'aman';
        }
    }

    // Attribute accessor untuk status stok
    protected function statusStok(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getStockStatus(),
        );
    }

    // Attribute accessor untuk status kadaluarsa
    protected function statusKadaluarsa(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getExpiryStatus(),
        );
    }

    // Attribute accessor untuk sisa hari (untuk tampilan)
    protected function sisaHari(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getRemainingDaysForDisplay(),
        );
    }

    // Helper untuk mendapatkan data bahan baku dengan fallback
    public function getBahanBakuSafe()
    {
        return $this->bahanBaku ?: BahanBaku::first();
    }

    // Get expired status untuk warna
    public function getExpiryColor()
    {
        $status = $this->getExpiryStatus();

        switch ($status) {
            case 'expired':
                return [
                    'text' => 'text-red-600',
                    'bg' => 'bg-red-100',
                    'icon' => 'fas fa-exclamation-triangle',
                    'label' => 'Expired',
                ];
            case 'hari_ini':
                return [
                    'text' => 'text-red-600',
                    'bg' => 'bg-red-100',
                    'icon' => 'fas fa-exclamation-triangle',
                    'label' => 'Hari Ini',
                ];
            case 'kritis': // Kurang dari 3 hari
                return [
                    'text' => 'text-red-600',
                    'bg' => 'bg-red-100',
                    'icon' => 'fas fa-exclamation-triangle',
                    'label' => 'Kritis',
                ];
            case 'mendekati':
                return [
                    'text' => 'text-orange-600',
                    'bg' => 'bg-orange-100',
                    'icon' => 'fas fa-clock',
                    'label' => 'Mendekati',
                ];
            default:
                return [
                    'text' => 'text-green-600',
                    'bg' => 'bg-green-100',
                    'icon' => 'fas fa-check-circle',
                    'label' => 'Aman',
                ];
        }
    }
}