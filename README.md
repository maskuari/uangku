# Uangku

Aplikasi pencatatan keuangan pribadi berbasis Laravel 13 dan MySQL. Setiap akun memiliki saldo dan transaksi sendiri. Data tersimpan di server, sehingga akun yang sama dapat digunakan dari beberapa perangkat.

## Menjalankan di Laragon

1. Pastikan PHP 8.3+, Composer, dan MySQL aktif.
2. Buat database MySQL bernama `uangku` jika belum ada.
3. Salin `.env.example` menjadi `.env`, lalu sesuaikan `APP_URL` dan kredensial `DB_*`.
4. Jalankan:

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan serve
   ```

5. Buka `http://127.0.0.1:8000/register` dan buat akun pertama.

Frontend memakai CSS dan JavaScript statis di `public/assets`, sehingga tidak memerlukan proses build Node.

## Cara kerja saldo

Saldo saat ini = saldo awal + seluruh pemasukan - seluruh pengeluaran. Saldo awal dan target pengeluaran bulanan dapat diatur di halaman **Pengaturan**. Transaksi dapat ditambah, diedit, dihapus, dicari, dan disaring berdasarkan bulan atau jenis.

Halaman **Rekap** menampilkan pemasukan dan pengeluaran tiap tanggal pada bulan yang dipilih, rincian kategori, serta daftar transaksi. Tombol **Ekspor PDF** mengunduh rekap bulan tersebut.

Di **Pengaturan**, transaksi yang lebih tua dari dua bulan dapat dibersihkan secara manual. Saldo saat ini dipertahankan dengan memindahkan nilai bersih transaksi yang dihapus ke saldo awal. Unduh PDF bulan lama sebelum membersihkan data jika arsipnya diperlukan.

## Deploy

Arahkan document root hosting ke folder `public`. Gunakan database MySQL yang sama untuk semua perangkat. Simpan `APP_KEY` dan set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` ke alamat HTTPS situs. Jalankan `php artisan migrate --force` setelah deploy. Jangan mengunggah `.env` ke repositori publik.

## Verifikasi

```bash
php artisan test
```
