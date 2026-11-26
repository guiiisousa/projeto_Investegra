<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AssistenteService;
use App\Services\CotacaoService;

/* Endpoints JSON usados pelo front-end (assistente, cotações, busca). */
class ApiController extends Controller
{
    public function chat()
    {
        $this->requireLoginJson();

        if (!$this->isPost()) {
            $this->json(["sucesso" => false, "erro" => "Método não permitido."], 405);
        }

        $entrada = json_decode(file_get_contents("php://input"), true);

        if (!is_array($entrada)) {
            $this->json(["sucesso" => false, "erro" => "Dados inválidos."], 400);
        }

        $pergunta = trim($entrada["pergunta"] ?? "");
        $contexto = $entrada["contexto"] ?? "geral";
        $dados = $entrada["dados"] ?? null;
        $historico = $entrada["historico"] ?? [];

        if ($pergunta === "") {
            $this->json(["sucesso" => false, "erro" => "Digite uma pergunta."], 400);
        }

        if (mb_strlen($pergunta) > 1000) {
            $this->json(["sucesso" => false, "erro" => "A pergunta é muito longa."], 400);
        }

        [$status, $corpo] = AssistenteService::responder(
            $pergunta,
            (string) $contexto,
            $dados,
            $historico
        );

        $this->json($corpo, $status);
    }

    public function cotacao()
    {
        $this->requireLoginJson();

        [$status, $corpo] = CotacaoService::cotacaoUnica($_GET["ticker"] ?? "");

        $this->json($corpo, $status);
    }

    public function cotacaoFii()
    {
        $this->requireLoginJson();
        $ticker = $_GET["ticker"] ?? "";

        if (!is_string($ticker)) {
            $this->json(["sucesso" => false, "mensagem" => "Ticker de FII inválido."], 400);
        }

        [$status, $corpo] = CotacaoService::cotacaoFii($ticker);

        $this->json($corpo, $status);
    }

    public function cotacaoEtf()
    {
        $this->requireLoginJson();
        $ticker = $_GET["ticker"] ?? "";

        if (!is_string($ticker)) {
            $this->json(["sucesso" => false, "mensagem" => "Ticker de ETF inválido."], 400);
        }

        [$status, $corpo] = CotacaoService::cotacaoEtf($ticker);

        $this->json($corpo, $status);
    }

    public function cotacaoCripto()
    {
        $this->requireLoginJson();
        $ticker = $_GET["ticker"] ?? "";

        if (!is_string($ticker)) {
            $this->json(["sucesso" => false, "mensagem" => "Ticker de criptomoeda inválido."], 400);
        }

        [$status, $corpo] = CotacaoService::cotacaoCripto($ticker);

        $this->json($corpo, $status);
    }

    public function dadosFii()
    {
        $this->requireLoginJson();
        $ticker = $_GET["ticker"] ?? "";

        if (!is_string($ticker)) {
            $this->json(["sucesso" => false, "mensagem" => "Ticker de FII inválido."], 400);
        }

        [$status, $corpo] = CotacaoService::dadosFii($ticker);

        $this->json($corpo, $status);
    }

    public function buscarAtivos()
    {
        $this->requireLoginJson();

        $termo = $_GET["termo"] ?? "";
        $categoria = $_GET["categoria"] ?? null;

        if (!is_string($termo) || ($categoria !== null && !is_string($categoria))) {
            $this->json(["sucesso" => false, "mensagem" => "Parâmetros de busca inválidos."], 400);
        }

        [$status, $corpo] = CotacaoService::buscarAtivos($termo, $categoria);

        $this->json($corpo, $status);
    }
}
