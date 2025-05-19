<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin(); // Pastikan hanya admin yang bisa akses
require_once __DIR__ . '/functions.php';

// Ambil semua data user
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/includes/admin_header.php'; ?>
    
    <main class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Kelola User</h1>
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <a href="admin_dashboard.php" class="w-full sm:w-auto bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition duration-200 text-sm sm:text-base text-center">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali ke Dashboard
                </a>
                <a href="add_user.php" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md flex items-center justify-center text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah User
                </a>
            </div>
        </div>

        <!-- Tabel User -->
        <div class="bg-white rounded-lg shadow overflow-hidden animate__animated animate__fadeIn">
            <div class="p-3 sm:p-4 border-b">
                <h2 class="text-lg sm:text-xl font-semibold">Daftar User</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gray-200 overflow-hidden">
                                        <?php if (!empty($u['profile_pic'])): ?>
                                            <img src="assets/uploads/<?= $u['profile_pic'] ?>" alt="Profile" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-2 sm:ml-4">
                                        <div class="text-xs sm:text-sm font-medium"><?= $u['name'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm"><?= $u['email'] ?></td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm"><?= $u['role'] ?></td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                            <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                <div class="flex space-x-2">
                                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_user.php?id=<?= $u['id'] ?>" class="text-red-600 hover:text-red-900" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
