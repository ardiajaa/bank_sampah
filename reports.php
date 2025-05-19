<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin(); // Pastikan hanya admin yang bisa akses

// Set timezone ke Jakarta, Indonesia
date_default_timezone_set('Asia/Jakarta');

// Ambil data transaksi
$transactions = $pdo->query("
    SELECT t.*, u.name AS user_name, u.email 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil statistik
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM transactions) AS total_transactions,
        (SELECT SUM(debit) FROM transactions) AS total_debit,
        (SELECT SUM(berat)/1000 FROM transactions) AS total_berat_kg
")->fetch(PDO::FETCH_ASSOC);

// Set header untuk file Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_bank_sampah_'.date('YmdHis').'.xls"');

// Mulai membuat tabel Excel
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
    <style>
        .title { font-size: 18pt; font-weight: bold; }
        .header { background: #4F81BD; color: white; font-weight: bold; }
        .stat { background: #D6E3BC; }
        .total { background: #F2DCDB; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 5px; }
    </style>
</head>
<body>';

// Judul Laporan
echo '<table>
        <tr>
            <td colspan="7" class="title">Laporan Bank Sampah</td>
        </tr>
        <tr>
            <td colspan="7">Tanggal Export: '.date('d/m/Y H:i:s').' WIB</td>
        </tr>
    </table><br>';

// Statistik Utama
echo '<table>
        <tr class="stat">
            <th>Total Pengguna</th>
            <th>Total Transaksi</th>
            <th>Total Saldo</th>
            <th>Total Sampah</th>
        </tr>
        <tr>
            <td>'.$stats['total_users'].'</td>
            <td>'.$stats['total_transactions'].'</td>
            <td>Rp '.number_format($stats['total_debit'], 0, ',', '.').'</td>
            <td>'.number_format($stats['total_berat_kg'], 2, ',', '.').' kg</td>
        </tr>
    </table><br>';

// Detail Transaksi
echo '<table>
        <tr class="header">
            <th>Tanggal</th>
            <th>Nama User</th>
            <th>Email</th>
            <th>Jenis Transaksi</th>
            <th>Berat (kg)</th>
            <th>Debit</th>
            <th>Saldo</th>
        </tr>';

foreach ($transactions as $t) {
    echo '<tr>
            <td>'.date('d/m/Y', strtotime($t['tanggal'])).'</td>
            <td>'.$t['user_name'].'</td>
            <td>'.$t['email'].'</td>
            <td>'.$t['jenis'].'</td>
            <td>'.number_format($t['berat']/1000, 2).'</td>
            <td>Rp '.number_format($t['debit'], 0, ',', '.').'</td>
            <td>Rp '.number_format($t['saldo'], 0, ',', '.').'</td>
        </tr>';
}

echo '</table></body></html>';
exit;
