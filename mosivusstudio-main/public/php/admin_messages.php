<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config.php';
setCorsHeaders();

try {
    $db = getDB();
    $rows = $db->query("SELECT * FROM contact_submissions ORDER BY created_at DESC")->fetchAll();
    echo json_encode($rows);
} catch (PDOException $e) {
    jsonResponse([], 200);
}
