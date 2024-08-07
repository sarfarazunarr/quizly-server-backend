<?php
require '../config.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');

function getScore($id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        return $data;
    } else {
        http_response_code(404);
        return ['message' => 'Submission not found'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    } else {
        $result = getScore($id);
        echo json_encode($result);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>