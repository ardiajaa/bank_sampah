# Bank Sampah - Sistem Manajemen Bank Sampah

Proyek ini adalah sistem manajemen bank sampah berbasis web yang dibangun menggunakan PHP Native dengan Laragon, TCPDF untuk generate laporan, dan HTML5 QR Code untuk scan QR Code.

## Login Credentials

### Admin
- Email: admin@admin.com
- Password: mahameru

### User/Nasabah
- Email: ardi@gmail.com
- Password: mahameru

## Fitur Utama

### Fitur Admin
1. Manajemen Data
   - Kelola data nasabah (tambah, edit, hapus)
   - Kelola jenis sampah (kategori, harga per kg)
   - Kelola data admin
2. Transaksi
   - Proses transaksi setor sampah
   - Verifikasi transaksi via QR Code
   - Riwayat transaksi
3. Laporan
   - Generate laporan harian/bulanan
   - Export laporan ke PDF
4. Dashboard
   - Statistik transaksi
   - Grafik perkembangan nasabah
   - Notifikasi transaksi terbaru

### Fitur User/Nasabah
1. Profil
   - Update data pribadi
   - Lihat saldo tabungan
2. Transaksi
   - Setor sampah
   - Riwayat transaksi
3. QR Code
   - Generate QR Code pribadi
   - Scan QR Code untuk verifikasi
4. Dashboard
   - Statistik transaksi pribadi
   - Grafik perkembangan tabungan
   - Notifikasi transaksi terbaru

## Tampilan Aplikasi

### Halaman Login
![Halaman Login](https://github.com/ardiajaa/bank_sampah/blob/main/1.png)

### Dashboard Admin
![Dashboard Admin](https://github.com/ardiajaa/bank_sampah/blob/main/2.png)

### Dashboard User
![Dashboard User](https://github.com/ardiajaa/bank_sampah/blob/main/3.png)

## Teknologi yang Digunakan
- PHP Native
- Laragon (local development environment)
- MySQL
- TCPDF (untuk generate PDF)
- HTML5 QR Code (untuk scan QR Code)
- Chillerlan QR Code (untuk generate QR Code)
- Bootstrap 5 (untuk tampilan)
- Chart.js (untuk visualisasi data)

## Instalasi dengan Laragon

1. Clone repository ini:
   ```bash
   git clone https://github.com/ardiajaa/bank_sampah.git
   ```

2. Letakkan folder proyek di direktori Laragon:
   - Pindahkan folder `bank_sampah` ke `C:\laragon\www`

3. Import database:
   - Buka Laragon dan start Apache & MySQL
   - Buka phpMyAdmin melalui Laragon
   - Buat database baru dengan nama `bank_sampah`
   - Import file `bank_sampah.sql` di \config\bank_sampah.sql

4. Konfigurasi koneksi database:
   Buka file `config.php` di `C:\laragon\www\bank_sampah` dan sesuaikan:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'bank_sampah');
   ```

5. Jalankan aplikasi:
   - Buka browser
   - Akses `http://bank_sampah.test` (Laragon akan otomatis membuat virtual host)
   - Jika virtual host belum dibuat, buka Laragon > Menu > Quick add > Virtual Host






