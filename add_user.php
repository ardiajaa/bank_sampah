<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin(); // Pastikan hanya admin yang bisa akses
require_once __DIR__ . '/functions.php';

$error = '';
$success = '';

// Proses form jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $role = sanitize($_POST['role']);
    
    // Validasi input
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = 'Semua field harus diisi!';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert data user baru
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $hashed_password, $role])) {
            $success = 'User baru berhasil ditambahkan!';
            // Redirect ke manage_users.php setelah 2 detik
            header('Refresh: 2; URL=manage_users.php');
        } else {
            $error = 'Terjadi kesalahan saat menambahkan user baru.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .form-container {
            max-width: 95%;
            margin: 0 auto;
            padding: 1rem;
        }
        @media (min-width: 640px) {
            .form-container {
                max-width: 600px;
            }
        }
        .form-input {
            transition: all 0.3s ease;
            width: 100%;
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        @media (min-width: 640px) {
            .form-input {
                font-size: 1rem;
            }
        }
        .form-input:focus {
            transform: scale(1.01);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        .form-card {
            animation: slideUp 0.5s ease;
            padding: 1.5rem;
        }
        @media (min-width: 640px) {
            .form-card {
                padding: 2rem;
            }
        }
        @keyframes slideUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen">
    <?php include __DIR__ . '/includes/admin_header.php'; ?>
    
    <main class="container mx-auto p-4 sm:p-6">
        <div class="form-container animate__animated animate__fadeIn">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 p-4 sm:p-6 bg-white rounded-xl shadow-lg">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center animate__animated animate__fadeInLeft">
                    <i class="fas fa-user-plus mr-2 sm:mr-3 text-blue-500 text-2xl sm:text-3xl"></i>
                    Tambah User Baru
                </h1>
                <a href="manage_users.php" class="w-full sm:w-auto bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition duration-200 text-sm sm:text-base text-center">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form Tambah User -->
            <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 form-card">
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center animate__animated animate__shakeX text-sm sm:text-base">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center animate__animated animate__fadeIn text-sm sm:text-base">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4 sm:space-y-6">
                    <div class="mb-4 sm:mb-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="name" name="name" required
                                   class="form-input pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <div class="mb-4 sm:mb-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="email" id="email" name="email" required
                                   class="form-input pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="contoh@email.com">
                        </div>
                    </div>

                    <div class="mb-4 sm:mb-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="password" id="password" name="password" required
                                   class="form-input pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Masukkan password">
                        </div>
                    </div>

                    <div class="mb-4 sm:mb-6 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Peran User</label>
                        <div class="relative">
                            <i class="fas fa-user-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="role" name="role" required
                                    class="form-input pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="" disabled selected>Pilih peran user</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3 rounded-lg transition duration-300 transform hover:scale-105 flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Tambah User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
