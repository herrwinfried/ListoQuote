<?php
session_start();
ob_start();

date_default_timezone_set("Europe/Istanbul");

$db_path = '/var/db/database.sqlite';

$conn = new PDO("sqlite:$db_path");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$checkConfigTable = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='config'");
$isConfigTable = $checkConfigTable->fetch() !== false;

if (!$isConfigTable && basename($_SERVER['PHP_SELF']) !== "setup.php") {
    header("Location: /setup");
    exit();
} elseif ($isConfigTable && basename($_SERVER['PHP_SELF']) === "setup.php") {
    header("Location: /");
    exit();
} elseif ($isConfigTable) {
    $webInfo = $conn->prepare("SELECT * FROM config WHERE id = 1");
    $webInfo->execute();
    $webInfo = $webInfo->fetch(PDO::FETCH_ASSOC);
}

ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
