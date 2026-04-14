<?php
// ============================================================
//  Public API — Herkese Açık İstekler (İlçe Çekmek vb.)
// ============================================================

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Yalnızca GET.']));
}

$action = $_GET['action'] ?? '';

if ($action === 'get_districts') {
    $city_id = (int)($_GET['city_id'] ?? 0);
    
    if (!$city_id) {
        exit(json_encode(['success' => false, 'data' => []]));
    }
    
    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id, name FROM districts WHERE city_id = ? ORDER BY name ASC');
        $stmt->execute([$city_id]);
        $districts = $stmt->fetchAll();
        
        exit(json_encode(['success' => true, 'data' => $districts]));
    } catch (Exception $e) {
        exit(json_encode(['success' => false, 'data' => [], 'message' => 'DB Hatası']));
    }
}

// Varsayılan
exit(json_encode(['success' => false, 'message' => 'Geçersiz işlem.']));
