<?php

namespace App\Services;

/* Acesso à API brapi (cotações e busca de ativos). */
class CotacaoService
{
    private const URL_COTACAO = "https://brapi.dev/api/v2/stocks/quote?symbols=";
    private const URL_LISTA = "https://brapi.dev/api/quote/list";
    private const REGEX_TICKER = '/^[A-Z0-9^.-]{1,20}$/';

    /*
     * Cotações de vários ativos. Tenta primeiro em lote; se algum ticker
     * ficar de fora, consulta individualmente para que um ticker inválido
     * não impeça os demais.
     */
    public static function obter(array $tickers): array
    {
        $tickers = array_map(function ($ticker) {
            return strtoupper(trim($ticker));
        }, $tickers);

        $tickers = array_values(array_filter(array_unique($tickers), function ($ticker) {
            return preg_match(self::REGEX_TICKER, $ticker);
        }));

        if (empty($tickers)) {
            return [];
        }

        $cotacoes = [];

        $dados = self::consultarJson(self::URL_COTACAO . urlencode(implode(",", $tickers)));

        if (isset($dados["results"]) && is_array($dados["results"])) {
            foreach ($dados["results"] as $resultado) {

                $ticker = $resultado["symbol"] ?? null;

                if (!$ticker) {
                    continue;
                }

                $cotacoes[$ticker] = self::normalizar($resultado, $ticker);
            }
        }

        foreach ($tickers as $ticker) {

            if (isset($cotacoes[$ticker])) {
                continue;
            }

            $individual = self::consultarJson(self::URL_COTACAO . urlencode($ticker));

            if (empty($individual["results"]) || !is_array($individual["results"])) {
                continue;
            }

            $resultado = $individual["results"][0];
            $retornado = $resultado["symbol"] ?? $ticker;

            $cotacoes[$retornado] = self::normalizar($resultado, $retornado);
        }

        return $cotacoes;
    }

    /* Cotação de um único ticker. Devolve [status HTTP, corpo da resposta]. */
    public static function cotacaoUnica(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));

        if ($ticker === "") {
            return [400, ["sucesso" => false, "mensagem" => "Ticker não informado."]];
        }

        if (!preg_match(self::REGEX_TICKER, $ticker)) {
            return [400, ["sucesso" => false, "mensagem" => "Ticker inválido."]];
        }

        [$corpo, $codigo, $erro] = self::http(self::URL_COTACAO . urlencode($ticker));

        if ($corpo === false) {
            return [500, [
                "sucesso" => false,
                "mensagem" => "Erro ao consultar a API.",
                "detalhes" => $erro,
            ]];
        }

        if ($codigo !== 200) {
            return [$codigo, [
                "sucesso" => false,
                "mensagem" => "A API retornou um erro.",
                "codigo_http" => $codigo,
            ]];
        }

        $dados = json_decode($corpo, true);

        if (empty($dados["results"]) || !is_array($dados["results"])) {
            return [200, ["sucesso" => false, "mensagem" => "Ativo não encontrado."]];
        }

        return [200, [
            "sucesso" => true,
            "ativo" => self::normalizar($dados["results"][0], $ticker),
        ]];
    }

    /* Busca ativos pelo ticker ou nome (autocomplete). [status, corpo] */
    public static function buscarAtivos(string $termo): array
    {
        $termo = trim($termo);

        if ($termo === "" || strlen($termo) < 2) {
            return [200, ["sucesso" => true, "resultados" => []]];
        }

        $termo = strtoupper($termo);

        [$corpo, $codigo] = self::http(self::URL_LISTA);

        if ($corpo === false) {
            return [500, ["sucesso" => false, "mensagem" => "Não foi possível consultar a API."]];
        }

        if ($codigo !== 200) {
            return [502, ["sucesso" => false, "mensagem" => "A API retornou um erro."]];
        }

        $dados = json_decode($corpo, true);

        if (!isset($dados["stocks"]) || !is_array($dados["stocks"])) {
            return [200, ["sucesso" => true, "resultados" => []]];
        }

        $resultados = [];

        foreach ($dados["stocks"] as $ativo) {

            $ticker = strtoupper($ativo["stock"] ?? "");
            $nome = $ativo["name"] ?? "";

            if ($ticker === "") {
                continue;
            }

            /* Procura tanto pelo ticker quanto pelo nome. */
            if (stripos($ticker, $termo) !== false || stripos($nome, $termo) !== false) {
                $resultados[] = ["ticker" => $ticker, "nome" => $nome];
            }

            /* Não precisamos retornar centenas de resultados. */
            if (count($resultados) >= 8) {
                break;
            }
        }

        return [200, ["sucesso" => true, "resultados" => $resultados]];
    }

    /* ---------- internos ---------- */

    /* Converte um resultado da brapi no formato usado pelo sistema. */
    private static function normalizar(array $resultado, string $ticker): array
    {
        $ativo = $resultado["data"] ?? $resultado;

        return [
            "ticker" => $resultado["symbol"] ?? $ticker,
            "nome" => $ativo["longName"] ?? $ativo["shortName"] ?? "",
            "moeda" => $ativo["currency"] ?? "BRL",
            "preco_atual" => $ativo["regularMarketPrice"] ?? null,
            "variacao" => $ativo["regularMarketChange"] ?? null,
            "variacao_percentual" => $ativo["regularMarketChangePercent"] ?? null,
            "maxima_dia" => $ativo["regularMarketDayHigh"] ?? null,
            "minima_dia" => $ativo["regularMarketDayLow"] ?? null,
            "volume" => $ativo["regularMarketVolume"] ?? null,
            "preco_anterior" => $ativo["regularMarketPreviousClose"] ?? null,
            "abertura" => $ativo["regularMarketOpen"] ?? null,
            "horario" => $ativo["regularMarketTime"] ?? null,
        ];
    }

    /* GET + decodificação JSON; null se falhar ou não for HTTP 200. */
    private static function consultarJson(string $url): ?array
    {
        [$corpo, $codigo] = self::http($url);

        if ($corpo === false || $codigo !== 200) {
            return null;
        }

        $dados = json_decode($corpo, true);

        return is_array($dados) ? $dados : null;
    }

    /* Retorna [corpo|false, código HTTP, erro do curl]. */
    private static function http(string $url): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => ["Accept: application/json"],
        ]);

        $corpo = curl_exec($curl);
        $erro = curl_error($curl);
        $codigo = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [$corpo, $codigo, $erro];
    }
}
