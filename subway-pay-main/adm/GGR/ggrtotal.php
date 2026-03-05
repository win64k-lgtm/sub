<?php
include './../../conectarbanco.php';

$conn = getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ggr_total FROM ggr"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row["ggr_total"];
} else {
    echo "0";
}

$conn->close();
?>
