<?php

namespace App\Services;

/* Acesso à API brapi (cotações e busca de ativos). */
class CotacaoService
{
    private const URL_COTACAO = "https://brapi.dev/api/v2/stocks/quote?symbols=";
    private const URL_LISTA = "https://brapi.dev/api/quote/list";
    private const URL_TICKERS = "https://brapi.dev/api/v2/tickers?search=";
    private const URL_INDICADORES_FII = "https://brapi.dev/api/v2/fii/indicators?symbols=";
    private const URL_COTACAO_CRIPTO = "https://brapi.dev/api/v2/crypto?coin=";
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

    public static function obterParaAtivos(array $ativos): array
    {
        $acoes = [];
        $consultasEspecificas = [];

        foreach ($ativos as $ativo) {
            $ticker = strtoupper(trim((string) ($ativo["ticker"] ?? "")));
            $categoria = trim((string) ($ativo["categoria"] ?? ""));

            if ($ticker === "") {
                continue;
            }

            if ($categoria === "Ações") {
                $acoes[] = $ticker;
            } elseif (in_array($categoria, ["FIIs", "ETFs", "Criptomoedas"], true)) {
                $consultasEspecificas[$categoria . ":" . $ticker] = [
                    "categoria" => $categoria,
                    "ticker" => $ticker,
                ];
            }
        }

        $cotacoes = self::obter($acoes);

        foreach ($consultasEspecificas as $consulta) {
            [$status, $resposta] = match ($consulta["categoria"]) {
                "FIIs" => self::cotacaoFii($consulta["ticker"]),
                "ETFs" => self::cotacaoEtf($consulta["ticker"]),
                default => self::cotacaoCripto($consulta["ticker"]),
            };

            if ($status === 200 && is_array($resposta["ativo"] ?? null)) {
                $cotacoes[$consulta["ticker"]] = $resposta["ativo"];
            }
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

    /* Preço atual e DY mensal de um FII selecionado. */
    public static function dadosFii(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));

        if (!preg_match(self::REGEX_TICKER, $ticker)) {
            return [400, ["sucesso" => false, "mensagem" => "Ticker de FII inválido."]];
        }

        $cotacao = self::obterCotacaoFii($ticker);

        if ($cotacao === null) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "A Brapi não retornou uma cotação atual válida para esse FII.",
            ]];
        }

        [$corpo, $codigo, $erro] = self::http(
            self::URL_INDICADORES_FII . urlencode($ticker)
        );

        if ($corpo === false) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "Não foi possível consultar o dividend yield na Brapi.",
                "detalhes" => $erro,
            ]];
        }

        if ($codigo === 401 || $codigo === 403) {
            return [$codigo, [
                "sucesso" => false,
                "mensagem" => "A Brapi exige acesso Pro para os indicadores deste FII. Configure BRAPI_API_KEY no ambiente do servidor.",
            ]];
        }

        if ($codigo !== 200) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "A Brapi não conseguiu retornar os indicadores deste FII.",
                "codigo_http" => $codigo,
            ]];
        }

        $dadosIndicadores = json_decode($corpo, true);
        $indicador = null;

        foreach (($dadosIndicadores["fiis"] ?? []) as $resultado) {
            if (strtoupper((string) ($resultado["symbol"] ?? "")) === $ticker) {
                $indicador = $resultado;
                break;
            }
        }

        $dyMensalRaw = $indicador["dividendYield1m"] ?? null;
        if (
            !is_numeric($dyMensalRaw)
            || !is_finite((float) $dyMensalRaw)
            || (float) $dyMensalRaw < 0
        ) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "A Brapi não retornou o dividend yield mensal deste FII.",
            ]];
        }

        return [200, [
            "sucesso" => true,
            "ativo" => [
                "ticker" => $ticker,
                "nome" => $cotacao["nome"],
                "preco_atual" => $cotacao["preco_atual"],
                "variacao_percentual" => $cotacao["variacao_percentual"],
                "dy_mensal" => (float) $dyMensalRaw * 100,
                "data_referencia_dy" => $indicador["asOfDate"] ?? null,
            ],
        ]];
    }

    /* Cotação de FII sem exigir acesso aos indicadores Pro. */
    public static function cotacaoFii(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));

        if (!preg_match(self::REGEX_TICKER, $ticker)) {
            return [400, ["sucesso" => false, "mensagem" => "Ticker de FII inválido."]];
        }

        $cotacao = self::obterCotacaoFii($ticker);

        if ($cotacao === null) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "A Brapi não retornou uma cotação atual válida para esse FII.",
            ]];
        }

        return [200, [
            "sucesso" => true,
            "ativo" => [
                "ticker" => $ticker,
                "nome" => $cotacao["nome"],
                "preco_atual" => $cotacao["preco_atual"],
                "variacao_percentual" => $cotacao["variacao_percentual"],
            ],
        ]];
    }

    public static function cotacaoEtf(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));

        if (!preg_match(self::REGEX_TICKER, $ticker)) {
            return [400, ["sucesso" => false, "mensagem" => "Ticker de ETF inválido."]];
        }

        $dados = self::consultarJson(
            self::URL_TICKERS
                . urlencode($ticker)
                . "&subType=etf&limit=10"
        );

        if ($dados === null) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "Não foi possível consultar a cotação do ETF na Brapi.",
            ]];
        }

        foreach (($dados["results"] ?? []) as $resultado) {
            if (
                strtoupper((string) ($resultado["symbol"] ?? "")) !== $ticker
                || ($resultado["subType"] ?? "") !== "etf"
            ) {
                continue;
            }

            $quote = $resultado["quote"] ?? [];
            $preco = $quote["lastPrice"] ?? null;
            $variacao = $quote["changePercent"] ?? null;

            if (
                !is_numeric($preco)
                || !is_finite((float) $preco)
                || (float) $preco <= 0
            ) {
                return [502, [
                    "sucesso" => false,
                    "mensagem" => "A Brapi não retornou uma cotação válida para este ETF.",
                ]];
            }

            return [200, [
                "sucesso" => true,
                "ativo" => [
                    "ticker" => $ticker,
                    "nome" => $resultado["longName"]
                        ?? $resultado["name"]
                        ?? $ticker,
                    "preco_atual" => (float) $preco,
                    "variacao_percentual" => is_numeric($variacao)
                        && is_finite((float) $variacao)
                        ? (float) $variacao
                        : null,
                ],
            ]];
        }

        return [404, [
            "sucesso" => false,
            "mensagem" => "ETF não encontrado na Brapi.",
        ]];
    }

    public static function cotacaoCripto(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));

        if (!preg_match('/^[A-Z0-9]{2,12}$/', $ticker)) {
            return [400, ["sucesso" => false, "mensagem" => "Ticker de criptomoeda inválido."]];
        }

        [$corpo, $codigo, $erro] = self::http(
            self::URL_COTACAO_CRIPTO
                . urlencode($ticker)
                . "&currency=BRL"
        );

        if ($corpo === false) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "Não foi possível consultar a cotação da criptomoeda na Brapi.",
                "detalhes" => $erro,
            ]];
        }

        if ($codigo === 401 || $codigo === 403) {
            return [$codigo, [
                "sucesso" => false,
                "mensagem" => "A Brapi exige uma chave de acesso para consultar criptomoedas. Configure BRAPI_API_KEY no servidor.",
            ]];
        }

        if ($codigo !== 200) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "A Brapi não conseguiu retornar a cotação desta criptomoeda.",
                "codigo_http" => $codigo,
            ]];
        }

        $dados = json_decode($corpo, true);
        $coin = null;

        foreach (($dados["coins"] ?? []) as $resultado) {
            if (strtoupper((string) ($resultado["coin"] ?? "")) === $ticker) {
                $coin = $resultado;
                break;
            }
        }

        $preco = $coin["regularMarketPrice"] ?? null;
        $variacao = $coin["regularMarketChangePercent"] ?? null;

        if (
            !is_numeric($preco)
            || !is_finite((float) $preco)
            || (float) $preco <= 0
        ) {
            return [502, [
                "sucesso" => false,
                "mensagem" => "A Brapi não retornou uma cotação válida para esta criptomoeda.",
            ]];
        }

        return [200, [
            "sucesso" => true,
            "ativo" => [
                "ticker" => $ticker,
                "nome" => $coin["coinName"] ?? $ticker,
                "preco_atual" => (float) $preco,
                "variacao_percentual" => is_numeric($variacao)
                    && is_finite((float) $variacao)
                    ? (float) $variacao
                    : null,
            ],
        ]];
    }

    /* Busca ativos pelo ticker ou nome (autocomplete). [status, corpo] */
    public static function buscarAtivos(string $termo, ?string $categoria = null): array
    {
        $termo = trim($termo);

        if ($termo === "" || strlen($termo) < 2) {
            return [200, ["sucesso" => true, "resultados" => []]];
        }

        $categoriasPermitidas = ["Ações", "FIIs", "ETFs", "Criptomoedas"];
        if ($categoria !== null && !in_array($categoria, $categoriasPermitidas, true)) {
            return [400, ["sucesso" => false, "mensagem" => "Categoria de ativo inválida."]];
        }

        $termo = strtoupper($termo);

        if ($categoria === "ETFs") {
            $dados = self::consultarJson(
                self::URL_TICKERS
                    . urlencode($termo)
                    . "&subType=etf&limit=8"
            );

            if ($dados === null) {
                return [502, [
                    "sucesso" => false,
                    "mensagem" => "Não foi possível buscar ETFs na Brapi.",
                ]];
            }

            $resultados = [];
            foreach (($dados["results"] ?? []) as $ativo) {
                $ticker = strtoupper((string) ($ativo["symbol"] ?? ""));

                if ($ticker === "" || ($ativo["subType"] ?? "") !== "etf") {
                    continue;
                }

                $resultados[] = [
                    "ticker" => $ticker,
                    "nome" => $ativo["longName"]
                        ?? $ativo["name"]
                        ?? $ticker,
                    "categoria" => "ETFs",
                ];
            }

            return [200, ["sucesso" => true, "resultados" => $resultados]];
        }

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
                $categoriaAtivo = self::inferirCategoria($ticker, $nome);
                if ($categoria !== null && $categoriaAtivo !== $categoria) {
                    continue;
                }

                $resultados[] = [
                    "ticker" => $ticker,
                    "nome" => $nome,
                    "categoria" => $categoriaAtivo,
                ];
            }

            /* Não precisamos retornar centenas de resultados. */
            if (count($resultados) >= 8) {
                break;
            }
        }

        return [200, ["sucesso" => true, "resultados" => $resultados]];
    }

    /* ---------- internos ---------- */

    private static function obterCotacaoFii(string $ticker): ?array
    {
        $dadosTicker = self::consultarJson(
            self::URL_TICKERS
                . urlencode($ticker)
                . "&subType=fii&limit=10"
        );

        foreach (($dadosTicker["results"] ?? []) as $resultado) {
            if (
                strtoupper((string) ($resultado["symbol"] ?? "")) === $ticker
                && ($resultado["subType"] ?? "") === "fii"
            ) {
                $quote = $resultado["quote"] ?? [];
                $cotacao = $quote["lastPrice"] ?? null;

                if (
                    is_numeric($cotacao)
                    && is_finite((float) $cotacao)
                    && (float) $cotacao > 0
                ) {
                    $variacao = $quote["changePercent"] ?? null;

                    return [
                        "nome" => $resultado["longName"]
                            ?? $resultado["name"]
                            ?? $ticker,
                        "preco_atual" => (float) $cotacao,
                        "variacao_percentual" => is_numeric($variacao)
                            && is_finite((float) $variacao)
                            ? (float) $variacao
                            : null,
                    ];
                }

                return null;
            }
        }

        return null;
    }

    private static function inferirCategoria(string $ticker, string $nome): string
    {
        $texto = strtoupper($ticker . " " . $nome);

        if (
            str_contains($texto, "CRYPTO")
            || str_contains($texto, "CRIPT")
            || str_contains($texto, "BITCOIN")
            || str_contains($texto, "ETHEREUM")
        ) {
            return "Criptomoedas";
        }

        if (
            str_contains($texto, "ETF")
            || str_contains($texto, "ISHARES")
            || str_contains($texto, "TREND")
            || str_contains($texto, "IT NOW")
        ) {
            return "ETFs";
        }

        if (
            preg_match('/[0-9]11$/', $ticker)
            || str_contains($texto, "FII")
            || str_contains($texto, "FUNDO IMOBILI")
            || str_contains($texto, "FUNDO DE INVESTIMENTO IMOBILI")
        ) {
            return "FIIs";
        }

        return "Ações";
    }

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
        $headers = ["Accept: application/json"];
        $apiKey = getenv("BRAPI_API_KEY");

        if (is_string($apiKey) && trim($apiKey) !== "") {
            $headers[] = "Authorization: Bearer " . trim($apiKey);
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $corpo = curl_exec($curl);
        $erro = curl_error($curl);
        $codigo = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [$corpo, $codigo, $erro];
    }
}
