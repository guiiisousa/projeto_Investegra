<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ativo;
use App\Models\Carteira;
use App\Models\Usuario;

class ChatController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();
        $usuario = Usuario::perfil($usuarioId);

        $dadosIA = [
            "nome" => $usuario["nome"] ?? "",
            "perfil_risco" => $usuario["perfil_risco"] ?? "Não definido",
            "patrimonio" => Ativo::patrimonio($usuarioId),
            "quantidade_ativos" => Ativo::contar($usuarioId),
            "total_carteiras" => Carteira::contar($usuarioId),
        ];

        $chatNavAtivo = true;

        $this->view("chat/index", compact("dadosIA", "chatNavAtivo"));
    }
}
