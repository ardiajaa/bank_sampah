<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/includes/auth_check.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("UPDATE transactions SET 
                          verified = 1, 
                          verified_by = ?, 
                          verified_at = NOW() 
                          WHERE verification_code = ? AND verified = 0");
    $stmt->execute([$_SESSION['user_id'], $data['code']]);
    
    header('Content-Type: application/json');
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Kode tidak valid']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100">
    <?php include __DIR__.'/includes/admin_header.php'; ?>
    
    <div class="container mx-auto p-4 lg:p-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-8 sm:px-10 sm:py-12">
                    <h1 class="text-2xl sm:text-3xl font-bold text-center mb-8">Verifikasi Transaksi</h1>
                    
                    <div class="space-y-6">
                        <div id="reader" class="w-full h-64 sm:h-80 border-2 border-dashed border-gray-300 rounded-lg"></div>
                        
                        <div class="text-center space-y-4">
                            <button id="scan-btn" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                                Mulai Scan QR Code
                            </button>
                            <p class="text-sm text-gray-600">Atau masukkan kode verifikasi manual:</p>
                            <input type="text" id="manual-code" placeholder="Kode verifikasi" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button id="verify-btn" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors">
                                Verifikasi
                            </button>
                        </div>
                        
                        <div id="result" class="mt-4 hidden p-4 rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Library QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
    <script>
    const scanBtn = document.getElementById('scan-btn');
    const verifyBtn = document.getElementById('verify-btn');
    const resultDiv = document.getElementById('result');
    let html5QrCode;
    let isScanning = false;
    
    // Coba buka kamera otomatis saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        startScan();
    });
    
    scanBtn.addEventListener('click', toggleScan);
    verifyBtn.addEventListener('click', verifyManual);
    
    function toggleScan() {
        if (isScanning) {
            stopScan();
        } else {
            startScan();
        }
    }
    
    function startScan() {
        if (isScanning) return;
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: 250 };
        
        // Coba semua kamera yang tersedia
        const cameraIds = [];
        Html5Qrcode.getCameras()
            .then(devices => {
                if (devices && devices.length) {
                    devices.forEach(device => cameraIds.push(device.id));
                    tryNextCamera(0);
                }
            })
            .catch(err => {
                console.error(err);
                showResult('Gagal mengakses kamera. Pastikan izin kamera diberikan.', 'error');
                // Coba lagi setelah 2 detik
                setTimeout(() => {
                    startScan();
                }, 2000);
            });
    }
    
    function tryNextCamera(index) {
        if (index >= cameraIds.length) {
            showResult('Tidak ada kamera yang tersedia', 'error');
            return;
        }
        
        html5QrCode.start(
            cameraIds[index],
            { fps: 10, qrbox: 250 },
            onScanSuccess,
            onScanError
        ).then(() => {
            isScanning = true;
            scanBtn.textContent = "Stop Scan";
            scanBtn.classList.remove('bg-blue-600');
            scanBtn.classList.add('bg-red-600');
            resultDiv.classList.add('hidden');
        }).catch(err => {
            console.error(err);
            // Coba kamera berikutnya
            tryNextCamera(index + 1);
        });
    }
    
    function stopScan() {
        if (!isScanning) return;
        
        html5QrCode.stop().then(() => {
            isScanning = false;
            scanBtn.textContent = "Mulai Scan QR Code";
            scanBtn.classList.remove('bg-red-600');
            scanBtn.classList.add('bg-blue-600');
        }).catch(err => {
            console.error(err);
        });
    }
    
    function onScanSuccess(decodedText) {
        try {
            const qrData = JSON.parse(decodedText);
            if (qrData.app === 'BankSampah' && qrData.action === 'verify') {
                verifyTransaction(qrData.code);
            } else {
                showResult('QR Code tidak valid', 'error');
                // Tetap lanjut scanning setelah error
                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 2000);
            }
        } catch (e) {
            showResult('Format QR tidak dikenali', 'error');
            // Tetap lanjut scanning setelah error
            setTimeout(() => {
                resultDiv.classList.add('hidden');
            }, 2000);
        }
    }
    
    function onScanError(error) {
        console.error(error);
    }
    
    function verifyManual() {
        const code = document.getElementById('manual-code').value.trim();
        if (code.length > 5) {
            verifyTransaction(code);
        } else {
            showResult('Kode verifikasi terlalu pendek', 'error');
        }
    }
    
    function verifyTransaction(code) {
        fetch('verify_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showResult('Transaksi berhasil diverifikasi!', 'success');
                // Tetap lanjut scanning setelah sukses
                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 2000);
            } else {
                showResult(data.message || 'Gagal memverifikasi', 'error');
                // Tetap lanjut scanning setelah error
                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 2000);
            }
        });
    }
    
    function showResult(message, type) {
        resultDiv.textContent = message;
        resultDiv.className = 'mt-4 p-4 rounded-lg text-center ' + 
            (type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
        resultDiv.classList.remove('hidden');
    }
    </script>
</body>
</html>