Panduan singkat - Sewa Alat Gunung (MySQL)
1. Buat database:
   - Jalankan: CREATE DATABASE sewa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   - Import file sewa_db.sql lewat phpMyAdmin atau mysql CLI: mysql -u root -p sewa_db < sewa_db.sql

2. Sesuaikan koneksi di config.php jika perlu (DB_HOST, DB_NAME, DB_USER, DB_PASS).

3. Tempatkan seluruh file di folder webserver Anda (misal htdocs/sewa-alat).
   Buka: http://localhost/sewa-alat/

4. Fitur:
   - Kelola alat (alat.php)
   - Buat transaksi sewa (transaksi.php) — mengurangi stok
   - Proses pengembalian pada daftar transaksi (akan menambah stok)
   - Laporan (laporan.php)

5. Keamanan:
   - Saat produksi: tambahkan autentikasi, validasi input, CSRF protection, dan sanitasi lebih ketat.

Butuh saya tambahkan fitur: login admin, PDF laporan, atau API? Balas singkat apa yang mau ditambah.
