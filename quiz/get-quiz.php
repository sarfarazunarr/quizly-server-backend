<?php
require '../config.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');
function getQuiz($quizId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $stmt->execute([$quizId]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($quiz) {
        header('Content-Type: application/json');
        echo json_encode($quiz);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Quiz not found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $quizId = $_GET['id'] ?? '';

    if (empty($quizId)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    } else {
        getQuiz($quizId);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
