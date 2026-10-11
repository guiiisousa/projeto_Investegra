<?php

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    public static function findByEmail(string $email): ?array
    {
        return self::fetchOne(
            "SELECT id, nome, email, senha, perfil_risco
             FROM usuarios
             WHERE email = ?",
            "s",
            [$email]
        );
    }

    public static function emailExists(string $email): bool
    {
        return self::fetchOne(
            "SELECT id FROM usuarios WHERE email = ?",
            "s",
            [$email]
        ) !== null;
    }

    public static function create(
        string $nome,
        string $email,
        string $documento,
        string $senhaHash,
        string $tipo
    ): bool {
        return self::execute(
            "INSERT INTO usuarios (nome, email, documento, senha, tipo_usuario)
             VALUES (?, ?, ?, ?, ?)",
            "sssss",
            [$nome, $email, $documento, $senhaHash, $tipo]
        );
    }

    /* Perfil de investidor completo (sem a senha). */
    public static function perfil(int $id): ?array
    {
        return self::fetchOne(
            "SELECT nome, idade, experiencia, renda_mensal,
                    horizonte_investimento, tolerancia_risco, objetivo,
                    descricao_pessoal, score_risco, perfil_risco, analise_ia
             FROM usuarios
             WHERE id = ?",
            "i",
            [$id]
        );
    }

    public static function salvarPerfil(int $id, array $d): bool
    {
        return self::execute(
            "UPDATE usuarios
             SET idade = ?, experiencia = ?, renda_mensal = ?,
                 horizonte_investimento = ?, tolerancia_risco = ?,
                 objetivo = ?, descricao_pessoal = ?,
                 score_risco = ?, perfil_risco = ?
             WHERE id = ?",
            "isdssssisi",
            [
                $d["idade"], $d["experiencia"], $d["renda_mensal"],
                $d["horizonte"], $d["tolerancia"], $d["objetivo"],
                $d["descricao"], $d["score"], $d["perfil"], $id,
            ]
        );
    }

    public static function salvarAnaliseIA(int $id, string $analise): bool
    {
        return self::execute(
            "UPDATE usuarios SET analise_ia = ? WHERE id = ?",
            "si",
            [$analise, $id]
        );
    }
}
