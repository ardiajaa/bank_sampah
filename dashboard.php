<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/functions.php';

$user = getUserById($_SESSION['user_id']);
$saldo = getSaldo($_SESSION['user_id']);

// Ambil statistik tambahan dengan penanganan null
$stats = $pdo->prepare("SELECT 
    COUNT(*) as total_transaksi,
    COALESCE(SUM(berat), 0) as total_berat,
    COALESCE(SUM(debit), 0) as total_debit
    FROM transactions WHERE user_id = ?");
$stats->execute([$_SESSION['user_id']]);
$stats = $stats->fetch(PDO::FETCH_ASSOC);

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE user_id = ? AND name LIKE ?" : "WHERE user_id = ?";
$params = $search ? [$_SESSION['user_id'], "%$search%"] : [$_SESSION['user_id']];

$stmt = $pdo->prepare("SELECT t.*, u.name as nama_user 
                      FROM transactions t 
                      JOIN users u ON t.user_id = u.id 
                      $where 
                      ORDER BY tanggal DESC");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .sidebar {
            transition: all 0.3s;
        }
        .profile-pic {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 640px) {
            .stat-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto p-4">
        <!-- Statistik Ringkas -->
        <div class="grid stat-grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card Saldo -->
            <div class="stat-card bg-white rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">Saldo Anda</h3>
                        <p class="text-2xl font-bold text-blue-600">Rp <?= number_format($saldo ?? 0, 0, ',', '.') ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card Total Transaksi -->
            <div class="stat-card bg-white rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">Total Transaksi</h3>
                        <p class="text-2xl font-bold text-purple-600"><?= $stats['total_transaksi'] ?? 0 ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-exchange-alt text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card Total Berat -->
            <div class="stat-card bg-white rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">Total Berat</h3>
                        <p class="text-2xl font-bold text-green-600"><?= number_format($stats['total_berat'] ?? 0, 0, ',', '.') ?> gr</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-weight-hanging text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card Total Debit -->
            <div class="stat-card bg-white rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">Total Debit</h3>
                        <p class="text-2xl font-bold text-indigo-600">Rp <?= number_format($stats['total_debit'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-coins text-indigo-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden animate__animated animate__fadeIn">
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 border-b">
                <h3 class="text-lg font-medium text-gray-800 mb-2 sm:mb-0">Transaksi Terakhir</h3>
                <div class="flex space-x-2">
                    <a href="add_transaction.php" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Transaksi
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kredit</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="8" class="px-4 sm:px-6 py-4 text-center text-gray-500">Tidak ada transaksi</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap"><?= $transaction['nama_user'] ?></td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($transaction['tanggal'])) ?></td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap"><?= $transaction['jenis'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $transaction['nama_user'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($transaction['tanggal'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $transaction['jenis'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $transaction['berat'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-600"><?= $transaction['debit'] > 0 ? 'Rp ' . number_format($transaction['debit'], 0, ',', '.') : '-' ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-red-600"><?= $transaction['kredit'] > 0 ? 'Rp ' . number_format($transaction['kredit'], 0, ',', '.') : '-' ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">Rp <?= number_format($transaction['saldo'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="edit_transaction.php?id=<?= $transaction['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_transaction.php?id=<?= $transaction['id'] ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="mt-4">
        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        $('.sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('-translate-x-full');
        });
    </script>
</body>

</html>