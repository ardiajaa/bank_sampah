<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin();

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
header('Content-Disposition: attachment; filename="transaksi_'.date('YmdHis').'.xls"');

// Buat tabel Excel dengan styling yang lebih baik
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #d1d5db;
            text-align: left;
        }
        td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }
        .text-green {
            color: #16a34a;
        }
        .text-yellow {
            color: #ca8a04;
        }
    </style>
</head>
<body>';

echo '<table>
        <tr>
            <th>Tanggal</th>
            <th>Nama User</th>
            <th>Email</th>
            <th>Jenis Transaksi</th>
            <th>Berat (kg)</th>
            <th>Debit</th>
            <th>Saldo</th>
            <th>Status</th>
        </tr>';

$saldo = 0; // Inisialisasi saldo awal
foreach ($transactions as $t) {
    // Update saldo berdasarkan debit dan kredit
    $saldo += $t['debit'] - $t['kredit'];
    
    $status = $t['verified'] ? '<span class="text-green">Terverifikasi</span>' : '<span class="text-yellow">Belum Terverifikasi</span>';
    
    echo '<tr>
            <td>'.date('d/m/Y', strtotime($t['tanggal'])).'</td>
            <td>'.$t['user_name'].'</td>
            <td>'.$t['email'].'</td>
            <td>'.$t['jenis'].'</td>
            <td>'.number_format($t['berat']/1000, 2).'</td>
            <td>Rp '.number_format($t['debit'], 0, ',', '.').'</td>
            <td>Rp '.number_format($saldo, 0, ',', '.').'</td>
            <td>'.$status.'</td>
        </tr>';
}

echo '</table>
</body>
</html>';
exit;
?>
