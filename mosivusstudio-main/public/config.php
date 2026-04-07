<?php
/**
 * Mosivus Studio — Database Configuration
 * Compatible with Hostinger shared hosting (MySQL + PHP)
 *
 * UPDATE these values with your Hostinger MySQL credentials.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u504923415_Mosivus_Forms');
define('DB_USER', 'u504923415_mosivus_admin');
define('DB_PASS', 'Abhishek@mosivusstudio4930');
define('DB_CHARSET', 'utf8mb4');

// Allowed origins (update after deploying)
define('ALLOWED_ORIGINS', [
    'https://mosivusstudio.com',
    'https://www.mosivusstudio.com',
    'http://localhost:5173',
    'https://mosivusstudio.lovable.app',
]);

/**
 * Get a PDO database connection.
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/**
 * Set CORS headers based on the request origin.
 */
function setCorsHeaders(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, ALLOWED_ORIGINS, true)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Send a JSON response and exit.
 */
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}
