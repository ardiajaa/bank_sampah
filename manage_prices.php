<?php
require_once 'includes/auth_check.php';
requireAdmin();

// Ambil harga sampah
$prices = $pdo->query("SELECT * FROM waste_prices ORDER BY jenis")->fetchAll();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update harga dan jenis yang ada
    if (isset($_POST['prices'])) {
        foreach ($_POST['prices'] as $id => $data) {
            $stmt = $pdo->prepare("UPDATE waste_prices SET jenis = ?, harga_per_kg = ? WHERE id = ?");
            $stmt->execute([$data['jenis'], $data['harga'], $id]);
        }
    }
    
    // Tambah jenis sampah baru
    if (!empty($_POST['new_jenis']) && !empty($_POST['new_price'])) {
        $stmt = $pdo->prepare("INSERT INTO waste_prices (jenis, harga_per_kg) VALUES (?, ?)");
        $stmt->execute([$_POST['new_jenis'], $_POST['new_price']]);
    }
    
    $_SESSION['success'] = "Perubahan berhasil disimpan!";
    header("Location: manage_prices.php");
    exit();
}

// Handle DELETE request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM waste_prices WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Jenis sampah berhasil dihapus!";
    header("Location: manage_prices.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Harga Sampah - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/admin_header.php'; ?>
    
    <main class="container mx-auto px-2 sm:px-4 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Kelola Harga Sampah</h1>
            <a href="admin_dashboard.php" class="w-full sm:w-auto bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition duration-200 text-sm sm:text-base text-center">
                <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['success']) && is_string($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn text-sm sm:text-base">
                <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow overflow-hidden animate__animated animate__fadeIn">
            <form method="POST">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Jenis Sampah</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Harga per Kg (Rp)</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Terakhir Diupdate</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($prices as $price): ?>
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="prices[<?= $price['id'] ?>][jenis]" value="<?= $price['jenis'] ?>"
                                           class="w-32 sm:w-48 px-2 sm:px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs sm:text-sm" required>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="prices[<?= $price['id'] ?>][harga]" value="<?= $price['harga_per_kg'] ?>" 
                                           class="w-24 sm:w-32 px-2 sm:px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs sm:text-sm" required>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                    <?= date('d M Y H:i', strtotime($price['updated_at'])) ?>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <a href="?delete=<?= $price['id'] ?>" 
                                       class="text-red-500 hover:text-red-700 transition-colors"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus jenis sampah ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- Baris untuk menambahkan jenis baru -->
                            <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="new_jenis" placeholder="Jenis Sampah Baru"
                                           class="w-32 sm:w-48 px-2 sm:px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs sm:text-sm">
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="new_price" placeholder="Harga per Kg"
                                           class="w-24 sm:w-32 px-2 sm:px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs sm:text-sm">
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                    -
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <button type="submit" class="text-green-500 hover:text-green-700 transition-colors">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-3 sm:px-6 py-4 bg-gray-50 border-t">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 active:scale-95 text-sm sm:text-base flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> 
                            <span>Simpan Perubahan</span>
                        </button>
                        <p class="w-full sm:w-auto text-center sm:text-left text-xs sm:text-sm text-gray-600 leading-tight sm:leading-normal">
                            *Isi jenis dan harga baru untuk menambahkan jenis sampah yang baru
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>