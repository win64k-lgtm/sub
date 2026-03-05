<?php

function non_null($form, $field)
{
    if (!isset($form[$field])) {
        return "Argumento $field inválido";
    }
    return null;
}

function validate_args($form)
{
    foreach (["token"] as $field) {
        if ($error = non_null($form, $field)) {
            return $error;
        }
    }
    return null;
}

function query($conn, $sql)
{
    try {
        $stmt = $conn->query($sql);
        return ["is_error" => false, "response" => $stmt];
    } catch (Exception $e) {
        return ["is_error" => true, "response" => $e->getMessage()];
    }
}

function get_connect()
{
    include "./../conectarbanco.php";
    return getConnection();
}

function get_game($conn, $token)
{
    $stmt = $conn->prepare("SELECT g.entry_value FROM game g INNER JOIN token t ON t.value = :token AND t.email = g.email");
    $stmt->bindParam(":token", $token);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

$error = validate_args($_GET);

if ($error) {
    http_response_code(400);
    return;
}

$conn = get_connect();

$token = $_GET["token"];
$game = get_game($conn, $token);

echo json_encode([
    "game" => $game,
]);

http_response_code(200);
?>
