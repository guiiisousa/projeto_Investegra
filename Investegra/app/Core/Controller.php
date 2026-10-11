<?php

namespace App\Core;

abstract class Controller
{
    public function __construct()
    {
        Session::start();
    }

    protected function view(string $view, array $dados = []): void
    {
        View::render($view, $dados);
    }

    protected function redirect(string $destino)
    {
        header("Location: " . $destino);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    /* Exige login e devolve o id do usuário logado. */
    protected function requireLogin(): int
    {
        if (!Session::check()) {
            $this->redirect("login.php");
        }

        return Session::userId();
    }

    /* Respostas JSON (endpoints /api). */
    protected function json(array $dados, int $status = 200)
    {
        http_response_code($status);
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireLoginJson(): int
    {
        if (!Session::check()) {
            $this->json(["sucesso" => false, "erro" => "Não autenticado."], 401);
        }

        return Session::userId();
    }

    protected function abort(string $mensagem)
    {
        exit($mensagem);
    }
}
