<?php

namespace App\Models;

class Usuario
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getNameByEmail($email)
    {
        $sql = "
            SELECT nome
            FROM usuarios
            WHERE email = ?
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_assoc();
    }

     public function criar($nome, $email, $senha)
    {
        $sql = "
            INSERT INTO usuarios
            (nome, email, senha)
            VALUES (?, ?, ?)
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "sss",
            $nome,
            $email,
            $senha
        );

        return $stmt->execute();
    }
    public function getConnection()
    {
        return $this->conn;
    }

    public function getUsuarioByEmail($email)
    {
         $sql = "
            SELECT *
            FROM usuarios
            WHERE email = ?
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}