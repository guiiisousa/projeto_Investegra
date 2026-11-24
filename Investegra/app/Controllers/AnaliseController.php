<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Alerta;
use App\Models\Ativo;
use App\Models\Carteira;
use App\Models\Usuario;
use App\Services\AnaliseIAService;

class AnaliseController extends Controller
{
    /* Análise de distribuição (gráfico por categoria). */
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $patrimonio = Ativo::patrimonio($usuarioId);
        $total_ativos = Ativo::contar($usuarioId);
        $categorias = Ativo::distribuicaoPorCategoria($usuarioId);
        $carteira_principal = Carteira::principal($usuarioId);

        $labels = [];
        $valores = [];

        foreach ($categorias as $categoria) {
            $labels[] = $categoria["categoria"];
            $valores[] = (float) $categoria["valor"];
        }

        $total_carteiras = Carteira::contar($usuarioId);
        $media_carteira = $total_carteiras > 0
            ? $patrimonio / $total_carteiras
            : 0;
        $maior_ativo = Ativo::maiorAtivo($usuarioId);
        $maior_categoria = Ativo::maiorCategoria($usuarioId);

        $this->view("analises/index", compact(
            "patrimonio", "total_ativos", "categorias",
            "carteira_principal", "labels", "valores",
            "total_carteiras", "media_carteira",
            "maior_ativo", "maior_categoria"
        ));
    }

    /* Análise da carteira feita pela IA. */
    public function carteira()
    {
        $this->redirect("analises.php");
    }
}
