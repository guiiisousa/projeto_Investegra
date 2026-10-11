<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ativo;
use App\Models\Carteira;

class IndicadoresController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $patrimonio = Ativo::patrimonio($usuarioId);
        $total_carteiras = Carteira::contar($usuarioId);
        $total_ativos = Ativo::contar($usuarioId);
        $maior_ativo = Ativo::maiorAtivo($usuarioId);
        $maior_categoria = Ativo::maiorCategoria($usuarioId);

        $media_carteira = $total_carteiras > 0
            ? $patrimonio / $total_carteiras
            : 0;

        $this->view("indicadores/index", compact(
            "patrimonio", "total_carteiras", "total_ativos",
            "maior_ativo", "maior_categoria", "media_carteira"
        ));
    }
}
