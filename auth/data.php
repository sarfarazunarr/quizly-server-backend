<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    global $pdo;
    if ($_SESSION['user_id']) {
        $userId = $_SESSION['user_id'] ?? null;
        if (empty($userId)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid input', 'userid' => $userId]);
        }
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>