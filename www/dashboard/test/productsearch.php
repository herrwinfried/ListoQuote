<?php
require("../../auth.php");

$list_id = $_GET['list_id'];
$q = $_GET['q'];

if (!$list_id || strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products_list_child WHERE list_id = ? AND product_name LIKE ?");
$like = "%$q%";
$stmt->execute([$list_id, $like]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);
