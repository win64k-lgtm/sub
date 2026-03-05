<?php
include "./../../conectarbanco.php";

try {
    $conn = getConnection();
    $novoValor2 = $_POST["novo_valor"];
    $stmt = $conn->prepare("UPDATE app SET saques_min = :val");
    $stmt->bindParam(":val", $novoValor2);
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
