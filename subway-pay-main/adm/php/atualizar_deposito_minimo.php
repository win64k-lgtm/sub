<?php
include "./../../conectarbanco.php";

try {
    $conn = getConnection();
    $novoValor = $_POST["novo_valor"];
    $stmt = $conn->prepare("UPDATE app SET deposito_min = :val");
    $stmt->bindParam(":val", $novoValor);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "Valor atualizado com sucesso!";
    } else {
        echo "Nenhuma linha alterada.";
    }
} catch (PDOException $e) {
    echo "Erro ao atualizar o valor: " . $e->getMessage();
}
?>
