<?php

if (!isset($_SESSION["email"])) {
    http_response_code(401);
    return;
}

if (!isset($_GET["token"])) {
    http_response_code(400);
    return;
}

function non_null($form, $field)
{
    if (!isset($form[$field])) {
        return "Campo $field inválido";
    }
    return null;
}

function validate_form($form)
{
    foreach (["out"] as $field) {
        if ($error = non_null($form, $field)) {
            return $error;
        }
    }
    return null;
}

function get_connect()
{
    include "./../conectarbanco.php";
    return getConnection();
}

$email = $_SESSION["email"];
$error = validate_form($_GET);
$token = $_GET["token"];

if ($error) {
    echo $error;
    http_response_code(400);
    return;
}

$conn = get_connect();
$stmt = $conn->prepare("SELECT g.entry_value FROM game g INNER JOIN token t ON g.email = t.email AND t.value = :token WHERE g.email = :email");
$stmt->bindParam(":token", $token);
$stmt->bindParam(":email", $email);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $entry = $row["entry_value"];
    $upd = $conn->prepare("UPDATE appconfig SET saldo = saldo - :entry WHERE email = :email");
    $upd->bindParam(":entry", $entry);
    $upd->bindParam(":email", $email);
    $upd->execute();
}
$stmt = $conn->prepare("DELETE FROM token WHERE value = :token");
$stmt->bindParam(":token", $token);
$stmt->execute();

header("Location: ../painel");

echo $out;
