<?php

/**
 * Consulta cotações de vários ativos na brapi.
 *
 * Caso uma consulta em lote falhe, o sistema tenta consultar
 * os ativos individualmente para que um ticker inválido
 * não impeça os demais de funcionar.
 */

function obterCotacoes(array $tickers): array
{
    /*
    |--------------------------------------------------------------------------
    | Limpa e organiza os tickers
    |--------------------------------------------------------------------------
    */

    $tickers = array_map(function ($ticker) {
        return strtoupper(trim($ticker));
    }, $tickers);

    $tickers = array_unique($tickers);

    $tickers = array_values(array_filter($tickers, function ($ticker) {
        return preg_match('/^[A-Z0-9^.-]{1,20}$/', $ticker);
    }));

    if (empty($tickers)) {
        return [];
    }


    /*
    |--------------------------------------------------------------------------
    | Primeiro tenta consultar todos de uma vez
    |--------------------------------------------------------------------------
    */

    $url = "https://brapi.dev/api/v2/stocks/quote?symbols="
         . urlencode(implode(",", $tickers));

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json"
        ]
    ]);

    $resposta = curl_exec($curl);

    $codigoHttp = curl_getinfo(
        $curl,
        CURLINFO_HTTP_CODE
    );

    curl_close($curl);


    /*
    |--------------------------------------------------------------------------
    | Organiza os resultados
    |--------------------------------------------------------------------------
    */

    $cotacoes = [];

    if (
        $resposta !== false &&
        $codigoHttp === 200
    ) {

        $dados = json_decode(
            $resposta,
            true
        );

        if (
            is_array($dados) &&
            isset($dados["results"]) &&
            is_array($dados["results"])
        ) {

            foreach ($dados["results"] as $resultado) {

                $ticker = $resultado["symbol"] ?? null;

                if (!$ticker) {
                    continue;
                }

                $ativo = $resultado["data"]
                    ?? $resultado;


                $cotacoes[$ticker] = [

                    "ticker" => $ticker,

                    "nome" =>
                        $ativo["longName"]
                        ?? $ativo["shortName"]
                        ?? "",

                    "moeda" =>
                        $ativo["currency"]
                        ?? "BRL",

                    "preco_atual" =>
                        $ativo["regularMarketPrice"]
                        ?? null,

                    "variacao" =>
                        $ativo["regularMarketChange"]
                        ?? null,

                    "variacao_percentual" =>
                        $ativo["regularMarketChangePercent"]
                        ?? null,

                    "maxima_dia" =>
                        $ativo["regularMarketDayHigh"]
                        ?? null,

                    "minima_dia" =>
                        $ativo["regularMarketDayLow"]
                        ?? null,

                    "volume" =>
                        $ativo["regularMarketVolume"]
                        ?? null,

                    "preco_anterior" =>
                        $ativo["regularMarketPreviousClose"]
                        ?? null,

                    "abertura" =>
                        $ativo["regularMarketOpen"]
                        ?? null,

                    "horario" =>
                        $ativo["regularMarketTime"]
                        ?? null
                ];
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    |
    | Se algum ticker não apareceu na consulta em lote,
    | tentamos consultar individualmente.
    |
    */

    foreach ($tickers as $ticker) {

        if (isset($cotacoes[$ticker])) {
            continue;
        }


        $urlIndividual =
            "https://brapi.dev/api/v2/stocks/quote?symbols="
            . urlencode($ticker);


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $urlIndividual,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json"
            ]
        ]);

        $respostaIndividual =
            curl_exec($curl);

        $codigoIndividual =
            curl_getinfo(
                $curl,
                CURLINFO_HTTP_CODE
            );

        curl_close($curl);


        if (
            $respostaIndividual === false ||
            $codigoIndividual !== 200
        ) {
            continue;
        }


        $dadosIndividual =
            json_decode(
                $respostaIndividual,
                true
            );


        if (
            !is_array($dadosIndividual) ||
            !isset($dadosIndividual["results"]) ||
            !is_array($dadosIndividual["results"]) ||
            count($dadosIndividual["results"]) === 0
        ) {
            continue;
        }


        $resultado =
            $dadosIndividual["results"][0];


        $tickerRetornado =
            $resultado["symbol"]
            ?? $ticker;


        $ativo =
            $resultado["data"]
            ?? $resultado;


        /*
        |--------------------------------------------------------------------------
        | Adiciona a cotação individual encontrada
        |--------------------------------------------------------------------------
        */

        $cotacoes[$tickerRetornado] = [

            "ticker" =>
                $tickerRetornado,

            "nome" =>
                $ativo["longName"]
                ?? $ativo["shortName"]
                ?? "",

            "moeda" =>
                $ativo["currency"]
                ?? "BRL",

            "preco_atual" =>
                $ativo["regularMarketPrice"]
                ?? null,

            "variacao" =>
                $ativo["regularMarketChange"]
                ?? null,

            "variacao_percentual" =>
                $ativo["regularMarketChangePercent"]
                ?? null,

            "maxima_dia" =>
                $ativo["regularMarketDayHigh"]
                ?? null,

            "minima_dia" =>
                $ativo["regularMarketDayLow"]
                ?? null,

            "volume" =>
                $ativo["regularMarketVolume"]
                ?? null,

            "preco_anterior" =>
                $ativo["regularMarketPreviousClose"]
                ?? null,

            "abertura" =>
                $ativo["regularMarketOpen"]
                ?? null,

            "horario" =>
                $ativo["regularMarketTime"]
                ?? null
        ];
    }


    return $cotacoes;
}