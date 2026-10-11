<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION["id"]);
    }

    public static function userId(): ?int
    {
        return isset($_SESSION["id"]) ? (int) $_SESSION["id"] : null;
    }

    public static function login(array $usuario): void
    {
        session_regenerate_id(true);

        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nome"] = $usuario["nome"];
        $_SESSION["email"] = $usuario["email"];
        $_SESSION["perfil_risco"] = $usuario["perfil_risco"];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
