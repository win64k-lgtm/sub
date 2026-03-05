
<?php
include './../../conectarbanco.php';

$conn = getConnection();

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$sql = "SELECT email, nome, pix, valor, status FROM saque_afiliado";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$data = array_reverse($data);

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>