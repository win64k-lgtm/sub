<?php
include "./../../conectarbanco.php";

$conn = getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientID = $_POST["clientID"];
    $clientSecret = $_POST["clientSecret"];
    $stmt = $conn->query("SELECT * FROM gateway LIMIT 1");
    if ($stmt->rowCount() > 0) {
        $upd = $conn->prepare("UPDATE gateway SET client_id = :cid, client_secret = :cs");
        $upd->bindParam(":cid", $clientID);
        $upd->bindParam(":cs", $clientSecret);
        $upd->execute();
        echo "Sucesso: Valores atualizados com sucesso!";
    } else {
        $ins = $conn->prepare("INSERT INTO gateway (client_id, client_secret) VALUES (:cid, :cs)");
        $ins->bindParam(":cid", $clientID);
        $ins->bindParam(":cs", $clientSecret);
        $ins->execute();
        echo "Sucesso: Nova linha adicionada!";
    }
}

$client_id = "";
$client_secret = "";
$stmt = $conn->query("SELECT client_id, client_secret FROM gateway LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $client_id = $row["client_id"];
    $client_secret = $row["client_secret"];
}
?>
