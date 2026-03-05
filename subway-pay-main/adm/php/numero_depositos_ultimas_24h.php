<?php
include "./../../conectarbanco.php";

$conn = getConnection();

$twentyFourHoursAgo = date("d-m-Y H:i:s", strtotime("-24 hours"));
$status = isset($_GET["status"]) ? $_GET["status"] : null;
if (!empty($status)) {
    $stmt = $conn->prepare("SELECT COUNT(*) as depositCount FROM confirmar_deposito WHERE data >= :data AND status = :status");
    $stmt->bindParam(":data", $twentyFourHoursAgo);
    $stmt->bindParam(":status", $status);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) as depositCount FROM confirmar_deposito WHERE data >= :data");
    $stmt->bindParam(":data", $twentyFourHoursAgo);
    $stmt->execute();
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row ? $row["depositCount"] : "0";
?>
