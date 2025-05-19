<?php
session_start();
require 'config/database.php';

$email = 'admin@admin.com';
$password = 'mahameru';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    echo "User ditemukan!<br>";
    echo "Hash di DB: " . $user['password'] . "<br>";
    echo "Password verify: " . (password_verify($password, $user['password']) ? 'Match' : 'Tidak Match');
} else {
    echo "User tidak ditemukan!";
}
?>