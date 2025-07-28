<?php
require("connection.php");

if (isset($_COOKIE['remember_me'])) {
    $tokenHash = hash('sha256', $_COOKIE['remember_me']);
    $conn->prepare("DELETE FROM auth_tokens WHERE token_hash = ?")->execute([$tokenHash]);
    setcookie('remember_me', '', time() - 3600, "/");
}

session_destroy();
header("Location: /login");
