<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Credentials: true');
function uploadImage($file) {
    // Specify the directory where the image will be saved
    $uploadDir = 'uploads/';
    // Create the directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate a unique name for the uploaded file
    $fileName = basename($file['name']);
    $targetFilePath = $uploadDir . uniqid() . '_' . $fileName;

    // Check if the file is an image
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        // Move the file to the target directory
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            // Return the URL of the uploaded image
            return $targetFilePath;
        } else {
            throw new Exception('File upload failed.');
        }
    } else {
        throw new Exception('Invalid file type.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    try {
        $imageUrl = uploadImage($_FILES['image']);
        // Return the image URL as JSON
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['imageUrl' => $imageUrl]);
    } catch (Exception $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Method not allowed']);
}
?>
