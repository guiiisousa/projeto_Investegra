<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ativo;
use App\Models\Carteira;
use App\Models\Categoria;
use App\Models\Historico;
use App\Services\CotacaoService;

class AtivoController extends Controller
{
    /* Lista os ativos de uma carteira com cotação e rentabilidade. */
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $carteira_id = filter_input(INPUT_GET, "carteira", FILTER_VALIDATE_INT);

        if (!$carteira_id) {
            $this->redirect("carteiras.php");
        }

        $carteira = Carteira::find($carteira_id, $usuarioId);

        if (!$carteira) {
            $this->redirect("carteiras.php");
        }

        $ativos = Ativo::allByCarteira($carteira_id);
        $cotacoes = CotacaoService::obterParaAtivos($ativos);

        $resumo = Ativo::resumir($ativos, $cotacoes);
        $ativos = $resumo["ativos"];
        $totalInvestido = $resumo["totalInvestido"];
        $historicoCarteira = Historico::atividadesPorCarteira(
            $usuarioId,
            $carteira_id
        );

        $labelsCategoriasCarteira = [];
        $valoresCategoriasCarteira = [];
        $categoriasCarteira = [];

        foreach ($ativos as $ativo) {
            $categoria = $ativo["categoria"] ?? "Sem categoria";
            $categoriasCarteira[$categoria] =
                ($categoriasCarteira[$categoria] ?? 0)
                + (float) $ativo["valor_investido"];
        }

        arsort($categoriasCarteira);

        foreach ($categoriasCarteira as $categoria => $valor) {
            $labelsCategoriasCarteira[] = $categoria;
            $valoresCategoriasCarteira[] = round((float) $valor, 2);
        }

        $labelsEvolucaoCarteira = [];
        $valoresEvolucaoCarteira = [];
        $patrimonioEvolucao = 0.0;

        foreach (array_reverse($historicoCarteira) as $registro) {
            $detalhes = !empty($registro["detalhes"])
                ? json_decode($registro["detalhes"], true)
                : null;

            if (!is_array($detalhes) || $registro["entidade"] !== "Ativo") {
                continue;
            }

            if (isset($detalhes["antes"], $detalhes["depois"])) {
                $antes = (float) ($detalhes["antes"]["quantidade"] ?? 0)
                    * (float) ($detalhes["antes"]["preco_medio"] ?? 0);
                $depois = (float) ($detalhes["depois"]["quantidade"] ?? 0)
                    * (float) ($detalhes["depois"]["preco_medio"] ?? 0);
                $patrimonioEvolucao += $depois - $antes;
            } elseif ($registro["acao"] === "criou") {
                $patrimonioEvolucao +=
                    (float) ($detalhes["quantidade"] ?? 0)
                    * (float) ($detalhes["preco_medio"] ?? 0);
            } elseif ($registro["acao"] === "excluiu") {
                $patrimonioEvolucao -=
                    (float) ($detalhes["quantidade"] ?? 0)
                    * (float) ($detalhes["preco_medio"] ?? 0);
            }

            $patrimonioEvolucao = max(0, $patrimonioEvolucao);
            $labelsEvolucaoCarteira[] = date(
                "d/m",
                strtotime($registro["data_registro"])
            );
            $valoresEvolucaoCarteira[] = round($patrimonioEvolucao, 2);
        }

        if (empty($labelsEvolucaoCarteira) && count($ativos) > 0) {
            $labelsEvolucaoCarteira[] = "Atual";
            $valoresEvolucaoCarteira[] = round($totalInvestido, 2);
        }

