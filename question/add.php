<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
function addQuestion($quizId, $questionText, $questionType, $answers, $correct_answer) {
    global $pdo;

    $createdBy = $_SESSION['user_id'] ?? null;
    if (!$createdBy) {
        http_response_code(401);
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['message' => 'User not authenticated']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_type, possible_answers, correct_answer, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$quizId, $questionText, $questionType, json_encode($answers), $correct_answer, $createdBy])) {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Question added successfully', 'question_id' => $pdo->lastInsertId()]);
    } else {
        http_response_code(500);
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['message' => 'Failed to add question']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $quizId = $data['quiz_id'] ?? '';
    $questionText = $data['question_text'] ?? '';
    $questionType = $data['question_type'] ?? '';
    $answersTemp = $data['answers'] ?? '';
    $correct_answer = $data['correct_answer'] ?? '';
    $answers = explode(",", $answersTemp);

    if (empty($quizId) || empty($questionText) || empty($questionType)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        addQuestion($quizId, $questionText, $questionType, $answers, $correct_answer);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
