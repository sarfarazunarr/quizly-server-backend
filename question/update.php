<?php
require '../config.php';
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function updateQuestion($questionId, $questionText, $questionType, $answers, $correct_answer) {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, question_type = ?, possible_answers = ?, correct_answer = ? WHERE id = ? AND created_by = ?");
    $createdBy = $_SESSION['user_id'] ?? null;

    if (!$createdBy) {
        http_response_code(401);
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['message' => 'User not authenticated']);
        return;
    }

    if ($stmt->execute([$questionText, $questionType, json_encode($answers), $correct_answer, $questionId, $createdBy])) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Question updated successfully']);
    } else {
        http_response_code(500);
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['message' => 'Failed to update question']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $questionId = $data['id'] ?? '';
    $questionText = $data['question_text'] ?? '';
    $questionType = $data['question_type'] ?? '';
    $answersTemp = $data['answers'] ?? '';
    $correct_answer = $data['correct_answer'] ?? '';
    $answers = explode(",", $answersTemp);


    if (empty($questionId) || empty($questionText) || empty($questionType)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        updateQuestion($questionId, $questionText, $questionType, $answers, $correct_answer);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
