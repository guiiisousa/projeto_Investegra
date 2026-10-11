<?php

namespace App\Core;

class Config
{
    private static ?array $dados = null;

    public static function get(string $chave, $padrao = null)
    {
        if (self::$dados === null) {
            self::$dados = require BASE_PATH . '/config/config.php';
        }

        return self::$dados[$chave] ?? $padrao;
    }
}
