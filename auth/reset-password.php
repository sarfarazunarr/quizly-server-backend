<?php
require '../config.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');
function resetPassword($email, $newPassword) {
    global $pdo;

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    if ($stmt->execute([$hashedPassword, $email])) {
        if ($stmt->rowCount() > 0) {
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['message' => 'Password reset successfully']);
        } else {
            header('HTTP/1.1 404 Not Found');
            http_response_code(404);
            echo json_encode(['message' => 'Email not found']);
        }
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        http_response_code(500);
        echo json_encode(['message' => 'Failed to reset password']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $newPassword = $data['new_password'] ?? '';

    if (empty($email) || empty($newPassword)) {
        header('HTTP/1.1 400 Bad Request');
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    } else {
        resetPassword($email, $newPassword);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
