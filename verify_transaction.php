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
        echo json_encode(['status' => 'error', 'message' => 'Kode tidak valid atau sudah diverifikasi']);
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
    <meta http-equiv="Permissions-Policy" content="camera=(self)">
</head>
<body class="bg-gray-100">
    <?php include __DIR__.'/includes/admin_header.php'; ?>

    <div class="container mx-auto p-4 lg:p-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-8 sm:px-10 sm:py-12">
                    <h1 class="text-2xl sm:text-3xl font-bold text-center mb-8">Verifikasi Transaksi</h1>

                    <div class="space-y-6">
                        <div class="relative">
                            <div id="reader" class="w-full h-64 sm:h-80 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden">
                                <div id="camera-status" class="h-full flex items-center justify-center text-gray-500">
                                    <div class="text-center">
                                        <p>Kamera belum diaktifkan</p>
                                        <p class="text-sm mt-2">Pastikan browser mengizinkan akses kamera</p>
                                    </div>
                                </div>
                            </div>
                            <div id="loading-overlay" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                                <div class="text-white text-center">
                                    <div class="loader border-white border-2 border-t-transparent rounded-full w-8 h-8 animate-spin mx-auto mb-2"></div>
                                    <p>Mengaktifkan kamera...</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center space-y-4">
                            <button id="scan-btn" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                Mulai Scan QR Code
                            </button>

                            <div id="camera-selection" class="hidden">
                                <label for="camera-select" class="block text-sm font-medium text-gray-700 mb-2">Pilih Kamera:</label>
                                <select id="camera-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Memuat kamera...</option>
                                </select>
                            </div>

                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-600 mb-3">Atau masukkan kode verifikasi manual:</p>
                                <input type="text" id="manual-code" placeholder="Masukkan kode verifikasi"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button id="verify-btn" class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                    Verifikasi Manual
                                </button>
                            </div>
                        </div>

                        <div id="result" class="hidden p-4 rounded-lg text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menggunakan file dari node_modules -->
    <script src="/node_modules/html5-qrcode/html5-qrcode.min.js"></script>

    <script>
    let html5QrCode;
    let isScanning = false;
    let availableCameras = [];
    let currentCameraId = null;

    // DOM elements
    const scanBtn = document.getElementById('scan-btn');
    const verifyBtn = document.getElementById('verify-btn');
    const resultDiv = document.getElementById('result');
    const cameraStatus = document.getElementById('camera-status');
    const loadingOverlay = document.getElementById('loading-overlay');
    const cameraSelection = document.getElementById('camera-selection');
    const cameraSelect = document.getElementById('camera-select');
    const manualCodeInput = document.getElementById('manual-code');

    // Event listeners
    scanBtn.addEventListener('click', toggleScan);
    verifyBtn.addEventListener('click', verifyManual);
    cameraSelect.addEventListener('change', switchCamera);
    manualCodeInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') verifyManual();
    });

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', async () => {
        await loadAvailableCameras();
    });

    // Load available cameras
    async function loadAvailableCameras() {
        try {
            showLoading(true);
            availableCameras = await Html5Qrcode.getCameras();
            
            if (availableCameras.length > 0) {
                populateCameraSelect();
                cameraStatus.innerHTML = '<p class="text-green-600">Kamera siap digunakan</p>';
                scanBtn.disabled = false;
            } else {
                cameraStatus.innerHTML = '<p class="text-red-600">Tidak ada kamera yang ditemukan</p>';
                scanBtn.disabled = true;
            }
        } catch (error) {
            console.error("Error loading cameras:", error);
            let errorMessage = 'Gagal memuat kamera: ';
            
            if (error.message.includes('Permission denied') || error.message.includes('NotAllowedError')) {
                errorMessage += 'Akses kamera ditolak. Mohon izinkan akses kamera di pengaturan browser.';
                cameraStatus.innerHTML = `
                    <div class="text-center text-red-600">
                        <p>Akses kamera ditolak</p>
                        <p class="text-sm mt-2">Mohon izinkan akses kamera di pengaturan browser dan refresh halaman.</p>
                    </div>
                `;
            } else {
                errorMessage += error.message;
                cameraStatus.innerHTML = '<p class="text-red-600">' + errorMessage + '</p>';
            }
            
            scanBtn.disabled = true;
        } finally {
            showLoading(false);
        }
    }

    // Populate camera selection dropdown
    function populateCameraSelect() {
        cameraSelect.innerHTML = '';
        
        availableCameras.forEach(camera => {
            const option = document.createElement('option');
            option.value = camera.id;
            option.textContent = camera.label || `Kamera ${camera.id}`;
            cameraSelect.appendChild(option);
        });

        cameraSelection.classList.toggle('hidden', availableCameras.length <= 1);
        
        // Prioritize back camera
        const backCamera = availableCameras.find(cam => 
            cam.label && cam.label.toLowerCase().includes('back')
        );
        currentCameraId = backCamera ? backCamera.id : availableCameras[0]?.id;
        if (currentCameraId) cameraSelect.value = currentCameraId;
    }

    // Switch camera
    async function switchCamera() {
        const newCameraId = cameraSelect.value;
        if (newCameraId && newCameraId !== currentCameraId) {
            currentCameraId = newCameraId;
            if (isScanning) {
                await stopScan();
                await startScan();
            }
        }
    }

    // Toggle scanning
    async function toggleScan() {
        if (isScanning) {
            await stopScan();
        } else {
            await startScan();
        }
    }

    // Start scanning
    async function startScan() {
        if (!currentCameraId) {
            showResult('Tidak ada kamera yang dipilih', 'error');
            return;
        }

        try {
            showLoading(true);
            html5QrCode = new Html5Qrcode("reader");
            
            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            await html5QrCode.start(
                currentCameraId, 
                config,
                onScanSuccess,
                onScanError
            );

            isScanning = true;
            updateScanButton();
            cameraStatus.innerHTML = '<p class="text-green-600">Scanning aktif</p>';
        } catch (error) {
            console.error("Error starting scan:", error);
            showResult(`Gagal memulai scan: ${error.message}`, 'error');
        } finally {
            showLoading(false);
        }
    }

    // Stop scanning
    async function stopScan() {
        if (!html5QrCode) return;
        
        try {
            showLoading(true);
            await html5QrCode.stop();
            isScanning = false;
            updateScanButton();
            cameraStatus.innerHTML = '<p class="text-gray-600">Kamera dimatikan</p>';
        } catch (error) {
            console.error("Error stopping scan:", error);
        } finally {
            showLoading(false);
        }
    }

    // Update scan button appearance
    function updateScanButton() {
        if (isScanning) {
            scanBtn.textContent = "Stop Scan";
            scanBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            scanBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        } else {
            scanBtn.textContent = "Mulai Scan QR Code";
            scanBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
            scanBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    }

    // Handle successful scan
    async function onScanSuccess(decodedText) {
        await stopScan();
        
        try {
            const qrData = JSON.parse(decodedText);
            if (qrData.code) {
                verifyTransaction(qrData.code);
            } else {
                showResult('Format QR tidak valid', 'error');
            }
        } catch {
            if (decodedText.length >= 6) {
                verifyTransaction(decodedText);
            } else {
                showResult('Kode terlalu pendek', 'error');
            }
        }
    }

    // Handle scan errors
    function onScanError(error) {
        if (!error.message.includes('QR code not found')) {
            console.warn("Scan error:", error);
        }
    }

    // Verify manual code
    function verifyManual() {
        const code = manualCodeInput.value.trim();
        if (code.length >= 6) {
            verifyTransaction(code);
        } else {
            showResult('Kode harus minimal 6 karakter', 'error');
            manualCodeInput.focus();
        }
    }

    // Verify transaction with backend
    async function verifyTransaction(code) {
        showLoading(true);
        showResult('Memverifikasi...', 'info');

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ code })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal memverifikasi');
            }

            if (data.status === 'success') {
                showResult('Transaksi berhasil diverifikasi!', 'success');
                manualCodeInput.value = '';
            } else {
                showResult(data.message || 'Kode tidak valid', 'error');
            }
        } catch (error) {
            console.error("Verification error:", error);
            showResult(error.message || 'Terjadi kesalahan', 'error');
        } finally {
            showLoading(false);
        }
    }

    // Show result message
    function showResult(message, type) {
        resultDiv.textContent = message;
        resultDiv.className = 'p-4 rounded-lg text-center ' + 
            (type === 'success' ? 'bg-green-100 text-green-800' : 
             type === 'error' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800');
        resultDiv.classList.remove('hidden');
        
        if (type !== 'success') {
            setTimeout(() => {
                resultDiv.classList.add('hidden');
            }, 5000);
        }
    }

    // Show/hide loading overlay
    function showLoading(show) {
        loadingOverlay.classList.toggle('hidden', !show);
    }

    // Clean up when page unloads
    window.addEventListener('beforeunload', async () => {
        if (isScanning && html5QrCode) {
            await html5QrCode.stop();
        }
    });
    </script>

    <style>
    .loader {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
</body>
</html>