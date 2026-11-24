<?php

namespace App\Models;

use App\Core\Model;

class Historico extends Model
{
    public static function registrarAtividade(
        int $usuarioId,
        string $acao,
        string $entidade,
        string $descricao,
        ?int $carteiraId = null,
        ?array $detalhes = null
    ): void {
        self::garantirTabelaAtividades();

        $detalhesJson = $detalhes !== null
            ? json_encode($detalhes, JSON_UNESCAPED_UNICODE)
            : null;

        self::execute(
            "INSERT INTO historico_atividades
                (usuario_id, acao, entidade, descricao, carteira_id, detalhes)
             VALUES (?, ?, ?, ?, ?, ?)",
            "isssis",
            [$usuarioId, $acao, $entidade, $descricao, $carteiraId, $detalhesJson]
        );
    }

    public static function atividades(int $usuarioId, int $limite = 100): array
    {
        self::garantirTabelaAtividades();

        return self::fetchAll(
            "SELECT acao, entidade, descricao, carteira_id, detalhes, data_registro
             FROM historico_atividades
             WHERE usuario_id = ?
             ORDER BY data_registro DESC, id DESC
             LIMIT " . (int) $limite,
            "i",
            [$usuarioId]
        );
    }

    public static function atividadesPorCarteira(int $usuarioId, int $carteiraId): array
    {
        self::garantirTabelaAtividades();

        return self::fetchAll(
            "SELECT acao, entidade, descricao, detalhes, data_registro
             FROM historico_atividades
             WHERE usuario_id = ? AND carteira_id = ?
             ORDER BY data_registro DESC, id DESC
             LIMIT 50",
            "ii",
            [$usuarioId, $carteiraId]
        );
    }

    private static function garantirTabelaAtividades(): void
    {
        self::execute(
            "CREATE TABLE IF NOT EXISTS historico_atividades (
                id int NOT NULL AUTO_INCREMENT,
                usuario_id int NOT NULL,
                acao varchar(30) NOT NULL,
                entidade varchar(50) NOT NULL,
                descricao varchar(255) NOT NULL,
                carteira_id int NULL,
                detalhes text NULL,
                data_registro datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY usuario_id (usuario_id),
                KEY carteira_id (carteira_id),
                CONSTRAINT historico_atividades_ibfk_1
                    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $carteiraIdExiste = self::fetchOne(
            "SHOW COLUMNS FROM historico_atividades LIKE 'carteira_id'"
        );
        if ($carteiraIdExiste === null) {
            self::execute(
                "ALTER TABLE historico_atividades
                 ADD COLUMN carteira_id int NULL"
            );
        }

        $detalhesExistem = self::fetchOne(
            "SHOW COLUMNS FROM historico_atividades LIKE 'detalhes'"
        );
        if ($detalhesExistem === null) {
            self::execute(
                "ALTER TABLE historico_atividades
                 ADD COLUMN detalhes text NULL"
            );
        }
    }

    /* Grava o patrimônio atual (soma de todas as carteiras) do usuário. */
    public static function registrarPatrimonio(int $usuarioId): void
    {
        $linha = self::fetchOne(
            "SELECT COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS patrimonio
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE c.usuario_id = ?",
            "i",
            [$usuarioId]
        );

        self::execute(
            "INSERT INTO historico_patrimonio (usuario_id, valor_total)
             VALUES (?, ?)",
            "id",
            [$usuarioId, $linha["patrimonio"]]
        );
    }

    /* Últimos registros (mais recentes primeiro). */
    public static function ultimos(int $usuarioId, int $limite = 12): array
    {
        return self::fetchAll(
            "SELECT valor_total, data_registro
             FROM historico_patrimonio
             WHERE usuario_id = ?
             ORDER BY data_registro DESC
             LIMIT " . (int) $limite,
            "i",
            [$usuarioId]
        );
    }

    /* Histórico completo em ordem cronológica. */
    public static function todos(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT valor_total, data_registro
             FROM historico_patrimonio
             WHERE usuario_id = ?
             ORDER BY data_registro ASC",
            "i",
            [$usuarioId]
        );
    }
}
