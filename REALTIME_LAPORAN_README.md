# Real-time Laporan Penjualan

## Fitur yang Diimplementasikan

### 1. Auto-refresh Real-time

-   Data transaksi otomatis diperbarui setiap **5 detik**
-   Indikator visual "Real-time" dengan animasi pulse
-   Menampilkan waktu update terakhir

### 2. Tombol Toggle Auto-refresh

-   Toggle antara mode **Auto** dan **Manual**
-   Animasi spin pada icon saat auto-refresh aktif
-   Warna hijau untuk mode auto, abu-abu untuk manual

### 3. Filter Real-time

-   Filter berdasarkan tanggal, metode pembayaran, dan kasir
-   Data diperbarui tanpa reload halaman
-   Tombol reset untuk menghapus semua filter

### 4. Search Real-time

-   Pencarian berdasarkan nomor invoice atau nama produk
-   Debouncing 300ms untuk menghindari request berlebihan
-   Filter di sisi client untuk respons yang cepat

### 5. Notifikasi Visual

-   Notifikasi muncul saat ada transaksi baru
-   Highlight baris baru dengan background hijau
-   Animasi fade-in untuk baris baru
-   Auto-dismiss setelah 3 detik

### 6. API Endpoint

-   Route: `GET /kasir/laporan/api/transactions`
-   Support query parameters: `tanggal`, `metode`, `kasir`, `search`
-   Response JSON dengan data transaksi

## Cara Menggunakan

### Default Behavior

-   Saat halaman dibuka, auto-refresh langsung aktif
-   Data ditampilkan untuk hari ini (tanggal default)
-   Interval refresh: 5 detik

### Filter Data

1. Pilih tanggal, metode pembayaran, atau kasir
2. Klik tombol "Terapkan"
3. Data akan diperbarui secara real-time

### Reset Filter

-   Klik tombol "Reset" untuk menghapus semua filter
-   Tanggal akan kembali ke hari ini

### Toggle Auto-refresh

-   Klik tombol "Auto" untuk mematikan auto-refresh (menjadi "Manual")
-   Klik lagi untuk mengaktifkan kembali

### Search

-   Ketik di kotak pencarian
-   Hasil akan difilter secara otomatis setelah 300ms

## Technical Details

### Backend (Controller)

File: `app/Http/Controllers/Kasir/LaporanController.php`

Method baru:

```php
public function getTransactions(Request $request)
```

### Routes

File: `routes/web.php`

Route baru:

```php
Route::get('laporan/api/transactions', [KasirLaporanController::class, 'getTransactions'])
    ->name('laporan.api.transactions');
```

### Frontend (View)

File: `resources/views/kasir/laporan/index.blade.php`

Fungsi JavaScript utama:

-   `fetchTransactions()` - Mengambil data dari API
-   `startAutoRefresh()` - Memulai auto-refresh
-   `stopAutoRefresh()` - Menghentikan auto-refresh
-   `toggleAutoRefresh()` - Toggle auto-refresh
-   `renderTransactions()` - Render tabel dengan animasi
-   `showNewTransactionNotification()` - Tampilkan notifikasi transaksi baru

### Konfigurasi

```javascript
const REFRESH_INTERVAL_MS = 5000; // 5 detik
```

Untuk mengubah interval refresh, edit nilai konstanta di atas.

## Browser Compatibility

-   Chrome ✓
-   Firefox ✓
-   Safari ✓
-   Edge ✓

## Performance

-   Menggunakan Fetch API untuk request async
-   Debouncing pada search input
-   Animasi CSS untuk performa optimal
-   Minimal DOM manipulation

## Future Improvements

-   WebSocket untuk real-time push (menghindari polling)
-   Pagination untuk data besar
-   Export dengan filter yang diterapkan
-   Chart real-time untuk visualisasi penjualan
