<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Simulações - INVESTEGRA
    </title>


    <link
        rel="stylesheet"
        href="css/simulacao.css?v=6"
    >

    <link
        rel="stylesheet"
        href="css/assistente.css"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >
    <script src="js/simulacao.js?v=5" defer></script>

</head>


<body>


<div class="layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">

        <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
            <div class="logo">
                INVESTEGRA
            </div>
        </a>


        <nav>

            <?php require BASE_PATH . "/app/views/partials/perfil_nav.php"; ?>

            <a href="carteiras.php">
                Carteiras
            </a>
            <a
                href="simulacao.php"
                class="ativo"
            >
                Simulações
            </a>

            <a href="analises.php">
                Análises
            </a>

            <a href="recomendacoes.php">
                Recomendações
            </a>


        </nav>

    </aside>


    <!-- =====================================================
         CONTEÚDO
    ====================================================== -->

    <main class="conteudo">


        <div class="cabecalho">

            <div>

                <h1>
                    Simulação de Investimentos
                </h1>

                <p>
                    Estime a evolução de um investimento
                    ao longo do tempo.
                </p>

            </div>


            <a
                href="historico_simulacoes.php"
                class="btn-historico"
            >
                Ver histórico
            </a>

        </div>


        <!-- =================================================
             FORMULÁRIO + INFORMAÇÕES
        ================================================== -->

        <section class="simulacao-container">


            <!-- FORMULÁRIO -->

            <div class="card-simulacao">

                <div class="card-titulo">

                    <h2>
                        Nova simulação
                    </h2>

                    <p>
                        Informe os parâmetros do investimento.
                    </p>

                </div>

                <div class="campo">
                    <label for="tipo_investimento">Tipo de investimento</label>
                    <select id="tipo_investimento">
                        <option value="compostos" <?= $tipoInvestimentoSelecionado === "compostos" ? "selected" : "" ?>>
                            Juros compostos
                        </option>
                        <option value="simples" <?= $tipoInvestimentoSelecionado === "simples" ? "selected" : "" ?>>
                            Juros simples
                        </option>
                        <option value="cdi" <?= $tipoInvestimentoSelecionado === "cdi" ? "selected" : "" ?>>
                            CDI
                        </option>
                        <option value="milhao" <?= $tipoInvestimentoSelecionado === "milhao" ? "selected" : "" ?>>
                            Meta do primeiro milhão
                        </option>
                        <option value="bola_neve" <?= $tipoInvestimentoSelecionado === "bola_neve" ? "selected" : "" ?>>
                            Bola de neve dos FIIs
                        </option>
                    </select>
                </div>

                <form
                    action="processar_simulacao.php"
                    method="POST"
                    id="formSimulacao"
                    <?= $tipoInvestimentoSelecionado === "bola_neve" ? "hidden" : "" ?>
                >

                    <input
                        type="hidden"
                        name="tipo_investimento"
                        id="tipo_investimento_post"
                        value="<?= htmlspecialchars($tipoInvestimentoSelecionado, ENT_QUOTES, "UTF-8") ?>"
                    >
                    <input
                        type="hidden"
                        name="tipo_juros"
                        id="tipo_juros"
                        value="<?= $tipoInvestimentoSelecionado === "simples" ? "simples" : "compostos" ?>"
                    >
                    <input
                        type="hidden"
                        name="usar_cdi"
                        id="usar_cdi"
                        value="<?= $tipoInvestimentoSelecionado === "cdi" ? "1" : "0" ?>"
                    >


                    <!-- APORTE INICIAL -->

                    <div class="campo">

                        <label for="aporte_inicial" id="rotulo_aporte_inicial">
                            Aporte inicial
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                R$
                            </span>

                            <input
                                type="number"
                                name="aporte_inicial"
                                id="aporte_inicial"
                                min="0"
                                step="0.01"
                                placeholder="1000.00"
                                required
                            >

                        </div>

                    </div>


                    <!-- APORTE MENSAL -->

                    <div class="campo" id="campo_aporte_mensal">

                        <label for="aporte_mensal">
                            Aporte mensal
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                R$
                            </span>

                            <input
                                type="number"
                                name="aporte_mensal"
                                id="aporte_mensal"
                                min="0"
                                step="0.01"
                                placeholder="200.00"
                                required
                            >

                        </div>

                    </div>

                    <!-- RENTABILIDADE -->

                    <div class="campo" id="campo_rentabilidade">

                        <label for="rentabilidade" id="rotulo_rentabilidade">
                            Rentabilidade anual esperada
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                %
                            </span>

                            <input
                                type="number"
                                name="rentabilidade"
                                id="rentabilidade"
                                min="0"
                                step="0.01"
                                placeholder="10"
                                required
                            >

                        </div>

                        <small class="campo-ajuda" id="ajuda_rentabilidade">
                            Taxa anual utilizada apenas
                            para fins de simulação.
                        </small>

                    </div>

                    <div id="campos_cdi" hidden>
                        <div class="campo">
                            <label for="taxa_cdi_anual">CDI anual de referência (%)</label>
                            <div class="campo-prefixo">
                                <span>%</span>
                                <input
                                    type="number"
                                    name="taxa_cdi_anual"
                                    id="taxa_cdi_anual"
                                    min="0"
                                    step="0.01"
                                    placeholder="Ex.: 14.90"
                                    disabled
                                >
                            </div>
                        </div>

                        <div class="campo">
                            <label for="percentual_cdi">Percentual do CDI</label>
                            <div class="campo-prefixo">
                                <span>%</span>
                                <input
                                    type="number"
                                    name="percentual_cdi"
                                    id="percentual_cdi"
                                    min="0"
                                    step="0.01"
                                    value="100"
                                    disabled
                                >
                            </div>
                            <small class="campo-ajuda">
                                Ex.: 100% do CDI ou 110% do CDI.
                            </small>
                        </div>

                        <div class="campo">
                            <label for="prazo_dias_cdi">Prazo do investimento (dias corridos)</label>
                            <input
                                type="number"
                                name="prazo_dias"
                                id="prazo_dias_cdi"
                                min="1"
                                max="36500"
                                step="1"
                                placeholder="Ex.: 365"
                                disabled
                            >
                        </div>

                        <p class="campo-ajuda">
                            Estimativa bruta diária com base de 252 dias úteis/ano. O resultado líquido
                            desconta IOF (até 30 dias) e IR regressivo para renda fixa tributável.
                            A taxa CDI informada é mantida constante na projeção.
                        </p>
                    </div>

                    <p class="campo-ajuda" id="ajuda_meta_milhao" hidden>
                        A projeção usa juros compostos e estima o prazo para atingir R$ 1.000.000,
                        considerando aportes mensais por até 100 anos.
                    </p>

                    <!-- PRAZO -->

                    <div class="campo" id="campo_prazo">

                        <label for="prazo_quantidade">
                            Prazo do investimento
                        </label>

                        <div class="campo-prazo">
                            <input
                                type="number"
                                name="prazo_quantidade"
                                id="prazo_quantidade"
                                min="1"
                                max="1200"
                                value="10"
                                required
                            >
                            <select name="prazo_unidade" id="prazo_unidade" aria-label="Unidade do prazo">
                                <option value="anos">Anos</option>
                                <option value="meses">Meses</option>
                            </select>
                        </div>

                    </div>


                    <button
                        type="submit"
                        class="btn-simular"
                        id="btn_simular"
                    >
                        Simular investimento
                    </button>

                </form>

            </div>


            <!-- INFORMAÇÕES -->

            <div class="card-informacao">

                <h2>
                    Como funciona?
                </h2>


                <p>
                    A simulação estima a evolução de um
                    investimento utilizando os parâmetros
                    informados pelo usuário.
                </p>


                <div class="info-item">

                    <strong>
                        Aporte inicial
                    </strong>

                    <span>
                        Valor aplicado no início da simulação.
                    </span>

                </div>


                <div class="info-item">

                    <strong>
                        Aporte mensal
                    </strong>

                    <span>
                        Valor acrescentado ao investimento
                        todos os meses.
                    </span>

                </div>


                <div class="info-item">

                    <strong>
                        Rentabilidade
                    </strong>

                    <span>
                        Taxa anual utilizada no cálculo.
                    </span>

                </div>


                <div class="info-item">

                    <strong>
                        Prazo
                    </strong>

                    <span>
                        Período utilizado na simulação.
                    </span>

                </div>


                <div class="aviso-simulacao">

                    <strong>
                        ⚠ Atenção
                    </strong>

                    <p>
                        Os resultados são estimativas matemáticas
                        e não representam garantia de rentabilidade
                        futura.
                    </p>

                </div>

            </div>

        </section>

        <section
            class="bola-neve-card"
            id="simulacao-fiis"
            <?= $tipoInvestimentoSelecionado === "bola_neve" ? "" : "hidden" ?>
        >
            <div class="card-titulo">
                <h2>Efeito bola de neve dos FIIs</h2>
                <p>Escolha um FII e informe seus dados para projetar o reinvestimento dos proventos.</p>
            </div>

            <form action="processar_bola_neve.php" method="POST" id="formBolaNeve">
                <input type="hidden" name="tipo_investimento" value="bola_neve">

                <div class="campo">
                    <label for="busca_fii">Nome ou ticker do FII</label>
                    <div class="campo-ativo">
                        <input
                            type="text"
                            id="busca_fii"
                            placeholder="Digite o ticker ou nome do FII..."
                            autocomplete="off"
                            maxlength="50"
                            required
                        >
                        <input type="hidden" name="ticker" id="ticker_fii">
                        <div id="resultados_fii" class="resultados-ativos"></div>
                    </div>
                    <p id="fii_selecionado" class="ativo-selecionado"></p>
                </div>

                <div class="campo">
                    <label for="quantidade_fii">Quantidade de cotas</label>
                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade_fii"
                        min="0"
                        step="0.01"
                        placeholder="Ex.: 10"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="preco_medio_fii">Cotação atual por cota (R$)</label>
                    <input
                        type="number"
                        name="preco_medio"
                        id="preco_medio_fii"
                        min="0.01"
                        step="0.01"
                        placeholder="Ex.: 100,00"
                        required
                    >
                    <small class="campo-ajuda">
                        A cotação atual e o DY mensal são preenchidos pela Brapi. Informe manualmente
                        a quantidade de cotas que deseja simular.
                    </small>
                </div>

                <div class="campo">
                    <label for="dy_mensal_fii">DY mensal estimado (%)</label>
                    <input
                        type="number"
                        name="dy_mensal"
                        id="dy_mensal_fii"
                        min="0"
                        step="0.01"
                        placeholder="Ex.: 0,8"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="prazo_bola_neve">Prazo da projeção</label>
                    <div class="campo-prazo">
                        <input
                            type="number"
                            name="prazo_quantidade"
                            id="prazo_bola_neve"
                            min="1"
                            max="1200"
                            value="10"
                            required
                        >
                        <select
                            name="prazo_unidade"
                            id="prazo_unidade_bola_neve"
                            aria-label="Unidade do prazo da projeção"
                        >
                            <option value="anos">Anos</option>
                            <option value="meses">Meses</option>
                        </select>
                    </div>
                </div>

                <p class="campo-ajuda">
                    Informe seus dados atuais de cotas e preço médio. A projeção mantém o preço e o DY
                    constantes e reinveste os proventos em cotas inteiras, sem novos aportes.
                </p>

                <button type="submit" class="btn-simular">
                    Simular reinvestimento
                </button>
            </form>
        </section>

        <?php if ($resultadoBolaNeve !== null): ?>
            <section class="resultado-container">
                <div class="card-resultado">
                    <div class="card-titulo">
                        <h2>Projeção do FII</h2>
                        <p>
                            FII <?= htmlspecialchars(
                                $resultadoBolaNeve["ticker"]
                                    ?? ($resultadoBolaNeve["fiis"][0]["ticker"] ?? ""),
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                            ·
                            <?php
                            $totalMesesPrazoFii = (int) (
                                $resultadoBolaNeve["prazo_meses"]
                                ?? ((int) $resultadoBolaNeve["prazo_anos"] * 12)
                            );
                            $anosPrazoFii = intdiv($totalMesesPrazoFii, 12);
                            $mesesPrazoFii = $totalMesesPrazoFii % 12;
                            if ($anosPrazoFii > 0) {
                                echo $anosPrazoFii . ($anosPrazoFii === 1 ? " ano" : " anos");
                            }
                            if ($mesesPrazoFii > 0) {
                                echo ($anosPrazoFii > 0 ? " e " : "")
                                    . $mesesPrazoFii
                                    . ($mesesPrazoFii === 1 ? " mês" : " meses");
                            }
                            ?>
                        </p>
                    </div>

                    <div class="resultado-grid">
                        <div class="resultado-item">
                            <span>Valor inicial</span>
                            <strong>R$ <?= number_format($resultadoBolaNeve["total_inicial"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>Valor projetado</span>
                            <strong>R$ <?= number_format($resultadoBolaNeve["total_final"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>Proventos no período</span>
                            <strong>R$ <?= number_format($resultadoBolaNeve["total_proventos"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>Cotas reinvestidas</span>
                            <strong><?= number_format($resultadoBolaNeve["total_cotas_reinvestidas"], 0, ",", ".") ?></strong>
                        </div>
                    </div>

                    <div class="tabela-bola-neve-wrapper">
                        <table class="tabela-bola-neve">
                            <thead>
                                <tr>
                                    <th>FII</th>
                                    <th>Cotas finais</th>
                                    <th>Renda mensal final</th>
                                    <th>Saldo de proventos</th>
                                    <th>Valor projetado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultadoBolaNeve["fiis"] as $fii): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fii["ticker"], ENT_QUOTES, "UTF-8") ?></td>
                                        <td><?= number_format($fii["quantidade_final"], 0, ",", ".") ?></td>
                                        <td>R$ <?= number_format($fii["renda_mensal_final"], 2, ",", ".") ?></td>
                                        <td>R$ <?= number_format($fii["saldo_proventos"], 2, ",", ".") ?></td>
                                        <td>R$ <?= number_format($fii["valor_final"], 2, ",", ".") ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($resultadoCdi !== null): ?>
            <section class="resultado-container">
                <div class="card-resultado">
                    <div class="card-titulo">
                        <h2>Simulação CDI líquida</h2>
                        <p>
                            <?= number_format($resultadoCdi["percentual_cdi"], 2, ",", ".") ?>% do CDI
                            · CDI anual de referência:
                            <?= number_format($resultadoCdi["taxa_cdi_anual"], 2, ",", ".") ?>%
                            · <?= (int) $resultadoCdi["prazo_dias"] ?> dias corridos
                            (<?= (int) $resultadoCdi["dias_uteis_estimados"] ?> dias úteis estimados)
                        </p>
                    </div>

                    <div class="resultado-principal">
                        <span>Valor líquido estimado no resgate</span>
                        <strong>
                            R$ <?= number_format($resultadoCdi["valor_liquido"], 2, ",", ".") ?>
                        </strong>
                    </div>

                    <div class="resultado-grid">
                        <div class="resultado-item">
                            <span>Valor aplicado</span>
                            <strong>R$ <?= number_format($resultadoCdi["aporte_inicial"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>Valor bruto</span>
                            <strong>R$ <?= number_format($resultadoCdi["valor_bruto"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>IOF (<?= number_format($resultadoCdi["aliquota_iof"], 0, ",", ".") ?>%)</span>
                            <strong>R$ <?= number_format($resultadoCdi["iof"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>IR (<?= number_format($resultadoCdi["aliquota_ir"], 1, ",", ".") ?>%)</span>
                            <strong>R$ <?= number_format($resultadoCdi["ir"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>Rendimento líquido</span>
                            <strong>R$ <?= number_format($resultadoCdi["rendimento_liquido"], 2, ",", ".") ?></strong>
                        </div>
                        <div class="resultado-item">
                            <span>Retorno líquido</span>
                            <strong><?= number_format($resultadoCdi["rentabilidade_liquida"], 2, ",", ".") ?>%</strong>
                        </div>
                    </div>

                    <p class="campo-ajuda">
                        Estimativa para renda fixa tributável, sem taxas da instituição. A taxa CDI é mantida
                        constante e o número de dias úteis é aproximado pela convenção de 252 dias por ano.
                    </p>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($resultadoMetaMilhao !== null): ?>
            <section class="resultado-container">
                <div class="card-resultado">
                    <div class="card-titulo">
                        <h2>Meta do primeiro milhão</h2>
                        <p>Estimativa com juros compostos e os aportes informados.</p>
                    </div>

                    <div class="resultado-principal">
                        <span>Prazo estimado para atingir R$ 1.000.000</span>
                        <strong>
                            <?php
                            $mesesMeta = $resultadoMetaMilhao["meses_para_meta"];

                            if ($mesesMeta === null) {
                                echo "Não atingida em até 100 anos";
                            } elseif ($mesesMeta === 0) {
                                echo "Meta já atingida";
                            } else {
                                $anosMeta = intdiv($mesesMeta, 12);
                                $mesesRestantesMeta = $mesesMeta % 12;

                                if ($anosMeta > 0) {
                                    echo $anosMeta
                                        . ($anosMeta === 1 ? " ano" : " anos");
                                }

                                if ($mesesRestantesMeta > 0) {
                                    echo ($anosMeta > 0 ? " e " : "")
                                        . $mesesRestantesMeta
                                        . ($mesesRestantesMeta === 1 ? " mês" : " meses");
                                }
                            }
                            ?>
                        </strong>
                    </div>

                    <div class="resultado-detalhes">
                        <p>
                            <strong>Aporte inicial:</strong>
                            R$ <?= number_format($resultadoMetaMilhao["aporte_inicial"], 2, ",", ".") ?>
                        </p>
                        <p>
                            <strong>Aporte mensal:</strong>
                            R$ <?= number_format($resultadoMetaMilhao["aporte_mensal"], 2, ",", ".") ?>
                        </p>
                        <p>
                            <strong>Rentabilidade anual:</strong>
                            <?= number_format($resultadoMetaMilhao["rentabilidade"], 2, ",", ".") ?>%
                        </p>
                    </div>
                </div>
            </section>
        <?php endif; ?>


        <!-- =================================================
             RESULTADO
        ================================================== -->

        <?php if ($resultado !== null): ?>


        <section class="resultado-container">


            <div class="card-resultado">


                <div class="card-titulo">

                    <h2>
                        Resultado da simulação
                    </h2>

                    <p>

                        <?php

                        if (!empty($resultado["usar_cdi"])) {
                            echo "CDI informado · juros compostos";
                        } else {
                            echo $resultado["tipo_juros"] === "compostos"
                                ? "Juros compostos"
                                : "Juros simples";
                        }

                        ?>

                    </p>

                </div>


                <!-- VALOR PRINCIPAL -->

                <div class="resultado-principal">

                    <span>
                        Montante final estimado
                    </span>

                    <strong>

                        R$

                        <?php

                        echo number_format(
                            $resultado["montante"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                    </strong>

                </div>


                <!-- INDICADORES -->

                <div class="resultado-grid">


                    <div class="resultado-item">

                        <span>
                            Total aportado
                        </span>

                        <strong>

                            R$

                            <?php

                            echo number_format(
                                $resultado["total_aportado"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="resultado-item">

                        <span>
                            Rendimento estimado
                        </span>

                        <strong>

                            R$

                            <?php

                            echo number_format(
                                $resultado["rendimento"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="resultado-item">

                        <span>
                            Rentabilidade total
                        </span>

                        <strong>

                            <?php

                            echo number_format(
                                $resultado["rentabilidade_resultado"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                            %

                        </strong>

                    </div>


                    <div class="resultado-item">

                        <span>
                            Prazo
                        </span>

                        <strong>

                            <?php
                            $totalMesesPrazo = (int) (
                                $resultado["prazo_meses"]
                                ?? ((int) $resultado["prazo_anos"] * 12)
                            );
                            $anosPrazo = intdiv($totalMesesPrazo, 12);
                            $mesesPrazo = $totalMesesPrazo % 12;
                            if ($anosPrazo > 0) {
                                echo $anosPrazo . ($anosPrazo === 1 ? " ano" : " anos");
                            }
                            if ($mesesPrazo > 0) {
                                echo ($anosPrazo > 0 ? " e " : "")
                                    . $mesesPrazo
                                    . ($mesesPrazo === 1 ? " mês" : " meses");
                            }
                            ?>

                        </strong>

                    </div>

                    <div class="resultado-item resultado-meta-milhao">
                        <span>Meta do primeiro milhão</span>
                        <strong>
                            <?php
                            $mesesParaMilhao = $resultado["meses_para_milhao"] ?? null;

                            if ($mesesParaMilhao === null) {
                                echo "Não atingida em até 100 anos";
                            } elseif ($mesesParaMilhao === 0) {
                                echo "Já atingida no início";
                            } else {
                                $anosParaMilhao = intdiv($mesesParaMilhao, 12);
                                $mesesRestantes = $mesesParaMilhao % 12;

                                if ($anosParaMilhao > 0) {
                                    echo $anosParaMilhao
                                        . ($anosParaMilhao === 1 ? " ano" : " anos");
                                }

                                if ($mesesRestantes > 0) {
                                    echo ($anosParaMilhao > 0 ? " e " : "")
                                        . $mesesRestantes
                                        . ($mesesRestantes === 1 ? " mês" : " meses");
                                }
                            }
                            ?>
                        </strong>
                    </div>

                </div>


                <!-- DETALHES -->

                <div class="resultado-detalhes">

                    <p>

                        <strong>
                            Aporte inicial:
                        </strong>

                        R$

                        <?php

                        echo number_format(
                            $resultado["aporte_inicial"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                    </p>


                    <p>

                        <strong>
                            Aporte mensal:
                        </strong>

                        R$

                        <?php

                        echo number_format(
                            $resultado["aporte_mensal"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                    </p>


                    <p>

                        <strong>
                            <?= !empty($resultado["usar_cdi"])
                                ? "Taxa CDI anual informada:"
                                : "Rentabilidade considerada:" ?>
                        </strong>

                        <?php

                        echo number_format(
                            $resultado["rentabilidade_anual"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                        % ao ano

                    </p>

                </div>

            </div>


            <!-- =================================================
                 GRÁFICO
            ================================================== -->

            <div class="card-grafico">

                <div class="grafico-cabecalho">

                    <div>

                        <h2>
                            Evolução estimada
                        </h2>

                        <p>
                            Crescimento do patrimônio ao longo
                            do período simulado.
                        </p>

                    </div>

                </div>


                <div class="grafico-wrapper">

                    <svg
                        class="grafico-svg"
                        viewBox="0 0 900 380"
                        preserveAspectRatio="none"
                    >

                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | CONFIGURAÇÕES DO GRÁFICO
                        |--------------------------------------------------------------------------
                        */

                        $largura = 900;

                        $altura = 380;

                        $margemEsquerda = 75;

                        $margemDireita = 25;

                        $margemSuperior = 30;

                        $margemInferior = 55;


                        $areaLargura =
                            $largura
                            - $margemEsquerda
                            - $margemDireita;


                        $areaAltura =
                            $altura
                            - $margemSuperior
                            - $margemInferior;


                        $maiorValor = 0;


                        foreach (
                            $dadosGrafico
                            as $ponto
                        ) {

                            if (
                                $ponto["valor"]
                                > $maiorValor
                            ) {

                                $maiorValor =
                                    $ponto["valor"];

                            }

                        }


                        if ($maiorValor <= 0) {
                            $maiorValor = 1;
                        }


                        /*
                        | Arredondamento do eixo Y
                        */

                        $escala =
                            pow(
                                10,
                                floor(
                                    log10($maiorValor)
                                )
                            );


                        $maximoEixo =
                            ceil(
                                $maiorValor / $escala
                            ) * $escala;


                        if (
                            $maximoEixo <= $maiorValor
                        ) {

                            $maximoEixo *= 1.1;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | LINHAS HORIZONTAIS
                        |--------------------------------------------------------------------------
                        */

                        for ($i = 0; $i <= 4; $i++) {

                            $valorEixo =
                                $maximoEixo
                                * ($i / 4);


                            $y =
                                $margemSuperior
                                +
                                $areaAltura
                                -
                                (
                                    $areaAltura
                                    * ($i / 4)
                                );

                            ?>

                            <line
                                x1="<?php echo $margemEsquerda; ?>"
                                y1="<?php echo $y; ?>"
                                x2="<?php echo $largura - $margemDireita; ?>"
                                y2="<?php echo $y; ?>"
                                class="linha-grade"
                            />

                            <text
                                x="<?php echo $margemEsquerda - 10; ?>"
                                y="<?php echo $y + 4; ?>"
                                text-anchor="end"
                                class="texto-eixo"
                            >
                                R$ <?php
                                echo number_format(
                                    $valorEixo,
                                    0,
                                    ",",
                                    "."
                                );
                                ?>
                            </text>

                            <?php
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | LINHA DO GRÁFICO
                        |--------------------------------------------------------------------------
                        */

                        $pontos = [];

                        $quantidadePontos =
                            count($dadosGrafico);


                        foreach (
                            $dadosGrafico
                            as $indice => $ponto
                        ) {

                            if (
                                $quantidadePontos > 1
                            ) {

                                $x =
                                    $margemEsquerda
                                    +
                                    (
                                        $areaLargura
                                        *
                                        (
                                            $indice /
                                            ($quantidadePontos - 1)
                                        )
                                    );

                            } else {

                                $x =
                                    $margemEsquerda;

                            }


                            $y =
                                $margemSuperior
                                +
                                $areaAltura
                                -
                                (
                                    $areaAltura
                                    *
                                    (
                                        $ponto["valor"]
                                        /
                                        $maximoEixo
                                    )
                                );


                            $pontos[] =
                                $x . "," . $y;

                        }


                        $polyline =
                            implode(
                                " ",
                                $pontos
                            );

                        ?>


                        <polyline
                            points="<?php echo $polyline; ?>"
                            class="linha-grafico"
                        />


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | PONTOS
                        |--------------------------------------------------------------------------
                        */

                        foreach (
                            $dadosGrafico
                            as $indice => $ponto
                        ) {

                            if (
                                $quantidadePontos > 1
                            ) {

                                $x =
                                    $margemEsquerda
                                    +
                                    (
                                        $areaLargura
                                        *
                                        (
                                            $indice /
                                            ($quantidadePontos - 1)
                                        )
                                    );

                            } else {

                                $x =
                                    $margemEsquerda;

                            }


                            $y =
                                $margemSuperior
                                +
                                $areaAltura
                                -
                                (
                                    $areaAltura
                                    *
                                    (
                                        $ponto["valor"]
                                        /
                                        $maximoEixo
                                    )
                                );


                            /*
                            | Mostra apenas alguns
                            | rótulos para não poluir.
                            */

                            $mostrarRotulo =
                                $indice === 0
                                ||
                                $indice ===
                                $quantidadePontos - 1
                                ||
                                (
                                    $ponto["mes"] <= 24
                                    &&
                                    $ponto["mes"] % 6 === 0
                                )
                                ||
                                (
                                    $ponto["mes"] > 24
                                    &&
                                    $ponto["mes"] % 12 === 0
                                );

                            ?>

                            <circle
                                cx="<?php echo $x; ?>"
                                cy="<?php echo $y; ?>"
                                r="4"
                                class="ponto-grafico"
                            />

                            <?php

                            if ($mostrarRotulo) {

                                ?>

                                <text
                                    x="<?php echo $x; ?>"
                                    y="<?php echo $altura - 20; ?>"
                                    text-anchor="middle"
                                    class="texto-eixo"
                                >

                                    <?php

                                    if (
                                        $ponto["mes"] == 0
                                    ) {

                                        echo "Início";

                                    } elseif (
                                        $ponto["mes"] < 12
                                    ) {

                                        echo
                                            $ponto["mes"]
                                            . "m";

                                    } else {

                                        echo
                                            number_format(
                                                $ponto["mes"] / 12,
                                                0
                                            )
                                            . " ano"
                                            .
                                            (
                                                $ponto["mes"] / 12 != 1
                                                ? "s"
                                                : ""
                                            );

                                    }

                                    ?>

                                </text>

                                <?php

                            }

                        }

                        ?>

                    </svg>

                </div>


                <div class="legenda-grafico">

                    <span>
                        ● Evolução estimada
                    </span>

                    <span>
                    <?= !empty($resultado["usar_cdi"])
                        ? "CDI informado"
                        : ($resultado["tipo_juros"] === "compostos"
                            ? "Juros compostos"
                            : "Juros simples") ?>
                    </span>

                </div>

            </div>


        </section>


        <?php endif; ?>

        <?php if (!empty($resultadosDaSessao)): ?>
            <section class="resultado-container">
                <div class="card-resultado">
                    <div class="card-titulo">
                        <h2>Simulações feitas nesta sessão</h2>
                        <p>Os resultados anteriores continuam disponíveis ao trocar o tipo de simulação.</p>
                    </div>
                    <div class="resultados-sessao">
                        <?php foreach (array_reverse($resultadosDaSessao) as $resultadoSessao): ?>
                            <div class="resultado-sessao-item">
                                <strong>
                                    <?= htmlspecialchars($resultadoSessao["titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>
                                </strong>
                                <span>
                                    <?= htmlspecialchars($resultadoSessao["resumo"] ?? "", ENT_QUOTES, "UTF-8") ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>


        <!-- =================================================
             ASSISTENTE
        ================================================== -->



    </main>

</div>


</body>

</html>