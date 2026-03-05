<?php

session_start();

if (!isset($_SESSION["emailadm"])) {
    header("Location: ../login");

    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    exit();
}

include "./../../conectarbanco.php";

$conn = getConnection();

$field = $_GET["field"];
$value = $_POST["value"];

$stmt = $conn->query("SELECT * FROM app LIMIT 1");
if ($stmt->rowCount() > 0) {
    $sql = "UPDATE app SET $field = $value";
} else {
    $sql = "INSERT INTO app SET  $field = $value";
}

$result = $conn->query($sql);

header("Location: ./");
