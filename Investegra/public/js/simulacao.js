document.addEventListener("DOMContentLoaded", function () {
    const tipoInvestimento = document.getElementById("tipo_investimento");
    const formSimulacao = document.getElementById("formSimulacao");
    const formBolaNeve = document.getElementById("formBolaNeve");
    const secaoBolaNeve = document.getElementById("simulacao-fiis");
    const tipoInvestimentoPost = document.getElementById("tipo_investimento_post");
    const tipoJuros = document.getElementById("tipo_juros");
    const usarCdi = document.getElementById("usar_cdi");
    const campoAporteMensal = document.getElementById("campo_aporte_mensal");
    const aporteMensal = document.getElementById("aporte_mensal");
    const campoRentabilidade = document.getElementById("campo_rentabilidade");
    const rentabilidade = document.getElementById("rentabilidade");
    const campoPrazo = document.getElementById("campo_prazo");
    const prazoQuantidade = document.getElementById("prazo_quantidade");
    const prazoUnidade = document.getElementById("prazo_unidade");
    const prazoBolaNeve = document.getElementById("prazo_bola_neve");
    const prazoUnidadeBolaNeve = document.getElementById("prazo_unidade_bola_neve");
    const camposCdi = document.getElementById("campos_cdi");
    const taxaCdiAnual = document.getElementById("taxa_cdi_anual");
    const percentualCdi = document.getElementById("percentual_cdi");
    const prazoDiasCdi = document.getElementById("prazo_dias_cdi");
    const rotuloAporteInicial = document.getElementById("rotulo_aporte_inicial");
    const taxaLabel = document.getElementById("rotulo_rentabilidade");
    const taxaAjuda = document.getElementById("ajuda_rentabilidade");
    const ajudaMetaMilhao = document.getElementById("ajuda_meta_milhao");
    const botaoSimular = document.getElementById("btn_simular");
    const buscaFii = document.getElementById("busca_fii");
    const tickerFii = document.getElementById("ticker_fii");
    const resultadosFii = document.getElementById("resultados_fii");
    const fiiSelecionado = document.getElementById("fii_selecionado");
    const formBolaNeveFii = document.getElementById("formBolaNeve");
    const precoFii = document.getElementById("preco_medio_fii");
    const dyFii = document.getElementById("dy_mensal_fii");

    const configurarLimitePrazo = function (quantidade, unidade) {
        if (!quantidade || !unidade) {
            return;
        }

        const atualizarLimite = function () {
            const emMeses = unidade.value === "meses";
            quantidade.max = emMeses ? "1200" : "100";
            quantidade.placeholder = emMeses ? "Ex.: 6" : "Ex.: 10";
        };

        unidade.addEventListener("change", atualizarLimite);
        atualizarLimite();
    };

    configurarLimitePrazo(prazoQuantidade, prazoUnidade);
    configurarLimitePrazo(prazoBolaNeve, prazoUnidadeBolaNeve);

    if (tipoInvestimento) {
        const atualizarTipoInvestimento = function () {
            const tipo = tipoInvestimento.value;
            const ehBolaNeve = tipo === "bola_neve";
            const ehMilhao = tipo === "milhao";
            const ehCdi = tipo === "cdi";

            if (formSimulacao) {
                formSimulacao.hidden = ehBolaNeve;
            }

            if (formBolaNeve) {
                formBolaNeve.hidden = !ehBolaNeve;
            }

            if (secaoBolaNeve) {
                secaoBolaNeve.hidden = !ehBolaNeve;
            }

            if (tipoInvestimentoPost) {
                tipoInvestimentoPost.value = tipo;
            }

            if (tipoJuros) {
                tipoJuros.value = tipo === "simples" ? "simples" : "compostos";
            }

            if (usarCdi) {
                usarCdi.value = ehCdi ? "1" : "0";
            }

            if (campoAporteMensal) {
                campoAporteMensal.hidden = ehCdi;
            }

            if (aporteMensal) {
                aporteMensal.disabled = ehCdi;
                aporteMensal.required = !ehCdi;
            }

            if (campoRentabilidade) {
                campoRentabilidade.hidden = ehCdi;
            }

            if (rentabilidade) {
                rentabilidade.disabled = ehCdi;
                rentabilidade.required = !ehCdi;
            }

            if (campoPrazo) {
                campoPrazo.hidden = ehMilhao || ehCdi;
            }

            if (prazoQuantidade) {
                prazoQuantidade.disabled = ehMilhao || ehCdi;
                prazoQuantidade.required = !ehMilhao && !ehCdi;
            }

            if (prazoUnidade) {
                prazoUnidade.disabled = ehMilhao || ehCdi;
                prazoUnidade.required = !ehMilhao && !ehCdi;
            }

            if (camposCdi) {
                camposCdi.hidden = !ehCdi;
            }

            [taxaCdiAnual, percentualCdi, prazoDiasCdi].forEach((campo) => {
                if (campo) {
                    campo.disabled = !ehCdi;
                    campo.required = ehCdi;
                }
            });

            if (rotuloAporteInicial) {
                rotuloAporteInicial.textContent = ehCdi
                    ? "Valor aplicado"
                    : "Aporte inicial";
            }

            if (taxaLabel && taxaAjuda) {
                if (ehMilhao) {
                    taxaLabel.textContent = "Rentabilidade anual estimada";
                    taxaAjuda.textContent = "A estimativa considera juros compostos.";
                } else {
                    taxaLabel.textContent = "Rentabilidade anual esperada";
                    taxaAjuda.textContent = "Taxa anual utilizada apenas para fins de simulação.";
                }
            }

            if (ajudaMetaMilhao) {
                ajudaMetaMilhao.hidden = !ehMilhao;
            }

            if (botaoSimular) {
                if (ehMilhao) {
                    botaoSimular.textContent = "Calcular prazo até R$ 1 milhão";
                } else if (ehCdi) {
                    botaoSimular.textContent = "Calcular CDI líquido";
                } else {
                    botaoSimular.textContent = "Simular investimento";
                }
            }
        };

        tipoInvestimento.addEventListener("change", atualizarTipoInvestimento);
        atualizarTipoInvestimento();
    }

    if (
        buscaFii
        && tickerFii
        && resultadosFii
        && fiiSelecionado
        && formBolaNeveFii
        && precoFii
        && dyFii
    ) {
        let temporizadorBusca = null;
        let idBusca = 0;
        let idConsultaDados = 0;

        const mostrarMensagemBusca = function (mensagem, classe) {
            resultadosFii.replaceChildren();
            const item = document.createElement("div");
            item.className = classe;
            item.textContent = mensagem;
            resultadosFii.appendChild(item);
            resultadosFii.classList.add("visivel");
        };

        buscaFii.addEventListener("input", function () {
            const termo = buscaFii.value.trim();
            tickerFii.value = "";
            idConsultaDados += 1;
            precoFii.value = "";
            dyFii.value = "";
            fiiSelecionado.textContent = "";
            fiiSelecionado.classList.remove("mostrar");
            buscaFii.setCustomValidity("");
            clearTimeout(temporizadorBusca);
            const buscaAtual = ++idBusca;

            if (termo.length < 2) {
                resultadosFii.replaceChildren();
                resultadosFii.classList.remove("visivel");
                return;
            }

            temporizadorBusca = setTimeout(async function () {
                mostrarMensagemBusca("Buscando FIIs...", "carregando-ativos");

                try {
                    const resposta = await fetch(
                        "api/buscar_ativos.php?termo=" + encodeURIComponent(termo)
                    );
                    if (!resposta.ok) {
                        throw new Error("A busca de ativos retornou um erro.");
                    }

                    const dados = await resposta.json();
                    if (buscaAtual !== idBusca) {
                        return;
                    }
                    if (!dados.sucesso) {
                        throw new Error(dados.mensagem || "Não foi possível buscar FIIs.");
                    }

                    const fiis = (dados.resultados || []).filter(function (ativo) {
                        return ativo.categoria === "FIIs";
                    });

                    if (fiis.length === 0) {
                        mostrarMensagemBusca("Nenhum FII encontrado para essa busca.", "resultado-vazio");
                        return;
                    }

                    resultadosFii.replaceChildren();
                    fiis.forEach(function (fii) {
                        const item = document.createElement("button");
                        item.type = "button";
                        item.className = "resultado-ativo";

                        const ticker = document.createElement("strong");
                        ticker.textContent = fii.ticker;
                        const nome = document.createElement("span");
                        nome.textContent = fii.nome;
                        item.append(ticker, nome);

                        item.addEventListener("click", function () {
                            const consultaAtual = ++idConsultaDados;
                            tickerFii.value = fii.ticker;
                            buscaFii.value = fii.ticker + " — " + fii.nome;
                            fiiSelecionado.textContent = "Buscando cotação e dividend yield...";
                            fiiSelecionado.classList.add("mostrar");
                            fiiSelecionado.classList.remove("erro");
                            resultadosFii.replaceChildren();
                            resultadosFii.classList.remove("visivel");

                            precoFii.value = "";
                            dyFii.value = "";

                            (async function () {
                                try {
                                    const resposta = await fetch(
                                        "api/dados_fii.php?ticker="
                                            + encodeURIComponent(fii.ticker)
                                    );
                                    const dadosAtivo = await resposta.json();
                                    if (consultaAtual !== idConsultaDados) {
                                        return;
                                    }
                                    if (!resposta.ok || !dadosAtivo.sucesso) {
                                        throw new Error(
                                            dadosAtivo.mensagem
                                                || "Não foi possível carregar os dados deste FII."
                                        );
                                    }
                                    if (!dadosAtivo.ativo || typeof dadosAtivo.ativo !== "object") {
                                        throw new Error("A Brapi retornou dados incompletos para este FII.");
                                    }

                                    const cotacao = Number(dadosAtivo.ativo.preco_atual);
                                    const dyMensal = Number(dadosAtivo.ativo.dy_mensal);
                                    if (
                                        !Number.isFinite(cotacao)
                                        || !Number.isFinite(dyMensal)
                                        || cotacao <= 0
                                        || dyMensal < 0
                                    ) {
                                        throw new Error("A Brapi retornou valores inválidos para este FII.");
                                    }

                                    precoFii.value = cotacao.toFixed(2);
                                    dyFii.value = dyMensal.toFixed(2);
                                    const referencia = dadosAtivo.ativo.data_referencia_dy
                                        ? " · DY referente a " + dadosAtivo.ativo.data_referencia_dy
                                        : "";
                                    fiiSelecionado.textContent =
                                        "FII selecionado: " + fii.ticker
                                        + " · Cotação e DY preenchidos pela Brapi"
                                        + referencia;
                                    fiiSelecionado.classList.remove("erro");
                                } catch (erro) {
                                    console.error("Erro ao carregar dados do FII pela Brapi.", erro);
                                    if (consultaAtual === idConsultaDados) {
                                        precoFii.value = "";
                                        dyFii.value = "";
                                        fiiSelecionado.textContent =
                                            erro instanceof Error
                                                ? erro.message
                                                : "Não foi possível carregar os dados deste FII.";
                                        fiiSelecionado.classList.add("erro");
                                    }
                                }
                            })();
                        });

                        resultadosFii.appendChild(item);
                    });
                    resultadosFii.classList.add("visivel");
                } catch (erro) {
                    console.error("Erro ao buscar FIIs para a simulação.", erro);
                    if (buscaAtual === idBusca) {
                        mostrarMensagemBusca(
                            "Não foi possível buscar FIIs. Tente novamente.",
                            "resultado-erro"
                        );
                    }
                }
            }, 350);
        });

        formBolaNeveFii.addEventListener("submit", function (evento) {
            if (!tickerFii.value) {
                evento.preventDefault();
                buscaFii.setCustomValidity("Selecione um FII nos resultados da busca.");
                buscaFii.reportValidity();
            }
        });
    }
});
