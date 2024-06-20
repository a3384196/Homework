<?php
session_start();

// 檢查是否已登入
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 檢查 session 是否已過期
if (isset($_SESSION['expire']) && time() > $_SESSION['expire']) {
    session_unset();
    session_destroy();
    header("Location: index.html");
    exit();
}

// 如果 session 沒有過期，更新過期時間
$_SESSION['expire'] = time() + (15 * 60);

include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Message Board</h2>
        <form id="messageForm" method="POST" action="post_message.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" required></textarea>
            </div>
            <div class="form-group">
                <label for="files">Attach files</label>
                <input type="file" class="form-control-file" id="files" name="files[]" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Post Message</button>
        </form>
        <div id="messages" class="mt-5">
            <!-- 留言列表 -->
            <?php
            $stmt = $pdo->prepare("SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at DESC");
            $stmt->execute();
            $messages = $stmt->fetchAll();
            
            foreach ($messages as $message) {
                echo "<div class='message'>";
                echo "<h5>" . htmlspecialchars($message['username']) . ":</h5>";
                echo "<p>" . nl2br(htmlspecialchars($message['content'])) . "</p>";
                
                // 顯示附加的檔案
                $stmt = $pdo->prepare("SELECT * FROM files WHERE message_id = ?");
                $stmt->execute([$message['id']]);
                $files = $stmt->fetchAll();
                
                if ($files) {
                    echo "<ul>";
                    foreach ($files as $file) {
                        echo "<li><a href='" . htmlspecialchars($file['filepath']) . "' download>" . htmlspecialchars($file['filename']) . "</a></li>";
                    }
                    echo "</ul>";
                }
                
                echo "<small>Posted on " . $message['created_at'] . "</small>";
                
                // 顯示修改和刪除按鈕
                if ($_SESSION['user_id'] == $message['user_id']) {
                    echo "<form method='POST' action='edit_message.php' style='display:inline-block;'>";
                    echo "<input type='hidden' name='message_id' value='" . $message['id'] . "'>";
                    echo "<button type='submit' class='btn btn-warning btn-sm'>Edit</button>";
                    echo "</form>";
                    
                    echo "<form method='POST' action='delete_message.php' style='display:inline-block;' onsubmit='return confirm(\"Are you sure you want to delete this message?\");'>";
                    echo "<input type='hidden' name='message_id' value='" . $message['id'] . "'>";
                    echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
                    echo "</form>";
                }
                
                echo "</div>";
            }
            ?>
        </div>
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
        // 定期檢查 session 是否過期
        setInterval(function() {
            $.ajax({
                url: 'check_session.php',
                success: function(data) {
                    if (data === 'expired') {
                        alert('Your session has expired due to inactivity.');
                        window.location.href = 'index.html';
                    }
                }
            });
        }, 60000); // 每分鐘檢查一次
    </script>
</body>
</html>
