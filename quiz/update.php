<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function updateQuiz($quizId, $title, $description, $category, $isPublic) {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, description = ?, category = ?, isPublic = ?, WHERE id = ? AND created_by = ?");
    $createdBy = $_SESSION['user_id'] ?? null;

    if (!$createdBy) {
        http_response_code(401);
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['message' => 'User not authenticated']);
        return;
    }

    if ($stmt->execute([$title, $description, $category, $isPublic, $quizId, $createdBy])) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Quiz updated successfully']);
    } else {
        http_response_code(500);
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['message' => 'Failed to update quiz']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $quizId = $data['id'] ?? '';
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $category = $data['category'] ?? '';
    $isPublic = $data['isPublic'] ?? 1;

    if (empty($quizId) || empty($title) || empty($description)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        updateQuiz($quizId, $title, $description, $category, $isPublic);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
