<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Session::check()) {
            $this->redirect("dashboard.php");
        }

        $mensagem = "";
        $tipoMensagem = "erro";

        if (isset($_GET["erro"])) {
            $mensagem = "E-mail ou senha incorretos.";
        }

        if (isset($_GET["cadastro"])) {
            $mensagem = "Cadastro realizado com sucesso. Faça login.";
            $tipoMensagem = "sucesso";
        }

        $this->view("auth/login", compact("mensagem", "tipoMensagem"));
    }

    public function login()
    {
        if (!$this->isPost()) {
            $this->redirect("login.php");
        }

        $email = trim($_POST["email"] ?? "");
        $senha = $_POST["senha"] ?? "";

        if ($email === "" || $senha === "") {
            $this->redirect("login.php?erro=1");
        }

        $usuario = Usuario::findByEmail($email);

        if (!$usuario || !password_verify($senha, $usuario["senha"])) {
            $this->redirect("login.php?erro=1");
        }

        Session::login($usuario);

        $this->redirect("dashboard.php");
    }

    public function cadastroForm()
    {
        $mensagens = [
            "campos" => "Preencha todos os campos.",
            "email" => "E-mail inválido.",
            "confirmacao" => "As senhas não coincidem.",
            "senha_curta" => "A senha deve possuir pelo menos 6 caracteres.",
            "tipo" => "Tipo de usuário inválido.",
            "existe" => "Este e-mail já está cadastrado.",
            "falha" => "Erro ao realizar o cadastro.",
        ];

        $mensagem = $mensagens[$_GET["erro"] ?? ""] ?? "";

        $this->view("auth/cadastro", compact("mensagem"));
    }

    public function cadastro()
    {
        if (!$this->isPost()) {
            $this->redirect("cadastro.php");
        }

        $nome = trim($_POST["nome"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $documento = trim($_POST["documento"] ?? "");
        $tipo = $_POST["tipo_usuario"] ?? "PF";
        $senha = $_POST["senha"] ?? "";
        $confirmar = $_POST["confirmar_senha"] ?? "";

        if ($nome === "" || $email === "" || $documento === "" || $senha === "") {
            $this->redirect("cadastro.php?erro=campos");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect("cadastro.php?erro=email");
        }

        if ($senha !== $confirmar) {
            $this->redirect("cadastro.php?erro=confirmacao");
        }

        if (strlen($senha) < 6) {
            $this->redirect("cadastro.php?erro=senha_curta");
        }

        if (!in_array($tipo, ["PF", "PJ"])) {
            $this->redirect("cadastro.php?erro=tipo");
        }

        if (Usuario::emailExists($email)) {
            $this->redirect("cadastro.php?erro=existe");
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);

        if (Usuario::create($nome, $email, $documento, $hash, $tipo)) {
            $this->redirect("login.php?cadastro=sucesso");
        }

        $this->redirect("cadastro.php?erro=falha");
    }

    public function logout()
    {
        Session::logout();

        $this->redirect("login.php");
    }
}
