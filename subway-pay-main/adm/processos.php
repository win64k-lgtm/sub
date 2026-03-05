<?php
try {
    session_start();

    if (!isset($_SESSION["emailadm"])) {
        header("Location: ../login");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        exit();
    }

    include "./../conectarbanco.php";

    $conn = getConnection();

    function required($form, $field)
    {
        if (!isset($form[$field]) || !$form[$field]) {
            return "$field é requerido";
        }

        return null;
    }

    function validate_form($form, $fields)
    {
        foreach ($fields as $field) {
            if ($error = required($form, $field)) {
                return $error;
            }
        }

        return null;
    }

    function get_form()
    {
        return [
            "valor" => $_POST["valor"],
        ];
    }

    $form = get_form();
    $error = validate_form($form, ["valor"]);
    $valor = $form["valor"];

    if (isset($_GET["opcao"])) {
        $opcao = $_GET["opcao"];
    }

    $sql = "SELECT * FROM app";
    $stmt = $conn->query($sql);
    $result = $stmt;

    if ($error) {
        $msg = $error;
        var_dump($msg);
        var_dump($form);
    } else {
        switch ($opcao) {
            case "depositoMin":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET deposito_min = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET deposito_min = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            case "saqueMin":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET saques_min = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET saques_min = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            case "apostaMax":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET aposta_max = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET aposta_max = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            case "apostaMin":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET aposta_min = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET aposta_min = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            case "rolloverSaque":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET rollover_saque = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET rollover_saque = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            case "taxaSaque":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET taxa_saque = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET taxa_saque = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            case "dificuldadeJogo":
                if ($result->rowCount() > 0) {
                    $sql_update = "UPDATE app SET dificuldade_jogo = '$valor'";
                    $conn->query($sql_update);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                } else {
                    $sql_insert = "INSERT INTO app SET dificuldade_jogo = '$valor'";
                    $conn->query($sql_insert);
                    if (true) {
                        header("Location: /adm");
                        exit();
                    } else {
                        header("Location: /adm");
                        exit();
                    }
                }

            default:
                echo "entrei default";
                break;
        }
    }
} catch (Exception $ex) {
    var_dump($ex);
    exit();
}
?>
