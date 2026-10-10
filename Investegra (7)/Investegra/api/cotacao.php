<?php

header("Content-Type: application/json; charset=UTF-8");

$ticker = strtoupper(trim($_GET["ticker"] ?? ""));

if ($ticker === "") {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Ticker não informado."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (!preg_match('/^[A-Z0-9^.-]{1,20}$/', $ticker)) {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Ticker inválido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Consulta à brapi
|--------------------------------------------------------------------------
*/

$url = "https://brapi.dev/api/v2/stocks/quote?symbols=" . urlencode($ticker);

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

if ($resposta === false) {

    $erro = curl_error($curl);

    curl_close($curl);

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao consultar a API.",
        "detalhes" => $erro
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$codigo_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);


/*
|--------------------------------------------------------------------------
| Verifica resposta HTTP
|--------------------------------------------------------------------------
*/

if ($codigo_http !== 200) {

    http_response_code($codigo_http);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A API retornou um erro.",
        "codigo_http" => $codigo_http
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Converte JSON
|--------------------------------------------------------------------------
*/

$dados = json_decode($resposta, true);

if (
    !is_array($dados) ||
    !isset($dados["results"]) ||
    !is_array($dados["results"]) ||
    count($dados["results"]) === 0
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Ativo não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Dados do ativo
|--------------------------------------------------------------------------
|
| Na resposta atual da brapi, os dados ficam dentro de:
|
| results[0]["data"]
|
*/

$resultado = $dados["results"][0];

$ativo = $resultado["data"] ?? $resultado;


/*
|--------------------------------------------------------------------------
| Retorno padronizado para o INVESTEGRA
|--------------------------------------------------------------------------
*/

echo json_encode([

    "sucesso" => true,

    "ativo" => [

        "ticker" => $resultado["symbol"] ?? $ticker,

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
    ]

], JSON_UNESCAPED_UNICODE);