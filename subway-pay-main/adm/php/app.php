<?php
include "./../../conectarbanco.php";

$conn = getConnection();

$stmt = $conn->query("SELECT COUNT(*) as id FROM appconfig");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row ? $row["id"] : "0";
?>
