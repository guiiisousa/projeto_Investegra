<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
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

        Session::setQuizConcluido(true);

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

        $nome = $usuario["nome"] ?? "";
        $idade = $usuario["idade"] ?? "";
        $experiencia = $usuario["experiencia"] ?? "";
        $horizonte = $usuario["horizonte_investimento"] ?? "";
        $tolerancia = $usuario["tolerancia_risco"] ?? "";
        $objetivo = $usuario["objetivo"] ?? "";
        $descricao = $usuario["descricao_pessoal"] ?? "";
        $perfil = $usuario["perfil_risco"] ?? "Moderado";
        if (($usuario["score_risco"] ?? null) === null) {
            $perfil = "Não definido";
        }

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

    public function perfil()
    {
        $usuarioId = $this->requireLogin();

        $usuario = Usuario::perfil($usuarioId);

        if (!$usuario) {
            $this->abort("Usuário não encontrado.");
        }

        $quizConcluido = ($usuario["score_risco"] ?? null) !== null;
        Session::setQuizConcluido($quizConcluido);

        $nome = $usuario["nome"] ?? "";
        $idade = $usuario["idade"] ?? "";
        $experiencia = $usuario["experiencia"] ?? "";
        $horizonte = $usuario["horizonte_investimento"] ?? "";
        $tolerancia = $usuario["tolerancia_risco"] ?? "";
        $objetivo = $usuario["objetivo"] ?? "";
        $descricao = $usuario["descricao_pessoal"] ?? "";
        $perfil = $usuario["perfil_risco"] ?? "Moderado";
        if (($usuario["score_risco"] ?? null) === null) {
            $perfil = "Não definido";
        }

        $score = (int) ($usuario["score_risco"] ?? 0);
        $analise_ia = $usuario["analise_ia"] ?? "";

        $renda_formatada = number_format((float) ($usuario["renda_mensal"] ?? 0), 2, ",", ".");
        $descricao_perfil = PerfilService::descricao($perfil);

        $this->view("perfil/perfil", compact(
            "nome", "idade", "experiencia", "renda_formatada", "horizonte",
            "tolerancia", "objetivo", "descricao", "score", "perfil", "quizConcluido",
            "analise_ia", "descricao_perfil"
        ));
    }
}
