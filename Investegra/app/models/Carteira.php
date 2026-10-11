<?php

namespace App\Models;

use App\Core\Model;
use App\Services\CotacaoService;

class Carteira extends Model
{
    public static function find(int $id, int $usuarioId): ?array
    {
        return self::fetchOne(
            "SELECT id, nome, descricao
             FROM carteiras
             WHERE id = ? AND usuario_id = ?
             LIMIT 1",
            "ii",
            [$id, $usuarioId]
        );
    }

    public static function create(string $nome, string $descricao, int $usuarioId): bool
    {
        return self::execute(
            "INSERT INTO carteiras (nome, descricao, usuario_id) VALUES (?, ?, ?)",
            "ssi",
            [$nome, $descricao, $usuarioId]
        );
    }

    public static function update(int $id, int $usuarioId, string $nome, string $descricao): bool
    {
        return self::execute(
            "UPDATE carteiras SET nome = ?, descricao = ?
             WHERE id = ? AND usuario_id = ?",
            "ssii",
            [$nome, $descricao, $id, $usuarioId]
        );
    }

    public static function delete(int $id, int $usuarioId): bool
    {
        return self::execute(
            "DELETE FROM carteiras WHERE id = ? AND usuario_id = ?",
            "ii",
            [$id, $usuarioId]
        );
    }

    /* Carteiras do usuário já com ativos, cotações e totais. */
    public static function resumoPorUsuario(int $usuarioId): array
    {
        $linhas = self::fetchAll(
            "SELECT id, nome, descricao, criado_em
             FROM carteiras
             WHERE usuario_id = ?
             ORDER BY criado_em DESC",
            "i",
            [$usuarioId]
        );

        $carteiras = [];

        foreach ($linhas as $c) {
            $c["ativos"] = [];
            $c["total_investido"] = 0;
            $c["total_atual"] = 0;
            $c["resultado"] = 0;
            $c["cotacoes_disponiveis"] = 0;
            $c["total_ativos"] = 0;

            $carteiras[$c["id"]] = $c;
        }

        if (empty($carteiras)) {
            return [];
        }

        $ativos = Ativo::allByUser($usuarioId);

        $tickers = [];

        foreach ($ativos as $a) {
            if (!empty($a["ticker"])) {
                $tickers[] = strtoupper(trim($a["ticker"]));
            }
        }

        $cotacoes = CotacaoService::obter($tickers);

        foreach ($ativos as $a) {

            $id = (int) $a["carteira_id"];

            if (!isset($carteiras[$id])) {
                continue;
            }

            $a = Ativo::comCotacao($a, $cotacoes);

            $carteiras[$id]["total_investido"] += $a["valor_investido"];
            $carteiras[$id]["total_ativos"]++;

            if ($a["valor_atual"] !== null) {
                $carteiras[$id]["total_atual"] += $a["valor_atual"];
                $carteiras[$id]["resultado"] += $a["resultado"];
                $carteiras[$id]["cotacoes_disponiveis"]++;
            }

            $carteiras[$id]["ativos"][] = $a;
        }

        return $carteiras;
    }

    public static function contar(int $usuarioId): int
    {
        $linha = self::fetchOne(
            "SELECT COUNT(*) AS total FROM carteiras WHERE usuario_id = ?",
            "i",
            [$usuarioId]
        );

        return (int) $linha["total"];
    }

    /* Patrimônio (quantidade x preço médio) de cada carteira. */
    public static function patrimonioPorCarteira(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT ca.nome,
                    COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS patrimonio
             FROM carteiras ca
             LEFT JOIN ativos a ON a.carteira_id = ca.id
             WHERE ca.usuario_id = ?
             GROUP BY ca.id, ca.nome
             ORDER BY patrimonio DESC",
            "i",
            [$usuarioId]
        );
    }

    /* Carteira com maior patrimônio. */
    public static function principal(int $usuarioId): ?array
    {
        return self::fetchOne(
            "SELECT c.nome,
                    COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS valor
             FROM carteiras c
             LEFT JOIN ativos a ON a.carteira_id = c.id
             WHERE c.usuario_id = ?
             GROUP BY c.id, c.nome
             ORDER BY valor DESC
             LIMIT 1",
            "i",
            [$usuarioId]
        );
    }
}
