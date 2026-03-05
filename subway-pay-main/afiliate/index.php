<?php
include "../conectarbanco.php";

try {
  $conn = getConnection();
  $stmt = $conn->query("SELECT nome_unico, nome_um, nome_dois, saques_min FROM app");
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $nomeUnico = $row["nome_unico"];
    $nomeUm = $row["nome_um"];
    $nomeDois = $row["nome_dois"];
    $saqueMinimo = $row["saques_min"];
  } else {
    return false;
  }
} catch (PDOException $e) {
  die("Conexão falhou: " . $e->getMessage());
}
?>



<?php
session_start();
ini_set("display_errors", 1);
ini_set("display_startup_erros", 1);
error_reporting(E_ALL);

include "./../conectarbanco.php";

$conn = getConnection();

if (isset($_SESSION["email"])) {
  $email = $_SESSION["email"];

  $stmt = $conn->prepare("SELECT plano FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $plano = $stmt->fetchColumn();

  $cpa = $conn->query("SELECT cpa FROM app")->fetchColumn();
  $stmt = $conn->prepare("SELECT cpa FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $cpa_u = $stmt->fetchColumn();
  if ($cpa_u != 0) {
    $cpa = $cpa_u;
  }

  $stmt = $conn->prepare("SELECT cont_cpa FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $cont_cpa = $stmt->fetchColumn();

  $stmt = $conn->prepare("SELECT saldo_comissao FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $saldo_comissao = $stmt->fetchColumn();

  $stmt = $conn->prepare("SELECT linkafiliado FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $linkAfiliado = $stmt->fetchColumn();

  $stmt = $conn->prepare("SELECT count(*) FROM appconfig WHERE afiliado = (SELECT id FROM appconfig WHERE email = :email LIMIT 1)");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $cads = $stmt->fetchColumn();

  $stmt = $conn->prepare("SELECT count(*) FROM appconfig WHERE afiliado = (SELECT id FROM appconfig WHERE email = :email LIMIT 1) AND status_primeiro_deposito = '1'");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $cad_ativo = $stmt->fetchColumn();

  $stmt = $conn->prepare("SELECT saldo_cpa FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $saldo_cpa = $stmt->fetchColumn();

  $stmt = $conn->prepare("SELECT comissaofake FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $rev_ativo_sum = $stmt->fetchColumn();

  $saldo_comissao = floatval($saldo_cpa) + floatval($rev_ativo_sum);

  $upd = $conn->prepare("UPDATE appconfig SET saldo_comissao = :sc WHERE email = :email");
  $upd->bindParam(":sc", $saldo_comissao);
  $upd->bindParam(":email", $_SESSION["email"]);
  $upd->execute();

  $stmt = $conn->prepare("SELECT indicados FROM appconfig WHERE email = :email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $indicados = $stmt->fetchColumn();
} else {
  header("Location: /login");
  exit();
}
?>



<!DOCTYPE html>

<html lang="pt-br" class="w-mod-js wf-spacemono-n4-active wf-spacemono-n7-active wf-active w-mod-ix">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        .wf-force-outline-none[tabindex="-1"]:focus {
            outline: none;
        }
    </style>
    <meta charset="pt-br">
    <title>
        <?= $nomeUnico ?> 🌊
    </title>

    <meta property="og:image" content="../img/logo.png">

    <meta content="<?= $nomeUnico ?> 🌊" property="og:title">

    <meta name="twitter:image" content="../img/logo.png">

    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="arquivos/page.css" rel="stylesheet" type="text/css">
    <script src="arquivos/webfont.js" type="text/javascript"></script>


    <script type="text/javascript">
        WebFont.load({
            google: {
                families: ["Space Mono:regular,700"]
            }
        });
    </script>

    <script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool@latest'></script>

    <script type="text/javascript">
        ! function (o, c) {
            var n = c.documentElement,
                t = " w-mod-";
            n.className += t + "js", ("ontouchstart" in o || o.DocumentTouch && c instanceof DocumentTouch) && (n
                .className += t + "touch")
        }(window, document);
    </script>
    <link rel="apple-touch-icon" sizes="180x180" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/logo.png">


    <link rel="icon" type="image/x-icon" href="../img/logo.png">

    <link rel="stylesheet" href="arquivos/css" media="all">

    <?php include "../pixels.php"; ?>

</head>

<body>
    <div>
        <div data-collapse="small" data-animation="default" data-duration="400" role="banner" class="navbar w-nav">
            <div class="container w-container">
                <a href="/painel" aria-current="page" class="brand w-nav-brand" aria-label="home">
                    <img src="arquivos/l2.png" loading="lazy" height="28" alt="" class="image-6">
                    <div class="nav-link logo">
                        <?= $nomeUnico ?>
                    </div>
                </a>
                <nav role="navigation" class="nav-menu w-nav-menu">
                    <a href="../painel" class="nav-link w-nav-link" style="max-width: 940px;">Jogar</a>
                    <a href="../saque" class="nav-link w-nav-link" style="max-width: 940px;">Saque</a>

                    <a href="../afiliate/" class="nav-link w-nav-link w--current" style="max-width: 940px;">Indique e
                        Ganhe</a>
                    <a href="../logout.php" class="nav-link w-nav-link" style="max-width: 940px;">Sair</a>
                    <a href="../deposito/" class="button nav w-button">Depositar</a>
                </nav>



                <?php include "../pixels.php"; ?>

                <style>
                    .nav-bar {
                        display: none;
                        background-color: #333;
                        padding: 20px;
                        width: 90%;

                        position: fixed;
                        top: 0;
                        left: 0;
                        z-index: 1000;
                    }

                    .nav-bar a {
                        color: white;
                        text-decoration: none;
                        padding: 10px;
                        display: block;
                        margin-bottom: 10px;
                    }

                    .nav-bar a.login {
                        color: white;
                    }

                    .button.w-button {
                        text-align: center;
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var menuButton = document.querySelector('.menu-button');
                        var navBar = document.querySelector('.nav-bar');

                        menuButton.addEventListener('click', function () {
                            if (navBar.style.display === 'block') {
                                navBar.style.display = 'none';
                            } else {
                                navBar.style.display = 'block';
                            }
                        });
                    });
                </script>


                <style>
                    .menu-button2 {
                        border-radius: 15px;
                        background-color: #000;
                    }
                </style>



                <div class="w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button"
                    tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                    <div class="" style="-webkit-user-select: text;">

                        <a href="../deposito/" class="menu-button2 w-nav-dep nav w-button">DEPOSITAR</a>
                    </div>
                </div>
                <div class="menu-button w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button"
                    tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                    <div class="icon w-icon-nav-menu"></div>
                </div>
            </div>
            <div class="w-nav-overlay" data-wf-ignore="" id="w-nav-overlay-0"></div>
        </div>
        <div class="nav-bar">
            <a href="../painel/" class="button w-button">
                <div>Jogar</div>
            </a>
            <a href="../saque/" class="button w-button">
                <div>Saque</div>
            </a>

            </a>
            <a href="../afiliate/" class="button w-button">
                <div>Indique & Ganhe</div>
            </a>
            <a href="../logout.php" class="button w-button">
                <div>Sair</div>
            </a>
            <a href="../deposito/" class="button w-button">Depositar</a>
        </div>

        <section id="hero" class="hero-section dark wf-section"
            style="background-image: url('/af835635b84ba0916d7c0ddd4e0bd25b.jpg') !important; background-attachment: fixed !important; background-position: center; background-size: cover;">
            <div class="minting-container w-container">
                <img src="arquivos/image.gif" loading="lazy" width="240"
                    data-w-id="6449f730-ebd9-23f2-b6ad-c6fbce8937f7" alt="Roboto #6340" class="mint-card-image">

                <h<h2>Divulgue & Ganhe</h2>
                    <p>Este é o resumo de seu resultado divulgando. <br>
                    <p>Seu link de divulgação é: <br>
                        <?php echo $linkAfiliado; ?>
                    </p>
                    <br>

                    <p>
                        <a id="copiarLinkBtn" class="primary-button dark w-button" onclick="copiarLink()">Copiar link de
                            afiliado</a>
                    </p>

                    <br><br>

                    <script>
                        function copiarLink() {
                            var linkText = '<?php echo $linkAfiliado; ?>';
                            var input = document.createElement('textarea');
                            input.value = linkText;
                            document.body.appendChild(input);
                            input.select();
                            document.execCommand('copy');
                            document.body.removeChild(input);
                            alert('Link copiado para a área de transferência: ' + linkText);
                        }
                    </script>

                    <div class="properties">

                        <div class="properties">
                            <h3 class="rarity-heading">Extrato</h3>
                            <div class="rarity-row roboto-type">
                                <div class="rarity-number full">Contabilização pode demorar até 24 horas.</div>
                            </div>
                            <div class="rarity-row roboto-type">
                                <div class="rarity-number full">Saldo disponível:</div>
                                <div class="padded">R$
                                    <?php echo floatval($saldo_cpa) +
                                          floatval($rev_ativo_sum); ?>
                                </div>

                            </div>
                            <div class="w-layout-grid grid">
                                <div>
                                    <div class="rarity-row blue">
                                        <div class="rarity-number">Comissão CPA</div>
                                        <div>R$
                                            <?php echo $saldo_cpa; ?>
                                        </div>
                                    </div>
                                    <div class="rarity-row">
                                        <div class="rarity-number">Comissão REV</div>
                                        <div>R$
                                            <?php echo $rev_ativo_sum; ?>
                                        </div>
                                    </div>

                                    <div class="rarity-row blue">
                                        <div class="rarity-number">Indicações</div>
                                        <div>
                                            <?php echo $indicados; ?> cadastros
                                        </div>

                                    </div>
                                </div>
                                <div>

                                    <div class="rarity-row">
                                        <div class="rarity-number">Valor por depósito do indicado (CPA)</div>
                                        <div>
                                            R$
                                            <?php echo $cpa_u; ?>
                                        </div>


                                    </div>
                                    <div class="rarity-row blue">
                                        <div class="rarity-number">% Sobre a perda do indicado (REV)</div>
                                        <div>
                                            <?php echo $plano; ?>%
                                        </div>
                                    </div>

                                </div>
                            </div>




                            <?php
              $saqueMinimo = $row["saques_min"];

              $saldoTotal = floatval($saldo_cpa) + floatval($rev_ativo_sum);

              $habilitarSaque = $saldoTotal >= $saqueMinimo;
              ?>

                            <div class="grid-box">
                                <?php if ($habilitarSaque) { ?>
                                <a href="../saque-afiliado" class="primary-button w-button">Sacar saldo disponível</a>
                                <?php } else { ?>
                                <a href="#" class="primary-button w-button" style='background-color: gray;'>Saldo
                                    insuficiente para saque</a>
                                <?php } ?>
                                <a href="#" target="_blank" class="primary-button dark w-button">Suporte para
                                    afiliados</a>
                            </div>
                            <br>

                        </div>
                    </div>
        </section>



        <div class="intermission wf-section"></div>
        <div id="about" class="comic-book white wf-section">
            <div class="minting-container left w-container">
                <div class="w-layout-grid grid-2">
                    <img src="arquivos/money.png" loading="lazy" width="240" alt="Roboto #6340"
                        class="mint-card-image v2">
                    <div>
                        <h2>Indique um amigo e ganhe R$ no PIX</h2>
                        <h3>Como funciona?</h3>
                        <p>Convide seus amigos que ainda não estão na plataforma. Você receberá R$5 por cada amigo que
                            se
                            inscrever e fizer um depósito. Não há limite para quantos amigos você pode convidar. Isso
                            significa que também não há limite para quanto você pode ganhar!</p>
                        <h3>Como recebo o dinheiro?</h3>
                        <p>O saldo é adicionado diretamente ao seu saldo no painel abaixo, com o qual você pode sacar
                            via
                            PIX.</p>
                    </div>
                </div>
            </div>
        </div>



        <div class="footer-section wf-section">
            <div class="domo-text">
                <?= $nomeUm ?> <br>
            </div>
            <div class="domo-text purple">
                <?= $nomeDois ?> <br>
            </div>
            <div class="follow-test">© Copyright xlk Limited, with registered offices at Dr. M.L. King Boulevard 117,
                accredited by license GLH-16289876512. </div>
            <div class="follow-test">
                <a href="/legal">
                    <strong class="bold-white-link">Termos de uso</strong>
                </a>
            </div>
            <div class="follow-test">contato@
                <?php
                                        $nomeUnico = strtolower(str_replace(" ", "", $nomeUnico));
                                        echo $nomeUnico;
                                        ?>.com
            </div>
        </div>





        <script type="text/javascript">
            var hidden = false;

            $(document).ready(function () {
                $("form").submit(function () {
                    $(this).submit(function () {
                        return false;
                    });
                    return true;
                });
            });

            function copyToClipboard(bt, text) {
                const elem = document.createElement('textarea');
                elem.value = text;
                document.body.appendChild(elem);
                elem.select();
                document.execCommand('copy');
                document.body.removeChild(elem);
                document.getElementById('depCopiaCodigo').innerHTML = "URL Copiada";
            }
        </script>
    </div>
    <div id="imageDownloaderSidebarContainer">
        <div class="image-downloader-ext-container">
            <div tabindex="-1" class="b-sidebar-outer">
                <div id="image-downloader-sidebar" tabindex="-1" role="dialog" aria-modal="false" aria-hidden="true"
                    class="b-sidebar shadow b-sidebar-right bg-light text-dark" style="width: 500px; display: none;">
                    <!---->
                    <div class="b-sidebar-body">
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="visibility: visible;">
        <div></div>
        <div>
            <div
                style="display: flex; flex-direction: column; z-index: 999999; bottom: 88px; position: fixed; right: 16px; direction: ltr; align-items: end; gap: 8px;">
                <div style="display: flex; gap: 8px;"></div>
            </div>
            <style>
                @-webkit-keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-launcherOnOpen {
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

                @keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-launcherOnOpen {
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

                @keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-widgetOnLoad {
                    0% {
                        opacity: 0;
                    }

                    100% {
                        opacity: 1;
                    }
                }

                @-webkit-keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-widgetOnLoad {
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
</body>

</html>