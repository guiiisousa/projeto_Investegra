<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Análise da Carteira - INVESTEGRA</title>

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
        }

        .pagina {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 0;
        }

        .cabecalho {
            margin-bottom: 30px;
        }

        .cabecalho h1 {
            color: #00a86b;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .cabecalho p {
            color: #777;
            font-size: 15px;
        }

        .resumo {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .card span {
            display: block;
            color: #777;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .card strong {
            color: #00a86b;
            font-size: 22px;
        }

        .informacoes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .secao {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .secao h2 {
            color: #333;
            font-size: 19px;
            margin-bottom: 18px;
        }

        .linha {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .linha:last-child {
            border-bottom: none;
        }

        .linha span {
            color: #777;
        }

        .linha strong {
            color: #333;
        }

        .distribuicao-item {
            margin-bottom: 14px;
        }

        .distribuicao-topo {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .barra {
            width: 100%;
            height: 8px;
            background: #e8eee9;
            border-radius: 10px;
            overflow: hidden;
        }

        .barra-preenchida {
            height: 100%;
            background: #00a86b;
            border-radius: 10px;
        }

        .analise {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .titulo-analise {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .icone-ia {
            width: 45px;
            height: 45px;
            background: #e8f8f1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .titulo-analise h2 {
            color: #00a86b;
            font-size: 21px;
        }

        .texto-analise {
            color: #444;
            line-height: 1.7;
            font-size: 15px;
        }

        .texto-analise strong {
            color: #333;
        }

        .alertas {
            background: #fffaf0;
            border-left: 4px solid #e0a800;
        }

        .alertas h2 {
            color: #9a7200;
        }

        .alertas ul {
            padding-left: 20px;
            color: #666;
            line-height: 1.8;
        }

        .acoes {
            display: flex;
            gap: 10px;
        }

        .botao {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s;
        }

        .principal {
            background: #00a86b;
            color: white;
        }

        .principal:hover {
            background: #008f5b;
            transform: translateY(-2px);
        }

        .secundario {
            background: #edf2ef;
            color: #444;
        }

        .secundario:hover {
            background: #dfe8e3;
        }

        @media (max-width: 850px) {

            .resumo {
                grid-template-columns: repeat(2, 1fr);
            }

            .informacoes {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 550px) {

            .pagina {
                width: 94%;
                padding: 25px 0;
            }

            .resumo {
                grid-template-columns: 1fr;
            }

            .cabecalho h1 {
                font-size: 27px;
            }

            .analise,
            .secao {
                padding: 20px;
            }

            .acoes {
                flex-direction: column;
            }

            .botao {
                text-align: center;
            }

        }

    </style>

</head>

<body>

<div class="pagina">

    <header class="cabecalho">

        <h1>Análise da Carteira</h1>

        <p>
            Uma análise automatizada baseada nos dados da sua carteira
            e no seu perfil de investidor.
        </p>

    </header>


    <!-- RESUMO -->

    <section class="resumo">

        <div class="card">

            <span>Perfil de risco</span>

            <strong>
                <?= $perfil ?>
            </strong>

        </div>


        <div class="card">

            <span>Patrimônio</span>

            <strong>
                R$
                <?= number_format($patrimonio, 2, ",", ".") ?>
            </strong>

        </div>


        <div class="card">

            <span>Ativos</span>

            <strong>
                <?= $quantidade_ativos ?>
            </strong>

        </div>


        <div class="card">

            <span>Score de risco</span>

            <strong>
                <?= (int) ($usuario["score_risco"] ?? 0) ?>/14
            </strong>

        </div>

    </section>


    <!-- INFORMAÇÕES -->

    <section class="informacoes">


        <!-- PERFIL -->

        <div class="secao">

            <h2>Perfil do investidor</h2>

            <div class="linha">
                <span>Perfil</span>
                <strong><?= $perfil ?></strong>
            </div>

            <div class="linha">
                <span>Experiência</span>
                <strong><?= $experiencia ?></strong>
            </div>

            <div class="linha">
                <span>Horizonte</span>
                <strong><?= $horizonte ?></strong>
            </div>

            <div class="linha">
                <span>Objetivo</span>
                <strong><?= $objetivo ?></strong>
            </div>

        </div>


        <!-- DISTRIBUIÇÃO -->

        <div class="secao">

            <h2>Distribuição da carteira</h2>

            <?php if (empty($distribuicao)): ?>

                <p style="color: #777;">
                    Nenhum ativo cadastrado.
                </p>

            <?php else: ?>

                <?php foreach ($distribuicao as $categoria => $percentual): ?>

                    <div class="distribuicao-item">

                        <div class="distribuicao-topo">

                            <span>
                                <?= htmlspecialchars(
                                    $categoria,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </span>

                            <strong>
                                <?= number_format(
                                    $percentual,
                                    2,
                                    ",",
                                    "."
                                ) ?>%
                            </strong>

                        </div>

                        <div class="barra">

                            <div
                                class="barra-preenchida"
                                style="width: <?= min($percentual, 100) ?>%;"
                            ></div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>


    <!-- ANÁLISE DA IA -->

    <section class="analise">

        <div class="titulo-analise">

            <div class="icone-ia">
                🤖
            </div>

            <h2>Análise inteligente</h2>

        </div>


        <div class="texto-analise">

            <?= nl2br(
                htmlspecialchars(
                    $analise,
                    ENT_QUOTES,
                    "UTF-8"
                )
            ) ?>

        </div>

    </section>


    <!-- ALERTAS -->

    <?php if (!empty($alertas)): ?>

        <section class="secao alertas">

            <h2>⚠ Pontos identificados pelo sistema</h2>

            <ul>

                <?php foreach ($alertas as $alerta): ?>

                    <li>
                        <?= htmlspecialchars(
                            $alerta,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </section>

        <br>

    <?php endif; ?>


    <!-- AVISO -->

    <section class="secao" style="margin-bottom: 25px;">

        <p style="
            color: #777;
            font-size: 13px;
            line-height: 1.6;
        ">
            As informações apresentadas possuem caráter educacional e
            informativo. A análise automatizada não constitui recomendação
            financeira profissional nem garante resultados futuros.
        </p>

    </section>


    <!-- BOTÕES -->

    <div class="acoes">

        <a
            href="dashboard.php"
            class="botao principal"
        >
            Voltar ao Dashboard
        </a>

        <a
            href="carteiras.php"
            class="botao secundario"
        >
            Minhas Carteiras
        </a>

    </div>

</div>

</body>

</html>