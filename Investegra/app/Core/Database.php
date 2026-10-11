<?php

namespace App\Core;

class Database
{
    private static ?\mysqli $conexao = null;

    public static function connection(): \mysqli
    {
        if (self::$conexao === null) {

            $c = Config::get('db');


            $mysqli = new \mysqli(
                $c['host'],
                $c['user'],
                $c['pass'],
                $c['name']
            );

            if ($mysqli->connect_error) {
                die("Erro na conexão com o banco de dados.");
            }

            $mysqli->set_charset("utf8mb4");

            self::$conexao = $mysqli;
        }

        return self::$conexao;
    }
}
