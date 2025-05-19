<?php
$host = 'localhost';
$dbname = 'bank_sampah';
$username = 'root'; // Ganti jika perlu
$password = ''; // Ganti jika perlu

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>