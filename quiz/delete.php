<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function deleteQuiz($quizId) {
    global $pdo;

    $createdBy = $_SESSION['user_id'] ?? null;
    if (!$createdBy) {
        http_response_code(401);
        echo json_encode(['message' => 'User not authenticated']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ? AND created_by = ?");
    if ($stmt->execute([$quizId, $createdBy])) {
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Quiz deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Quiz not found or not authorized']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to delete quiz']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $quizId = $data['id'] ?? '';

    if (empty($quizId)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    } else {
        deleteQuiz($quizId);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
