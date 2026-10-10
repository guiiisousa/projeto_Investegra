<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$id_usuario = $_SESSION["id"];


/*
|--------------------------------------------------------------------------
| Buscar dados do usuário
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        nome,
        idade,
        experiencia,
        renda_mensal,
        horizonte_investimento,
        tolerancia_risco,
        objetivo,
        descricao_pessoal,
        score_risco,
        perfil_risco,
        analise_ia
    FROM usuarios
    WHERE id = ?
";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Usuário não encontrado.");
}

$usuario = $resultado->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Dados para exibição
|--------------------------------------------------------------------------
*/

$nome = htmlspecialchars(
    $usuario["nome"] ?? ""
);

$idade = htmlspecialchars(
    $usuario["idade"] ?? ""
);

$experiencia = htmlspecialchars(
    $usuario["experiencia"] ?? ""
);

$renda = $usuario["renda_mensal"] ?? 0;

$horizonte = htmlspecialchars(
    $usuario["horizonte_investimento"] ?? ""
);

$tolerancia = htmlspecialchars(
    $usuario["tolerancia_risco"] ?? ""
);

$objetivo = htmlspecialchars(
    $usuario["objetivo"] ?? ""
);

$descricao = htmlspecialchars(
    $usuario["descricao_pessoal"] ?? ""
);

$score = (int) ($usuario["score_risco"] ?? 0);

$perfil = htmlspecialchars(
    $usuario["perfil_risco"] ?? "Moderado"
);

$analise_ia = $usuario["analise_ia"] ?? "";


/*
|--------------------------------------------------------------------------
| Formatar renda
|--------------------------------------------------------------------------
*/

$renda_formatada = number_format(
    (float) $renda,
    2,
    ",",
    "."
);


/*
|--------------------------------------------------------------------------
| Texto explicativo do perfil
|--------------------------------------------------------------------------
*/

$descricao_perfil = "";

