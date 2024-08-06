<?php
require '../config.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');
function getQuestion($quizId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt->execute([$quizId]);
    $question = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($question) {
        header('Content-Type: application/json');
        echo json_encode($question);
    } else {
        http_response_code(404);
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['message' => 'Question not found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $quizId = $_GET['id'] ?? '';

    if (empty($quizId)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        getQuestion($quizId);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