        $this->view("ativos/index", array_merge(
            $resumo,
            compact(
                "carteira",
                "carteira_id",
                "historicoCarteira",
                "labelsCategoriasCarteira",
                "valoresCategoriasCarteira",
                "labelsEvolucaoCarteira",
                "valoresEvolucaoCarteira"
            )
        ));
    }

    public function create()
    {
        $usuarioId = $this->requireLogin();

        $id_carteira = filter_input(INPUT_GET, "carteira", FILTER_VALIDATE_INT);

        if (!$id_carteira) {
            $this->redirect("carteiras.php?erro=1");
        }

        $carteira = Carteira::find($id_carteira, $usuarioId);

        if (!$carteira) {
            $this->redirect("carteiras.php?erro=1");
        }

        $categorias = Categoria::all();

        $this->view("ativos/create", compact("carteira", "id_carteira", "categorias"));
    }

    public function store()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $dados = $this->dadosDoFormulario();
        $id_carteira = filter_input(INPUT_POST, "carteira_id", FILTER_VALIDATE_INT);

        if (!$id_carteira || !$this->dadosValidos($dados)) {
            $this->redirect("novo_ativo.php?carteira=" . $id_carteira . "&erro=1");
        }

        if (!Carteira::find($id_carteira, $usuarioId)) {
            $this->redirect("carteiras.php?erro=1");
        }

        if (!Categoria::exists($dados["categoria_id"])) {
            $this->redirect("novo_ativo.php?carteira=" . $id_carteira . "&erro=1");
        }

        $dados["carteira_id"] = $id_carteira;

        if (Ativo::create($dados)) {
            Historico::registrarPatrimonio($usuarioId);
            Historico::registrarAtividade(
                $usuarioId,
                "criou",
                "Ativo",
                "Ativo comprado: " . strtoupper(trim($dados["ticker"])),
                $id_carteira,
                [
                    "ticker" => $dados["ticker"],
                    "quantidade" => $dados["quantidade"],
                    "preco_medio" => $dados["preco_medio"],
                    "data_compra" => $dados["data_compra"],
                    "observacao" => $dados["observacao"],
                    "renda_fixa_nome" => $dados["renda_fixa_nome"],
                    "renda_fixa_indexador" => $dados["renda_fixa_indexador"],
                    "renda_fixa_taxa" => $dados["renda_fixa_taxa"],
                    "renda_fixa_vencimento" => $dados["renda_fixa_vencimento"],
                ]
            );

            $this->redirect("ativos.php?carteira=" . $id_carteira . "&sucesso=1");
        }

        $this->redirect("ativos.php?carteira=" . $id_carteira . "&erro=1");
    }

    public function edit()
    {
        $usuarioId = $this->requireLogin();

        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect("carteiras.php?erro=1");
        }

        $ativo = Ativo::findOwned($id, $usuarioId);

        if (!$ativo) {
            $this->redirect("carteiras.php?erro=1");
        }

        $categorias = Categoria::all();

        $this->view("ativos/edit", compact("ativo", "categorias"));
    }

    public function update()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $dados = $this->dadosDoFormulario();

        if (!$id || !$this->dadosValidos($dados)) {
            $this->redirect("carteiras.php?erro=1");
        }

        $ativo = Ativo::findOwned($id, $usuarioId);

        if (!$ativo) {
            $this->redirect("carteiras.php?erro=1");
        }

        $destino = "ativos.php?carteira=" . $ativo["carteira_id"];

        if (Ativo::update($id, $dados)) {
            Historico::registrarPatrimonio($usuarioId);
            Historico::registrarAtividade(
                $usuarioId,
                "editou",
                "Ativo",
                "Ativo atualizado: " . strtoupper(trim($dados["ticker"])),
                (int) $ativo["carteira_id"],
                [
                    "antes" => [
                        "ticker" => $ativo["ticker"],
                        "quantidade" => $ativo["quantidade"],
                        "preco_medio" => $ativo["preco_medio"],
                        "data_compra" => $ativo["data_compra"],
                        "observacao" => $ativo["observacao"],
                        "renda_fixa_nome" => $ativo["renda_fixa_nome"] ?? null,
                        "renda_fixa_indexador" => $ativo["renda_fixa_indexador"] ?? null,
                        "renda_fixa_taxa" => $ativo["renda_fixa_taxa"] ?? null,
                        "renda_fixa_vencimento" => $ativo["renda_fixa_vencimento"] ?? null,
                    ],
                    "depois" => [
                        "ticker" => $dados["ticker"],
                        "quantidade" => $dados["quantidade"],
                        "preco_medio" => $dados["preco_medio"],
                        "data_compra" => $dados["data_compra"],
                        "observacao" => $dados["observacao"],
                        "renda_fixa_nome" => $dados["renda_fixa_nome"],
                        "renda_fixa_indexador" => $dados["renda_fixa_indexador"],
                        "renda_fixa_taxa" => $dados["renda_fixa_taxa"],
                        "renda_fixa_vencimento" => $dados["renda_fixa_vencimento"],
                    ],
                ]
            );

            $this->redirect($destino . "&sucesso=1");
        }

        $this->redirect($destino . "&erro=1");
    }

    public function comprar()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $preco = filter_input(INPUT_POST, "preco_medio", FILTER_VALIDATE_FLOAT);
        $dataCompra = $_POST["data_compra"] ?? "";

        if (
            !$id
            || !is_string($dataCompra)
            || !$this->dataTransacaoValida($dataCompra)
            || $preco === false
            || $preco === null
            || !is_finite((float) $preco)
            || $preco <= 0
        ) {
            $this->redirect("carteiras.php?erro=1");
        }

        $ativo = Ativo::findOwned($id, $usuarioId);

        if (!$ativo) {
            $this->redirect("carteiras.php?erro=1");
        }

        $dataCompra = trim($dataCompra);
        $ehRendaFixa = Categoria::nomePorId((int) $ativo["categoria_id"]) === "Renda Fixa";
        $quantidadeAtual = (float) $ativo["quantidade"];
        $precoMedioAtual = (float) $ativo["preco_medio"];
        $quantidadeMovimentada = null;
        $precoMovimentacao = round((float) $preco, 2);
        if ($precoMovimentacao <= 0) {
            $this->redirect("ativos.php?carteira=" . $ativo["carteira_id"] . "&erro=valor");
        }

        if ($ehRendaFixa) {
            $valorAtual = round($quantidadeAtual * $precoMedioAtual, 2);
            $novaQuantidade = 1.0;
            $precoMedioNovo = round($valorAtual + $precoMovimentacao, 2);
            $dataCompraAtivo = !empty($ativo["data_compra"])
                ? $ativo["data_compra"]
                : $dataCompra;
            $detalhesMovimentacao = [
                "valor_adicionado" => $precoMovimentacao,
                "data_compra" => $dataCompra,
                "renda_fixa" => true,
            ];
        } else {
            $quantidade = filter_input(INPUT_POST, "quantidade", FILTER_VALIDATE_FLOAT);

            if (
                $quantidade === false
                || $quantidade === null
                || !is_finite((float) $quantidade)
                || (float) $quantidade <= 0
            ) {
                $this->redirect("ativos.php?carteira=" . $ativo["carteira_id"] . "&erro=quantidade");
            }

            if (
                Categoria::nomePorId((int) $ativo["categoria_id"]) !== "Criptomoedas"
                && floor((float) $quantidade) !== (float) $quantidade
            ) {
                $this->redirect("ativos.php?carteira=" . $ativo["carteira_id"] . "&erro=quantidade");
            }

            $quantidadeMovimentada = round((float) $quantidade, 8);
            if ($quantidadeMovimentada <= 0) {
                $this->redirect("ativos.php?carteira=" . $ativo["carteira_id"] . "&erro=quantidade");
            }
            $novaQuantidade = $quantidadeAtual + $quantidadeMovimentada;
            $precoMedioNovo = $novaQuantidade > 0
                ? (($quantidadeAtual * $precoMedioAtual) + ($quantidadeMovimentada * $precoMovimentacao)) / $novaQuantidade
                : $precoMovimentacao;
            $dataCompraAtivo = $dataCompra;
            $detalhesMovimentacao = [
                "quantidade_comprada" => $quantidadeMovimentada,
                "preco_compra" => $precoMovimentacao,
                "data_compra" => $dataCompra,
            ];
        }

        $dados = [
            "ticker" => $ativo["ticker"],
            "categoria_id" => (int) $ativo["categoria_id"],
            "quantidade" => $novaQuantidade,
            "preco_medio" => $precoMedioNovo,
            "data_compra" => $dataCompraAtivo,
            "observacao" => $ativo["observacao"] ?? "",
            "renda_fixa_nome" => $ativo["renda_fixa_nome"] ?? null,
            "renda_fixa_indexador" => $ativo["renda_fixa_indexador"] ?? null,
            "renda_fixa_taxa" => $ativo["renda_fixa_taxa"] ?? null,
            "renda_fixa_vencimento" => $ativo["renda_fixa_vencimento"] ?? null,
        ];

        $destino = "ativos.php?carteira=" . $ativo["carteira_id"];

        if (Ativo::update($id, $dados)) {
            Historico::registrarPatrimonio($usuarioId);
            Historico::registrarAtividade(
                $usuarioId,
                "comprou",
                "Ativo",
                ($ehRendaFixa ? "Aporte realizado: " : "Compra realizada: ")
                    . strtoupper($ativo["ticker"]),
                (int) $ativo["carteira_id"],
                array_merge(
                    [
                        "antes" => [
                            "quantidade" => $quantidadeAtual,
                            "preco_medio" => $precoMedioAtual,
                        ],
                        "depois" => [
                            "quantidade" => $novaQuantidade,
                            "preco_medio" => $precoMedioNovo,
                        ],
                    ],
                    $detalhesMovimentacao
                )
            );
            $this->redirect($destino . "&sucesso=1");
        }

        $this->redirect($destino . "&erro=1");
    }

    public function vender()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $preco = filter_input(INPUT_POST, "preco_medio", FILTER_VALIDATE_FLOAT);
        $dataVenda = $_POST["data_venda"] ?? "";

        if (
            !$id
            || !is_string($dataVenda)
            || !$this->dataTransacaoValida($dataVenda)
            || $preco === false
            || $preco === null
            || !is_finite((float) $preco)
            || $preco <= 0
        ) {
            $this->redirect("carteiras.php?erro=1");
        }

        $ativo = Ativo::findOwned($id, $usuarioId);

        if (!$ativo) {
            $this->redirect("carteiras.php?erro=1");
        }

        $dataVenda = trim($dataVenda);
        $ehRendaFixa = Categoria::nomePorId((int) $ativo["categoria_id"]) === "Renda Fixa";
        $quantidadeAtual = (float) $ativo["quantidade"];
        $precoMedioAtual = (float) $ativo["preco_medio"];
        $precoMovimentacao = round((float) $preco, 2);
        if ($precoMovimentacao <= 0) {
            $this->redirect("ativos.php?carteira=" . $ativo["carteira_id"] . "&erro=valor");
        }
        $destino = "ativos.php?carteira=" . $ativo["carteira_id"];

        if ($ehRendaFixa) {
            $valorAtual = round($quantidadeAtual * $precoMedioAtual, 2);
            if ($precoMovimentacao > $valorAtual) {
                $this->redirect($destino . "&erro=valor");
            }

            $valorRestante = round($valorAtual - $precoMovimentacao, 2);
            $novaQuantidade = $valorRestante > 0 ? 1.0 : 0.0;
            $precoMedioNovo = $valorRestante;
            $quantidadeMovimentada = null;
            $detalhesMovimentacao = [
                "valor_resgatado" => $precoMovimentacao,
                "data_venda" => $dataVenda,
                "renda_fixa" => true,
            ];
        } else {
            $quantidade = filter_input(INPUT_POST, "quantidade", FILTER_VALIDATE_FLOAT);
            if (
                $quantidade === false
                || $quantidade === null
                || !is_finite((float) $quantidade)
                || (float) $quantidade <= 0
            ) {
                $this->redirect($destino . "&erro=quantidade");
            }

            $quantidadeMovimentada = round((float) $quantidade, 8);
            if ($quantidadeMovimentada <= 0) {
                $this->redirect($destino . "&erro=quantidade");
            }

            if (
                Categoria::nomePorId((int) $ativo["categoria_id"]) !== "Criptomoedas"
                && floor((float) $quantidade) !== (float) $quantidade
            ) {
                $this->redirect($destino . "&erro=quantidade");
            }

            if ($quantidadeMovimentada > $quantidadeAtual) {
                $this->redirect($destino . "&erro=quantidade");
            }

            $novaQuantidade = round($quantidadeAtual - $quantidadeMovimentada, 8);
            $precoMedioNovo = $precoMedioAtual;
            $detalhesMovimentacao = [
                "quantidade_vendida" => $quantidadeMovimentada,
                "preco_venda" => $precoMovimentacao,
                "data_venda" => $dataVenda,
            ];
        }

        $depois = [
            "quantidade" => $novaQuantidade,
            "preco_medio" => $precoMedioNovo,
        ];
        $ok = $novaQuantidade <= 0
            ? Ativo::delete($id)
            : Ativo::update($id, [
                "ticker" => $ativo["ticker"],
                "categoria_id" => (int) $ativo["categoria_id"],
                "quantidade" => $novaQuantidade,
                "preco_medio" => $precoMedioNovo,
                "data_compra" => $ativo["data_compra"],
                "observacao" => $ativo["observacao"] ?? "",
                "renda_fixa_nome" => $ativo["renda_fixa_nome"] ?? null,
                "renda_fixa_indexador" => $ativo["renda_fixa_indexador"] ?? null,
                "renda_fixa_taxa" => $ativo["renda_fixa_taxa"] ?? null,
                "renda_fixa_vencimento" => $ativo["renda_fixa_vencimento"] ?? null,
            ]);

        if ($ok) {
            Historico::registrarPatrimonio($usuarioId);
            Historico::registrarAtividade(
                $usuarioId,
                "vendeu",
                "Ativo",
                ($ehRendaFixa ? "Resgate realizado: " : "Venda realizada: ")
                    . strtoupper($ativo["ticker"]),
                (int) $ativo["carteira_id"],
                array_merge(
                    [
                        "antes" => [
                            "quantidade" => $quantidadeAtual,
                            "preco_medio" => $precoMedioAtual,
                        ],
                        "depois" => $depois,
                    ],
                    $detalhesMovimentacao
                )
            );
            $this->redirect($destino . "&sucesso=1");
        }

        $this->redirect($destino . "&erro=1");
    }

    public function destroy()
    {
        $usuarioId = $this->requireLogin();

        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect("carteiras.php?erro=1");
        }

        $ativo = Ativo::findOwned($id, $usuarioId);

        if (!$ativo) {
            $this->redirect("carteiras.php?erro=1");
        }

        $destino = "ativos.php?carteira=" . $ativo["carteira_id"];

        if (Ativo::delete($id)) {
            Historico::registrarPatrimonio($usuarioId);
            Historico::registrarAtividade(
                $usuarioId,
                "excluiu",
                "Ativo",
                "Ativo removido da carteira: " . strtoupper(trim($ativo["ticker"])),
                (int) $ativo["carteira_id"],
                [
                    "ticker" => $ativo["ticker"],
                    "quantidade" => $ativo["quantidade"],
                    "preco_medio" => $ativo["preco_medio"],
                    "data_compra" => $ativo["data_compra"],
                    "observacao" => $ativo["observacao"],
                    "renda_fixa_nome" => $ativo["renda_fixa_nome"] ?? null,
                    "renda_fixa_indexador" => $ativo["renda_fixa_indexador"] ?? null,
                    "renda_fixa_taxa" => $ativo["renda_fixa_taxa"] ?? null,
                    "renda_fixa_vencimento" => $ativo["renda_fixa_vencimento"] ?? null,
                ]
            );

            $this->redirect($destino . "&sucesso=1");
        }

        $this->redirect($destino . "&erro=1");
    }

    /* ---------- auxiliares ---------- */

    private function dataTransacaoValida(string $data): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return false;
        }

        [$ano, $mes, $dia] = array_map("intval", explode("-", $data));

        return checkdate($mes, $dia, $ano);
    }

    private function dadosDoFormulario(): array
    {
        $data = trim($_POST["data_compra"] ?? "");
        $categoriaId = filter_input(INPUT_POST, "categoria_id", FILTER_VALIDATE_INT);
        $nomeCategoria = is_int($categoriaId)
            ? Categoria::nomePorId($categoriaId)
            : null;
        $ehRendaFixa = $nomeCategoria === "Renda Fixa";
        $ehCripto = $nomeCategoria === "Criptomoedas";
        $produtoRendaFixa = trim($_POST["produto_renda_fixa"] ?? "");

        return [
            "ticker" => $ehRendaFixa
                ? strtoupper($produtoRendaFixa)
                : strtoupper(trim($_POST["ticker"] ?? "")),
            "categoria_id" => $categoriaId,
            "quantidade" => $ehRendaFixa
                ? 1
                : filter_input(
                    INPUT_POST,
                    "quantidade",
                    $ehCripto ? FILTER_VALIDATE_FLOAT : FILTER_VALIDATE_INT
                ),
            "preco_medio" => filter_input(INPUT_POST, "preco_medio", FILTER_VALIDATE_FLOAT),
            "data_compra" => $data === "" ? null : $data,
            "observacao" => trim($_POST["observacao"] ?? ""),
            "renda_fixa_nome" => $ehRendaFixa
                ? trim($_POST["renda_fixa_nome"] ?? "")
                : null,
            "renda_fixa_indexador" => $ehRendaFixa
                ? trim($_POST["renda_fixa_indexador"] ?? "")
                : null,
            "renda_fixa_taxa" => $ehRendaFixa
                ? filter_input(INPUT_POST, "renda_fixa_taxa", FILTER_VALIDATE_FLOAT)
                : null,
            "renda_fixa_vencimento" => $ehRendaFixa
                ? (trim($_POST["renda_fixa_vencimento"] ?? "") ?: null)
                : null,
        ];
    }

    private function dadosValidos(array $d): bool
    {
        if (
            $d["ticker"] === ""
            || !$d["categoria_id"]
            || !Categoria::exists((int) $d["categoria_id"])
            || $d["quantidade"] === false
            || $d["quantidade"] === null
            || !is_finite((float) $d["quantidade"])
            || (float) $d["quantidade"] <= 0
            || $d["preco_medio"] === false
            || $d["preco_medio"] === null
            || $d["preco_medio"] <= 0
        ) {
            return false;
        }

        $categoria = Categoria::nomePorId((int) $d["categoria_id"]);

        if ($categoria === "Criptomoedas") {
            return preg_match('/^[A-Z0-9]{2,12}$/', $d["ticker"]) === 1;
        }

        if ($categoria !== "Renda Fixa") {
            return preg_match('/^[A-Z0-9][A-Z0-9.^-]{0,19}$/', $d["ticker"]) === 1;
        }

        $produtosPermitidos = [
            "CDB",
            "LCI",
            "LCA",
            "TESOURO",
            "DEBENTURE",
            "CRI",
            "CRA",
            "POUPANCA",
            "OUTRO",
        ];
        $indexadoresPermitidos = [
            "Prefixado",
            "CDI",
            "IPCA+",
            "SELIC",
            "TR",
            "Outro",
        ];

        $vencimentoValido = true;
        if ($d["renda_fixa_vencimento"] !== null) {
            $vencimento = \DateTime::createFromFormat(
                "Y-m-d",
                $d["renda_fixa_vencimento"]
            );
            $vencimentoValido = $vencimento !== false
                && $vencimento->format("Y-m-d") === $d["renda_fixa_vencimento"];
        }

        return in_array($d["ticker"], $produtosPermitidos, true)
            && $d["renda_fixa_nome"] !== ""
            && mb_strlen($d["renda_fixa_nome"]) <= 100
            && in_array($d["renda_fixa_indexador"], $indexadoresPermitidos, true)
            && $d["renda_fixa_taxa"] !== false
            && $d["renda_fixa_taxa"] !== null
            && is_finite((float) $d["renda_fixa_taxa"])
            && (float) $d["renda_fixa_taxa"] >= 0
            && (float) $d["renda_fixa_taxa"] <= 9999.9999
            && $vencimentoValido;
    }
}
