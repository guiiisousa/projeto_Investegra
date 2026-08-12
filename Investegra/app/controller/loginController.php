<?php

namespace App\Controllers;

use App\Models\Usuario;

class LoginController
{
    private $usuarioModel;

    public function __construct($conn)
    {
        $this->usuarioModel = new Usuario($conn);
    }

    public function entrar()
    {
        session_start();

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario =
            $this->usuarioModel
                ->getNameByEmail($email);

        if (
            $usuario &&
            password_verify(
                $senha,
                $usuario['senha']
            )
        ) {

            $_SESSION['id'] =
                $usuario['id'];

            $_SESSION['nome'] =
                $usuario['nome'];

            header(
                'Location: /dashboard'
            );

            exit;
        }

        $erro = 'E-mail ou senha inválidos.';

        require __DIR__ .
            '/../../views/login/index.php';
    }

    public function index()
    {
        require __DIR__ .
            '/../../views/login/index.php';
    }
}