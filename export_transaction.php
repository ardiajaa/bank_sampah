<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin();

// Set timezone ke Jakarta, Indonesia
date_default_timezone_set('Asia/Jakarta');

// Filter dan pencarian
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE u.name LIKE ? OR u.email LIKE ? OR t.jenis LIKE ?" : "";
$params = $search ? ["%$search%", "%$search%", "%$search%"] : [];

// Ambil data transaksi
$stmt = $pdo->prepare("
    SELECT t.*, u.name AS user_name, u.email 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    $where
    ORDER BY t.tanggal DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

$saldo = 0; // Inisialisasi saldo awal
foreach ($transactions as $t) {
    // Update saldo berdasarkan debit dan kredit
    $saldo += $t['debit'] - $t['kredit'];
    
    echo '<tr>
            <td>'.date('d/m/Y', strtotime($t['tanggal'])).'</td>
            <td>'.$t['user_name'].'</td>
            <td>'.$t['email'].'</td>
            <td>'.$t['jenis'].'</td>
            <td>'.number_format($t['berat']/1000, 2).'</td>
            <td>Rp '.number_format($t['debit'], 0, ',', '.').'</td>
            <td>Rp '.number_format($saldo, 0, ',', '.').'</td>
        </tr>';
}

echo '</table></body></html>';
exit;
?>
