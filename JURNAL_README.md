# Quick Start - Jurnal Database Integration

## Setup Cepat

1. **Jalankan Migrasi**

    ```bash
    php artisan migrate
    ```

2. **Isi Data Dummy (Optional)**

    ```bash
    php artisan db:seed --class=JurnalSeeder
    ```

3. **Akses Halaman**
    ```
    http://localhost/kasir/jurnal
    ```

## Fitur Utama

✅ Tampil daftar transaksi dari database  
✅ Filter berdasarkan tanggal  
✅ Tambah transaksi (pemasukan/pengeluaran)  
✅ Edit transaksi  
✅ Hapus transaksi  
✅ Summary cards (total pemasukan, pengeluaran, saldo)  
✅ Filter jenis & kategori  
✅ Search keterangan

## File yang Diubah

-   `app/Http/Controllers/Kasir/JurnalController.php` - CRUD logic
-   `app/Models/Jurnal.php` - Model configuration
-   `resources/views/kasir/jurnal/index.blade.php` - View & JavaScript
-   `resources/views/layouts/kasir/index.blade.php` - CSRF token
-   `database/seeders/JurnalSeeder.php` - Data dummy (NEW)

## Testing

Lihat file `JURNAL_DATABASE_INTEGRATION.md` untuk dokumentasi lengkap dan testing guide.

## Troubleshooting

**Error CSRF Token**: Pastikan meta tag csrf-token ada di layout  
**Error 404**: Periksa routes dengan `php artisan route:list | grep jurnal`  
**Data tidak muncul**: Cek koneksi database di `.env`

## Catatan

-   Database menggunakan kolom `nominal` (sudah benar)
-   Kategori sudah tanpa typo: `Operasional`, `Utilitas`, `Transportasi`
-   Migration lama masih ada typo tapi database sudah diperbaiki
