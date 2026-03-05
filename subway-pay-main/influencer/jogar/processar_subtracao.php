<?php
include "./../conectarbanco.php";

$conn = getConnection();

session_start();
$session_email = $_SESSION["email"];

if (!$session_email) {
    die("Sessão do navegador não encontrada.");
}

$valor = floatval($_POST["valor"]);

$stmt = $conn->prepare("SELECT saldo FROM appconfig WHERE email = :email");
$stmt->bindParam(":email", $session_email);
$stmt->execute();
$saldo = $stmt->fetchColumn();

if ($saldo !== false && $saldo >= $valor && $valor > 0) {
    $stmt_update = $conn->prepare("UPDATE appconfig SET saldo = saldo - :valor, percas = percas + :valor2 WHERE email = :email");
    $stmt_update->bindParam(":valor", $valor);
    $stmt_update->bindParam(":valor2", $valor);
    $stmt_update->bindParam(":email", $session_email);
    $stmt_update->execute();

    header("Location: ../jogar");
    exit();
} else {
    echo "Saldo insuficiente ou valor inválido.";
}
?>
