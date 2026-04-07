<?php
require_once __DIR__ . '/../config.php';
setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['status' => 'error', 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$name    = trim($input['name']    ?? '');
$email   = trim($input['email']   ?? '');
$phone   = trim($input['phone']   ?? '');
$role    = trim($input['role']    ?? '');
$source  = trim($input['source']  ?? '');
$message = trim($input['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    jsonResponse(['status' => 'error', 'message' => 'Name, email, and message are required.'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['status' => 'error', 'message' => 'Invalid email address.'], 400);
}
if (strlen($name) > 200 || strlen($email) > 255 || strlen($phone) > 30 || strlen($message) > 5000) {
    jsonResponse(['status' => 'error', 'message' => 'Input exceeds maximum length.'], 400);
}

try {
    $db = getDB();

    $db->exec("
        CREATE TABLE IF NOT EXISTS contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(30) DEFAULT '',
            role VARCHAR(100) DEFAULT '',
            source VARCHAR(200) DEFAULT '',
            message TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'New',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $stmt = $db->prepare("
        INSERT INTO contact_submissions (name, email, phone, role, source, message)
        VALUES (:name, :email, :phone, :role, :source, :message)
    ");
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':phone'   => $phone,
        ':role'    => $role,
        ':source'  => $source,
        ':message' => $message,
    ]);

    jsonResponse(['status' => 'success', 'message' => 'Form submitted successfully']);

} catch (PDOException $e) {
    error_log('Contact form DB error: ' . $e->getMessage());
    jsonResponse(['status' => 'error', 'message' => 'Server error. Please try again later.'], 500);
}
