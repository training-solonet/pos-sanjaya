# Integrasi Database Jurnal - POS Sanjaya

## Perubahan yang Dilakukan

### 1. Controller (JurnalController.php)

**File**: `app/Http/Controllers/Kasir/JurnalController.php`

Ditambahkan fungsi-fungsi:

-   `index()` - Menampilkan daftar jurnal berdasarkan tanggal dengan summary
-   `store()` - Menyimpan transaksi baru ke database
-   `edit()` - Mengambil data transaksi untuk diedit
-   `update()` - Mengupdate transaksi yang sudah ada
-   `destroy()` - Menghapus transaksi

### 2. Model (Jurnal.php)

**File**: `app/Models/Jurnal.php`

Konfigurasi model:

-   Table: `jurnal`
-   Fillable fields: `tgl`, `jenis`, `keterangan`, `nomimal`, `kategori`, `role`
-   Cast: `tgl` sebagai datetime

### 3. View (index.blade.php)

**File**: `resources/views/kasir/jurnal/index.blade.php`

Perubahan:

-   Menampilkan data dari database menggunakan loop `@forelse`
-   Summary cards mengambil data dari controller
-   Filter tanggal menggunakan form GET
-   JavaScript untuk AJAX CRUD operations (Create, Read, Update, Delete)
-   Modal form untuk tambah/edit transaksi
-   Validasi form

### 4. Layout (index.blade.php)

**File**: `resources/views/layouts/kasir/index.blade.php`

Ditambahkan:

-   Meta tag CSRF token untuk keamanan AJAX requests

## Struktur Database

**Tabel**: `jurnal`

| Kolom      | Tipe      | Keterangan                     |
| ---------- | --------- | ------------------------------ |
| id         | bigint    | Primary key                    |
| tgl        | datetime  | Tanggal transaksi              |
| jenis      | enum      | 'pemasukan' atau 'pengeluaran' |
| keterangan | text      | Deskripsi transaksi            |
| nominal    | integer   | Jumlah nominal transaksi       |
| kategori   | enum      | Kategori transaksi             |
| role       | enum      | 'admin' atau 'manajemen'       |
| created_at | timestamp | Waktu dibuat                   |
| updated_at | timestamp | Waktu diupdate                 |

## Kategori Transaksi

### Pemasukan:

-   Penjualan
-   Lainnya

### Pengeluaran:

-   Operasional
-   Utilitas
-   Bahan Baku
-   Transportasi
-   Lainnya

## Cara Menggunakan

### 1. Jalankan Migrasi (jika belum)

```bash
php artisan migrate
```

### 2. (Opsional) Jalankan Seeder untuk Data Dummy

Untuk testing, Anda bisa menjalankan seeder untuk mengisi data dummy:

```bash
php artisan db:seed --class=JurnalSeeder
```

Data yang akan dimasukkan:

-   3 transaksi pemasukan hari ini (total Rp 1.100.000)
-   3 transaksi pengeluaran hari ini (total Rp 1.400.000)
-   2 transaksi kemarin untuk testing filter tanggal

### 3. Akses Halaman Jurnal

URL: `http://localhost/kasir/jurnal` atau sesuai konfigurasi Laragon Anda

### 4. Fitur yang Tersedia:

#### a. Filter Tanggal

-   Pilih tanggal di header
-   Sistem akan menampilkan transaksi sesuai tanggal yang dipilih

#### b. Tambah Transaksi

-   Klik tombol "Tambah Pemasukan" (hijau) atau "Tambah Pengeluaran" (merah)
-   Isi form:
    -   Tanggal
    -   Kategori (otomatis sesuai jenis)
    -   Keterangan
    -   Nominal
-   Klik "Simpan"

#### c. Edit Transaksi

-   Klik icon edit (pensil hijau) pada baris transaksi
-   Ubah data yang diperlukan
-   Klik "Simpan"

#### d. Hapus Transaksi

-   Klik icon hapus (tong sampah merah)
-   Konfirmasi penghapusan
-   Data akan terhapus dari database

#### e. Filter Transaksi

-   **Filter Jenis**: Pilih pemasukan, pengeluaran, atau semua
-   **Filter Kategori**: Pilih kategori spesifik atau semua
-   **Search**: Cari berdasarkan keterangan

### 4. Summary Cards

Menampilkan ringkasan untuk tanggal yang dipilih:

-   Total Pemasukan + jumlah transaksi
-   Total Pengeluaran + jumlah transaksi
-   Saldo Bersih (Pemasukan - Pengeluaran)

## Testing

### 1. Test Tambah Transaksi

```
1. Klik "Tambah Pemasukan"
2. Isi:
   - Tanggal: hari ini
   - Kategori: Penjualan
   - Keterangan: Test penjualan roti
   - Nominal: 50000
3. Klik Simpan
4. Cek apakah muncul di tabel dan summary terupdate
```

### 2. Test Edit Transaksi

```
1. Klik icon edit pada transaksi yang baru dibuat
2. Ubah nominal menjadi 75000
3. Klik Simpan
4. Cek apakah data terupdate di tabel
```

### 3. Test Hapus Transaksi

```
1. Klik icon hapus pada transaksi
2. Konfirmasi penghapusan
3. Cek apakah data hilang dari tabel
```

### 4. Test Filter

```
1. Tambah beberapa transaksi dengan jenis dan kategori berbeda
2. Coba filter berdasarkan:
   - Jenis (pemasukan/pengeluaran)
   - Kategori
   - Search keterangan
3. Pastikan filter berfungsi dengan baik
```

## Troubleshooting

### Error: CSRF Token Mismatch

**Solusi**: Pastikan meta tag csrf-token sudah ada di layout dan browser tidak memblokir cookies

### Error: Column not found

**Solusi**: Jalankan migrasi ulang

```bash
php artisan migrate:fresh
```

### Data tidak muncul

**Solusi**:

1. Cek koneksi database di `.env`
2. Pastikan tabel `jurnal` sudah ada
3. Cek di browser console untuk error JavaScript

### Error 404 saat submit form

**Solusi**: Pastikan routes sudah terdaftar

```bash
php artisan route:list | grep jurnal
```

## Routes yang Digunakan

```php
// Kasir routes (prefix: kasir)
GET     /kasir/jurnal           - kasir.jurnal.index (tampil halaman)
POST    /kasir/jurnal           - kasir.jurnal.store (simpan baru)
GET     /kasir/jurnal/{id}/edit - kasir.jurnal.edit (ambil data edit)
PUT     /kasir/jurnal/{id}      - kasir.jurnal.update (update data)
DELETE  /kasir/jurnal/{id}      - kasir.jurnal.destroy (hapus data)
```

## Catatan Penting

1. **Database sudah diperbaiki**: Kolom di database menggunakan nama yang benar (`nominal` bukan `nomimal`) dan kategori sudah tanpa typo (`Operasional`, `Utilitas`, `Transportasi`).

2. **Migration lama**: File migration `2024_01_01_000011_create_jurnal_table.php` masih menggunakan typo, tetapi database yang sebenarnya sudah diperbaiki secara manual atau melalui migration lain.

3. **Validasi**: Form sudah memiliki validasi client-side (JavaScript) dan server-side (Laravel).

4. **Security**: Menggunakan CSRF token untuk melindungi dari CSRF attacks.

5. **Responsif**: UI sudah responsif untuk mobile dan desktop.

## Update Selanjutnya (Opsional)

1. Export ke Excel/PDF
2. Pagination untuk data banyak
3. Grafik statistik
4. Filter range tanggal
5. Print laporan
