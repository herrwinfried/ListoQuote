<?php
require("../../auth.php");

$sql = "SELECT * FROM products_list ORDER BY list_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();

$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($lists);
