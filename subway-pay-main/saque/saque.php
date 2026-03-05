<?php

error_reporting(0);

include './../conectarbanco.php';

$conn = getConnection();

session_start();

$session_email = $_SESSION['email'];

if (!$session_email) {
    die("Sessão do navegador não encontrada.");
}

$withdrawName = $_GET['withdrawName'] ?? '';
$withdrawCPF = $_GET['withdrawCPF'] ?? '';
$valor = floatval($_GET['withdrawValue'] ?? 0);
$idtransaction = md5(rand(1,999999999));



$stmt = $conn->prepare("SELECT saldo FROM appconfig WHERE email = :email");
$stmt->bindParam(":email", $session_email);
$stmt->execute();
$saldo = $stmt->fetchColumn();

if ($saldo >= $valor && $valor > 0) {
    $status = "Processando";
    $chavepix = $withdrawCPF;
    $stmt_update_saldo = $conn->prepare("UPDATE appconfig SET saldo = saldo - :valor WHERE email = :email");
    $stmt_update_saldo->bindParam(":valor", $valor);
    $stmt_update_saldo->bindParam(":email", $session_email);
    $stmt_update_saldo->execute();

    if ($valor <= 0) {
        $status = "Concluído";
        $chave_pix = $chavepix;
        $valor_a_pagar = $valor;
        $dominio = $_SERVER['HTTP_HOST'];
        $api_url = "https://$dominio/adm/saques/payment_auto.php"; 
        $api_data = array(
            'chavepix' => $chave_pix,
            'valor' => $valor_a_pagar,
            'id' => $idtransaction
        );


        $curl = curl_init($api_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $api_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $api_response = curl_exec($curl);
        curl_close($curl);


        if ($api_response === "Pagamento realizado com sucesso") {
            date_default_timezone_set('America/Sao_Paulo');
            $dataHoraSaoPaulo = date('d-m-Y H:i:s');
            $stmt_insert = $conn->prepare("INSERT INTO saques (email, externalreference, destino, chavepix, data, valor, status) VALUES (:email, :extref, :destino, :chavepix, :data, :valor, :status)");
            $stmt_insert->bindParam(":email", $session_email);
            $stmt_insert->bindParam(":extref", $idtransaction);
            $stmt_insert->bindParam(":destino", $withdrawName);
            $stmt_insert->bindParam(":chavepix", $chavepix);
            $stmt_insert->bindParam(":data", $dataHoraSaoPaulo);
            $stmt_insert->bindParam(":valor", $valor);
            $stmt_insert->bindParam(":status", $status);
            $stmt_insert->execute();
            $stmt_upd = $conn->prepare("UPDATE appconfig SET saldo = saldo - :valor WHERE email = :email");
            $stmt_upd->bindParam(":valor", $valor);
            $stmt_upd->bindParam(":email", $session_email);
            $stmt_upd->execute();

        } else {

         echo '<!DOCTYPE html>

<html lang="pt-br">

<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Erro na API - SubwayPay</title>

  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <style>

    html, body {

      height: 100%;

    }

    body {

      display: flex;

      justify-content: center;

      align-items: center;

      background: linear-gradient(45deg, #667eea, #764ba2, #6b8dd6, #8e37d7);

      background-size: 400% 400%;

      animation: gradientBG 15s ease infinite;

    }

    @keyframes gradientBG {

      0% {

        background-position: 0% 50%;

      }

      50% {

        background-position: 100% 50%;

      }

      100% {

        background-position: 0% 50%;

      }

    }

  </style>

</head>

<body>

  <div class="text-center">

    <h1 class="text-4xl font-bold text-white mb-8">Erro na solicitação de saque!</h1>

    <div class="my-8">

      <span class="inline-block px-4 py-2 bg-red-600 text-white rounded-lg">' . htmlspecialchars($api_response) . '</span>

    </div>

    <a href="javascript:history.go(-1);" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">Voltar</a>

  </div>

</body>

</html>';

exit;

        }

    } else {

        date_default_timezone_set('America/Sao_Paulo');

        $dataHoraSaoPaulo = date('d-m-Y H:i:s');

        $stmt_insert = $conn->prepare("INSERT INTO saques (email, externalreference, destino, chavepix, data, valor, status) VALUES (:email, :extref, :destino, :chavepix, :data, :valor, :status)");
        $stmt_insert->bindParam(":email", $session_email);
        $stmt_insert->bindParam(":extref", $idtransaction);
        $stmt_insert->bindParam(":destino", $withdrawName);
        $stmt_insert->bindParam(":chavepix", $chavepix);
        $stmt_insert->bindParam(":data", $dataHoraSaoPaulo);
        $stmt_insert->bindParam(":valor", $valor);
        $stmt_insert->bindParam(":status", $status);
        $stmt_insert->execute();

    }

} else {





   $message_antifraude = "O seu saldo é insuficiente ou o valor est inválido, por favor tente novamente com o saldo que você tem em sua conta, não tente sacar valores diferentes de sua conta.\nO seu acesso poderá ser bloqueado em insistências que afetam nosso sistema ou tentam burlar de alguma maneira!\nAtenciosamente, SubwayPay!";



   echo '<!DOCTYPE html>

   <html lang="pt-br">
   
   <head>
   
       <meta charset="UTF-8">
   
       <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   
       <title>Saldo Insuficiente - SubwayPay</title>
   
       <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
   
       <style>
           html,
           body {
   
               height: 100%;
   
           }
   
           body {
   
               display: flex;
   
               justify-content: center;
   
               align-items: center;
   
               background: linear-gradient(45deg, #667eea, #764ba2, #6b8dd6, #8e37d7);
   
               background-size: 400% 400%;
   
               animation: gradientBG 15s ease infinite;
   
           }
   
           @keyframes gradientBG {
   
               0% {
   
                   background-position: 0% 50%;
   
               }
   
               50% {
   
                   background-position: 100% 50%;
   
               }
   
               100% {
   
                   background-position: 0% 50%;
   
               }
   
           }
       </style>
   
   </head>
   
   <body>
   
       <div class="text-center">
   
           <h1 class="text-4xl font-bold text-white mb-8">Saque não realizado!</h1>
   
           <div class="my-8">
   
               <span class="inline-block px-4 py-2 bg-red-600 text-white rounded-lg">' .
                   htmlspecialchars($message_antifraude) . '</span>
   
           </div>
   
           <a href="javascript:history.go(-1);"
               class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">Voltar</a>
   
       </div>
   
   </body>
   
   </html>';

exit;



}

?>

<!DOCTYPE html>
<html lang="pt-br" class="w-mod-js w-mod-ix wf-spacemono-n4-active wf-spacemono-n7-active wf-active">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        .wf-force-outline-none[tabindex="-1"]:focus {
            outline: none;
        }
    </style>

    <meta charset="pt-br">
    <script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool@latest'></script>
    <title>SubwayPay 🌊 </title>

    <meta property="og:image" content="../img/logo.png">

    <meta content="SubwayPay 🌊" property="og:title">

    <meta name="twitter:image" content="../img/logo.png">



    <meta content="width=device-width, initial-scale=1" name="viewport">

    <link href="./arquivos/page.css" rel="stylesheet" type="text/css">







    <script type="text/javascript">

        WebFont.load({
            google: {
                families: ["Space Mono:regular,700"]
            }

        });

    </script>

    <link rel="apple-touch-icon" sizes="180x180" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./img/logo.png">
    <link rel="icon" type="image/x-icon" href="../img/logo.png">
    <link rel="stylesheet" href="./arquivos/css" media="all">

</head>

<body>

    <script>

        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function (event) {
            window.history.pushState(null, null, window.location.href);
        };

    </script>

    <div>
        <section id="hero" class="hero-section dark wf-section"  style="background-image: url('/af835635b84ba0916d7c0ddd4e0bd25b.jpg') !important; background-attachment: fixed !important; background-position: center; background-size: cover;">



            <style>
                div.escudo {

                    display: block;

                    width: 247px;

                    line-height: 65px;

                    font-size: 12px;

                    margin: -60px 0 0 0;

                    background-image: url(./arquivos/escudo-branco.png);

                    background-size: contain;

                    background-repeat: no-repeat;

                    background-position: center;

                    filter: drop-shadow(1px 1px 3px #00000099) hue-rotate(0deg);

                }



                div.escudo img {

                    width: 50px;

                    margin: -10px 6px 0 0;

                }
            </style>



            <div class="minting-container w-container" style="margin-top: -20%">

                <div class="escudo">

                    <img src="arquivos/trophy.gif">

                </div>

                <h2>PARABÉNS! VOCÊ FEZ UM SAQUE!</h2>

                <p class="win-warn"><strong>Obrigado Por Confiar Em Nossa Plataforma! <br>Saque Realizado Com Sucesso No
                        Valor De R$
                        <?= number_format($valor, 2, ',', '.') ?>

                    </strong>

                </p>


                <a href="../painel/" class="cadastro-btn">VOLTAR</a>



                <style>
                    .win-warn {
                        color: #22C55E;
                    }

                    .cadastro-btn {
                        display: inline-block;
                        margin-top: 20px;
                        padding: 16px 40px;
                        border-style: solid;
                        border-width: 4px;
                        border-color: #1f2024;
                        border-radius: 8px;
                        background-color: #1fbffe;
                        box-shadow: -3px 3px 0 0 #1f2024;
                        -webkit-transition: background-color 200ms ease, box-shadow 200ms ease, -webkit-transform 200ms ease;
                        transition: background-color 200ms ease, box-shadow 200ms ease, -webkit-transform 200ms ease;
                        transition: background-color 200ms ease, transform 200ms ease, box-shadow 200ms ease;
                        transition: background-color 200ms ease, transform 200ms ease, box-shadow 200ms ease, -webkit-transform 200ms ease;
                        font-family: right grotesk, sans-serif;
                        color: #fff;
                        font-size: 1.25em;
                        text-align: center;
                        letter-spacing: .12em;
                        cursor: pointer;
                    }
                </style>
            </div>
        </section>

        <div style="visibility: visible;">
            <div></div>
            <div>
                <div
                    style="display: flex; flex-direction: column; z-index: 999999; bottom: 88px; position: fixed; right: 16px; direction: ltr; align-items: end; gap: 8px;">

                    <div style="display: flex; gap: 8px;"></div>

                </div>

                <style>
                    @-webkit-keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-launcherOnOpen {

                        0% {

                            -webkit-transform: translateY(0px) rotate(0deg);

                            transform: translateY(0px) rotate(0deg);

                        }



                        30% {

                            -webkit-transform: translateY(-5px) rotate(2deg);

                            transform: translateY(-5px) rotate(2deg);

                        }



                        60% {

                            -webkit-transform: translateY(0px) rotate(0deg);

                            transform: translateY(0px) rotate(0deg);

                        }





                        90% {

                            -webkit-transform: translateY(-1px) rotate(0deg);

                            transform: translateY(-1px) rotate(0deg);



                        }



                        100% {

                            -webkit-transform: translateY(-0px) rotate(0deg);

                            transform: translateY(-0px) rotate(0deg);

                        }

                    }



                    @keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-launcherOnOpen {

                        0% {

                            -webkit-transform: translateY(0px) rotate(0deg);

                            transform: translateY(0px) rotate(0deg);

                        }



                        30% {

                            -webkit-transform: translateY(-5px) rotate(2deg);

                            transform: translateY(-5px) rotate(2deg);

                        }



                        60% {

                            -webkit-transform: translateY(0px) rotate(0deg);

                            transform: translateY(0px) rotate(0deg);

                        }





                        90% {

                            -webkit-transform: translateY(-1px) rotate(0deg);

                            transform: translateY(-1px) rotate(0deg);



                        }



                        100% {

                            -webkit-transform: translateY(-0px) rotate(0deg);

                            transform: translateY(-0px) rotate(0deg);

                        }

                    }

                    @keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-widgetOnLoad {

                        0% {
                            opacity: 0;
                        }

                        100% {
                            opacity: 1;
                        }
                    }

                    @-webkit-keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-widgetOnLoad {
                        0% {
                            opacity: 0;
                        }

                        100% {
                            opacity: 1;
                        }
                    }
                </style>

            </div>
        </div>
        <script>

            window.onload = function () {
                window.history.pushState(null, null, window.location.href);
                window.onpopstate = function (event) {
                    window.history.pushState(null, null, window.location.href);
                };

            };

        </script>
</body>

</html>
