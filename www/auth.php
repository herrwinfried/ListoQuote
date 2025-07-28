<?php
require("connection.php");

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $tokenHash = hash('sha256', $token);

    $data = $conn->prepare("SELECT * FROM auth_tokens WHERE token_hash = ? AND expires_at > datetime('now')");
    $data->execute([$tokenHash]);
    $dataRow = $data->fetch(PDO::FETCH_ASSOC);

    if ($dataRow) {
        $_SESSION['user_id'] = $dataRow['user_id'];
    } else {
        setcookie('remember_me', '', time() - 3600, "/");
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

?>