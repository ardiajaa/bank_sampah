<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin(); // Pastikan hanya admin yang bisa akses
require_once __DIR__ . '/functions.php';

// Ambil data untuk dashboard
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM transactions) AS total_transactions,
        (SELECT COALESCE(SUM(debit), 0) FROM transactions) AS total_debit,
        (SELECT COALESCE(SUM(berat)/1000, 0) FROM transactions) AS total_berat_kg
")->fetch(PDO::FETCH_ASSOC);

// Ambil 5 transaksi terbaru
$recent_transactions = $pdo->query("
    SELECT t.*, u.name AS user_name 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.tanggal DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil 5 user terbaru
$recent_users = $pdo->query("
    SELECT id, name, email, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/includes/admin_header.php'; ?>
    
    <main class="container mx-auto p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 sm:gap-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">
                <a href="manage_prices.php" class="w-full sm:w-auto bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm sm:text-base text-center flex items-center justify-center">
                    <i class="fas fa-tags mr-1 sm:mr-2"></i> 
                    <span>Kelola Harga</span>
                </a>
                <a href="reports.php" class="w-full sm:w-auto bg-green-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm sm:text-base text-center flex items-center justify-center">
                    <i class="fas fa-file-export mr-1 sm:mr-2"></i> 
                    <span>Export Laporan</span>
                </a>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card bg-white p-6 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Pengguna</p>
                        <h3 class="text-2xl font-bold"><?= $stats['total_users'] ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card bg-white p-6 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Transaksi</p>
                        <h3 class="text-2xl font-bold"><?= $stats['total_transactions'] ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card bg-white p-6 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Debit</p>
                        <h3 class="text-2xl font-bold">Rp <?= number_format((float)$stats['total_debit'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card bg-white p-6 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-weight-hanging text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Sampah</p>
                        <h3 class="text-2xl font-bold"><?= number_format((float)$stats['total_berat_kg'], 2, ',', '.') ?> kg</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dua Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Transaksi Terbaru -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Transaksi Terbaru</h2>
                    <a href="admin_transaction.php" class="text-blue-600 hover:underline text-sm">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <?php if (empty($recent_transactions)): ?>
                        <div class="p-8 text-center">
                            <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Belum ada transaksi</p>
                        </div>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recent_transactions as $t): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= $t['user_name'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= $t['jenis'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= number_format($t['berat']/1000, 2) ?> kg</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Terbaru -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">User Terbaru</h2>
                    <a href="manage_users.php" class="text-blue-600 hover:underline text-sm">Kelola User</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recent_users as $u): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium"><?= $u['name'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><?= $u['email'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>