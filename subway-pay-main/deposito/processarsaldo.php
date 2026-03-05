<?php
$valor_transacao_multiplicado = isset($_GET["valor_transacao_multiplicado"])
    ? $_GET["valor_transacao_multiplicado"]
    : 0;

session_start();

include "./../conectarbanco.php";

try {
    $conn = getConnection();
    if (isset($_SESSION["email"])) {
        $email = $_SESSION["email"];
        $saldo = isset($_POST["valor_transacao_multiplicado"]) ? $_POST["valor_transacao_multiplicado"] : 0;
        $stmt = $conn->prepare("UPDATE appconfig SET saldo = saldo + :saldo WHERE email = :email");
        $stmt->bindParam(":saldo", $saldo);
        $stmt->bindParam(":email", $email);
        if ($stmt->execute()) {
            echo "Saldo atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar o saldo.";
        }
    } else {
        echo "Email não encontrado na sessão.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
