<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';
require_once 'libraries/TCPDF/tcpdf.php';

// Ambil data laporan
$reports = $pdo->prepare("
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
        SUM(berat) AS total_berat,
        SUM(debit - kredit) as total_saldo,
        COUNT(*) as total_transaksi
    FROM transactions
    WHERE user_id = ?
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan DESC
");
$reports->execute([$_SESSION['user_id']]);
$report_data = $reports->fetchAll();

// Hitung statistik
$stats = [
    'total_berat' => array_sum(array_column($report_data, 'total_berat')),
    'total_saldo' => array_sum(array_column($report_data, 'total_saldo'))
];

// Buat PDF jika parameter cetak ada
if (isset($_GET['cetak'])) {
    // Buat instance TCPDF dengan orientasi landscape untuk tampilan lebih luas
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Konfigurasi dokumen dengan desain modern
    $pdf->SetCreator('Bank Sampah Digital');
    $pdf->SetAuthor('Bank Sampah');
    $pdf->SetTitle('Laporan Bulanan Daur Ulang');
    $pdf->SetSubject('Laporan Komprehensif Pengelolaan Sampah');
    
    // Atur margin dengan ruang lebih lebar
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    
    // Tambah halaman dengan background warna lembut
    $pdf->AddPage();
    $pdf->SetFillColor(240, 248, 255); // Warna biru muda lembut
    $pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');
    
    // Header dengan desain modern
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor(33, 97, 140); // Warna biru tua
    $pdf->Cell(0, 15, 'LAPORAN BULANAN BANK SAMPAH', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, 'Periode: '.date('F Y'), 0, 1, 'C');
    $pdf->Ln(10);
    
    // Kotak statistik utama dengan desain modern
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetDrawColor(200, 220, 240);
    $pdf->RoundedRect(20, 50, $pdf->getPageWidth() - 40, 40, 5, '1111', 'BD', ['color' => [200, 220, 240]]);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(25, 55);
    $pdf->Cell(60, 10, 'Total Berat Sampah', 0, 0);
    $pdf->Cell(60, 10, 'Total Saldo', 0, 0);
    $pdf->Cell(60, 10, 'Total Bulan Aktif', 0, 1);
    
    $pdf->SetFont('helvetica', '', 11);
    $pdf->SetX(25);
    $pdf->Cell(60, 10, number_format($stats['total_berat'], 0, ',', '.').' gram', 0, 0, 'L');
    $pdf->Cell(60, 10, 'Rp '.number_format($stats['total_saldo'], 0, ',', '.'), 0, 0, 'L');
    $pdf->Cell(60, 10, count($report_data).' bulan', 0, 1, 'L');
    
    // Tabel Detail Transaksi dengan desain modern
    $pdf->Ln(15);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(33, 97, 140);
    $pdf->Cell(0, 10, 'Detail Transaksi per Bulan', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(220, 235, 250);
    $pdf->SetTextColor(0, 0, 0);
    
    // Header Tabel
    $pdf->Cell(50, 8, 'Bulan', 1, 0, 'C', 1);
    $pdf->Cell(40, 8, 'Berat (gr)', 1, 0, 'C', 1);
    $pdf->Cell(50, 8, 'Saldo', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Transaksi', 1, 1, 'C', 1);
    
    // Isi Tabel
    $pdf->SetFont('helvetica', '', 9);
    foreach($report_data as $report) {
        $pdf->Cell(50, 7, date('F Y', strtotime($report['bulan'].'-01')), 1, 0, 'L');
        $pdf->Cell(40, 7, number_format($report['total_berat'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(50, 7, 'Rp '.number_format($report['total_saldo'], 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell(30, 7, $report['total_transaksi'].'x', 1, 1, 'C');
    }
    
    // Dampak Lingkungan dengan desain infografis
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(33, 97, 140);
    $pdf->Cell(0, 10, 'Dampak Lingkungan Positif', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    $dampak = [
        ['label' => 'Mengurangi Emisi CO2', 'value' => number_format($stats['total_berat'] * 1.5, 0, ',', '.').' gram'],
        ['label' => 'Menghemat Energi', 'value' => number_format($stats['total_berat'] * 2.3, 0, ',', '.').' watt-jam'],
        ['label' => 'Menghemat Air', 'value' => number_format($stats['total_berat'] * 3.8, 0, ',', '.').' liter']
    ];
    
    foreach ($dampak as $item) {
        $pdf->Cell(90, 7, $item['label'].':', 0, 0);
        $pdf->Cell(0, 7, $item['value'], 0, 1);
    }
    
    // Footer dengan catatan
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 5, 'Laporan ini dibuat secara otomatis oleh Sistem Bank Sampah Digital', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Terakhir diperbarui: '.date('d F Y H:i:s'), 0, 1, 'C');
    
    // Output PDF
    $pdf->Output('laporan_bulanan_bank_sampah.pdf', 'I');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.css">
    <style>
        .report-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .progress-bar {
            height: 10px;
            background: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 5px;
        }
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .text-3xl {
                font-size: 1.5rem;
            }
            .text-2xl {
                font-size: 1.25rem;
            }
            .p-6 {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-2 sm:px-4 py-6">
        <div class="flex flex-col gap-4 mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 text-center sm:text-left">Laporan Bulanan</h1>
            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-2 sm:gap-4">
                <a href="?cetak=1" class="col-span-1 bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition text-sm sm:text-base text-center">
                    <i class="fas fa-file-pdf mr-1 sm:mr-2"></i> Download PDF
                </a>
                <button onclick="window.print()" class="col-span-1 bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition text-sm sm:text-base text-center">
                    <i class="fas fa-print mr-1 sm:mr-2"></i> Cetak Laporan
                </button>
                <a href="dashboard.php" class="col-span-2 sm:col-span-1 bg-white border border-blue-600 text-blue-600 px-3 py-1.5 sm:px-4 sm:py-2 rounded-md hover:bg-blue-50 transition text-sm sm:text-base text-center">
                    <i class="fas fa-arrow-left mr-1 sm:mr-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Card Total Berat -->
            <div class="report-card bg-white rounded-lg p-4 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Berat Sampah</p>
                        <h3 class="text-xl sm:text-2xl font-bold mt-1"><?= number_format($stats['total_berat'], 0, ',', '.') ?> <span class="text-xs sm:text-sm">gram</span></h3>
                    </div>
                    <div class="bg-blue-100 p-2 sm:p-3 rounded-full text-blue-600">
                        <i class="fas fa-weight-hanging text-lg sm:text-xl"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= min(100, $stats['total_berat'] / 500000 * 100) ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1"><?= min(100, $stats['total_berat'] / 500000 * 100) ?>% dari target 500kg</p>
                </div>
            </div>

            <!-- Card Total Saldo -->
            <div class="report-card bg-white rounded-lg p-4 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Saldo</p>
                        <h3 class="text-xl sm:text-2xl font-bold mt-1">Rp <?= number_format($stats['total_saldo'], 0, ',', '.') ?></h3>
                    </div>
                    <div class="bg-green-100 p-2 sm:p-3 rounded-full text-green-600">
                        <i class="fas fa-wallet text-lg sm:text-xl"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= min(100, $stats['total_saldo'] / 500000 * 100) ?>%; background: linear-gradient(90deg, #38a169 0%, #68d391 100%)"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1"><?= min(100, $stats['total_saldo'] / 500000 * 100) ?>% dari target Rp 500.000</p>
                </div>
            </div>

            <!-- Card Bulan Aktif -->
            <div class="report-card bg-white rounded-lg p-4 sm:p-p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Bulan Aktif</p>
                        <?php
                        // Inisialisasi variabel
                        $total_bulan_aktif = 0;
                        $rata_berat = 0;
                        
                        // Cek apakah $report_data ada dan tidak kosong
                        if (isset($report_data) && is_array($report_data) && !empty($report_data)) {
                            $total_bulan_aktif = count($report_data);
                            
                            // Hitung rata-rata berat per bulan
                            if (isset($stats['total_berat']) && $total_bulan_aktif > 0) {
                                $rata_berat = round($stats['total_berat'] / ($total_bulan_aktif * 1000), 1);
                            }
                        }
                        ?>
                        <h3 class="text-xl sm:text-2xl font-bold mt-1"><?= $total_bulan_aktif ?> <span class="text-xs sm:text-sm">bulan</span></h3>
                    </div>
                    <div class="bg-purple-100 p-2 sm:p-3 rounded-full text-purple-600">
                        <i class="fas fa-calendar-alt text-lg sm:text-xl"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span>Rata-rata <?= $rata_berat ?>kg/bulan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik dan Tabel -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Grafik -->
            <div class="bg-white rounded-lg p-4 sm:p-6 shadow-sm">
                <h2 class="text-lg sm:text-xl font-semibold mb-3">Perkembangan Saldo</h2>
                <div class="relative" style="height: 250px">
                    <canvas id="saldoChart"></canvas>
                </div>
            </div>

            <!-- Tabel -->
            <div class="bg-white rounded-lg p-4 sm:p-6 shadow-sm">
                <h2 class="text-lg sm:text-xl font-semibold mb-3">Detail Transaksi per Bulan</h2>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat (gr)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($report_data as $report): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('F Y', strtotime($report['bulan'].'-01')) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= number_format($report['total_berat'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-600 font-medium">Rp <?= isset($report['total_saldo']) ? number_format($report['total_saldo'], 0, ',', '.') : '0' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $report['total_transaksi'] ?>x</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manfaat Lingkungan -->
        <div class="mt-8 bg-blue-50 rounded-xl p-6 shadow-sm border border-blue-100">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">Dampak Lingkungan Positif</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg flex items-start">
                    <div class="bg-blue-100 p-2 rounded-full mr-3 text-blue-600">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div>
                        <p class="font-medium">Mengurangi Emisi CO2</p>
                        <p class="text-sm text-gray-600"><?= number_format($stats['total_berat'] * 1.5, 0, ',', '.') ?> gram</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-3 text-green-600">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <p class="font-medium">Menghemat Energi</p>
                        <p class="text-sm text-gray-600"><?= number_format($stats['total_berat'] * 2.3, 0, ',', '.') ?> watt-jam</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg flex items-start">
                    <div class="bg-yellow-100 p-2 rounded-full mr-3 text-yellow-600">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div>
                        <p class="font-medium">Menghemat Air</p>
                        <p class="text-sm text-gray-600"><?= number_format($stats['total_berat'] * 3.8, 0, ',', '.') ?> liter</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script>
        // Grafik Saldo
        const ctx = document.getElementById('saldoChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(function($r) { return date('M Y', strtotime($r['bulan'].'-01')); }, $report_data)) ?>,
                datasets: [{
                    label: 'Total Saldo per Bulan (Rp)',
                    data: <?= json_encode(array_column($report_data, 'total_debit')) ?>,
                    backgroundColor: 'rgba(56, 161, 105, 0.2)',
                    borderColor: 'rgba(56, 161, 105, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>