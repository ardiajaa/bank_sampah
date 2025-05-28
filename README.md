# Bank Sampah - Sistem Manajemen Bank Sampah

Proyek ini adalah sistem manajemen bank sampah berbasis web yang dibangun menggunakan PHP Native, TCPDF untuk generate laporan, dan HTML5 QR Code untuk scan QR Code.

## Fitur Utama
- Manajemen data nasabah
- Transaksi setor sampah
- Generate QR Code untuk nasabah
- Scan QR Code untuk verifikasi transaksi
- Laporan transaksi harian/bulanan
- Dashboard admin

## Teknologi yang Digunakan
- PHP Native
- MySQL
- TCPDF (untuk generate PDF)
- HTML5 QR Code (untuk scan QR Code)
- Chillerlan QR Code (untuk generate QR Code)
- Bootstrap 5 (untuk tampilan)

## Instalasi

1. Clone repository ini:
   ```bash
   git clone https://github.com/ardiajaa/bank_sampah.git
   ```

2. Import database:
   - Buka phpMyAdmin atau MySQL client
   - Buat database baru dengan nama `bank_sampah`
   - Import file `bank_sampah.sql`

3. Konfigurasi koneksi database:
   Buka file `config.php` dan sesuaikan dengan setting database Anda:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'bank_sampah');
   ```

4. Jalankan aplikasi:
   - Buka terminal
   - Masuk ke direktori proyek
   - Jalankan PHP built-in server:
     ```bash
     php -S localhost:8000
     ```
   - Buka browser dan akses `http://localhost:8000`

## Struktur Direktori


