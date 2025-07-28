<?php
require("../../auth.php");

$id = strtolower(trim($_GET['id'] ?? ''));

if ($id === '') {
    echo json_encode(['success' => false]);
    exit;
}

$data = $conn->prepare("DELETE FROM products_list WHERE id = ?");
$success = $data->execute([$id]);
if ($success) {
    $data = $conn->prepare("DELETE FROM products_list_child WHERE list_id = ?");
    $success = $data->execute([$id]);
}


if (!$success) {
    echo json_encode(['success' => false]);
    exit;
} else {
    echo json_encode(['success' => true]);
}
?>