<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
function createQuiz($title, $description, $category, $IsPublic) {
    global $pdo;

    $createdBy = $_SESSION['user_id'] ?? null;
    if (!$createdBy) {
        header('HTTP/1.1 401 Unauthorized');
        http_response_code(401);
        echo json_encode(['message' => 'User not authenticated']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO quizzes (title, description, category, isPublic, created_by) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $category, $IsPublic, $createdBy])) {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(['message' => 'Quiz created successfully', 'quiz_id' => $pdo->lastInsertId()]);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        http_response_code(500);
        echo json_encode(['message' => 'Failed to create quiz']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $category = $data['category'] ?? 'general';
    $isPublic = $data['isPublic'] ?? 1;

    

    if (empty($title) || empty($description)) {
        header('HTTP/1.1 400 Bad Request');
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    } else {
        createQuiz($title, $description, $category, $isPublic);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
