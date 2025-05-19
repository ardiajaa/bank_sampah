<?php
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);
    
    if ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = 'Pendaftaran berhasil! Anda akan diarahkan ke halaman login dalam 5 detik...';
                $_POST = array(); // Clear form
                // Redirect to login after 5 seconds
                header("refresh:5;url=login.php");
            } else {
                $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .register-bg {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            background-size: 200% 200%;
            animation: gradientBG 10s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .register-card {
            transition: all 0.5s ease;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .register-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
        }
        .input-field {
            transition: all 0.3s ease;
            background: rgba(249, 250, 251, 0.8);
        }
        .input-field:focus {
            background: white;
            transform: scale(1.02);
        }
        .btn-register {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }
        .input-icon-container {
            position: relative;
        }
        .input-icon-container input {
            padding-left: 2.5rem;
        }
        .input-icon-container .icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
        }
    </style>
</head>
<body class="register-bg min-h-screen flex items-center justify-center p-4">
    <div class="register-card w-full max-w-md animate__animated animate__fadeInUp">
        <div class="p-8">
            <div class="text-center mb-8">
                <i class="fas fa-user-plus text-green-600 text-8xl mx-auto mb-8 animate__animated animate__bounceIn"></i>
                <h1 class="text-4xl font-bold text-gray-800 mb-2 animate__animated animate__fadeIn">Bank Sampah</h1>
                <p class="text-gray-600 text-lg animate__animated animate__fadeIn">Buat akun baru</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="POST" class="space-y-6">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="text" id="name" name="name" required 
                               class="input-field w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 relative"
                               placeholder="Masukkan nama lengkap Anda"
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="email" id="email" name="email" required 
                               class="input-field w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 relative"
                               placeholder="Masukkan email Anda"
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="password" id="password" name="password" required 
                               class="input-field w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 relative"
                               placeholder="••••••••">
                    </div>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               class="input-field w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 relative"
                               placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" 
                        class="btn-register w-full text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </button>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-gray-600">Sudah punya akun? <a href="login.php" class="text-green-600 hover:underline font-semibold">Masuk disini</a></p>
            </div>
        </div>
    </div>
</body>
</html>