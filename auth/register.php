<?php
require '../config.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function registerUser($username, $email, $password, $profileImage) {
    global $pdo;

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Email already exists']);
        return;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, profileImage) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashedPassword, $profileImage])) {
        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        http_response_code(500);
        echo json_encode(['message' => 'Failed to register user']);
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $profileImage = $data['profileImage'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        header('HTTP/1.1 400 Bad Request');
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    } else {
        registerUser($username, $email, $password, $profileImage);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
