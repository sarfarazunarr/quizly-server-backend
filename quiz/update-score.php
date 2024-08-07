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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(200);
    echo json_encode($origin);
    exit();
}

function updateScore($score, $quizId, $id)
{
    global $pdo;
    $user = $_SESSION['user_id'] ?? null;

    // Verify quiz ID
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $stmt->execute([$quizId]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        http_response_code(404);
        echo json_encode(['message' => 'Quiz not found']);
        return;
    }

    // Verify user access
    if ($quiz['created_by'] == $user) {
        // Update score
        $stmt = $pdo->prepare("UPDATE submissions SET score = ? WHERE id = ? AND quiz_id = ?");
        if ($stmt->execute([$score, $id, $quizId])) {
            http_response_code(200);
            echo json_encode(['message' => 'Score updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update score']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['message' => 'Access Denied']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? '';
    $quizId = $data['quiz_id'] ?? '';
    $score = $data['score'] ?? '';

    // Validate input
    if (empty($quizId)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid Quiz Id']);
    } else if (empty($id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid Id']);
    } else if (empty($score) && $score !== '0') {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid score']);
    } else {
        updateScore($score, $quizId, $id);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
