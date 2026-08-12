<?php

namespace App\Controllers;

use App\Models\Usuario;

class CadastroController
{
    private $usuarioModel;

    public function __construct($conn)
    {
        $this->usuarioModel = new Usuario($conn);
    }

    public function index()
    {
        require __DIR__ . '/../../views/cadastro/cadastro.php';
    }

    public function cadastrar()
    {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $erro = "Preencha todos os campos.";

            require __DIR__ . '/../../views/cadastro/cadastro.php';
            return;
        }

        $usuarioExistente = $this->usuarioModel
            ->getUsuarioByEmail($email);

        if ($usuarioExistente) {
            $erro = "Este e-mail já está cadastrado.";

            require __DIR__ . '/../../views/cadastro/cadastro.php';
            return;
        }

        $senhaHash = password_hash(
            $senha,
            PASSWORD_DEFAULT
        );

        $sucesso = $this->usuarioModel->criar(
            $nome,
            $email,
            $senhaHash
        );

        if (!$sucesso) {
            $erro = "Não foi possível realizar o cadastro.";

            require __DIR__ . '/../../views/cadastro/cadastro.php';
            return;
        }

        header('Location: /login');
        exit;
    }
}