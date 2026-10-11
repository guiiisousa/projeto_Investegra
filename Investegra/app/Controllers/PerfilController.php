<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Services\AnaliseIAService;
use App\Services\PerfilService;

class PerfilController extends Controller
{
    public function questionario()
    {
        $this->requireLogin();

        $this->view("perfil/questionario");
    }

    public function processar()
    {
        $usuarioId = $this->requireLogin();

        [$erro, $dados] = PerfilService::avaliar($_POST);

        if ($erro !== null) {
            $this->abort($erro);
        }

        if (!Usuario::salvarPerfil($usuarioId, $dados)) {
            $this->abort("Erro ao salvar o perfil.");
        }

        /* A análise da IA é opcional: se falhar, o perfil já está salvo. */
        $analise = AnaliseIAService::analisarPerfil([
            "idade" => $dados["idade"],
            "experiencia" => $dados["experiencia"],
            "renda_mensal" => $dados["renda_mensal"],
            "horizonte_investimento" => $dados["horizonte"],
            "tolerancia_risco" => $dados["tolerancia"],
            "objetivo" => $dados["objetivo"],
            "descricao_pessoal" => $dados["descricao"],
            "perfil_risco" => $dados["perfil"],
        ]);

        if ($analise !== "") {
            Usuario::salvarAnaliseIA($usuarioId, $analise);
        }

        $this->redirect("resultado_quiz.php");
    }

    public function resultado()
    {
        $usuarioId = $this->requireLogin();

        $usuario = Usuario::perfil($usuarioId);

        if (!$usuario) {
            $this->abort("Usuário não encontrado.");
        }

        $esc = function ($valor) {
            return htmlspecialchars($valor ?? "");
        };

        $nome = $esc($usuario["nome"]);
        $idade = $esc($usuario["idade"]);
        $experiencia = $esc($usuario["experiencia"]);
        $horizonte = $esc($usuario["horizonte_investimento"]);
        $tolerancia = $esc($usuario["tolerancia_risco"]);
        $objetivo = $esc($usuario["objetivo"]);
        $descricao = $esc($usuario["descricao_pessoal"]);
        $perfil = htmlspecialchars($usuario["perfil_risco"] ?? "Moderado");

        $score = (int) ($usuario["score_risco"] ?? 0);
        $analise_ia = $usuario["analise_ia"] ?? "";

        $renda_formatada = number_format((float) ($usuario["renda_mensal"] ?? 0), 2, ",", ".");

        $descricao_perfil = PerfilService::descricao($perfil);

        $this->view("perfil/resultado", compact(
            "nome", "idade", "experiencia", "renda_formatada", "horizonte",
            "tolerancia", "objetivo", "descricao", "score", "perfil",
            "analise_ia", "descricao_perfil"
        ));
    }
}
