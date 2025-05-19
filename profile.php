<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';

$user = getUserById($_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle password change
    if (isset($_POST['current_password'])) {
        $current_password = sanitize($_POST['current_password']);
        $new_password = sanitize($_POST['new_password']);
        $confirm_password = sanitize($_POST['confirm_password']);
        
        if (!password_verify($current_password, $user['password'])) {
            $error = 'Password saat ini salah!';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Password baru dan konfirmasi password tidak cocok!';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                $success = 'Password berhasil diubah!';
            } else {
                $error = 'Terjadi kesalahan saat mengubah password.';
            }
        }
    }
    
    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_pic']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $error = 'Hanya file gambar (JPEG, PNG, GIF) yang diizinkan!';
        } else {
            $upload_dir = 'assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                if ($user['profile_pic'] !== 'default.png') {
                    @unlink($upload_dir . $user['profile_pic']);
                }
                
                $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                if ($stmt->execute([$new_filename, $_SESSION['user_id']])) {
                    $_SESSION['profile_pic'] = $new_filename;
                    $success = 'Foto profil berhasil diubah!';
                } else {
                    $error = 'Terjadi kesalahan saat mengupdate database.';
                }
            } else {
                $error = 'Gagal mengupload file. Pastikan folder assets/uploads ada dan memiliki izin tulis';
            }
        }
    }
    
    $user = getUserById($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }
        .profile-pic:hover {
            transform: scale(1.05);
        }
        .profile-pic-container {
            position: relative;
            display: inline-block;
            margin: 0 auto;
        }
        .profile-pic-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            cursor: pointer;
        }
        .profile-pic-container:hover .profile-pic-overlay {
            opacity: 1;
        }
        @media (min-width: 640px) {
            .profile-pic {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-6 sm:px-6">
        <div class="flex flex-col gap-4 sm:flex-row justify-between items-start sm:items-center mb-6">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Profil Pengguna</h1>
            </div>
            <a href="dashboard.php" class="w-full sm:w-auto bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition duration-200 text-sm sm:text-base text-center">
                <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 animate__animated animate__fadeIn">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__shakeX text-sm sm:text-base">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn text-sm sm:text-base">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="text-center mb-6 sm:mb-8">
                <div class="profile-pic-container">
                    <img src="assets/uploads/<?= $user['profile_pic'] ?>" alt="Profile" class="profile-pic">
                    <div class="profile-pic-overlay">
                        <form id="profilePicForm" method="POST" enctype="multipart/form-data">
                            <label for="profile_pic" class="cursor-pointer text-white">
                                <i class="fas fa-camera text-xl sm:text-2xl"></i>
                                <p class="text-xs sm:text-sm mt-1">Ganti Foto</p>
                            </label>
                            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" class="hidden" onchange="document.getElementById('profilePicForm').submit()">
                        </form>
                    </div>
                </div>
                <h2 class="text-lg sm:text-xl font-semibold mt-3 sm:mt-4"><?= $user['name'] ?></h2>
                <p class="text-gray-600 text-sm sm:text-base"><?= $user['email'] ?></p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Akun</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Nama Lengkap</label>
                            <div class="px-3 py-2 bg-gray-100 rounded-md text-sm sm:text-base">
                                <?= $user['name'] ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Email</label>
                            <div class="px-3 py-2 bg-gray-100 rounded-md text-sm sm:text-base">
                                <?= $user['email'] ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Tanggal Bergabung</label>
                            <div class="px-3 py-2 bg-gray-100 rounded-md text-sm sm:text-base">
                                <?= date('d F Y', strtotime($user['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Ubah Password</h3>
                    <form action="profile.php" method="POST">
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-gray-700 text-sm font-bold mb-1">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="new_password" class="block text-gray-700 text-sm font-bold mb-1">Password Baru</label>
                                <input type="password" id="new_password" name="new_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-1">Konfirmasi Password Baru</label>
                                <input type="password" id="confirm_password" name="confirm_password" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition transform hover:scale-105">
                                    <i class="fas fa-key mr-1"></i> Ubah Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>