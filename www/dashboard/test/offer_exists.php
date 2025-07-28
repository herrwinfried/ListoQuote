<?php
require("../../auth.php");

$listName = strtolower(trim($_GET['list_name'] ?? ''));
$listDate = strtolower(trim($_GET['list_date'] ?? ''));

header('Content-Type: application/json');

if ($listName === '' || $listDate === '') {
    echo json_encode(['exists' => false]);
    exit;
}

$data = $conn->prepare("SELECT COUNT(*) FROM offers_list WHERE LOWER(list_name) = ? AND LOWER(list_date) = ?");
$data->execute([$listName, $listDate]);

$exists = $data->fetchColumn() > 0;

echo json_encode(['exists' => $exists]);
