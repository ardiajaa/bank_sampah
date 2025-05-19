<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin(); // Pastikan hanya admin yang bisa akses
require_once __DIR__ . '/functions.php';

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = $_GET['id'];

// Cek apakah user mencoba menghapus dirinya sendiri
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
    header('Location: manage_users.php');
    exit;
}

// Hapus user dari database
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    $_SESSION['success'] = 'User berhasil dihapus!';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan saat menghapus user: ' . $e->getMessage();
}

// Redirect kembali ke halaman manage_users.php
header('Location: manage_users.php');
exit;
?>
