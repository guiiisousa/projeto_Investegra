<?php

namespace App\Models;

use App\Core\Model;

class Ativo extends Model
{
    public static function allByUser(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT a.id, a.carteira_id, a.ticker, a.quantidade,
                    a.preco_medio, a.data_compra, c.nome AS categoria
             FROM ativos a
             INNER JOIN carteiras ca ON a.carteira_id = ca.id
             LEFT JOIN categorias c ON a.categoria_id = c.id
             WHERE ca.usuario_id = ?
             ORDER BY a.data_compra DESC, a.id DESC",
            "i",
            [$usuarioId]
        );
    }

    public static function allByCarteira(int $carteiraId): array
    {
        return self::fetchAll(
            "SELECT a.id, a.ticker, a.quantidade, a.preco_medio,
                    a.data_compra, a.observacao, c.nome AS categoria
             FROM ativos a
             LEFT JOIN categorias c ON a.categoria_id = c.id
             WHERE a.carteira_id = ?
             ORDER BY a.data_compra DESC, a.id DESC",
            "i",
            [$carteiraId]
        );
    }

    /* Ativo + nome da carteira, somente se pertencer ao usuário. */
    public static function findOwned(int $id, int $usuarioId): ?array
    {
        return self::fetchOne(
            "SELECT a.id, a.carteira_id, a.categoria_id, a.ticker,
                    a.quantidade, a.preco_medio, a.data_compra,
                    a.observacao, c.nome AS carteira_nome
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE a.id = ? AND c.usuario_id = ?",
            "ii",
            [$id, $usuarioId]
        );
    }

    public static function create(array $d): bool
    {
        return self::execute(
            "INSERT INTO ativos
                (carteira_id, categoria_id, ticker, quantidade,
                 preco_medio, data_compra, observacao)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            "iisddss",
            [
                $d["carteira_id"], $d["categoria_id"], $d["ticker"],
                $d["quantidade"], $d["preco_medio"],
                $d["data_compra"], $d["observacao"],
            ]
        );
    }

    public static function update(int $id, array $d): bool
    {
        return self::execute(
            "UPDATE ativos
             SET ticker = ?, categoria_id = ?, quantidade = ?,
                 preco_medio = ?, data_compra = ?, observacao = ?
             WHERE id = ?",
            "siddssi",
            [
                $d["ticker"], $d["categoria_id"], $d["quantidade"],
                $d["preco_medio"], $d["data_compra"], $d["observacao"], $id,
            ]
        );
    }

    public static function delete(int $id): bool
    {
        return self::execute("DELETE FROM ativos WHERE id = ?", "i", [$id]);
    }

    /* ---------- regras de negócio ---------- */

    /* Aplica a cotação a um ativo e calcula valores/resultado. */
    public static function comCotacao(array $ativo, array $cotacoes): array
    {
        $ticker = strtoupper(trim($ativo["ticker"]));

        $quantidade = (float) $ativo["quantidade"];
        $precoMedio = (float) $ativo["preco_medio"];

        $ativo["ticker"] = $ticker;
        $ativo["quantidade"] = $quantidade;
        $ativo["preco_medio"] = $precoMedio;
        $ativo["valor_investido"] = $quantidade * $precoMedio;

        $cotacao = $cotacoes[$ticker] ?? null;

        $ativo["cotacao"] = $cotacao;
        $ativo["preco_atual"] = null;
        $ativo["valor_atual"] = null;
        $ativo["resultado"] = null;
        $ativo["variacao_percentual"] = null;

        if ($cotacao !== null && isset($cotacao["preco_atual"])) {

            $precoAtual = (float) $cotacao["preco_atual"];
            $valorAtual = $quantidade * $precoAtual;

            $ativo["preco_atual"] = $precoAtual;
            $ativo["valor_atual"] = $valorAtual;
            $ativo["resultado"] = $valorAtual - $ativo["valor_investido"];
            $ativo["variacao_percentual"] = $cotacao["variacao_percentual"] ?? null;
        }

        return $ativo;
    }

    /* Totais e rentabilidade de uma lista de ativos de uma carteira. */
    public static function resumir(array $ativos, array $cotacoes): array
    {
        $totalInvestido = 0.0;
        $totalAtual = 0.0;
        $totalResultado = 0.0;

        foreach ($ativos as $i => $ativo) {

            $ativo = self::comCotacao($ativo, $cotacoes);

            $totalInvestido += $ativo["valor_investido"];

            if ($ativo["valor_atual"] !== null) {
                $totalAtual += $ativo["valor_atual"];
                $totalResultado += $ativo["resultado"];
            }

            $ativos[$i] = $ativo;
        }

        return [
            "ativos" => $ativos,
            "totalInvestido" => $totalInvestido,
            "totalAtual" => $totalAtual,
            "totalResultado" => $totalResultado,
            "rentabilidade" => $totalInvestido > 0
                ? ($totalResultado / $totalInvestido) * 100
                : null,
        ];
    }

    /* ---------- consultas agregadas por usuário ---------- */

    public static function contar(int $usuarioId): int
    {
        $linha = self::fetchOne(
            "SELECT COUNT(*) AS total
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE c.usuario_id = ?",
            "i",
            [$usuarioId]
        );

        return (int) $linha["total"];
    }

    public static function patrimonio(int $usuarioId): float
    {
        $linha = self::fetchOne(
            "SELECT COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS patrimonio
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE c.usuario_id = ?",
            "i",
            [$usuarioId]
        );

        return (float) $linha["patrimonio"];
    }

    /* Patrimônio e quantidade de ativos (considera carteiras vazias). */
    public static function resumoPatrimonio(int $usuarioId): array
    {
        $linha = self::fetchOne(
            "SELECT COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS patrimonio,
                    COUNT(a.id) AS quantidade_ativos
             FROM carteiras c
             LEFT JOIN ativos a ON a.carteira_id = c.id
             WHERE c.usuario_id = ?",
            "i",
            [$usuarioId]
        );

        return [
            "patrimonio" => (float) $linha["patrimonio"],
            "quantidade_ativos" => (int) $linha["quantidade_ativos"],
        ];
    }

    /* Valor investido por categoria, do maior para o menor. */
    public static function distribuicaoPorCategoria(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT COALESCE(cat.nome, 'Sem categoria') AS categoria,
                    COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS valor
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             LEFT JOIN categorias cat ON a.categoria_id = cat.id
             WHERE c.usuario_id = ?
             GROUP BY cat.id, cat.nome
             ORDER BY valor DESC",
            "i",
            [$usuarioId]
        );
    }

    public static function maiorAtivo(int $usuarioId): ?array
    {
        return self::fetchOne(
            "SELECT a.ticker, (a.quantidade * a.preco_medio) AS valor
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE c.usuario_id = ?
             ORDER BY valor DESC
             LIMIT 1",
            "i",
            [$usuarioId]
        );
    }

    /*
     * Categoria com maior valor investido.
     * Por padrão ignora ativos sem categoria (indicadores);
     * alertas e recomendações os consideram.
     */
    public static function maiorCategoria(int $usuarioId, bool $incluirSemCategoria = false): ?array
    {
        $juncao = $incluirSemCategoria ? "LEFT JOIN" : "INNER JOIN";

        return self::fetchOne(
            "SELECT cat.nome AS categoria,
                    SUM(a.quantidade * a.preco_medio) AS valor
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             $juncao categorias cat ON a.categoria_id = cat.id
             WHERE c.usuario_id = ?
             GROUP BY cat.id, cat.nome
             ORDER BY valor DESC
             LIMIT 1",
            "i",
            [$usuarioId]
        );
    }

    public static function totalCategorias(int $usuarioId): int
    {
        $linha = self::fetchOne(
            "SELECT COUNT(DISTINCT a.categoria_id) AS total
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE c.usuario_id = ?
               AND a.categoria_id IS NOT NULL",
            "i",
            [$usuarioId]
        );

        return (int) $linha["total"];
    }
}
