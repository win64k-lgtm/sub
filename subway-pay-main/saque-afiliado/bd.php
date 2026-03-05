<?php
include "./../../conectarbanco.php";

$conn = getConnection();

$stmt = $conn->query("SELECT email, nome, pix, valor, status FROM saque_afiliado");
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

$data = array_reverse($data);

$conn->close();

header("Content-Type: application/json");
echo json_encode($data);
?>
