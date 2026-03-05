<?php
include './../../conectarbanco.php';

$conn = getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ggr_pago FROM ggr"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo number_format($row["ggr_pago"], 2, '.', '');
} else {
    echo "0";
}

$conn->close();
?>
