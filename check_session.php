<?php
session_start();

if (isset($_SESSION['expire']) && time() > $_SESSION['expire']) {
    echo 'expired';
} else {
    echo 'active';
}
?>
