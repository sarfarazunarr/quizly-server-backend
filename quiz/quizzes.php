<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

function getQuiz($userId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE created_by = ?");
    $stmt->execute([$userId]);
    $quiz = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($quiz) {
        header('Content-Type: application/json');
        echo json_encode($quiz);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Quizes not found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_SESSION['user_id'] ?? '';

    if (empty($userId)) {
        http_response_code(400);
        echo json_encode(['message' => 'No ID Found']);
    } else {
        getQuiz($userId);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
