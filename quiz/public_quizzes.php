<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

function getQuiz()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM quizzes");
    if (!$stmt->execute()) {
        echo json_encode(['message' => 'Error executing query: ' . $stmt->errorInfo()[2]]);
        exit;
    }
    $quiz = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($quiz) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($quiz);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Quizes not found']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    getQuiz();

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>