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

function deleteQuestion($questionId) {
    global $pdo;

    $createdBy = $_SESSION['user_id'] ?? null;
    if (!$createdBy) {
        http_response_code(401);
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['message' => 'User not authenticated']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND created_by = ?");
    if ($stmt->execute([$questionId, $createdBy])) {
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Question deleted successfully']);
        } else {
            http_response_code(404);
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['message' => 'Question not found or not authorized']);
        }
    } else {
        http_response_code(500);
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['message' => 'Failed to delete question']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $questionId = $data['id'] ?? '';

    if (empty($questionId)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        deleteQuestion($questionId);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
