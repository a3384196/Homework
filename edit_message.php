<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message_id = $_POST['message_id'];
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ? AND user_id = ?");
    $stmt->execute([$message_id, $_SESSION['user_id']]);
    $message = $stmt->fetch();

    if (!$message) {
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Message</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Edit Message</h2>
        <form id="editMessageForm" method="POST" action="update_message.php" enctype="multipart/form-data">
            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" required><?php echo htmlspecialchars($message['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="files">Attach files</label>
                <input type="file" class="form-control-file" id="files" name="files[]" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Update Message</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</body>
</html>