if ($perfil === "Conservador") {

    $descricao_perfil =
        "O perfil conservador indica uma maior preocupação com a preservação do patrimônio e uma menor tolerância a oscilações. É importante observar liquidez, segurança e os riscos envolvidos em cada investimento.";

} elseif ($perfil === "Moderado") {

    $descricao_perfil =
        "O perfil moderado indica uma disposição intermediária ao risco, buscando equilibrar preservação do patrimônio e possibilidade de crescimento. A diversificação pode ser importante para equilibrar diferentes níveis de risco.";

} elseif ($perfil === "Arrojado") {

    $descricao_perfil =
        "O perfil arrojado indica maior tolerância a oscilações e maior disposição para assumir riscos em busca de crescimento no longo prazo. Ainda assim, é importante considerar diversificação, horizonte de investimento e capacidade de suportar perdas.";

} else {

    $descricao_perfil =
        "Seu perfil foi calculado pelo questionário com base nas respostas fornecidas.";

}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Resultado do Perfil - INVESTEGRA</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4fdf7;
            color: #333;
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .cabecalho {
            text-align: center;
            margin-bottom: 30px;
        }

        .cabecalho h1 {
            color: #00a86b;
            margin-bottom: 10px;
        }

        .cabecalho p {
            color: #666;
            font-size: 16px;
        }

        /*
        |--------------------------------------------------------------------------
        | Card principal
        |--------------------------------------------------------------------------
        */

        .resultado-principal {
            background: white;
            border-radius: 15px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .resultado-principal h2 {
            color: #555;
            margin-bottom: 20px;
        }

        .perfil {
            display: inline-block;
            padding: 15px 35px;
            border-radius: 30px;
            background: #e8f8f1;
            color: #00a86b;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .score {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
        }

        .score strong {
            color: #00a86b;
            font-size: 22px;
        }

        .descricao-perfil {
            max-width: 750px;
            margin: 0 auto;
            line-height: 1.7;
            color: #666;
        }

        /*
        |--------------------------------------------------------------------------
        | Informações
        |--------------------------------------------------------------------------
        */

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .card h2 {
            color: #00a86b;
            margin-bottom: 25px;
        }

        .informacoes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .informacao {
            background: #f8faf9;
            padding: 18px;
            border-radius: 10px;
        }

        .informacao span {
            display: block;
            color: #777;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .informacao strong {
            color: #333;
            font-size: 16px;
        }

        /*
        |--------------------------------------------------------------------------
        | Análise da IA
        |--------------------------------------------------------------------------
        */

        .analise-ia {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            border-left: 5px solid #00a86b;
        }

        .analise-ia h2 {
            color: #00a86b;
            margin-bottom: 10px;
        }

        .subtitulo-ia {
            color: #777;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .analise-conteudo {
            color: #444;
            line-height: 1.8;
            font-size: 16px;
        }

        /*
        |--------------------------------------------------------------------------
        | Aviso
        |--------------------------------------------------------------------------
        */

        .aviso {
            background: #f8f8f8;
            border-radius: 10px;
            padding: 18px;
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | Botões
        |--------------------------------------------------------------------------
        */

        .botoes {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .botao {
            display: inline-block;
            padding: 13px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .botao-principal {
            background: #00a86b;
            color: white;
        }

        .botao-principal:hover {
            background: #008f5b;
        }

        .botao-secundario {
            background: #e9efec;
            color: #333;
        }

        .botao-secundario:hover {
            background: #dce5e0;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsividade
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .container {
                width: 94%;
                margin: 25px auto;
            }

            .resultado-principal,
            .card,
            .analise-ia {
                padding: 22px;
            }

            .informacoes {
                grid-template-columns: 1fr;
            }

            .perfil {
                font-size: 22px;
                padding: 13px 25px;
            }

        }

    </style>

</head>


<body>

<div class="container">


    <!-- CABEÇALHO -->

    <div class="cabecalho">

        <h1>Resultado do seu perfil</h1>

        <p>
            Olá, <?= $nome ?>! Veja abaixo o resultado do seu questionário.
        </p>

    </div>


    <!-- RESULTADO PRINCIPAL -->

    <section class="resultado-principal">

        <h2>Seu perfil de risco</h2>

        <div class="perfil">
            <?= $perfil ?>
        </div>

        <div class="score">

            Pontuação:

            <strong>
                <?= $score ?>
            </strong>

            pontos

        </div>

        <p class="descricao-perfil">

            <?= $descricao_perfil ?>

        </p>

    </section>


    <!-- ANÁLISE DA IA -->

    <?php if (!empty($analise_ia)): ?>

        <section class="analise-ia">

            <h2>🤖 Análise complementar</h2>

            <p class="subtitulo-ia">

                Uma análise educacional gerada pelo sistema com auxílio
                de inteligência artificial.

            </p>

            <div class="analise-conteudo">

                <?= nl2br(
                    htmlspecialchars(
                        $analise_ia,
                        ENT_QUOTES,
                        "UTF-8"
                    )
                ) ?>

            </div>

            <div class="aviso">

                <strong>Importante:</strong>

                Esta análise tem finalidade exclusivamente educacional.
                Ela não substitui uma análise financeira profissional,
                não constitui recomendação de compra ou venda e não
                garante resultados futuros.

            </div>

        </section>

    <?php else: ?>

        <section class="analise-ia">

            <h2>🤖 Análise complementar</h2>

            <p class="subtitulo-ia">

                A análise por inteligência artificial ainda não está
                disponível.

            </p>

            <div class="aviso">

                Seu perfil tradicional foi calculado e salvo normalmente.
                A análise complementar da IA poderá aparecer quando o
                serviço Python/Ollama estiver disponível.

            </div>

        </section>

    <?php endif; ?>


    <!-- INFORMAÇÕES UTILIZADAS -->

    <section class="card">

        <h2>Informações do perfil</h2>

        <div class="informacoes">


            <div class="informacao">

                <span>Idade</span>

                <strong>
                    <?= $idade ?> anos
                </strong>

            </div>


            <div class="informacao">

                <span>Experiência</span>

                <strong>
                    <?= $experiencia ?>
                </strong>

            </div>


            <div class="informacao">

                <span>Renda mensal aproximada</span>

                <strong>
                    R$ <?= $renda_formatada ?>
                </strong>

            </div>


            <div class="informacao">

                <span>Horizonte de investimento</span>

                <strong>
                    <?= $horizonte ?>
                </strong>

            </div>


            <div class="informacao">

                <span>Tolerância ao risco</span>

                <strong>
                    <?= $tolerancia ?>
                </strong>

            </div>


            <div class="informacao">

                <span>Objetivo</span>

                <strong>
                    <?= $objetivo ?>
                </strong>

            </div>


        </div>


        <?php if (!empty($descricao)): ?>

            <div class="informacao" style="margin-top: 18px;">

                <span>Sobre você</span>

                <strong>
                    <?= $descricao ?>
                </strong>

            </div>

        <?php endif; ?>


    </section>


    <!-- BOTÕES -->

    <div class="botoes">

        <a
            href="questionario_perfil.php"
            class="botao botao-secundario"
        >
            Refazer questionário
        </a>


        <a
            href="dashboard.php"
            class="botao botao-principal"
        >
            Ir para o dashboard
        </a>

    </div>


</div>

</body>

</html>