<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$id = (int)$_GET['id'];

// Get transaction data
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    $_SESSION['error'] = 'Transaksi tidak ditemukan';
    redirect('dashboard.php');
}

// Delete transaction
$stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
if ($stmt->execute([$id, $_SESSION['user_id']])) {
    $_SESSION['success'] = [
        'title' => 'Transaksi dihapus!',
        'message' => 'Berhasil menghapus transaksi:',
        'details' => [
            'Jenis' => $transaction['jenis'],
            'Berat' => number_format($transaction['berat'], 0, ',', '.') . ' gram',
            'Tanggal' => date('d/m/Y', strtotime($transaction['tanggal']))
        ]
    ];
} else {
    $_SESSION['error'] = 'Gagal menghapus transaksi';
}

redirect('dashboard.php');
?>