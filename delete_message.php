<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: logs.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message_id = $_POST['message_id'];

    // 先刪除相關檔案
    $stmt = $pdo->prepare("SELECT * FROM files WHERE message_id = ?");
    $stmt->execute([$message_id]);
    $files = $stmt->fetchAll();
    foreach ($files as $file) {
        unlink($file['filepath']);
        $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
        $stmt->execute([$file['id']]);
    }

    // 然後刪除訊息
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND user_id = ?");
    $stmt->execute([$message_id, $_SESSION['user_id']]);

    header("Location: dashboard.php");
    exit();
}
?>
