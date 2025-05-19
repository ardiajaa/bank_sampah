<?php
// Ubah path sesuai struktur folder Anda
require_once __DIR__ . '/../functions.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: " . base_url('login.php'));
    exit();
}

// Untuk halaman admin
function requireAdmin() {
    if ($_SESSION['user_role'] !== 'admin') {
        header("Location: " . base_url('unauthorized.php'));
        exit();
    }
}