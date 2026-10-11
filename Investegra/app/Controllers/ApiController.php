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

    public function buscarAtivos()
    {
        $this->requireLoginJson();

        [$status, $corpo] = CotacaoService::buscarAtivos($_GET["termo"] ?? "");

        $this->json($corpo, $status);
    }
}
