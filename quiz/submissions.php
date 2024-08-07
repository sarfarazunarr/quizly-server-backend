<?php
require '../config.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

session_start();

function getSubmissions($quizId)
{
    global $pdo;
    $user = $_SESSION['user_id'] ?? null;

    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $stmt->execute([$quizId]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$quiz) {
        http_response_code(404);
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['message' => 'Quiz not found']);
    } else if ($quiz['created_by'] == $user) {
        $stmt = $pdo->prepare("SELECT * FROM submissions WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
        $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($submissions) {
            header('Content-Type: application/json');
            echo json_encode($submissions);
        } else {
            http_response_code(404);
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['message' => 'Submissions not found']);
        }
    } else {
            http_response_code(401);
            echo json_encode(['message' => 'Access Denied']);
    }


}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $quizId = $_GET['id'] ?? '';

    if (empty($quizId)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        getSubmissions($quizId);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>