<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
    http_response_code(200);
    echo json_encode(['message' => 'Logout successful']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
