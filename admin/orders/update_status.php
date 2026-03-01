<?php
/**
 * AJAX Endpoint - Update Order Status
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Must be logged in
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCSRF()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid Request']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$allowedStatuses = ['new', 'contacted', 'completed', 'cancelled'];

if ($id <= 0 || !in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
$success = $stmt->execute(['status' => $status, 'id' => $id]);

echo json_encode(['success' => $success]);
