<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO messages (user_id, content, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $content]);

    $message_id = $pdo->lastInsertId();

    if (!empty($_FILES['files']['name'][0])) {
        $uploadDir = 'uploads/';
        foreach ($_FILES['files']['name'] as $key => $filename) {
            $filepath = $uploadDir . basename($filename);
            move_uploaded_file($_FILES['files']['tmp_name'][$key], $filepath);
            
            $stmt = $pdo->prepare("INSERT INTO files (message_id, filename, filepath) VALUES (?, ?, ?)");
            $stmt->execute([$message_id, $filename, $filepath]);
        }
    }

    header("Location: dashboard.php");
    exit();
}
?>
