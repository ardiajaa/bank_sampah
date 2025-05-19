<?php
require_once __DIR__ . '/includes/auth_check.php';
requireAdmin();

// Filter dan pencarian
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE u.name LIKE ? OR u.email LIKE ? OR t.jenis LIKE ?" : "";
$params = $search ? ["%$search%", "%$search%", "%$search%"] : [];

// Ambil data transaksi
$stmt = $pdo->prepare("
    SELECT t.*, u.name AS user_name, u.email 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    $where
    ORDER BY t.tanggal DESC
");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Transactions - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/includes/admin_header.php'; ?>
    
    <main class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Semua Transaksi</h1>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                <form method="GET" class="flex w-full sm:w-auto">
                    <input type="text" name="search" placeholder="Cari..." 
                    class="px-3 py-1 border border-gray-300 rounded-l-md w-full sm:w-64"
                    value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-r-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <a href="export_transaction.php" class="bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700 transition-colors text-center flex items-center justify-center gap-1">
                    <i class="fas fa-file-export"></i> 
                    <span class="text-sm">Export</span>
                </a>
                <a href="admin_dashboard.php" class="w-full sm:w-auto bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition duration-200 text-sm sm:text-base text-center">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Berat (kg)</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($transactions) > 0): ?>
                            <?php foreach ($transactions as $t): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs sm:text-sm font-medium"><?= $t['user_name'] ?></div>
                                    <div class="text-xs text-gray-500"><?= $t['email'] ?></div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm"><?= $t['jenis'] ?></td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm"><?= number_format($t['berat']/1000, 2) ?></td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-green-600">Rp <?= number_format($t['debit'], 0, ',', '.') ?></td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">Rp <?= number_format($t['saldo'], 0, ',', '.') ?></td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                                    <?php if ($t['verified']): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Terverifikasi</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Belum Terverifikasi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-3 sm:px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <i class="fas fa-exclamation-circle text-3xl sm:text-4xl text-gray-400"></i>
                                        <p class="text-lg sm:text-xl font-medium text-gray-600">Belum Ada Transaksi</p>
                                        <?php if ($search): ?>
                                            <p class="text-sm sm:text-base text-gray-500">Tidak ada hasil untuk pencarian "<?= htmlspecialchars($search) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>