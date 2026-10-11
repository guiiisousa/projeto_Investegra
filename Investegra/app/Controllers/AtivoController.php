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

        $tickers = [];

        foreach ($ativos as $a) {
            if (!empty($a["ticker"])) {
                $tickers[] = strtoupper(trim($a["ticker"]));
            }
        }

        $resumo = Ativo::resumir($ativos, CotacaoService::obter($tickers));

        $this->view("ativos/index", array_merge(
            $resumo,
            compact("carteira", "carteira_id")
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

            $this->redirect($destino . "&sucesso=1");
        }

        $this->redirect($destino . "&erro=1");
    }

    /* ---------- auxiliares ---------- */

    private function dadosDoFormulario(): array
    {
        $data = trim($_POST["data_compra"] ?? "");

        return [
            "ticker" => strtoupper(trim($_POST["ticker"] ?? "")),
            "categoria_id" => filter_input(INPUT_POST, "categoria_id", FILTER_VALIDATE_INT),
            "quantidade" => filter_input(INPUT_POST, "quantidade", FILTER_VALIDATE_FLOAT),
            "preco_medio" => filter_input(INPUT_POST, "preco_medio", FILTER_VALIDATE_FLOAT),
            "data_compra" => $data === "" ? null : $data,
            "observacao" => trim($_POST["observacao"] ?? ""),
        ];
    }

    private function dadosValidos(array $d): bool
    {
        return $d["ticker"] !== ""
            && $d["categoria_id"]
            && $d["quantidade"] !== false && $d["quantidade"] !== null && $d["quantidade"] > 0
            && $d["preco_medio"] !== false && $d["preco_medio"] !== null && $d["preco_medio"] >= 0;
    }
}
