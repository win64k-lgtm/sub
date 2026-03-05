<?php

function bad_request()
{
    echo "a";
    http_response_code(400);
    exit();
}

if (!isset($_GET["token"])) {
    bad_request();
}

$externalReference = $_GET["token"];

include "./../conectarbanco.php";

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT status FROM confirmar_deposito WHERE externalreference = :ref");
    $stmt->bindParam(":ref", $externalReference);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "error";
    return;
}

if (!$result) {
    echo json_encode(["message" => "Token inválido"]);
    http_response_code(400);
    return;
}

echo json_encode($result);
http_response_code(200);
?>
