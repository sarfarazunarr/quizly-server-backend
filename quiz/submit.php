<?php
require '../config.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
function addSubmission($quizId, $username, $email='', $isPublic, $submissiondata, $score) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO submissions (quiz_id, isPublic, Email, user_name, submission_data, score) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$quizId, $isPublic, $email, $username, $submissiondata, $score])) {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Quiz Submitted successfully!', 'submission_id' => $pdo->lastInsertId()]);
    } else {
        http_response_code(500);
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['message' => 'Failed to save record']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $quizId = $data['quiz_id'] ?? 0;
    $username = $data['user_name'] ?? '';
    $email = $data['Email'] ?? '';
    $isPublic = $data['isPublic'] ?? 1;
    $submissiondata = $data['submission_data'] ?? '';
    $score = $data['score'] ?? 0;

    if(!$isPublic){
        if($email == ''){
            http_response_code(400);
            echo json_encode(['message' => 'Email is required!']);
        }
    }
    if (empty($quizId) || empty($username) || empty($submissiondata)) {
        http_response_code(400);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Invalid input']);
    } else {
        addSubmission($quizId, $username, $email, $isPublic, $submissiondata, $score);
    }
} else {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
