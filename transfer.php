<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';

$user = getUserById($_SESSION['user_id']);
$saldo = getSaldo($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Saldo - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 sm:px-6 py-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Informasi Transfer</h1>
            <a href="dashboard.php" class="bg-white border border-blue-600 text-blue-600 px-4 py-2 rounded-md hover:bg-blue-50 transition duration-200 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Transfer Methods Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6 animate__animated animate__fadeIn">
                    <!-- Transfer Instructions -->
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-blue-600 mb-3">Cara Transfer Saldo</h2>
                        <ol class="list-decimal pl-5 space-y-2 text-gray-700">
                            <li>Lakukan transfer ke salah satu rekening/e-wallet dibawah</li>
                            <li>Setelah transfer, kirim bukti transfer via WhatsApp</li>
                            <li>Saldo akan ditambahkan ke akun Anda setelah konfirmasi</li>
                        </ol>
                    </div>

                    <!-- Bank Transfer Section -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3 flex items-center">
                                <i class="fas fa-university mr-2 text-blue-500"></i>
                                Transfer via Bank
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- BCA Card -->
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-landmark text-blue-600"></i>
                                        </div>
                                        <h4 class="font-medium">BCA</h4>
                                    </div>
                                    <div class="pl-11">
                                        <p class="text-sm text-gray-600">Nomor Rekening:</p>
                                        <p class="font-mono text-lg">1234567890</p>
                                        <p class="text-sm text-gray-600">a.n. Ardi Bank Sampah</p>
                                    </div>
                                </div>

                                <!-- Mandiri Card -->
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-green-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-landmark text-green-600"></i>
                                        </div>
                                        <h4 class="font-medium">Mandiri</h4>
                                    </div>
                                    <div class="pl-11">
                                        <p class="text-sm text-gray-600">Nomor Rekening:</p>
                                        <p class="font-mono text-lg">0987654321</p>
                                        <p class="text-sm text-gray-600">a.n. Ardi Bank Sampah</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-Wallet Section -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3 flex items-center">
                                <i class="fas fa-wallet mr-2 text-purple-500"></i>
                                Transfer via E-Wallet
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- GoPay Card -->
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-purple-100 p-2 rounded-full mr-3">
                                            <i class="fab fa-google-pay text-purple-600"></i>
                                        </div>
                                        <h4 class="font-medium">GoPay</h4>
                                    </div>
                                    <div class="pl-11">
                                        <p class="text-sm text-gray-600">Nomor Telepon:</p>
                                        <p class="font-mono text-lg">6285843978908</p>
                                        <p class="text-sm text-gray-600">a.n. Ardi</p>
                                    </div>
                                </div>

                                <!-- DANA Card -->
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-center mb-3">
                                        <div class="bg-orange-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-money-bill-wave text-orange-600"></i>
                                        </div>
                                        <h4 class="font-medium">DANA</h4>
                                    </div>
                                    <div class="pl-11">
                                        <p class="text-sm text-gray-600">Nomor Telepon:</p>
                                        <p class="font-mono text-lg">6285843978908</p>
                                        <p class="text-sm text-gray-600">a.n. Ardi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Section -->
            <div>
                <div class="bg-white rounded-xl shadow-md p-6 animate__animated animate__fadeIn">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Konfirmasi Transfer</h3>
                        <div class="bg-blue-100 text-blue-800 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm whitespace-nowrap">
                            Saldo Anda: Rp <?= number_format($saldo, 0, ',', '.') ?>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Important Notice -->
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h4 class="font-medium text-yellow-800 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i> Penting!
                            </h4>
                            <p class="text-sm mt-2 text-gray-700">
                                Setelah transfer, harap konfirmasi dengan mengirim bukti transfer ke:
                            </p>
                            <a href="https://wa.me/6285843978908" 
                               class="mt-3 inline-flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md text-sm transition-all duration-200">
                                <i class="fab fa-whatsapp mr-2"></i> WhatsApp: 6285843978908
                            </a>
                        </div>

                        <!-- Verification Process -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-medium text-blue-800 flex items-center">
                                <i class="fas fa-clock mr-2"></i> Proses Verifikasi
                            </h4>
                            <p class="text-sm mt-2 text-gray-700">
                                Saldo akan ditambahkan maksimal 1x24 jam setelah bukti transfer dikirim.
                            </p>
                        </div>
                        
                        <!-- Help Section -->
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="font-medium text-gray-800 flex items-center">
                                <i class="fas fa-question-circle mr-2"></i> Bantuan
                            </h4>
                            <p class="text-sm mt-2 text-gray-700">
                                Untuk pertanyaan, hubungi kami di:
                                <a href="tel:6285843978908" class="text-blue-600">6285843978908</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>