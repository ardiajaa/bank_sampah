<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';

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
    <title>Transaksi - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <a href="dashboard.php" class="col-span-2 sm:col-span-1 bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition text-sm sm:text-base text-center">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Data Transaksi</h1>
            </div>
            <a href="add_transaction.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition transform hover:scale-105 text-sm sm:text-base w-full sm:w-auto text-center">
                <i class="fas fa-plus mr-1"></i> Tambah Transaksi
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden animate__animated animate__fadeIn">
            <div class="p-4 border-b bg-gray-50">
                <form method="GET" action="transactions.php" class="flex w-full max-w-md mx-auto">
                    <input type="text" name="search" placeholder="Cari data transaksi..." 
                           class="px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full text-sm sm:text-base"
                           value="<?= $search ?>">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm sm:text-base">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Nama</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Jenis</th>
                            <th class="px-3 sm:px-6 py-3 text-right text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Berat (g)</th>
                            <th class="px-3 sm:px-6 py-3 text-right text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Debit</th>
                            <th class="px-3 sm:px-6 py-3 text-right text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Kredit</th>
                            <th class="px-3 sm:px-6 py-3 text-right text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Saldo</th>
                            <th class="px-3 sm:px-6 py-3 text-center text-xs sm:text-sm font-semibold text-blue-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2"></i><br>
                                    Belum ada transaksi
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $saldo = 0; // Inisialisasi saldo awal
                            foreach ($transactions as $transaction): 
                                // Update saldo berdasarkan debit dan kredit
                                $saldo += $transaction['debit'] - $transaction['kredit'];
                            ?>
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap font-medium"><?= $transaction['nama_user'] ?></td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-gray-500"><?= date('d/m/Y', strtotime($transaction['tanggal'])) ?></td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs sm:text-sm">
                                            <?= $transaction['jenis'] ?>
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-right"><?= number_format($transaction['berat'], 0, ',', '.') ?></td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-right text-green-600 font-medium">
                                        <?= $transaction['debit'] > 0 ? 'Rp ' . number_format($transaction['debit'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-right text-red-600 font-medium">
                                        <?= $transaction['kredit'] > 0 ? 'Rp ' . number_format($transaction['kredit'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-right font-semibold text-gray-700">
                                        Rp <?= number_format($saldo, 0, ',', '.') ?>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2 sm:space-x-3">
                                            <a href="edit_transaction.php?id=<?= $transaction['id'] ?>" 
                                               class="text-blue-500 hover:text-blue-700 transition"
                                               title="Edit">
                                                <i class="fas fa-edit text-sm sm:text-base"></i>
                                            </a>
                                            <a href="delete_transaction.php?id=<?= $transaction['id'] ?>" 
                                               class="text-red-500 hover:text-red-700 transition"
                                               title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                                <i class="fas fa-trash text-sm sm:text-base"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>