<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Recomendacao;
use App\Services\RecomendacaoService;

class RecomendacaoController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        RecomendacaoService::gerar($usuarioId);

        $recomendacoes = Recomendacao::all($usuarioId);

        $this->view("recomendacoes/index", compact("recomendacoes"));
    }
}
