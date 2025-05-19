<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/functions.php';

// Load library QRCode
require_once __DIR__.'/vendor/autoload.php';
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Pastikan folder qrcodes ada
$qrDir = __DIR__.'/assets/qrcodes';
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}

// Ambil data harga sampah
$waste_prices = $pdo->query("SELECT jenis, harga_per_kg FROM waste_prices")
                   ->fetchAll(PDO::FETCH_KEY_PAIR);

$error = '';
$success = '';
$qr_code_url = '';
$verification_code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = sanitize($_POST['tanggal']);
    $jenis = sanitize($_POST['jenis']);
    $berat_kg = (float)$_POST['berat_kg'];
    
    // Validasi jenis sampah
    if (!array_key_exists($jenis, $waste_prices)) {
        $error = 'Jenis sampah tidak valid!';
    } else {
        // Hitung debit dan saldo
        $debit = min($berat_kg * $waste_prices[$jenis], 9999999.99);
        $saldo_sebelumnya = getSaldo($_SESSION['user_id']);
        $saldo = min($saldo_sebelumnya + $debit, 9999999.99);
        
        // Prepare statement
        $stmt = $pdo->prepare("INSERT INTO transactions 
                             (user_id, tanggal, jenis, berat, debit, saldo) 
                             VALUES (?, ?, ?, ?, ?, ?)");
        
        try {
            // Eksekusi transaksi
            if ($stmt->execute([
                $_SESSION['user_id'],
                $tanggal,
                $jenis,
                $berat_kg * 1000,
                $debit,
                $saldo
            ])) {
                $transaction_id = $pdo->lastInsertId();
                
                // Generate verification code
                $verification_code = 'VS-' . time() . '-' . bin2hex(random_bytes(3));
                $qrContent = json_encode([
                    'app' => 'BankSampah',
                    'action' => 'verify',
                    'id' => $transaction_id,
                    'code' => $verification_code,
                    'time' => time()
                ]);
                
                // Update transaksi dengan kode verifikasi
                $update = $pdo->prepare("UPDATE transactions SET verification_code = ? WHERE id = ?");
                $update->execute([$verification_code, $transaction_id]);
                
                // Generate QR Code
                $qrFile = "txn_$transaction_id.png";
                $qrPath = $qrDir.'/'.$qrFile;
                
                $options = new QROptions([
                    'version'    => 5,
                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                    'eccLevel'   => QRCode::ECC_L
                ]);
                
                (new QRCode($options))->render($qrContent, $qrPath);
                
                $qr_code_url = 'assets/qrcodes/'.$qrFile;
                $success = 'Transaksi berhasil ditambahkan! Silakan tunjukkan QR Code berikut ke petugas:';
                $_POST = array();
            } else {
                $error = 'Terjadi kesalahan saat menambahkan transaksi.';
            }
        } catch (PDOException $e) {
            $error = 'Nilai transaksi melebihi batas yang diperbolehkan. Silahkan kurangi jumlah sampah.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .qr-container {
            transition: all 0.3s ease;
            max-height: 0;
            overflow: hidden;
        }
        .qr-container.show {
            max-height: 500px;
            padding: 1rem 0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Transaksi</h1>
            <a href="transactions.php" class="col-span-2 sm:col-span-1 bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition text-sm sm:text-base text-center">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $success ?>
                </div>
                <?php if ($qr_code_url): ?>
                    <div class="qr-container show">
                        <img src="<?= $qr_code_url ?>" alt="QR Code" class="mx-auto mb-4">
                        <div class="text-center">
                            <p class="text-gray-700 mb-2">Atau masukkan kode verifikasi berikut:</p>
                            <div class="bg-gray-100 p-3 rounded-lg inline-block">
                                <span class="font-mono text-lg font-bold"><?= $verification_code ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form action="add_transaction.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal" class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               value="<?= isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d') ?>">
                    </div>
                    
                    <div>
                        <label for="jenis" class="block text-gray-700 text-sm font-bold mb-2">Jenis Sampah</label>
                        <select id="jenis" name="jenis" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Pilih Jenis</option>
                            <?php foreach ($waste_prices as $jenis => $harga): ?>
                                <option value="<?= $jenis ?>" <?= isset($_POST['jenis']) && $_POST['jenis'] === $jenis ? 'selected' : '' ?>>
                                    <?= $jenis ?> (Rp <?= number_format($harga, 0, ',', '.') ?>/kg)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="berat_kg" class="block text-gray-700 text-sm font-bold mb-2">Berat (kg)</label>
                        <input type="number" id="berat_kg" name="berat_kg" required min="0.1" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               value="<?= isset($_POST['berat_kg']) ? $_POST['berat_kg'] : '' ?>">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Perkiraan Nilai</label>
                        <div class="px-3 py-2 bg-gray-100 rounded-md" id="perkiraan-nilai">
                            Rp 0
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-1"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>

    <script>
        // Hitung nilai otomatis
        const jenisSelect = document.getElementById('jenis');
        const beratInput = document.getElementById('berat_kg');
        const nilaiDisplay = document.getElementById('perkiraan-nilai');
        
        // Data harga dari PHP
        const hargaSampah = <?= json_encode($waste_prices) ?>;
        
        function hitungNilai() {
            const jenis = jenisSelect.value;
            const berat = parseFloat(beratInput.value) || 0;
            
            if (jenis && berat > 0 && hargaSampah[jenis]) {
                const nilai = berat * hargaSampah[jenis];
                nilaiDisplay.textContent = 'Rp ' + nilai.toLocaleString('id-ID');
            } else {
                nilaiDisplay.textContent = 'Rp 0';
            }
        }
        
        jenisSelect.addEventListener('change', hitungNilai);
        beratInput.addEventListener('input', hitungNilai);
    </script>
</body>
</html>