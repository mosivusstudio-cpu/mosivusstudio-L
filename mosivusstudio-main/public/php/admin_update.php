<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config.php';
setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$table  = $input['table']  ?? '';
$id     = $input['id']     ?? '';
$status = $input['status'] ?? '';

$allowed = ['project_submissions', 'contact_submissions'];
if (!in_array($table, $allowed, true) || !is_numeric($id) || $status === '') {
    jsonResponse(['error' => 'Invalid request'], 400);
}

try {
    $db = getDB();
    $stmt = $db->prepare("UPDATE {$table} SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => (int)$id]);
    jsonResponse(['success' => true]);
} catch (PDOException $e) {
    error_log('Admin update error: ' . $e->getMessage());
    jsonResponse(['error' => 'Update failed'], 500);
}
