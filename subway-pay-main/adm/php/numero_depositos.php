<?php
include "./../../conectarbanco.php";

$conn = getConnection();

$status = isset($_GET["status"]) ? $_GET["status"] : null;
$sqlCountDeposits = "SELECT COUNT(*) as depositCount FROM confirmar_deposito WHERE status='PAID_OUT'";
if (!empty($status)) {
    $stmt = $conn->prepare($sqlCountDeposits . " AND status = :status");
    $stmt->bindParam(":status", $status);
    $stmt->execute();
} else {
    $stmt = $conn->query($sqlCountDeposits);
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row ? $row["depositCount"] : "0";
?>
