<?php

if (!isset($_GET['token'])) {
    http_response_code(400);
    return;
}

function non_null($form, $field) {
    if (!isset($form[$field])) {
        return "Campo $field inválido";
    }
    return null;
}

function validate_form($form) {
    foreach (array('out') as $field) {
        if ($error = non_null($form, $field)) {
            return $error;
        }
    }
    return null;
}

function query($conn, $sql) {
    //echo "Running query: " . $sql;
    
    $response = $conn->query($sql);

    if ($conn->error) {
        return [
        'is_error' => true,
        'response' => $conn->error
        ];
    }

    return [
        'is_error' => false,
        'response' => $response
    ];
}

function get_connect() {
    include "./../../conectarbanco.php";
    return getConnection();
}

$email = $_SESSION['email'];
$error = validate_form($_GET);
$out = $_GET['out'] * 100;
$token = $_GET['token'];

if ($error) {
    echo $error;
    http_response_code(400);
    return;
}

$conn = get_connect();
$stmt = $conn->prepare("UPDATE game SET out_value = :out FROM token WHERE game.email = token.email AND token.value = :token");
$stmt->bindParam(":out", $out);
$stmt->bindParam(":token", $token);
$stmt->execute();
$stmt = $conn->prepare("UPDATE appconfig SET saldo = saldo + :out FROM token WHERE appconfig.email = token.email AND token.value = :token");
$stmt->bindParam(":out", $out);
$stmt->bindParam(":token", $token);
$stmt->execute();
$stmt = $conn->prepare("DELETE FROM token WHERE value = :token");
$stmt->bindParam(":token", $token);
$stmt->execute();

header('Location: https://subwaypay.tech/painel/');