<?php
require_once __DIR__.'/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validasi input
    if (empty($input['admin_id']) || empty($input['code'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }
    
    // Verifikasi transaksi
    $stmt = $pdo->prepare("UPDATE transactions SET 
                          verified = 1, 
                          verified_by = ?, 
                          verified_at = NOW() 
                          WHERE verification_code = ? AND verified = 0");
    $stmt->execute([$input['admin_id'], $input['code']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan atau sudah diverifikasi']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan']);
}