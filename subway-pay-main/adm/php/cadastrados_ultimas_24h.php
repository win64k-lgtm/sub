<?php
try {
    include "./../../conectarbanco.php";

    $conn = getConnection();

    $leadAff = isset($_GET["leadAff"]) ? $_GET["leadAff"] : null;

    $sql = "SELECT id FROM appconfig";
    if (!empty($leadAff)) {
        $sql .= " WHERE lead_aff = :leadAff";
    }
    $sql .= " ORDER BY CASE WHEN data_cadastro IS NULL THEN 1 ELSE 0 END, data_cadastro ASC";

    if (!empty($leadAff)) {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":leadAff", $leadAff);
        $stmt->execute();
    } else {
        $stmt = $conn->query($sql);
    }

    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (!empty($leadAff)) {
        $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM appconfig WHERE lead_aff = :leadAff");
        $stmtTotal->bindParam(":leadAff", $leadAff);
        $stmtTotal->execute();
    } else {
        $stmtTotal = $conn->query("SELECT COUNT(*) as total FROM appconfig");
    }
    $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)["total"];

    $sql24 = "SELECT COUNT(*) as ultimas_24h FROM appconfig WHERE TO_TIMESTAMP(data_cadastro, 'DD-MM-YYYY HH24:MI') >= NOW() - INTERVAL '24 hours'";
    if (!empty($leadAff)) {
        $stmt24 = $conn->prepare("SELECT COUNT(*) as ultimas_24h FROM appconfig WHERE TO_TIMESTAMP(data_cadastro, 'DD-MM-YYYY HH24:MI') >= NOW() - INTERVAL '24 hours' AND lead_aff = :leadAff");
        $stmt24->bindParam(":leadAff", $leadAff);
        $stmt24->execute();
    } else {
        $stmt24 = $conn->query($sql24);
    }
    $ultimas24h = $stmt24->fetch(PDO::FETCH_ASSOC)["ultimas_24h"];

    header("Content-Type: application/json");
    echo json_encode([
        "data" => $data,
        "total" => $total,
        "ultimas_24h" => $ultimas24h,
    ]);
} catch (Exception $e) {
    var_dump($e);
    http_response_code(200);
}
?>
