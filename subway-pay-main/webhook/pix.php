<?php
ini_set("display_errors", 1);
ini_set("display_startup_erros", 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit();
}

function bad_request()
{
    http_response_code(400);
    exit();
}

$payload = file_get_contents("php://input");

$payload = json_decode($payload, true);
file_put_contents("teste.txt", $payload);

if (is_null($payload)) {
    bad_request();
}

if ($payload["typeTransaction"] !== "PIX") {
    bad_request();
}

function get_conn()
{
    include "./../conectarbanco.php";
    return getConnection();
}

$externalReference = $payload["idTransaction"];
$status = $payload["statusTransaction"];

if ($status === "PAID_OUT") {
    $conn = get_conn();

    $stmt = $conn->prepare("SELECT * FROM confirmar_deposito WHERE externalreference = :ref");
    $stmt->bindParam(":ref", $externalReference);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        bad_request();
    }

    if ($result["status"] === "PAID_OUT") {
        bad_request();
    }

    $upd = $conn->prepare("UPDATE confirmar_deposito SET status = 'PAID_OUT' WHERE externalreference = :ref");
    $upd->bindParam(":ref", $externalReference);
    $upd->execute();

    $valor_depositado = $result["valor"];
    $email = $result["email"];
    $stmtUser = $conn->prepare("SELECT * FROM appconfig WHERE email = :email");
    $stmtUser->bindParam(":email", $email);
    $stmtUser->execute();
    $resultUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $resultApp = $conn->query("SELECT * FROM app LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    $stmtDep = $conn->prepare("SELECT count(*) as total FROM confirmar_deposito WHERE email = :email");
    $stmtDep->bindParam(":email", $email);
    $stmtDep->execute();
    $resultDeposito = $stmtDep->fetch(PDO::FETCH_ASSOC);
    $conn->query(
        sprintf(
            "UPDATE appconfig SET depositou = depositou + '{$valor_depositado}' WHERE email = '{$email}'"
        )
    );

    if ($resultDeposito["total"] >= 1) {
        if (
            !is_null($resultUser["afiliado"]) &&
            !empty($resultUser["afiliado"])
        ) {
            if (intval($result["valor"]) >= $resultApp["deposito_min_cpa"]) {
                $randomNumber = rand(0, 100);
                if ($randomNumber <= intval($resultApp["chance_afiliado"])) {
                    if ($resultUser["cpa"] > 0) {
                        $conn->query(
                            sprintf(
                                "UPDATE appconfig SET status_primeiro_deposito=1 WHERE email = '{$resultUser["email"]}'"
                            )
                        );
                        $conn->query(
                            sprintf(
                                "UPDATE appconfig SET saldo_cpa = saldo_cpa + %s WHERE id = '%s'",
                                intval($resultUser["cpa"]),
                                $resultUser["afiliado"]
                            )
                        );
                    } else {
                        $conn->query(
                            sprintf(
                                "UPDATE appconfig SET status_primeiro_deposito=1 WHERE email = '{$resultUser["email"]}'"
                            )
                        );
                        $conn->query(
                            sprintf(
                                "UPDATE appconfig SET saldo_cpa = saldo_cpa + %s WHERE id = '%s'",
                                intval($resultApp["cpa"]),
                                $resultUser["afiliado"]
                            )
                        );
                    }
                }
            }
        }
    }

    $result = $conn->query(
        sprintf(
            "UPDATE appconfig SET saldo = saldo + %s WHERE email = '%s'",
            intval($result["valor"]),
            $result["email"]
        )
    );

    var_dump(
        json_encode([
            "success" => true,
            "message" => "Pagamento do PIX confirmado.",
        ])
    );
    http_response_code(200);
    exit();
}
