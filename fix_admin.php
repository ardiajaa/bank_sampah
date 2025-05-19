<?php
require 'config/database.php';

// Hapus admin lama (jika ada)
$pdo->exec("DELETE FROM users WHERE email = 'admin@admin.com'");

// Buat admin baru
$password = password_hash('mahameru', PASSWORD_BCRYPT);
$pdo->exec("INSERT INTO users (name, email, password, role) 
            VALUES ('Admin', 'admin@admin.com', '$password', 'admin')");

echo "✅ Admin berhasil direset!<br>
      Email: <b>admin@admin.com</b><br>
      Password: <b>mahameru</b>";
?>