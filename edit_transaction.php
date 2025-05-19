<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';

if (!isset($_GET['id'])) {
    redirect('transactions.php');
}

$id = (int)$_GET['id'];
$error = '';
$success = '';

// Get transaction data
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    redirect('transactions.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = sanitize($_POST['tanggal']);
    $jenis = sanitize($_POST['jenis']);
    $berat = (int)$_POST['berat'];
    $debit = (float)$_POST['debit'];
    $kredit = (float)$_POST['kredit'];
    
    // Calculate new saldo
    $saldo_sebelumnya = getSaldo($_SESSION['user_id']);
    $saldo = $saldo_sebelumnya + $debit - $kredit;
    
    $stmt = $pdo->prepare("UPDATE transactions SET tanggal = ?, jenis = ?, berat = ?, debit = ?, kredit = ?, saldo = ? 
                          WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$tanggal, $jenis, $berat, $debit, $kredit, $saldo, $id, $_SESSION['user_id']])) {
        $success = 'Transaksi berhasil diperbarui!';
        // Update transaction data
        $transaction = array_merge($transaction, [
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'berat' => $berat,
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldo
        ]);
    } else {
        $error = 'Terjadi kesalahan saat memperbarui transaksi. Silakan coba lagi.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Transaksi</h1>
            <a href="transactions.php" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 animate__animated animate__fadeIn">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__shakeX">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form action="edit_transaction.php?id=<?= $id ?>" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal" class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               value="<?= $transaction['tanggal'] ?>">
                    </div>
                    <div>
                        <label for="jenis" class="block text-gray-700 text-sm font-bold mb-2">Jenis Sampah</label>
                        <select id="jenis" name="jenis" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="Plastik" <?= $transaction['jenis'] === 'Plastik' ? 'selected' : '' ?>>Plastik</option>
                            <option value="Kertas" <?= $transaction['jenis'] === 'Kertas' ? 'selected' : '' ?>>Kertas</option>
                            <option value="Logam" <?= $transaction['jenis'] === 'Logam' ? 'selected' : '' ?>>Logam</option>
                            <option value="Kaca" <?= $transaction['jenis'] === 'Kaca' ? 'selected' : '' ?>>Kaca</option>
                            <option value="Organik" <?= $transaction['jenis'] === 'Organik' ? 'selected' : '' ?>>Organik</option>
                        </select>
                    </div>
                    <div>
                        <label for="berat" class="block text-gray-700 text-sm font-bold mb-2">Berat (gram)</label>
                        <input type="number" id="berat" name="berat" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               value="<?= $transaction['berat'] ?>">
                    </div>
                    <div>
                        <label for="debit" class="block text-gray-700 text-sm font-bold mb-2">Debit (Rp)</label>
                        <input type="number" id="debit" name="debit" min="0" step="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               value="<?= $transaction['debit'] ?>">
                    </div>
                    <div>
                        <label for="kredit" class="block text-gray-700 text-sm font-bold mb-2">Kredit (Rp)</label>
                        <input type="number" id="kredit" name="kredit" min="0" step="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               value="<?= $transaction['kredit'] ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Saldo Saat Ini</label>
                        <div class="px-3 py-2 bg-gray-100 rounded-md">
                            Rp <?= number_format($transaction['saldo'], 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition transform hover:scale-105">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>