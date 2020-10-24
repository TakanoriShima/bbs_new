<?php
    session_start();
    $_SESSION['user_id'] = null;
    $_SESSION['flash_message'] = "ログアウトしました";
    header('Location: index.php');

?>