<?php
include "./../../conectarbanco.php";

$conn = getConnection();

$stmt = $conn->query("SELECT SUM(valor) as total FROM confirmar_deposito WHERE status = 'PAID_OUT'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && $row["total"] !== null) {
    echo "R$ " . number_format($row["total"], 2, ",", "");
} else {
    echo "R$ 0";
}
?>
