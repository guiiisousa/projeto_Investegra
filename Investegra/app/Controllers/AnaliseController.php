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

        $this->view("analises/index", compact(
            "patrimonio", "total_ativos", "categorias",
            "carteira_principal", "labels", "valores"
        ));
    }

    /* Análise da carteira feita pela IA. */
    public function carteira()
    {
        $usuarioId = $this->requireLogin();

        $usuario = Usuario::perfil($usuarioId);

        if (!$usuario) {
            $this->abort("Usuário não encontrado.");
        }

        $resumo = Ativo::resumoPatrimonio($usuarioId);
        $patrimonio = $resumo["patrimonio"];
        $quantidade_ativos = $resumo["quantidade_ativos"];

        $distribuicao = [];

        foreach (Ativo::distribuicaoPorCategoria($usuarioId) as $categoria) {
            $distribuicao[$categoria["categoria"]] = $patrimonio > 0
                ? ((float) $categoria["valor"] / $patrimonio) * 100
                : 0;
        }

        $alertas = Alerta::mensagensNaoLidas($usuarioId);

        $analise = AnaliseIAService::analisarCarteira([
            "perfil" => $usuario["perfil_risco"] ?? "Não informado",
            "score" => (int) ($usuario["score_risco"] ?? 0),
            "experiencia" => $usuario["experiencia"] ?? "Não informado",
            "horizonte" => $usuario["horizonte_investimento"] ?? "Não informado",
            "objetivo" => $usuario["objetivo"] ?? "Não informado",
            "patrimonio" => $patrimonio,
            "quantidade_ativos" => $quantidade_ativos,
            "distribuicao" => $distribuicao,
            "alertas" => $alertas,
        ]);

        $esc = function ($valor) {
            return htmlspecialchars($valor ?? "Não informado", ENT_QUOTES, "UTF-8");
        };

        $perfil = $esc($usuario["perfil_risco"]);
        $experiencia = $esc($usuario["experiencia"]);
        $horizonte = $esc($usuario["horizonte_investimento"]);
        $objetivo = $esc($usuario["objetivo"]);

        $this->view("analises/carteira", compact(
            "usuario", "patrimonio", "quantidade_ativos", "distribuicao",
            "alertas", "analise", "perfil", "experiencia", "horizonte", "objetivo"
        ));
    }
}
